<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;

use App\Entity\UserContext;
use App\Entity\ListContext;
use App\Entity\Constants;
use App\Entity\ResourceClassification;
use App\Entity\Resource;
use App\Entity\File;
use App\Entity\ResourceClassificationNDB;
use App\Entity\ResourceContext;
use App\Entity\UserParameterNLC;
use App\Entity\UserParameter;
use App\Entity\Planification;
use App\Entity\PlanificationResource;
use App\Entity\Booking;
use App\Entity\Resource_List;

use App\Form\ResourceType;
use App\Form\ResourceAddType;
use App\Form\UserParameterNLCType;

use App\Api\AdministrationApi;
use App\Api\PlanningApi;

class ResourceController extends AbstractController
{
  /**
     * @Route("/resource/{page}", name="resource", requirements={"page"="\d+"})
     */
  public function index($page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      $rRepository = $em->getRepository(Resource::class);
      $numberRecords = $rRepository->getResourcesCount($userContext->getCurrentFile());
      $listContext = new ListContext($em, $connectedUser, 'resource', 'resource', $page, $numberRecords);
      $resources = $rRepository->getDisplayedResources($userContext->getCurrentFile(), $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());
      $resources_list = array();
      $prRepository = $em->getRepository(PlanificationResource::class);
      foreach ($resources as $resource) {
          $resource_list = new Resource_List($resource->getID(), $resource->getInternal(), $resource->getType(), $resource->getCode(), $resource->getName());
          $numberPlanificationPeriods = $prRepository->getPlanificationPeriodsCount($resource);
          $resource_list->setPlanified(($numberPlanificationPeriods > 0));
          array_push($resources_list, $resource_list);
      }
      return $this->render('resource/index.html.twig', array('userContext' => $userContext, 'listContext' => $listContext, 'listResources' => $resources_list));
  }
  /**
   * @Route("/resource/classification", name="resource_classification")
   */
  public function classification(Request $request)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $rcRepository = $em->getRepository(ResourceClassification::class);
      $activeRC = array();
      foreach (Constants::DISPLAYED_RESOURCE_TYPE as $resourceType) {
          $defaultActiveRC = Constants::RESOURCE_CLASSIFICATION_ACTIVE[$resourceType]; // Classifications actives par défaut
      $activeInternalRC_DB = $rcRepository->getInternalResourceClassificationCodes($userContext->getCurrentFile(), $resourceType, 1); // Classifications internes actives (lues en BD)
      $unactiveInternalRC_DB = $rcRepository->getInternalResourceClassificationCodes($userContext->getCurrentFile(), $resourceType, 0); // Classifications internes inactives (lues en BD)
      foreach (Constants::RESOURCE_CLASSIFICATION[$resourceType] as $resourceClassificationCode) {
          if ((in_array($resourceClassificationCode, $defaultActiveRC) || in_array($resourceClassificationCode, $activeInternalRC_DB))
              && !in_array($resourceClassificationCode, $unactiveInternalRC_DB)) {
              $resourceClassificationNDB = new ResourceClassificationNDB();
              $resourceClassificationNDB->setInternal(1);
              $resourceClassificationNDB->setType($resourceType);
              $resourceClassificationNDB->setCode($resourceClassificationCode);
              array_push($activeRC, $resourceClassificationNDB);
          }
      }
          $activeExternalRC = $rcRepository->getActiveExternalResourceClassifications($userContext->getCurrentFile(), $resourceType);
          foreach ($activeExternalRC as $resourceClassification) {
              $resourceClassificationNDB = new ResourceClassificationNDB();
              $resourceClassificationNDB->setInternal(0);
              $resourceClassificationNDB->setType($resourceType);
              $resourceClassificationNDB->setId($resourceClassification->getId());
              $resourceClassificationNDB->setName($resourceClassification->getName());
              array_push($activeRC, $resourceClassificationNDB);
          }
      }
      return $this->render('resource/classification.html.twig', array('userContext' => $userContext, 'activeRC' => $activeRC));
  }
  /**
   * @Route("/resource/add_internal/{type}/{code}", name="resource_add_internal")
   */
  public function add_internal(Request $request, $type, $code)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      $resource = new Resource($connectedUser, $userContext->getCurrentFile());
      $resource->setInternal(true);
      $resource->setType($type);
      $resource->setCode($code);
      $form = $this->createForm(ResourceAddType::class, $resource);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->persist($resource);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'resource.created.ok');
              if ($form->get('validateAndCreate')->isClicked()) {
                  return $this->redirectToRoute('resource_add_internal', array('type' => $type, 'code' => $code));
              } else {
                  return $this->redirectToRoute('resource', array('page' => 1));
              }
          }
      }
      return $this->render('resource/add.html.twig', array('userContext' => $userContext, 'resourceClassification' => null, 'resource' => $resource, 'form' => $form->createView()));
  }
  /**
   * @Route("/resource/add_external/{type}/{resourceClassificationID}", name="resource_add_external")
   * @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
   */
  public function add_external(Request $request, $type, \App\Entity\ResourceClassification $resourceClassification)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      $resource = new Resource($connectedUser, $userContext->getCurrentFile());
      $resource->setInternal(false);
      $resource->setType($type);
      $resource->setClassification($resourceClassification);
      $form = $this->createForm(ResourceAddType::class, $resource);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->persist($resource);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'resource.created.ok');
              if ($form->get('validateAndCreate')->isClicked()) {
                  return $this->redirectToRoute('resource_add_external', array('type' => $type, 'resourceClassificationID' => $resourceClassification->getId()));
              } else {
                  return $this->redirectToRoute('resource', array('page' => 1));
              }
          }
      }
      return $this->render('resource/add.html.twig', array('userContext' => $userContext, 'resourceClassification' => $resourceClassification, 'resource' => $resource, 'form' => $form->createView()));
  }
  /**
   * @Route("/resource/edit/{resourceID}", name="resource_edit")
   * @ParamConverter("resource", options={"mapping": {"resourceID": "id"}})
   */
  public function edit(Request $request, \App\Entity\Resource $resource)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
  $resourceContext = new ResourceContext($em, $userContext->getCurrentFile(), $resource); // contexte ressource
  $bRepository = $em->getRepository(Booking::class);
      $numberBookings = $bRepository->getResourceBookingsCount($userContext->getCurrentFile(), $resource);
      return $this->render(
      'resource/edit.html.twig',
      array('userContext' => $userContext, 'resource' => $resource, 'resourceContext' => $resourceContext, 'numberBookings' => $numberBookings)
  );
  }
  // Modification d'une ressource
  /**
   * @Route("/resource/modify/{resourceID}", name="resource_modify")
   * @ParamConverter("resource", options={"mapping": {"resourceID": "id"}})
   */
  public function modify(Request $request, \App\Entity\Resource $resource)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $form = $this->createForm(ResourceType::class, $resource);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'resource.updated.ok');
              return $this->redirectToRoute('resource_edit', array('resourceID' => $resource->getId()));
          }
      }
      return $this->render('resource/modify.html.twig', array('userContext' => $userContext, 'resource' => $resource, 'form' => $form->createView()));
  }
  // Suppression d'une ressource
  /**
   * @Route("/resource/delete/{resourceID}", name="resource_delete")
   * @ParamConverter("resource", options={"mapping": {"resourceID": "id"}})
   */
  public function delete(Request $request, \App\Entity\Resource $resource)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $em->remove($resource);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'resource.deleted.ok');
      return $this->redirectToRoute('resource', array('page' => 1));
  }
  /**
   * @Route("/resource/foreign/{resourceID}", name="resource_foreign")
   * @ParamConverter("resource", options={"mapping": {"resourceID": "id"}})
   */
  public function foreign(Request $request, \App\Entity\Resource $resource)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $pRepository = $em->getRepository(Planification::class);
      $listPlanifications = $pRepository->getResourcePlanificationsList($userContext->getCurrentFile(), $resource);
      return $this->render('resource/foreign.html.twig', array('userContext' => $userContext, 'resource' => $resource, 'listPlanifications' => $listPlanifications));
  }
  /**
   * @Route("/resource/booking_list/{resourceID}/{page}", name="resource_booking_list", requirements={"page"="\d+"})
   * @ParamConverter("resource", options={"mapping": {"resourceID": "id"}})
   */
  public function booking_list(Resource $resource, $page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $bRepository = $em->getRepository(Booking::class);
      $numberRecords = $bRepository->getResourceBookingsCount($userContext->getCurrentFile(), $resource);
      $listContext = new ListContext($em, $connectedUser, 'booking', 'booking', $page, $numberRecords);
      $listBookings = $bRepository->getResourceBookings($userContext->getCurrentFile(), $resource, $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());
      $planning_path = 'planning_one'; // La route du planning est "one" ou "many" selon le nombre de planifications actives à la date du jour
      $numberPlanifications = PlanningApi::getNumberOfPlanifications($em, $userContext->getCurrentFile());
      if ($numberPlanifications > 1) {
          $planning_path = 'planning_many';
      }
      return $this->render(
      'resource/booking.list.html.twig',
      array('userContext' => $userContext, 'listContext' => $listContext, 'resource' => $resource, 'listBookings' => $listBookings, 'planning_path' => $planning_path)
  );
  }
}
