<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectController extends Controller
{
    /**
     * @Route("/project", methods={"GET"})
     */
    public function allAction(Request $request)
    {
        $u = $this->auth($request);

        $em = $this->getDoctrine()
            ->getManager();

        $allPage = $em->createQuery(
            'select count(p.id) from AppBundle:Project p'
        )->getResult()[0]['1'];

        $page = 1;
        if (key_exists('page', $_GET)) {
            $n = intval($_GET['page']);
            if ($n > 0 && $n < $allPage) {
                $page = $n;
            }
        }

        $queryString = '';
        if (key_exists('category', $_GET)) {
          $queryString = 'select p, u.username from AppBundle:Project p join AppBundle:User u where p.managerUser = :id and p.managerUser = u.id and p.status = :category order by p.status asc';

          $query = $em
            ->createQuery(
                $queryString
            )
            ->setParameters(array('id'=>$u->getId(), 'category'=>$_GET['category']))
            ->setFirstResult(($page - 1) * 10)
            ->setMaxResults(10);
        } else {
          $queryString = 'select p, u.username from AppBundle:Project p join AppBundle:User u where p.managerUser = :id and p.managerUser = u.id order by p.status asc';

          $query = $em
            ->createQuery(
                $queryString
            )
            ->setParameter('id', $u->getId())
            ->setFirstResult(($page - 1) * 10)
            ->setMaxResults(10);
        }

        $projects = $query->getResult();
        return $this->render("project/index.html.twig", array(
            "data" => $projects
        ));
    }

    /**
     * @Route("/project", methods={"POST"})
     */
    public function addAction(Request $request)
    {
        $u = $this->auth($request);
        $data = json_decode(file_get_contents('php://input'), true);

        // Ensure project name unique.
        if (!$this->isUniqueProject($data['name'])) return;

        $project = new Project();
        $project->setName($data['name']);
        $project->setStartDate(new \DateTime());
        $project->setStatus(1);
        $project->setManagerUser($u->getId());

        $em = $this->getDoctrine()
            ->getManager();
        $em->persist($project);
        $em->flush();

        return $this->json($project->getJson());
    }
    
    private function isUniqueProject($name, $update = null) {
      $p = $this->getDoctrine()->getRepository(Project::class)->findOneBy(array('name'=>$name));
      if ($update == null) {
        if ($p) {
          // Send parameter error.
          (new Response('Already exists project name.', Response::HTTP_BAD_REQUEST ))->send();
          return false;
        }
      } else {
        if ($p && $p->getId() != $update->getId()) {
          (new Response('Already exists project name.', Response::HTTP_BAD_REQUEST ))->send();
          return false;
        }
      }
      return true;
    }

    private function auth(Request $request)
    {
        $u = $request->getSession()
            ->get("user");
        if (!$u) {
            return $this->redirect("/user/all");
        }
        return $u;
    }

    /**
     * @Route("/project/{id}", methods={"DELETE"})
     */
    public function deleteAction($id, Request $request)
    {
        $p = $this->authUserEqualProject($id, $request);

        $em = $this->getDoctrine()->getManager();
        $em->remove($p);
        $em->flush();
        return $this->json($p->getJson());
    }

    /**
     * @Route("/project/{id}", methods={"PUT"})
     */
    public function updateAction($id, Request $request) {
        $u = $this->auth($request);
        $project = $this->authUserEqualProject($id, $request);
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$this->isUniqueProject($data['name'], $project)) return;

        $data = json_decode(file_get_contents('php://input'), true);
        $project->setName($data['name']);
        $project->setStatus($data['status']);
        if ($data['status'] == 4) {
            $project->setEndDate(new \DateTime());
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json($project->getJson());
    }

    public function authUserEqualProject($id, Request $request) {
        $u = $this->auth($request);
        $p = $this->getDoctrine()
            ->getRepository(Project::class)
            ->findOneBy(array("id"=>$id));
        if (!$p) {
            return $this->createNotFoundException('Not found!');
        }

        if ($p->getManagerUser() != $u->getId()) {
            return $this->createNotFoundException('Can not delete!');
        }

        return $p;
    }
}