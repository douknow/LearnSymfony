<?php

namespace AppBundle\Controller;

use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/upload", name="upload")
     */
    public function uploadAction(Request $request)
    {
        $file = $request->files->get("avatar");
        $fileName = $file->getClientOriginalName();
        $dir = 'upload';
        $file->move($dir, $fileName);
        return new JsonResponse(array("url"=>'http://'.$request->getHttpHost().'/'.$dir.'/'.$fileName));
    }

    // /**
    //  * @Route("/adduser")
    //  */
    // public function addUserAction() {
    //   $em = $this->getDoctrine()->getManager();
    //   for ($i = 0; $i < 100; $i++) {
    //     $u = new User('hero ' .$i, 'MyPassword' .$i, '13277778787', 'i@gmail.com', 1, new \DateTime('2008-09-07'), 'black-and-white-facing-away-female-106567.jpg', 'Description: ' .$i);
    //     $em->persist($u);
    //   }

    //   $em->flush();
    // }
}
