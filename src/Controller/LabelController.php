<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends AbstractController
{
    /**
     * @Route("/label", name="label")
     */
    public function index()
    {
        return $this->render('label/index.html.twig', [
            'controller_name' => 'LabelController',
        ]);
    }
}
