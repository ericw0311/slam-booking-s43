<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;

use App\Entity\Label;
use App\Entity\UserParameter;
use App\Entity\UserContext;
use App\Entity\ListContext;
use App\Entity\UserParameterNLC;
use App\Entity\Booking;

use App\Form\LabelType;
use App\Form\UserParameterNLCType;

use App\Api\AdministrationApi;
use App\Api\PlanningApi;

class LabelController extends AbstractController
{
  /**
   * @Route("/{_locale}/label/{page}", name="label", requirements={"page"="\d+"})
   */
  public function index($page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $lRepository = $em->getRepository(Label::class);
      $numberRecords = $lRepository->getLabelsCount($userContext->getCurrentFile());
      $listContext = new ListContext($em, $connectedUser, 'label', 'label', $page, $numberRecords);
      $listLabels = $lRepository->getDisplayedLabels($userContext->getCurrentFile(), $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());

      return $this->render('label/index.html.twig', array('userContext' => $userContext, 'listContext' => $listContext, 'listLabels' => $listLabels));
  }

  // Ajout d'une étiquete
  /**
   * @Route("/{_locale}/label/add", name="label_add")
   */
  public function add(Request $request)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $label = new Label($connectedUser, $userContext->getCurrentFile());
      $form = $this->createForm(LabelType::class, $label);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->persist($label);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'label.created.ok');
              return $this->redirectToRoute('label', array('page' => 1));
          }
      }
      return $this->render('label/add.html.twig', array('userContext' => $userContext, 'form' => $form->createView()));
  }

  // Edition du detail d'une étiquete
  /**
   * @Route("/{_locale}/label/edit/{labelID}", name="label_edit")
   * @ParamConverter("label", options={"mapping": {"labelID": "id"}})
   */
  public function edit(Request $request, Label $label)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $bRepository = $em->getRepository(Booking::class);
      $numberBookings = $bRepository->getLabelBookingsCount($userContext->getCurrentFile(), $label);
      return $this->render('label/edit.html.twig', array('userContext' => $userContext, 'label' => $label, 'numberBookings' => $numberBookings));
  }

  // Modification d'une étiquete
  /**
     * @Route("/{_locale}/label/modify/{labelID}", name="label_modify")
     * @ParamConverter("label", options={"mapping": {"labelID": "id"}})
     */
  public function modify(Request $request, Label $label)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $form = $this->createForm(LabelType::class, $label);

      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'label.updated.ok');
              return $this->redirectToRoute('label_edit', array('labelID' => $label->getId()));
          }
      }
      return $this->render('label/modify.html.twig', array('userContext' => $userContext, 'label' => $label, 'form' => $form->createView()));
  }

  // Suppression d'une étiquete
  /**
     * @Route("/{_locale}/label/delete/{labelID}", name="label_delete")
     * @ParamConverter("label", options={"mapping": {"labelID": "id"}})
     */
  public function delete(Request $request, Label $label)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $em->remove($label);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'label.deleted.ok');
      return $this->redirectToRoute('label', array('page' => 1));
  }

  /**
   * @Route("/{_locale}/label/booking_list/{labelID}/{page}", name="label_booking_list", requirements={"page"="\d+"})
   * @ParamConverter("label", options={"mapping": {"labelID": "id"}})
   */
  public function booking_list(Label $label, $page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $bRepository = $em->getRepository(Booking::class);
      $numberRecords = $bRepository->getLabelBookingsCount($userContext->getCurrentFile(), $label);
      $listContext = new ListContext($em, $connectedUser, 'booking', 'booking', $page, $numberRecords);
      $listBookings = $bRepository->getLabelBookings($userContext->getCurrentFile(), $label, $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());
      $planning_path = 'planning_one'; // La route du planning est "one" ou "many" selon le nombre de planifications actives à la date du jour
      $numberPlanifications = PlanningApi::getNumberOfPlanifications($em, $userContext->getCurrentFile());
      if ($numberPlanifications > 1) {
          $planning_path = 'planning_many';
      }
      return $this->render(
  'label/booking.list.html.twig',
  array('userContext' => $userContext, 'listContext' => $listContext, 'label' => $label, 'listBookings' => $listBookings, 'planning_path' => $planning_path)
);
  }

  // Met à jour le nombre de lignes et colonnes d'affichage des listes
  /**
     * @Route("/{_locale}/label/number_lines_columns/{labelID}/{page}", name="label_number_lines_and_columns", requirements={"page"="\d+"})
     * @ParamConverter("label", options={"mapping": {"labelID": "id"}})
 */
  public function number_lines_and_columns(Request $request, Label $label, $page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $numberLines = AdministrationApi::getNumberLines($em, $connectedUser, 'booking');
      $numberColumns = AdministrationApi::getNumberColumns($em, $connectedUser, 'booking');
      $userParameterNLC = new UserParameterNLC($numberLines, $numberColumns);
      $form = $this->createForm(UserParameterNLCType::class, $userParameterNLC);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              AdministrationApi::setNumberLines($em, $connectedUser, 'booking', $userParameterNLC->getNumberLines());
              AdministrationApi::setNumberColumns($em, $connectedUser, 'booking', $userParameterNLC->getNumberColumns());
              $request->getSession()->getFlashBag()->add('notice', 'number.lines.columns.updated.ok');
              return $this->redirectToRoute('label_booking_list', array('labelID' => $label->getId(), 'page' => 1));
          }
      }
      return $this->render(
  'label/number.lines.and.columns.html.twig',
  array('userContext' => $userContext, 'label' => $label, 'page' => $page, 'form' => $form->createView())
);
  }
}
