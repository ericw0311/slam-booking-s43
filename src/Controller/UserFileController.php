<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserFileController extends AbstractController
{
    /**
     * @Route("/user/file", name="user_file")
     */
    public function index()
    {
        return $this->render('user_file/index.html.twig', [
            'controller_name' => 'UserFileController',
        ]);
    }
}
