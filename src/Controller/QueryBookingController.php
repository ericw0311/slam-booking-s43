<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QueryBookingController extends AbstractController
{
    /**
     * @Route("/query/booking", name="query_booking")
     */
    public function index()
    {
        return $this->render('query_booking/index.html.twig', [
            'controller_name' => 'QueryBookingController',
        ]);
    }
}
