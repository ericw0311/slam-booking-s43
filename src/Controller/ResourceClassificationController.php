<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResourceClassificationController extends AbstractController
{
    /**
     * @Route("/resource/classification", name="resource_classification")
     */
    public function index()
    {
        return $this->render('resource_classification/index.html.twig', [
            'controller_name' => 'ResourceClassificationController',
        ]);
    }
}
