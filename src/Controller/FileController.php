<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use App\Entity\File;
use App\Entity\User;
use App\Entity\UserContext;
use App\Entity\ListContext;
use App\Entity\UserFile;
use App\Entity\Timetable;
use App\Entity\Resource;
use App\Entity\Booking;
use App\Entity\Label;
use App\Entity\UserParameterNLC;
use App\Entity\UserParameter;
use App\Entity\FileBookingEmail;
use App\Entity\FileBookingPeriod;
use App\Form\FileType;
use App\Form\FileBookingEmailType;
use App\Form\FileBookingPeriodType;
use App\Form\UserParameterNLCType;
use App\Api\AdministrationApi;
use App\Api\PlanningApi;
use App\Entity\FileEditContext;

class FileController extends AbstractController
{
    /**
     * @Route("/{_locale}/file/{page}", name="file", requirements={"page"="\d+"})
     */
    public function index($page)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        $fRepository = $em->getRepository(File::class);
        $numberRecords = $fRepository->getUserFilesCount($connectedUser);
        $listContext = new ListContext($em, $connectedUser, 'file', 'file', $page, $numberRecords);
        $listFiles = $fRepository->getUserDisplayedFiles($connectedUser, $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());

        return $this->render('file/index.html.twig', array('userContext' => $userContext, 'listContext' => $listContext, 'listFiles' => $listFiles));
    }

    /**
     * @Route("/{_locale}/file/add", name="file_add")
     */
    public function add(Request $request)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

        $file = new File($connectedUser);
        $form = $this->createForm(FileType::class, $file);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($file);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'file.created.ok');
                return $this->redirectToRoute('file', array('page' => 1));
            }
        }
        return $this->render('file/add.html.twig', array('userContext' => $userContext, 'form' => $form->createView()));
    }
    /**
     * @Route("/{_locale}/file/edit/{fileID}", name="file_edit")
     * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
     */
    public function edit(Request $request, File $file)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
$fileEditContext = new FileEditContext($em, $file); // contexte dossier
return $this->render('file/edit.html.twig', array('userContext' => $userContext, 'file' => $file, 'fileEditContext' => $fileEditContext));
    }
    // Modification d'un dossier
    /**
       * @Route("/{_locale}/file/modify/{fileID}", name="file_modify")
       * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
       */
    public function modify(Request $request, File $file)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        $form = $this->createForm(FileType::class, $file);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'file.updated.ok');
                return $this->redirectToRoute('file_edit', array('fileID' => $file->getId()));
            }
        }
        return $this->render('file/modify.html.twig', array('userContext' => $userContext, 'file' => $file, 'form' => $form->createView()));
    }

    // Suppression d'un dossier
    /**
       * @Route("/{_locale}/file/delete/{fileID}", name="file_delete")
       * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
       */
    public function delete(Request $request, File $file)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
$currentFile = ($file->getId() == $userContext->getCurrentFileID()); // On repere si le dossier a supprimer est le dossier en cours.
$em->remove($file);
        $em->flush();
        if ($currentFile) { // Si le dossier supprime etait le dossier en cours, on positionne le premier dossier de la liste comme dossier en cours
            AdministrationApi::setFirstFileAsCurrent($em, $connectedUser);
        }
        $request->getSession()->getFlashBag()->add('notice', 'file.deleted.ok');
        return $this->redirectToRoute('file', array('page' => 1));
    }

    // Affichage des grilles horaires d'un dossier
    /**
       * @Route("/{_locale}/file/foreign/{fileID}", name="file_foreign")
       * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
       */
    public function foreign(Request $request, File $file)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        $ufRepository = $em->getRepository(UserFile::class);
        $listUserFiles = $ufRepository->getUserFilesExceptFileCreator($file);
        $tRepository = $em->getRepository(Timetable::class);
        $listUserTimetables = $tRepository->getUserTimetables($file);

        $rRepository = $em->getRepository(Resource::class);
        $listResources = $rRepository->getResources($file);
        $lRepository = $em->getRepository(Label::class);
        $listLabels = $lRepository->getLabels($file);
        return $this->render('file/foreign.html.twig', array('userContext' => $userContext, 'file' => $file,
  'listUserFiles' => $listUserFiles, 'listUserTimetables' => $listUserTimetables,
  'listResources' => $listResources, 'listLabels' => $listLabels));
    }

    // Positionne un dossier comme dossier en cours
    /**
       * @Route("/{_locale}/file/setcurrent/{fileID}", name="file_set_current")
       * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
       */
    public function set_current(Request $request, File $file)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        // Mise a jour du dossier en cours
        AdministrationApi::setCurrentFile($em, $connectedUser, $file);
        $userContext->setCurrentFile($file); // Mettre a jour le dossier en cours dans le contexte utilisateur
        $request->getSession()->getFlashBag()->add('notice', 'file.current.updated.ok');
        return $this->redirectToRoute('file_edit', array('fileID' => $file->getId()));
    }

    /**
     * @Route("/{_locale}/file/booking_list/{fileID}/{page}", name="file_booking_list", requirements={"page"="\d+"})
     * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
     */
    public function booking_list(File $file, $page)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        $bRepository = $em->getRepository(Booking::class);
        $numberRecords = $bRepository->getAllBookingsCount($file);
        $listContext = new ListContext($em, $connectedUser, 'booking', 'booking', $page, $numberRecords);
        $listBookings = $bRepository->getAllBookings($file, $listContext->getFirstRecordIndex(), $listContext->getMaxRecords());

        $planning_path = 'planning_one'; // La route du planning est "one" ou "many" selon le nombre de planifications actives à la date du jour
        $numberPlanifications = PlanningApi::getNumberOfPlanifications($em, $file);
        if ($numberPlanifications > 1) {
            $planning_path = 'planning_many';
        }
        return $this->render(
    'file/booking.list.html.twig',
    array('userContext' => $userContext, 'listContext' => $listContext, 'file' => $file, 'listBookings' => $listBookings, 'planning_path' => $planning_path)
);
    }

    // Met à jour les indicateurs d'envoi des mails à la saisie des réservations
    /**
       * @Route("/{_locale}/file/bookingemail/{fileID}", name="file_booking_email")
       * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
       */
    public function booking_email(Request $request, File $file)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        $fileAdministrator = AdministrationApi::getFileBookingEmailAdministrator($em, $file);
        $bookingUser = AdministrationApi::getFileBookingEmailUser($em, $file);
        $fileBookingEmail = new FileBookingEmail($fileAdministrator, $bookingUser);
        $form = $this->createForm(FileBookingEmailType::class, $fileBookingEmail);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                AdministrationApi::setFileBookingEmailAdministrator($em, $connectedUser, $file, $fileBookingEmail->getFileAdministrator());
                AdministrationApi::setFileBookingEmailUser($em, $connectedUser, $file, $fileBookingEmail->getBookingUser());
                $request->getSession()->getFlashBag()->add('notice', 'file.booking.email.updated.ok');
                return $this->redirectToRoute('file_edit', array('fileID' => $file->getId()));
            }
        }
        return $this->render(
    'file/booking.email.html.twig',
    array('userContext' => $userContext, 'file' => $file, 'form' => $form->createView())
);
    }

    // Met à jour les informations de période de réservation
    /**
       * @Route("/{_locale}/file/bookingperiod/{fileID}", name="file_booking_period")
       * @ParamConverter("file", options={"mapping": {"fileID": "id"}})
       */
    public function booking_period(Request $request, File $file)
    {
        $connectedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
        $before = AdministrationApi::getFileBookingPeriodBefore($em, $file);
        $beforeType = AdministrationApi::getFileBookingPeriodBeforeType($em, $file);
        $beforeNumber = AdministrationApi::getFileBookingPeriodBeforeNumber($em, $file);
        $after = AdministrationApi::getFileBookingPeriodAfter($em, $file);
        $afterType = AdministrationApi::getFileBookingPeriodAfterType($em, $file);
        $afterNumber = AdministrationApi::getFileBookingPeriodAfterNumber($em, $file);
        $fileBookingPeriod = new FileBookingPeriod($before, $beforeType, $beforeNumber, $after, $afterType, $afterNumber);
        $form = $this->createForm(FileBookingPeriodType::class, $fileBookingPeriod);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                AdministrationApi::setFileBookingPeriodBefore($em, $connectedUser, $file, $fileBookingPeriod->getBefore());
                AdministrationApi::setFileBookingPeriodBeforeType($em, $connectedUser, $file, $fileBookingPeriod->getBeforeType());
                AdministrationApi::setFileBookingPeriodBeforeNumber($em, $connectedUser, $file, $fileBookingPeriod->getBeforeNumber());
                AdministrationApi::setFileBookingPeriodAfter($em, $connectedUser, $file, $fileBookingPeriod->getAfter());
                AdministrationApi::setFileBookingPeriodAfterType($em, $connectedUser, $file, $fileBookingPeriod->getAfterType());
                AdministrationApi::setFileBookingPeriodAfterNumber($em, $connectedUser, $file, $fileBookingPeriod->getAfterNumber());
                $request->getSession()->getFlashBag()->add('notice', 'file.booking.period.updated.ok');
                return $this->redirectToRoute('file_edit', array('fileID' => $file->getId()));
            }
        }
        return $this->render(
    'file/booking.period.html.twig',
    array('userContext' => $userContext, 'file' => $file, 'form' => $form->createView())
);
    }
}
