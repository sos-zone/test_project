<?php

namespace TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function uploadAction(Request $request)
    {
        $file = $request->get('fileToUpload');
        $content = $file->getData();

        return new Response($content);
    }
}
