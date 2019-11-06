<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;

use App\Entity\Constants;
use App\Entity\UserContext;
use App\Entity\UserParameter;
use App\Entity\UserParameterNLC;

use App\Form\UserParameterNLCType;

use App\Api\AdministrationApi;

class ParameterController extends AbstractController
{
  /**
      * @Route("/{_locale}/parameter/number_lines_columns/{entityCode}/{listPath}", name="parameter_number_lines_columns")
      */
  public function index($entityCode, $listPath, Request $request)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $numberLines = AdministrationApi::getNumberLines($em, $connectedUser, $entityCode);
      $numberColumns = AdministrationApi::getNumberColumns($em, $connectedUser, $entityCode);
      $upRepository = $em->getRepository(UserParameter::class);
      $userParameterNLC = new UserParameterNLC($numberLines, $numberColumns);
      $form = $this->createForm(UserParameterNLCType::class, $userParameterNLC);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              AdministrationApi::setNumberLines($em, $connectedUser, $entityCode, $userParameterNLC->getNumberLines());
              AdministrationApi::setNumberColumns($em, $connectedUser, $entityCode, $userParameterNLC->getNumberColumns());
              $request->getSession()->getFlashBag()->add('notice', 'number.lines.columns.updated.ok');
              return $this->redirectToRoute($listPath, array('page' => 1));
          }
      }
      return $this->render('parameter/numberLinesColumns.html.twig', array('userContext' => $userContext, 'entityCode' => $entityCode, 'listPath' => $listPath, 'form' => $form->createView()));
  }
}
