<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserFileGroupController extends AbstractController
{
    /**
     * @Route("/user/file/group", name="user_file_group")
     */
    public function index()
    {
        return $this->render('user_file_group/index.html.twig', [
            'controller_name' => 'UserFileGroupController',
        ]);
    }
}
