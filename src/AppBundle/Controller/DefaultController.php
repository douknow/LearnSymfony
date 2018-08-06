<?php

namespace AppBundle\Controller;

use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
}
