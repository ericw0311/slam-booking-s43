<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\UserContext;
use App\Entity\FileContext;

class DefaultController extends AbstractController
{
    /**
     * @Route("/{_locale}/default", name="default")
     */
    public function index()
    {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      if ($userContext->getCurrentFileID() <= 0) {
          return $this->render('default/index.html.twig', array('userContext' => $userContext));
      } else {
          return $this->redirectToRoute('default_summary');
      }
    }

    /**
     * @Route("/{_locale}/default/summary", name="default_summary")
     */
    public function summary()
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        $fileContext = new FileContext($em, $userContext); // contexte dossier
      return $this->render('default/summary.html.twig', array('userContext' => $userContext, 'fileContext' => $fileContext));
    }
}
