<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Form\EventFilterType;
use App\Repository\EventRepository;
use App\Repository\RegistrationRepository;
use App\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        $user = $this->getUser();
        $events = $eventRepository->findBy(['created_by' => $user]);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $event->setCreatedBy($this->getUser()); // Set the authenticated user as the creator of the event

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($event->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet événement.');
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($event->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet événement.');
        }

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/list', name: 'app_event_list', methods: ['GET'])]
    public function list(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(EventFilterType::class);
        $allValues = $request->query->all();
        if (isset($allValues[$form->getName()])) {
            $form->submit($allValues[$form->getName()]);
        }

        $queryBuilder = $eventRepository->createQueryBuilder('e');

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();

            if ($filters && $filters['title']) {
                $queryBuilder->andWhere('e.title LIKE :title')
                             ->setParameter('title', '%' . $filters['title'] . '%');
            }

            if ($filters && $filters['date_from']) {
                $queryBuilder->andWhere('e.date >= :date_from')
                             ->setParameter('date_from', $filters['date_from']);
            }

            if ($filters && $filters['date_to']) {
                $queryBuilder->andWhere('e.date <= :date_to')
                             ->setParameter('date_to', $filters['date_to']);
            }

            if (isset($filters['is_public']) && $filters['is_public'] !== null) {
                $queryBuilder->andWhere('e.public = :public')
                             ->setParameter('public', $filters['is_public']);
            }
        }

        if (!$this->getUser()) {
            $queryBuilder->andWhere('e.public = :public')
                         ->setParameter('public', true);
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('event/list.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/registration', name: 'app_event_registration', methods: ['GET'])]
    public function registration(EventRepository $eventRepository, RegistrationRepository $registrationRepository): Response
    {
        $user = $this->getUser();
        $events = $eventRepository->findByNotCreatedByUser($user);
        $registrations = $registrationRepository->findBy(['user' => $user]);

        $registeredEventIds = array_map(function($registration) {
            return $registration->getEvent()->getId();
        }, $registrations);

        return $this->render('event/registration.html.twig', [
            'events' => $events,
            'registeredEventIds' => $registeredEventIds,
        ]);
    }

    #[Route('/event/{id}/register', name: 'app_event_register', methods: ['POST'])]
    public function register(Event $event, RegistrationService $registrationService): Response
    {
        $user = $this->getUser();
        $success = $registrationService->register($event, $user);
        if ($success) {
            $this->addFlash('success', 'Vous êtes inscrit à l\'événement.');
        } else {
            $this->addFlash('error', 'L\'inscription a échoué.');
        }

        return $this->redirectToRoute('app_event_registration');
    }

    #[Route('/event/{id}/unregister', name: 'app_event_unregister', methods: ['POST'])]
    public function unregister(Event $event, RegistrationService $registrationService): Response
    {
        $user = $this->getUser();
        $success = $registrationService->unregister($event, $user);
        if ($success) {
            $this->addFlash('success', 'Vous êtes désinscrit de l\'événement.');
        } else {
            $this->addFlash('error', 'La désinscription a échoué.');
        }

        return $this->redirectToRoute('app_event_registration');
    }
}
