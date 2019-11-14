<?php
namespace App\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\File;
use App\Entity\User;
use App\Entity\UserFile;
use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationResource;
use App\Entity\PlanificationView;

class DoctrineSubscriber implements EventSubscriber
{
    private $security;
    private $logger;
    private $translator;

    public function __construct($security, $logger, $translator)
    {
        $this->security = $security;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    public function getSecurity()
    {
        return $this->security;
    }

    public function getUser()
    {
        return $this->getSecurity()->getToken()->getUser();
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    public function getSubscribedEvents()
    {
        return array('postPersist', 'postUpdate');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->getLogger()->info('DoctrineSubscriber postPersist 1');
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->getLogger()->info('DoctrineSubscriber postPersist 2 User');
            $em = $args->getEntityManager();
            UserEvent::postPersist($em, $entity);
        } elseif ($entity instanceof File) {
            $this->getLogger()->info('DoctrineSubscriber postPersist 3 File');
            $em = $args->getEntityManager();
            FileEvent::postPersist($em, $this->getUser(), $entity, $this->getTranslator());
          } elseif ($entity instanceof UserFile) {
              $this->getLogger()->info('DoctrineSubscriber postPersist 4 UserFile');
              $em = $args->getEntityManager();
              UserFileEvent::postPersist($em, $this->getUser(), $entity);
          } elseif ($entity instanceof PlanificationPeriod) {
                $this->getLogger()->info('DoctrineSubscriber postPersist 4 PlanificationPeriod');
                $em = $args->getEntityManager();
                PlanificationPeriodEvent::postPersist($em, $this->getUser(), $entity);
          } elseif ($entity instanceof PlanificationResource) {
            $this->getLogger()->info('DoctrineSubscriber postPersist 5 PlanificationResource');
            $em = $args->getEntityManager();
            PlanificationResourceEvent::postPersist($em, $this->getUser(), $entity);
          } elseif ($entity instanceof PlanificationView) {
            $this->getLogger()->info('DoctrineSubscriber postPersist 6 PlanificationView');
            $em = $args->getEntityManager();
            PlanificationViewEvent::postPersist($em, $this->getUser(), $entity);
          }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->getLogger()->info('DoctrineSubscriber postUpdate 1');
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->getLogger()->info('DoctrineSubscriber postUpdate 2 User');
            $em = $args->getEntityManager();

            UserEvent::postUpdate($em, $entity);
        }
    }
}
