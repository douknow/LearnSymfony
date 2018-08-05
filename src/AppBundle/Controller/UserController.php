<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/user/add", name="AddUser")
     */
    public function addAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('user/add.html.twig');
    }
}
