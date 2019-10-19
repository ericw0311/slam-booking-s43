<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/documentation/{pageCode}", name="documentation")
     */
    public function documentation($pageCode)
    {
        return $this->render('index/documentation.html.twig', [
            'pageCode' => $pageCode,
        ]);
    }
}
