<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TimetableController extends AbstractController
{
    /**
     * @Route("/timetable", name="timetable")
     */
    public function index()
    {
        return $this->render('timetable/index.html.twig', [
            'controller_name' => 'TimetableController',
        ]);
    }
}
