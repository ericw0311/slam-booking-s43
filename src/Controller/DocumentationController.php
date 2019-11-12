<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController extends AbstractController
{
    /**
     * @Route("/{_locale}/documentation", name="documentation")
     */
    public function index()
    {
        return $this->render('documentation/index.html.twig', [
            'controller_name' => 'DocumentationController',
        ]);
    }
}
