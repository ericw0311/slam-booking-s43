<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;

use App\Entity\User;
use App\Entity\UserFile;
use App\Entity\UserParameter;
use App\Entity\UserContext;
use App\Entity\ListContext;
use App\Entity\ResourceClassification;
use App\Entity\Resource;
use App\Entity\UserParameterNLC;
use App\Entity\Booking;

use App\Form\UserFileAddType;
use App\Form\UserFileEmailType;
use App\Form\UserFileType;
use App\Form\UserFileAccountType;
use App\Form\UserParameterNLCType;

use App\Api\AdministrationApi;
use App\Api\ResourceApi;
use App\Api\PlanningApi;

class UserFileController extends AbstractController
{
  /**
   * @Route("/{_locale}/userfile/{page}", name="user_file", requirements={"page"="\d+"})
   */
  public function index($page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      $ufRepository = $em->getRepository(UserFile::class);
      $numberRecords = $ufRepository->getUserFilesCount($userContext->getCurrentFile());
      $listContext = new ListContext($em, $connectedUser, 'userFile', 'user', $page, $numberRecords);
      $listUserFiles = $ufRepository->getDisplayedUserFiles($userContext->getCurrentFile(), $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());

      return $this->render('user_file/index.html.twig', array('userContext' => $userContext, 'listContext' => $listContext, 'listUserFiles' => $listUserFiles));
  }
  // 1ere etape de l'ajout d'un utilisateur au dossier en cours (userFile): saisie de son email.
  // Si l'utilisateur (user) correspondant existe, le userFile est cree a partir de l'utilisateur trouve.
  // Si l'utilisateur (user) correspondant n'existe pas, le userFile est cree par un formulaire (2eme etape).
  /**
   * @Route("/{_locale}/userfile/email", name="user_file_email")
   */
  public function email(Request $request)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
$userFile = new UserFile($connectedUser, $userContext->getCurrentFile()); // Initialisation du userFile. Les zones lastName, firstName et email sont gerees par le formulaire UserFileEmailType
$userFile->setAdministrator(false);
      $userFile->setUserCreated(false);
      $form = $this->createForm(UserFileEmailType::class, $userFile);
      $userFound = false;
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $uRepository = $em->getRepository(User::class); // On recherche l'utilisateur d'apres l'email saisi
              $user = $uRepository->findOneBy(array('email' => $userFile->getEmail()));
              if ($user === null) { // L'utilisateur n'existe pas, on appelle le formulaire pour creer le userFile
                  return $this->redirectToRoute('user_file_add', array('email' => $userFile->getEmail()));
              } else { // L'utilisateur existe, on cree le userFile a partir de l'utilisateur
                  $userFound = true;
                  $userFile->setAccount($user);
                  $userFile->setAccountType($user->getAccountType());
                  $userFile->setLastName($user->getLastName());
                  $userFile->setFirstName($user->getFirstName());
                  $userFile->setUniqueName($user->getUniqueName());
                  $userFile->setUserCreated(true);
                  $userFile->setUsername($user->getUserName());
                  $em->persist($userFile);
                  $em->flush();
                  if ($userFound) { // Mise a jour du dossier en cours de l'utilisateur trouve
                      AdministrationApi::setCurrentFileIfNotDefined($em, $user, $userFile->getFile());
                  }
                  $request->getSession()->getFlashBag()->add('notice', 'userFile.created.ok');
                  return $this->redirectToRoute('user_file', array('page' => 1));
              }
          }
      }
      return $this->render('user_file/email.html.twig', array('userContext' => $userContext, 'form' => $form->createView()));
  }
  // 2eme etape de l'ajout d'un utilisateur au dossier en cours (userFile): saisie de son email.
  // L'utilisateur (user) correspondant a l'email saisi a l'etape 1 n'existe pas, le userFile est cree par un formulaire.
  /**
   * @Route("/{_locale}/userfile/add/{email}", name="user_file_add")
   */
  public function add(Request $request, $email)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      $userFile = new UserFile($connectedUser, $userContext->getCurrentFile());
      $userFile->setEmail($email);
      $userFile->setAdministrator(false);
      $userFile->setUserCreated(false);
      $form = $this->createForm(UserFileAddType::class, $userFile);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->persist($userFile);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'userFile.created.ok');
              return $this->redirectToRoute('user_file', array('page' => 1));
          }
      }
      return $this->render('user_file/add.html.twig', array('userContext' => $userContext, 'userFile' => $userFile, 'form' => $form->createView()));
  }
  // Affichage du detail d'un utilisateur du dossier en cours (userFile)
  /**
  * @Route("/{_locale}/userfile/edit/{userFileID}", name="user_file_edit")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
  */
  public function edit(\App\Entity\UserFile $userFile)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      // L'utilisateur connecte est-il le createur du dossier ?
      $connectedUserIsFileCreator = ($connectedUser === $userContext->getCurrentFile()->getUser());
      // L'utilisateur selectionne est-il le createur du dossier ?
      $selectedUserIsFileCreator = ($userFile->getUserCreated() and $userFile->getAccount() === $userContext->getCurrentFile()->getUser());
      $atLeastOneUserClassification = false;
      $resourceType = 'USER';

      // Premiere classification interne active (N si non trouvée)
      $firstInternalResourceClassificationCode = ResourceApi::getFirstActiveInternalResourceClassification($em, $userContext->getCurrentFile(), $resourceType);
      // Il existe au moins une classification interne active
      if ($firstInternalResourceClassificationCode != "N") {
          $atLeastOneUserClassification = true;
      }
      if (!$atLeastOneUserClassification) {
          $rcRepository = $em->getRepository(ResourceClassification::class);
          // Premiere classification externe active
          $firstExternalResourceClassification = $rcRepository->getFirsrActiveExternalResourceClassification($userContext->getCurrentFile(), $resourceType);
          // Il existe au moins une classification externe active
          if ($firstExternalResourceClassification !== null) {
              $atLeastOneUserClassification = true;
          }
      }
      $bRepository = $em->getRepository(Booking::class);
      $numberBookings = $bRepository->getUserFileBookingsCount($userContext->getCurrentFile(), $userFile);
      return $this->render('user_file/edit.html.twig', array('userContext' => $userContext, 'userFile' => $userFile,
'connectedUserIsFileCreator' => $connectedUserIsFileCreator,
'selectedUserIsFileCreator' => $selectedUserIsFileCreator,
'atLeastOneUserClassification' => $atLeastOneUserClassification,
'numberBookings' => $numberBookings));
  }
  // Modification d'un utilisateur du dossier en cours (userFile)
  /**
  * @Route("/{_locale}/userfile/modify/{userFileID}", name="user_file_modify")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
  */
  public function modify(Request $request, \App\Entity\UserFile $userFile)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
$userFileUserCreated = $userFile->getUserCreated(); // Information sauvegardee car peut etre modifiee par la suite
if ($userFileUserCreated) { // L'utilisateur à modifier a un compte utilisateur de crée
    $form = $this->createForm(UserFileAccountType::class, $userFile);
} else {
    $form = $this->createForm(UserFileType::class, $userFile);
}
      $userFound = false;
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              if (!$userFile->getUserCreated()) { // On traite le cas tres particulier de modification de l'email
    $uRepository = $em->getRepository(User::class); // On recherche l'utilisateur d'apres l'email modifie
    $user = $uRepository->findOneBy(array('email' => $userFile->getEmail()));
                  if ($user != null) { // L'utilisateur existe, on met a jour le userFile a partir de l'utilisateur
                      $userFound = true;
                      $userFile->setAccount($user);
                      $userFile->setAccountType($user->getAccountType());
                      $userFile->setLastName($user->getLastName());
                      $userFile->setFirstName($user->getFirstName());
                      $userFile->setUniqueName($user->getUniqueName());
                      $userFile->setUserCreated(true);
                      $userFile->setUsername($user->getUsername());
                  }
              }
              $em->flush();
              if ($userFound) { // Mise a jour du dossier en cours de l'utilisateur trouve
                  AdministrationApi::setCurrentFileIfNotDefined($em, $user, $userFile->getFile());
              }
              $request->getSession()->getFlashBag()->add('notice', 'userFile.updated.ok');
              return $this->redirectToRoute('user_file_edit', array('userFileID' => $userFile->getId()));
          }
      }
      if ($userFileUserCreated) { // L'utilisateur a modifié a un compte utilisateur de créé
          $request->getSession()->getFlashBag()->add('notice', 'userFile.una.self');
      }
      return $this->render('user_file/modify.html.twig', array('userContext' => $userContext, 'userFile' => $userFile, 'form' => $form->createView()));
  }
  // Suppression d'un utilisateur du dossier en cours (userFile)
  /**
  * @Route("/{_locale}/userfile/delete/{userFileID}", name="user_file_delete")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
  */
  public function delete(Request $request, \App\Entity\UserFile $userFile)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $userAccount = $userFile->getAccount(); // Compte utilisateur attaché au userFile
      $em->remove($userFile);
      $em->flush();

      if ($userAccount != null) { // Le userFile a un compte utilisateur attaché
          $currentFileID = AdministrationApi::getCurrentFileID($em, $userAccount);
          if ($currentFileID == $userContext->getCurrentFileID()) { // Son dossier en cours est le dossier en cours de l'utilisateur connecte
  AdministrationApi::setFirstFileAsCurrent($em, $userAccount); // On met a jour son dossier en cours
          }
      }
      $request->getSession()->getFlashBag()->add('notice', 'userFile.deleted.ok');
      return $this->redirectToRoute('user_file', array('page' => 1));
  }

  //  Gestion des utilisateurs ressource
  /**
  * @Route("/{_locale}/userfile/resource/{userFileID}", name="user_file_resource")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
  */
  public function resource(Request $request, \App\Entity\UserFile $userFile)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $resourceType = 'USER';
      // Premiere classification interne active (N si non trouvée)
      $firstInternalResourceClassificationCode = ResourceApi::getFirstActiveInternalResourceClassification($em, $userContext->getCurrentFile(), $resourceType);
      // Il existe au moins une classification interne active
      if ($firstInternalResourceClassificationCode != "N") {
          return $this->redirectToRoute('user_file_resource_internal', array('userFileID' => $userFile->getID(), 'resourceClassificationCode' => $firstInternalResourceClassificationCode, 'yes' => 0));
      }
      $rcRepository = $em->getRepository(ResourceClassification::class);
      // Premiere classification externe active
      $firstExternalResourceClassification = $rcRepository->getFirsrActiveExternalResourceClassification($userContext->getCurrentFile(), $resourceType);
      // Il existe au moins une classification externe active
      if ($firstExternalResourceClassification !== null) {
          return $this->redirectToRoute('user_file_resource_external', array('userFileID' => $userFile->getID(), 'resourceClassificationID' => $firstExternalResourceClassification->getID(), 'yes' => 0));
      }
      // Cas ou aucune classification active. Normalement ce cas ne se produit pas (car dans ce cas on ne donne pas accès à la fonctionnalité utilisateur ressource)
      return $this->redirectToRoute('user_file_resource_internal', array('userFileID' => $userFile->getID(), 'resourceClassificationCode' => $firstInternalResourceClassificationCode, 'yes' => 0));
  }
  /**
  * @Route("/{_locale}/userfile/resourceinternal/{userFileID}/{resourceClassificationCode}/{yes}", name="user_file_resource_internal")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
  */
  public function resource_internal(Request $request, \App\Entity\UserFile $userFile, $resourceClassificationCode, $yes)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $resourceType = 'USER';
      $resourceClassificationID = 0;
      // Classifications internes actives
      $listActiveInternalRC = ResourceApi::getActiveInternalResourceClassifications($em, $userContext->getCurrentFile(), $resourceType);
      $rcRepository = $em->getRepository(ResourceClassification::class);
      // Classifications externes actives
      $listExternalRC = $rcRepository->getActiveExternalResourceClassifications($userContext->getCurrentFile(), $resourceType);
      $yesOrNo = ($yes > 0) ? 'yes' :'no';
      return $this->render(
    'user_file/resource.'.$yesOrNo.'.html.twig',
    array('userContext' => $userContext, 'userFile' => $userFile, 'resourceType' => $resourceType, 'yes' => $yes, 'internal' => 1,
  'resourceClassificationCode' => $resourceClassificationCode, 'listActiveInternalRC' => $listActiveInternalRC,
  'resourceClassificationID' => $resourceClassificationID, 'listExternalRC' => $listExternalRC)
);
  }
  /**
  * @Route("/{_locale}/userfile/resourceexternal/{userFileID}/{resourceClassificationID}/{yes}", name="user_file_resource_external")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
* @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
  */
  public function resource_external(Request $request, \App\Entity\UserFile $userFile, \App\Entity\ResourceClassification $resourceClassification, $yes)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $resourceType = 'USER';
      $resourceClassificationCode = 'N';
      // Classifications internes actives
      $listActiveInternalRC = ResourceApi::getActiveInternalResourceClassifications($em, $userContext->getCurrentFile(), $resourceType);
      $rcRepository = $em->getRepository(ResourceClassification::class);
      // Classifications externes actives
      $listExternalRC = $rcRepository->getActiveExternalResourceClassifications($userContext->getCurrentFile(), $resourceType);
      $yesOrNo = ($yes > 0) ? 'yes' :'no';
      return $this->render(
    'user_file/resource.'.$yesOrNo.'.html.twig',
    array('userContext' => $userContext, 'userFile' => $userFile, 'resourceType' => $resourceType, 'yes' => $yes, 'internal' => 0,
  'resourceClassificationCode' => $resourceClassificationCode, 'listActiveInternalRC' => $listActiveInternalRC,
  'resourceClassificationID' => $resourceClassification->getID(), 'listExternalRC' => $listExternalRC)
);
  }
  /**
  * @Route("/{_locale}/userfile/resourcevalidateinternal/{userFileID}/{resourceClassificationCode}/{yes}", name="user_file_resource_validate_internal")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
  */
  public function resource_validate_internal(Request $request, \App\Entity\UserFile $userFile, $resourceClassificationCode, $yes)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $resourceType = 'USER';
      $rRepository = $em->getRepository(Resource::class);
      $resourceFound = false;
      if ($userFile->getResource() !== null) {
          $resource = $rRepository->findOneBy(array('id' => $userFile->getResource()));
          if ($resource !== null) {
              $resourceFound = true;
          }
      }
      if ($yes > 0) {
          $userFile->setResourceUser(1);
      } else {
          $userFile->setResourceUser(0);
          $userFile->setResource(null);
      }

      if ($userFile->getResourceUser() > 0) { // Création ou mise à jour de la ressource rattachée à l'utilisateur
          if ($resourceFound) {
              $resource->setInternal(1);
              $resource->setCode($resourceClassificationCode);
              $resource->setClassification(null);
          } else {
              $resource = new Resource($connectedUser, $userContext->getCurrentFile());
              $resource->setInternal(1);
              $resource->setType($resourceType);
              $resource->setCode($resourceClassificationCode);
              $resource->setName($userFile->getFirstAndLastName());
              $em->persist($resource);
              $userFile->setResource($resource);
          }
      } else {
          if ($resourceFound) {
              $em->remove($resource);
          }
      }
      $em->persist($userFile);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'userFile.resource.updated.ok');
      return $this->redirectToRoute('user_file_edit', array('userFileID' => $userFile->getID()));
  }
  /**
  * @Route("/{_locale}/userfile/resourcevalidateexternal/{userFileID}/{resourceClassificationID}/{yes}", name="user_file_resource_validate_external")
  * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
* @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
  */
  public function resource_validate_external(Request $request, \App\Entity\UserFile $userFile, \App\Entity\ResourceClassification $resourceClassification, $yes)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $resourceType = 'USER';
      $rRepository = $em->getRepository(Resource::class);
      $resourceFound = false;
      if ($userFile->getResource() !== null) {
          $resource = $rRepository->findOneBy(array('id' => $userFile->getResource()));
          if ($resource !== null) {
              $resourceFound = true;
          }
      }
      if ($yes > 0) {
          $userFile->setResourceUser(1);
      } else {
          $userFile->setResourceUser(0);
          $userFile->setResource(null);
      }

      if ($userFile->getResourceUser() > 0) { // Création ou mise à jour de la ressource rattachée à l'utilisateur
          if ($resourceFound) {
              $resource->setInternal(0);
              $resource->setClassification($resourceClassification);
              $resource->setCodeNull();
          } else {
              $resource = new Resource($connectedUser, $userContext->getCurrentFile());
              $resource->setInternal(0);
              $resource->setType($resourceType);
              $resource->setClassification($resourceClassification);
              $resource->setName($userFile->getFirstAndLastName());
              $em->persist($resource);
              $userFile->setResource($resource);
          }
      } else { // Suppression de la ressource rattachée à l'utilisateur
          if ($resourceFound) {
              $em->remove($resource);
          }
      }
      $em->persist($userFile);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'userFile.resource.updated.ok');
      return $this->redirectToRoute('user_file_edit', array('userFileID' => $userFile->getID()));
  }
  /**
   * @Route("/{_locale}/userfile/booking_list/{userFileID}/{page}", name="user_file_booking_list", requirements={"page"="\d+"})
   * @ParamConverter("userFile", options={"mapping": {"userFileID": "id"}})
   */
  public function booking_list(UserFile $userFile, $page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $bRepository = $em->getRepository(Booking::class);
      $numberRecords = $bRepository->getUserFileBookingsCount($userContext->getCurrentFile(), $userFile);
      $listContext = new ListContext($em, $connectedUser, 'booking', 'booking', $page, $numberRecords);
      $listBookings = $bRepository->getUserFileBookings($userContext->getCurrentFile(), $userFile, $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());
      $planning_path = 'planning_one'; // La route du planning est "one" ou "many" selon le nombre de planifications actives à la date du jour
      $numberPlanifications = PlanningApi::getNumberOfPlanifications($em, $userContext->getCurrentFile());
      if ($numberPlanifications > 1) {
          $planning_path = 'planning_many';
      }
      return $this->render(
  'user_file/booking.list.html.twig',
  array('userContext' => $userContext, 'listContext' => $listContext, 'userFile' => $userFile, 'listBookings' => $listBookings, 'planning_path' => $planning_path)
);
  }
}
