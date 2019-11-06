<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;

use App\Entity\UserContext;
use App\Entity\ListContext;
use App\Entity\UserParameter;
use App\Entity\QueryBooking;

use App\Form\QueryBookingType;

class QueryBookingController extends AbstractController
{
  /**
   * @Route("/query_booking/{page}", name="query_booking", requirements={"page"="\d+"})
   */
  public function index($page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $qbRepository = $em->getRepository(QueryBooking::class);
      $numberRecords = $qbRepository->getQueryBookingCount($userContext->getCurrentFile());
      $listContext = new ListContext($em, $connectedUser, 'queryBooking', 'dashboard', $page, $numberRecords);
      $listQueryBooking = $qbRepository->getDisplayedQueryBooking($userContext->getCurrentFile(), $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());

      return $this->render('query_booking/index.html.twig', array('userContext' => $userContext, 'listContext' => $listContext, 'listQueryBooking' => $listQueryBooking));
  }

  // Ajout d'un tableau de bord
  /**
   * @Route("/query_booking/add", name="query_booking_add")
   */
  public function add(Request $request)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $queryBooking = new QueryBooking($connectedUser, $userContext->getCurrentFile());
      $form = $this->createForm(QueryBookingType::class, $queryBooking);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->persist($queryBooking);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'queryBooking.created.ok');
              return $this->redirectToRoute('query_booking', array('page' => 1));
          }
      }
      return $this->render('query_booking/add.html.twig', array('userContext' => $userContext, 'form' => $form->createView()));
  }

  // Edition du detail d'un tableau de bord
  /**
   * @Route("/query_booking/edit/{queryBookingID}", name="query_booking_edit")
   * @ParamConverter("queryBooking", options={"mapping": {"queryBookingID": "id"}})
   */
  public function edit(Request $request, QueryBooking $queryBooking)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      return $this->render('query_booking/edit.html.twig', array('userContext' => $userContext, 'queryBooking' => $queryBooking));
  }

  // Modification d'un tableau de bord
  /**
     * @Route("/query_booking/modify/{queryBookingID}", name="query_booking_modify")
     * @ParamConverter("queryBooking", options={"mapping": {"queryBookingID": "id"}})
     */
  public function modify(Request $request, QueryBooking $queryBooking)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $form = $this->createForm(QueryBookingType::class, $queryBooking);

      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'queryBooking.updated.ok');
              return $this->redirectToRoute('query_booking_edit', array('queryBookingID' => $queryBooking->getId()));
          }
      }
      return $this->render('query_booking/modify.html.twig', array('userContext' => $userContext, 'queryBooking' => $queryBooking, 'form' => $form->createView()));
  }

  // Suppression d'un tableau de bord
  /**
     * @Route("/query_booking/delete/{queryBookingID}", name="query_booking_delete")
     * @ParamConverter("queryBooking", options={"mapping": {"queryBookingID": "id"}})
     */
  public function delete(Request $request, QueryBooking $queryBooking)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $em->remove($queryBooking);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'queryBooking.deleted.ok');
      return $this->redirectToRoute('query_booking', array('page' => 1));
  }
}
