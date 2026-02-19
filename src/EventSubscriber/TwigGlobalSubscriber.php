<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Twig\GlobalVariables;

class TwigGlobalSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private bool $demoMode = true; // active le mode démo global

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onController',
        ];
    }

    public function onController(ControllerEvent $event)
    {
        $twig = $event->getRequest()->attributes->get('_twig');
        // si on est dans Symfony 6, mieux vaut utiliser Twig via service
        // mais ici on va utiliser global via container.yaml

        // rien à faire ici, on fera global via service.yaml
    }
}
