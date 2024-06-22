<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function register(Event $event, User $user): bool
    {
        if ($event->getRemainingPlaces() <= 0) {
            return false;
        }

        if (!$user) {
            return false;
        }

        $registration = $this->em->getRepository(Registration::class)->findOneBy(['event' => $event, 'user' => $user]);
        if ($registration) {
            return false;
        }

        $registration = new Registration();
        $registration->setEvent($event);
        $registration->setUser($user);
        $registration->setRegisteredAt(new \DateTimeImmutable());

        $this->em->persist($registration);
        $this->em->flush();

        return true;
    }

    public function unregister(Event $event, User $user): bool
    {
        if (!$user) {
            return false;
        }

        $registration = $this->em->getRepository(Registration::class)->findOneBy(['event' => $event, 'user' => $user]);
        if (!$registration) {
            return false;
        }

        $this->em->remove($registration);
        $this->em->flush();

        return true;
    }
}