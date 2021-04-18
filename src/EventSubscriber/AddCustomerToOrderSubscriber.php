<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class AddCustomerToOrderSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function addCustomer(ViewEvent $event)
    {
        $order = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$order instanceof Order || Request::METHOD_POST !== $method)
            return;
        $user = $this->security->getUser();
        $order->setCustomer($user);

    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW =>['addCustomer', EventPriorities::PRE_WRITE]
        ];
    }

}
