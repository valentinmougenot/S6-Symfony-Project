<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{

    #[Route(['/','/event'], name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/event/create', name: 'app_event_create')]
    public function create(): Response
    {
        return $this->render('event/create.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/event/list', name: 'app_event_list')]
    public function list(): Response
    {
        return $this->render('event/list.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/event/register', name: 'app_event_register')]
    public function register(): Response
    {
        return $this->render('event/register.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }
}
