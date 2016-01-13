<?php

namespace DoS\UserBundle\EventListener;

use DoS\UserBundle\Model\CustomerInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Resource\Event\ResourceEvent;

class UserSecurityRoleListener
{
    public function resizeUserRoles(ResourceEvent $event)
    {
        $object = $event->getSubject();

        if (!$object instanceof CustomerInterface) {
            return;
        }

        $user = $object->getUser();

        if (!$user instanceof IdentityInterface) {
            return;
        }

        $user->resizeSecurityRoles();
    }
}
