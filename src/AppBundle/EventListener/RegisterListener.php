<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegisterListener implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onRegisterCompleted',
        );
    }

    public function onRegisterCompleted(FormEvent $event)
    {
        $session = new Session();
        $session->start();
var_dump(array(
    $session
));die;
        // $url = $this->router->generate('homepage');

        // $event->setResponse(new RedirectResponse($url));
    }
}