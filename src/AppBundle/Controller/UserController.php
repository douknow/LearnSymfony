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
        return $this->render("user/add.html.twig");
    }

    /**
     * @Route("/user/{id}", methods={"GET"})
     */
    public function oneAction($id, Request $request) {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array("id"=>$id));
        return $this->json(array("data"=>$user->getJson()));
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

        $user = new User($username, $password, $phoneNumber, $email, $sex, new \DateTime($birthday), $avatar, $description);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $messages = "";
            foreach ($errors as $violation) {
                $messages .= $violation->getMessage() . "<br>";
            }
            return $this->json(Array("code"=>"500", "messages" => $messages));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json(Array($user->getJson()));
    }

    /**
     * @Route("/user/{id}", methods={"POST"})
     */
    public function updateAction($id, Request $request, ValidatorInterface $validator)
    {
        $username = $request->get("username");
        $password = $request->get("password");
        $phoneNumber = $request->get("phoneNumber");
        $email = $request->get("email");
        $sex = $request->get("sex");
        $birthday = $request->get("birthday");
        $avatar = $request->get("avatar");
        $description = $request->get("description");

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array("id"=>$id));

        $user->setUsername($username);
        $user->setPassword($password);
        $user->setPhoneNumber($phoneNumber);
        $user->setEmail($email);
        $user->setSex($sex);
        $user->setBirthday(new \DateTime($birthday));
        $user->setAvatar($avatar);
        $user->setDescription($description);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $messages = "";
            foreach ($errors as $violation) {
                $messages .= $violation->getMessage() . "<br>";
            }
            return $this->json(Array("code"=>"500", "messages" => $messages));
        }

        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json(Array("data"=>$user->getJson()));
    }

    /**
     * @Route("/user/delete", name="deleteUser")
     */
    public function deleteAction(Request $request)
    {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            // Delete user.
            $id = $request->get("id");
            $doctrine = $this->getDoctrine();
            $manager = $doctrine->getManager();
            $willDeleteUser = $doctrine
                ->getRepository(User::class)
                ->findOneBy(array("id"=>$id));
            $manager->remove($willDeleteUser);
            $manager->flush();
            return $this->redirect("/user/all");
        } else {
            // Error.
            return $this->render("user/error.html.twig", array("messages" => "未登录，请登录后重试"));
        }
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $username = $request->get("username");
        $password = $request->get("password");

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array("username"=>$username));
        if ($user) {
            // Validate password.
            if ($user->getPassword() == $password) {
                // Login ok.
                $request->getSession()->set("user", $user);
                return $this->redirect("user/all");
            } else {
                // Password wrong.
                return $this->render("user/error.html.twig", array("messages"=>"密码错误"));
            }
        } else {
            // Username not found.
            return $this->render("user/error.html.twig", array("messages"=>"用户名未找到"));
        }
    }
}
