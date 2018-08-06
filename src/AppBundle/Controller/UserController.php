<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends Controller
{
    /**
     * @Route("/user/add", name="AddUser")
     */
    public function editAction(Request $request)
    {
        // Valid input data.
        return $this->render("user/add.html.twig");
    }

    /**
     * @Route("/user/all", name="allUser")
     */
    public function allAction(Request $request)
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        return $this->render("user/all.html.twig", array("users" => $users));
    }

    /**
     * @Route("/user/submit", name="submitUser")
     */
    public function submitAction(Request $request, ValidatorInterface $validator)
    {
        $username = $request->get("username");
        $password = $request->get("password");
        $phoneNumber = $request->get("phoneNumber");
        $email = $request->get("email");
        $sex = $request->get("sex");
        $birthday = $request->get("birthday");
        $avatar = $request->get("avatar");

        $user = new User($username, $password, $phoneNumber, $email, $sex, new \DateTime($birthday), $avatar);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $messages = "";
            foreach ($errors as $violation) {
                $messages .= $violation->getMessage() ."<br>";
            }
            return $this->render("user/error.html.twig", Array("messages" => $messages));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect("all");
    }
}
