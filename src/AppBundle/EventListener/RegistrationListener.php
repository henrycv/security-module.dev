<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
// use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use AppBundle\Controller\UserController;
use AppBundle\Entity\UserRole;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegistrationListener implements EventSubscriberInterface
{
    private $router;

    private $em;

    public function __construct(UrlGeneratorInterface $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    public function onRegistrationCompleted($event)
    {
        // Save default Role
        $userRole = new UserRole();
        $userRole->setUserId($event->getUser()->getId());
        $userRole->setRoleId(UserController::DEFAULT_ROLE_USERS);
        $this->em->persist($userRole);

        $this->em->flush();
    }
}