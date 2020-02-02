<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\UserContext;
use App\Entity\FileContext;
use App\Entity\Innovation;
use App\Entity\InnovationUserFile;

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
    $iRepository = $em->getRepository(Innovation::class);
    $iufRepository = $em->getRepository(InnovationUserFile::class);

    $innovations = $iRepository->getUnreadInnovations($iufRepository->getUserFileInnovationQB($userContext->getCurrentUserFile()));

    $displayInnovations = (count($innovations) > 0);

      return $this->render('default/summary.html.twig', array('userContext' => $userContext, 'fileContext' => $fileContext,
        'displayInnovations' => $displayInnovations));
    }

    // Validation de la consultation d'une innovation
    /**
     * @Route("/{_locale}/default/validate_innovation/{innovationCode}", name="default_validate_innovation")
     */
    public function validateInnovation($innovationCode)
    {
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

    $iRepository = $em->getRepository(Innovation::class);
    $iufRepository = $em->getRepository(InnovationUserFile::class);

    $innovation = $iRepository->findOneBy(array('code' => $innovationCode));

    if ($innovation !== null) {
      $innovationUserFile = $iufRepository->findOneBy(array('innovation' => $innovation, 'userFile' => $userContext->getCurrentUserFile()));
      if ($innovationUserFile === null) {
        $innovationUserFile = new InnovationUserFile($innovation, $userContext->getCurrentUserFile());
        $em->persist($innovationUserFile);
        $em->flush();
      }
    }
    return $this->redirectToRoute('default_summary');
    }
}
