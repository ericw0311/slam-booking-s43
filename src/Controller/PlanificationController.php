<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PlanificationController extends AbstractController
{
    /**
     * @Route("/planification", name="planification")
     */
    public function index()
    {
        return $this->render('planification/index.html.twig', [
            'controller_name' => 'PlanificationController',
        ]);
    }
}
