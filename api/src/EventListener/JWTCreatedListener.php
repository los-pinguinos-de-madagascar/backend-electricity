<?php

namespace App\EventListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $expiration = new \DateTime('+100 day');
        $expiration->setTime(2, 0, 0);

        $payload        = $event->getData();
        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);
    }
}
