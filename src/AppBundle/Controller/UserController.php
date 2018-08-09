<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends Controller
{
    /**
     * @Route("/user/add", name="AddUser")
     */
    public function editAction(Request $request)
    {
        return $this->render("user/add.html.twig");
    }

    /**
     * @Route("/user/all", name="allUser")
     */
    public function allAction(Request $request)
    {
        $u = $request->getSession()->get("user");
        $em = $this->getDoctrine()->getManager();
        $page = 1;

        if (key_exists('page', $_GET)) {
            $number = intval($_GET['page']);
            if ($number > 0) {
                $page = $number;
            }
        }

        $allPage = $this->getDoctrine()
            ->getManager()
            ->createQuery('select count(u.id) from AppBundle:User u')
            ->getResult()[0]['1'];
        $allPage = intval((intval($allPage) % 10) == 0 ? intval($allPage) / 10 : (intval($allPage) / 10) + 1);

        if ($page > $allPage) {
            $page = $allPage;
        }

        $query = $em->createQuery(
            'SELECT u
        FROM AppBundle:User u'
        )->setFirstResult(($page - 1) * 10)->setMaxResults(10);

        $users = $query->getResult();

        return $this->render("user/all.html.twig", array("users" => $users, "allPage" => $allPage, "nowPage" => $page));
    }

    /**
     * @Route("/user/{id}", methods={"GET"})
     */
    public function oneAction($id, Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array("id" => $id));
        return $this->json(array("data" => $user->getJson()));
    }

    /**
     * @Route("/user", methods={"POST"})
     */
    public function addAction(Request $request, ValidatorInterface $validator)
    {
        $username = $request->get("username");
        $password = $request->get("password");
        $phoneNumber = $request->get("phoneNumber");
        $email = $request->get("email");
        $sex = $request->get("sex");
        $birthday = $request->get("birthday");
        $avatar = $request->get("avatar");
        $description = $request->get("description");

        $fu = $this->getDoctrine()
          ->getRepository(User::class)
          ->findOneBy(array("username"=>$username));

        if ($fu) {
          return (new Response('Already exists', Response::HTTP_BAD_REQUEST))->send();
        }

        $user = new User($username, $password, $phoneNumber, $email, $sex, new \DateTime($birthday), $avatar, $description);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $messages = "";
            foreach ($errors as $violation) {
                $messages .= $violation->getMessage() . "<br>";
            }
            return $this->json(Array("code" => "500", "messages" => $messages));
        }

        $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json(Array($user->getJson()));
    }

    /**
     * @Route("/user/{id}", methods={"PATCH"})
     */
    public function updateAction($id, Request $request, ValidatorInterface $validator)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'];
        $phoneNumber = $data['phoneNumber'];
        $email = $data['email'];
        $sex = $data['sex'];
        $birthday = $data['birthday'];
        $avatar = $data['avatar'];
        $description = $data['description'];

        $fu = $user = $this->getDoctrine()
          ->getRepository(User::class)
          ->findOneBy(array("username" => $username));

        if ($fu && $fu.id != $id) {
          return (new Response('Username already exists', Response::HTTP_BAD_REQUEST))->send();
        }

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array("id" => $id));

        $user->setUsername($username);
        $user->setPhoneNumber($phoneNumber);
        $user->setEmail($email);
        $user->setSex($sex);
        $user->setBirthday(new \DateTime($birthday));
        $user->setAvatar($avatar);
        $user->setDescription($description);

        $p = $user->getPassword();
        $user->setPassword("12345678");

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $messages = "";
            foreach ($errors as $violation) {
                $messages .= $violation->getMessage() . "<br>";
            }
            return $this->json(Array("code" => "500", "messages" => $messages));
        }

        $user->setPassword($p);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json(Array("data" => $user->getJson()));
    }

    /**
     * @Route("/user/{id}", methods={"DELETE"})
     */
    public function deleteAction($id, Request $request)
    {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            // Delete user.
            $doctrine = $this->getDoctrine();
            $manager = $doctrine->getManager();
            $willDeleteUser = $doctrine
                ->getRepository(User::class)
                ->findOneBy(array("id" => $id));
            $manager->remove($willDeleteUser);
            $manager->flush();
            return $this->json($willDeleteUser->getJson());
        } else {
            // Error.
            return $this->createNotFoundException("Not Found!");
        }
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
      $data = json_decode(file_get_contents('php://input'), true);
      $username = $data['username'];
      $password = $data['password'];

      $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->findOneBy(array("username" => $username));

      if ($user) {
        // Validate password.
        if (password_verify($password, $user->getPassword())) {
          // Login ok.
          $request->getSession()->set("user", $user);
          return $this->json($user->getJson());
        } else {
          // Password wrong.
          return (new Response('Password wrong', Response::HTTP_BAD_REQUEST))->send();
        }
      } else {
        // Username not found.
        return (new Response('User not found', Response::HTTP_NOT_FOUND))->send();
      }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
        $request->getSession()->remove("user");
        return $this->redirect("/user/all");
    }
}
