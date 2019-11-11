<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;

use App\Entity\UserFile;
use App\Entity\UserFileNDB;
use App\Entity\UserFileGroup;
use App\Entity\UserParameter;
use App\Entity\UserContext;
use App\Entity\ListContext;
use App\Entity\UserParameterNLC;
use App\Entity\Constants;

use App\Api\UserFileApi;

use App\Form\UserFileGroupType;
use App\Form\UserParameterNLCType;

class UserFileGroupController extends AbstractController
{
  /**
   * @Route("/user_file_group/{page}", name="user_file_group", requirements={"page"="\d+"})
   */
  public function index($page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $lRepository = $em->getRepository(UserFileGroup::class);
      $numberRecords = $lRepository->getUserFileGroupsCount($userContext->getCurrentFile());
      $listContext = new ListContext($em, $connectedUser, 'userFileGroup', 'userFileGroup', $page, $numberRecords);
      $listUserFileGroups = $lRepository->getDisplayedUserFileGroups($userContext->getCurrentFile(), $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());

      return $this->render('user_file_group/index.html.twig', array('userContext' => $userContext, 'listContext' => $listContext, 'listUserFileGroups' => $listUserFileGroups));
  }

  // Ajout d'un groupe d'utilisateurs
  /**
   * @Route("/user_file_group/add", name="user_file_group_add")
   */
  public function add(Request $request)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $userFileGroup = new UserFileGroup($connectedUser, $userContext->getCurrentFile(), "MANUAL");
      $form = $this->createForm(UserFileGroupType::class, $userFileGroup);
      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->persist($userFileGroup);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'userFileGroup.created.ok');
              return $this->redirectToRoute('user_file_group', array('page' => 1));
          }
      }
      return $this->render('user_file_group/add.html.twig', array('userContext' => $userContext, 'form' => $form->createView()));
  }

  // Edition du detail d'un groupe d'utilisateurs
  /**
   * @Route("/user_file_group/edit/{userFileGroupID}/{page}", name="user_file_group_edit", requirements={"page"="\d+"})
   * @ParamConverter("userFileGroup", options={"mapping": {"userFileGroupID": "id"}})
   */
  public function edit(Request $request, UserFileGroup $userFileGroup, $page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      // Nombre maximum d'enregistrements affichés.
      $maxRecords = Constants::UG_USER_MAX_NUMBER_COLUMNS * Constants::UG_USER_MAX_NUMBER_LINES;

      // Premier index affiché.
      $firstRecordIndex = ($page-1) * $maxRecords;

      $userFileIDList = '';
      $displayedUserFiles = array();
      $index = 0;

      foreach ($userFileGroup->getUserFiles() as $userFile) {
          $userFileIDList = ($userFileIDList == '') ? $userFile->getId() : ($userFileIDList.'-'.$userFile->getId());

          if ($index >= $firstRecordIndex and $index < ($firstRecordIndex + $maxRecords)) {
              $displayedUserFile = new UserFileNDB();
              $displayedUserFile->setImage($userFile->getAdministrator() ? 'administrator' : 'user');
              $displayedUserFile->setName($userFile->getFirstAndLastName());
              array_push($displayedUserFiles, $displayedUserFile);
          }
          $index++;
      }

      // Nombre d'enregistrements affichés
      $numberRecordsDisplayed = count($displayedUserFiles);

      // Nombre de lignes affichées
      $numberLinesDisplayed = min($numberRecordsDisplayed, Constants::UG_USER_MAX_NUMBER_LINES);

      // Nombre de colonnes affichées
      $numberColumnsDisplayed = ($numberLinesDisplayed > 0) ? ceil($numberRecordsDisplayed / $numberLinesDisplayed) : 0;

      // Nombre d'utilisateurs précédant ceux qui sont affichés
      $numberUserBefore = $firstRecordIndex;

      // Nombre d'utilisateurs suivant ceux qui sont affichés
      $numberUserAfter = 0;
      if (count($userFileGroup->getUserFiles()) > ($firstRecordIndex + $maxRecords)) {
          $numberUserAfter = count($userFileGroup->getUserFiles()) - ($firstRecordIndex + $maxRecords);
      }

      return $this->render(
          'user_file_group/edit.html.twig',
          array('userContext' => $userContext, 'userFileGroup' => $userFileGroup, 'userFileIDList' => $userFileIDList, 'userFiles' => $displayedUserFiles,
              'numberRecordsDisplayed' => $numberRecordsDisplayed, 'numberLinesDisplayed' => $numberLinesDisplayed, 'numberColumnsDisplayed' => $numberColumnsDisplayed,
              'numberUserBefore' => $numberUserBefore, 'numberUserAfter' => $numberUserAfter, 'page' => $page)
          );
  }

  // Modification d'un groupe d'utilisateurs
  /**
     * @Route("/user_file_group/modify/{userFileGroupID}/{page}", name="user_file_group_modify", requirements={"page"="\d+"})
     * @ParamConverter("userFileGroup", options={"mapping": {"userFileGroupID": "id"}})
  */
  public function modify(Request $request, UserFileGroup $userFileGroup, $page)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $form = $this->createForm(UserFileGroupType::class, $userFileGroup);

      if ($request->isMethod('POST')) {
          $form->submit($request->request->get($form->getName()));
          if ($form->isSubmitted() && $form->isValid()) {
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'userFileGroup.updated.ok');
              return $this->redirectToRoute('user_file_group_edit', array('userFileGroupID' => $userFileGroup->getId(), 'page' => $page));
          }
      }
      return $this->render('user_file_group/modify.html.twig', array('userContext' => $userContext, 'userFileGroup' => $userFileGroup, 'page' => $page, 'form' => $form->createView()));
  }

  // Suppression d'un groupe d'utilisateurs
  /**
     * @Route("/user_file_group/delete/{userFileGroupID}", name="user_file_group_delete")
     * @ParamConverter("userFileGroup", options={"mapping": {"userFileGroupID": "id"}})
     */
  public function delete(Request $request, UserFileGroup $userFileGroup)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
      $em->remove($userFileGroup);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'userFileGroup.deleted.ok');
      return $this->redirectToRoute('user_file_group', array('page' => 1));
  }

  // Mise a jour de la liste des utilisateurs
  /**
   * @Route("/user_file_group/users/{userFileGroupID}/{userFileIDList}",
   * defaults={"userFileIDList" = null},
   * name="user_file_group_users")
   * @ParamConverter("userFileGroup", options={"mapping": {"userFileGroupID": "id"}})
   */
  public function user_file_group_users(UserFileGroup $userFileGroup, $userFileIDList)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      $selectedUserFiles = UserFileApi::getSelectedUserFiles($em, $userFileIDList, false);
      $availableUserFiles = UserFileApi::initAvailableUserFiles($em, $userContext->getCurrentFile(), $userFileIDList);

      return $this->render('user_file_group/users.html.twig', array('userContext' => $userContext, 'userFileGroup' => $userFileGroup, 'selectedUserFiles' => $selectedUserFiles,
     'availableUserFiles' => $availableUserFiles, 'userFileIDList' => $userFileIDList));
  }

  // Validation de la mise a jour de la liste des utilisateurs
  /**
   * @Route("/user_file_group/validate_users/{userFileGroupID}/{userFileIDList}",
   * defaults={"userFileIDList" = null},
   * name="user_file_group_validate_users")
   * @ParamConverter("userFileGroup", options={"mapping": {"userFileGroupID": "id"}})
   */
  public function user_file_group_validate_users(Request $request, LoggerInterface $logger, UserFileGroup $userFileGroup, $userFileIDList)
  {
      $logger->info('UserFileGroupController.user_file_group_validate_users DBG 1');
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      // Tableau des utilisateurs de l'Url
      $url_userFileID = explode("-", $userFileIDList);
      $logger->info('UserFileGroupController.user_file_group_validate_users DBG 2 <'.$userFileIDList.'>');
      $logger->info('UserFileGroupController.user_file_group_validate_users DBG 3 <'.count($url_userFileID).'>');

      // Utilisateurs du groupe
      $userFileGroupUserFiles = $userFileGroup->getUserFiles();

      foreach ($userFileGroupUserFiles as $userFile) {
          if (!in_array($userFile->getID(), $url_userFileID)) { // L'utilisateur n'appartient pas a la liste de l'Url. Il est supprimé.
              $userFileGroup->removeUserFile($userFile);
          }
      }

      $ufRepository = $em->getRepository(UserFile::class);
      // Parcours des utilisateurs de l'Url.
      foreach ($url_userFileID as $userFileID) {
          $userFileGroup->addUserFile($ufRepository->find($userFileID));
      }

      $em->persist($userFileGroup);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'userFileGroup.updated.ok');
      return $this->redirectToRoute('user_file_group_edit', array('userFileGroupID' => $userFileGroup->getID(), 'page' => 1));
  }

  // Affichage du détail des Foreign key
  /**
   * @Route("/user_file_group/foreign/{userFileGroupID}", name="user_file_group_foreign")
   * @ParamConverter("userFileGroup", options={"mapping": {"userFileGroupID": "id"}})
   */
  public function foreign(UserFileGroup $userFileGroup)
  {
      $connectedUser = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

      return $this->render(
        'user_file_group/foreign.html.twig',
        array('userContext' => $userContext, 'userFileGroup' => $userFileGroup)
    );
  }
}
