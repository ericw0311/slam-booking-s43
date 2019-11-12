<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;

use App\Entity\UserContext;
use App\Entity\ResourceClassification;
use App\Entity\UserFile;
use App\Entity\Resource;

use App\Form\ResourceClassificationType;

use App\Api\ResourceApi;

class ResourceClassificationController extends AbstractController
{
  /**
   * @Route("/{_locale}/resource_classification/{resourceType}", name="resource_classification_index")
   */
public function index($resourceType)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    // Classifications internes actives
    $activeInternalRC = ResourceApi::getActiveInternalResourceClassifications($em, $userContext->getCurrentFile(), $resourceType);
    // Nombre de ressources par classification interne
    $numberResourcesInternalRC =  ResourceApi::getInternalClassificationNumberResources($em, $userContext->getCurrentFile(), $resourceType);
    $rcRepository = $em->getRepository(ResourceClassification::class);
    // Classifications externes
    $listExternalRC = $rcRepository->getExternalResourceClassifications($userContext->getCurrentFile(), $resourceType);
    // Nombre de ressources par classification externe
    $numberResourcesExternalRC =  ResourceApi::getExternalClassificationNumberResources($em, $userContext->getCurrentFile(), $resourceType, $listExternalRC);
    return $this->render(
    'resource_classification/index.html.twig',
    array('userContext' => $userContext,
        'resourceType' => $resourceType,
        'activeInternalRC' => $activeInternalRC,
        'numberResourcesInternalRC' => $numberResourcesInternalRC,
        'listExternalRC' => $listExternalRC,
        'numberResourcesExternalRC' => $numberResourcesExternalRC)
);
}

/**
 * @Route("/{_locale}/resource_classification/activate_internal/{resourceType}/{resourceClassificationCode}", name="resource_classification_activate_internal")
 */
public function activate_internal(Request $request, $resourceType, $resourceClassificationCode)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur

    $rcRepository = $em->getRepository(ResourceClassification::class);
    $resourceClassification = $rcRepository->findOneBy(array('file' => $userContext->getCurrentFile(), 'internal' => 1, 'type' => $resourceType, 'code' => $resourceClassificationCode));
    if ($resourceClassification === null) {
        $resourceClassification = new ResourceClassification($connectedUser, $userContext->getCurrentFile());
        $resourceClassification->setInternal(1);
        $resourceClassification->setType($resourceType);
        $resourceClassification->setCode($resourceClassificationCode);
        $resourceClassification->setName($resourceClassificationCode);
        $em->persist($resourceClassification);
        $resourceClassification->setActive(1);
    } else {
        $resourceClassification->setActive(1);
    }
    $em->flush();
    $request->getSession()->getFlashBag()->add('notice', 'resourceClassification.activated.ok');
    return $this->redirectToRoute('resource_classification_index', array('resourceType' => $resourceType));
}

/**
 * @Route("/{_locale}/resource_classification/unactivate_internal/{resourceType}/{resourceClassificationCode}", name="resource_classification_unactivate_internal")
 */
public function unactivate_internal(Request $request, $resourceType, $resourceClassificationCode)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    $rcRepository = $em->getRepository(ResourceClassification::class);
    $resourceClassification = $rcRepository->findOneBy(array('file' => $userContext->getCurrentFile(), 'internal' => 1, 'type' => $resourceType, 'code' => $resourceClassificationCode));

    if ($resourceClassification === null) {
        $resourceClassification = new ResourceClassification($connectedUser, $userContext->getCurrentFile());
        $resourceClassification->setInternal(1);
        $resourceClassification->setType($resourceType);
        $resourceClassification->setCode($resourceClassificationCode);
        $resourceClassification->setName($resourceClassificationCode);
        $em->persist($resourceClassification);
        $resourceClassification->setActive(0);
    } else {
        $resourceClassification->setActive(0);
    }
    $em->flush();
    $request->getSession()->getFlashBag()->add('notice', 'resourceClassification.unactivated.ok');
    return $this->redirectToRoute('resource_classification_index', array('resourceType' => $resourceType));
}

/**
 * @Route("/{_locale}/resource_classification/activate_external/{resourceType}/{resourceClassificationID}", name="resource_classification_activate_external")
 * @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
 */
public function activate_external(Request $request, $resourceType, \App\Entity\ResourceClassification $resourceClassification)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    $resourceClassification->setActive(1);
    $em->flush();
    $request->getSession()->getFlashBag()->add('notice', 'resourceClassification.activated.ok');

    return $this->redirectToRoute('resource_classification_index', array('resourceType' => $resourceType));
}

/**
 * @Route("/{_locale}/resource_classification/unactivate_external/{resourceType}/{resourceClassificationID}", name="resource_classification_unactivate_external")
 * @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
 */
public function unactivate_external(Request $request, $resourceType, \App\Entity\ResourceClassification $resourceClassification)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    $resourceClassification->setActive(0);
    $em->flush();
    $request->getSession()->getFlashBag()->add('notice', 'resourceClassification.unactivated.ok');
    return $this->redirectToRoute('resource_classification_index', array('resourceType' => $resourceType));
}

/**
 * @Route("/{_locale}/resource_classification/add/{resourceType}", name="resource_classification_add")
 */
public function add(Request $request, $resourceType)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    $resourceClassification = new ResourceClassification($connectedUser, $userContext->getCurrentFile());
    $resourceClassification->setInternal(0);
    $resourceClassification->setType($resourceType);
    $resourceClassification->setActive(1);
    $form = $this->createForm(ResourceClassificationType::class, $resourceClassification);
    if ($request->isMethod('POST')) {
        $form->submit($request->request->get($form->getName()));
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($resourceClassification);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'resourceClassification.created.ok');
            return $this->redirectToRoute('resource_classification_index', array('resourceType' => $resourceType));
        }
    }
    return $this->render(
    'resource_classification/add.html.twig',
    array('userContext' => $userContext, 'resourceType' => $resourceType, 'form' => $form->createView())
);
}

/**
 * @Route("/{_locale}/resource_classification/modify/{resourceType}/{resourceClassificationID}", name="resource_classification_modify")
 * @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
 */
public function modify(Request $request, $resourceType, \App\Entity\ResourceClassification $resourceClassification)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    $form = $this->createForm(ResourceClassificationType::class, $resourceClassification);
    if ($request->isMethod('POST')) {
        $form->submit($request->request->get($form->getName()));
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'resourceClassification.updated.ok');
            return $this->redirectToRoute('resource_classification_index', array('resourceType' => $resourceType));
        }
    }
    return $this->render(
    'resource_classification/modify.html.twig',
    array('userContext' => $userContext, 'resourceType' => $resourceType, 'resourceClassification' => $resourceClassification, 'form' => $form->createView())
);
}

/**
 * @Route("/{_locale}/resource_classification/delete/{resourceType}/{resourceClassificationID}", name="resource_classification_delete")
 * @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
 */
public function delete(Request $request, $resourceType, \App\Entity\ResourceClassification $resourceClassification)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    $form = $this->get('form.factory')->create();
    if ($request->isMethod('POST')) {
        $form->submit($request->request->get($form->getName()));
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($resourceClassification);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'resourceClassification.deleted.ok');
            return $this->redirectToRoute('resource_classification_index', array('resourceType' => $resourceType));
        }
    }
    return $this->render('resource_classification/delete.html.twig', array('userContext' => $userContext, 'resourceType' => $resourceType, 'resourceClassification' => $resourceClassification, 'form' => $form->createView()));
}

/**
 * @Route("/{_locale}/resource_classification/foreign_internal/{resourceType}/{resourceClassificationCode}", name="resource_classification_foreign_internal")
 */
public function foreign_internal($resourceType, $resourceClassificationCode)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    if ($resourceType == 'USER') {
        $ufRepository = $em->getRepository(UserFile::class);
        $listUserFiles = $ufRepository->getUserFilesFrom_IRC($userContext->getCurrentFile(), $resourceClassificationCode);
        return $this->render(
        'resource_classification/foreign.user.internal.html.twig',
        array('userContext' => $userContext, 'resourceType' => $resourceType, 'resourceClassificationCode' => $resourceClassificationCode,
            'action' => 'unactivate', 'listUserFiles' => $listUserFiles)
    );
    } else {
        $rRepository = $em->getRepository(Resource::class);
        $listResources = $rRepository->getResources_IRC($userContext->getCurrentFile(), $resourceType, $resourceClassificationCode);

        return $this->render(
        'resource_classification/foreign.internal.html.twig',
        array('userContext' => $userContext, 'resourceType' => $resourceType, 'resourceClassificationCode' => $resourceClassificationCode, 'listResources' => $listResources)
    );
    }
}

/**
 * @Route("/{_locale}/resource_classification/foreign_external/{resourceType}/{resourceClassificationID}/{action}", name="resource_classification_foreign_external")
 * @ParamConverter("resourceClassification", options={"mapping": {"resourceClassificationID": "id"}})
 */
public function foreign_external($resourceType, \App\Entity\ResourceClassification $resourceClassification, $action)
{
    $connectedUser = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $userContext = new UserContext($em, $connectedUser); // contexte utilisateur
    if ($resourceType == 'USER') {
        $ufRepository = $em->getRepository(UserFile::class);
        $listUserFiles = $ufRepository->getUserFilesFrom_ERC($userContext->getCurrentFile(), $resourceClassification);
        return $this->render(
        'resource_classification/foreign.user.external.html.twig',
        array('userContext' => $userContext, 'resourceType' => $resourceType, 'resourceClassification' => $resourceClassification,
            'action' => $action, 'listUserFiles' => $listUserFiles)
    );
    } else {
        $rRepository = $em->getRepository(Resource::class);
        $listResources = $rRepository->getResources_ERC($userContext->getCurrentFile(), $resourceType, $resourceClassification);

        return $this->render(
        'resource_classification/foreign.external.html.twig',
        array('userContext' => $userContext, 'resourceType' => $resourceType, 'resourceClassification' => $resourceClassification,
            'action' => $action, 'listResources' => $listResources)
    );
    }
    return $this->render('resource_classification/index.html.twig', array('userContext' => $userContext));
}
}
