<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\ProfilePasswordFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_event_index');
    }

    #[Route(path: '/profile', name: 'app_profile')]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('security/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/profile/edit', name: 'app_profile_edit')]
    public function profileEdit(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        // copy the user to avoid changing the original user
        $userClone = clone $user;
        $form = $this->createForm(ProfileFormType::class, $userClone);
        $form->handleRequest($request);
        
        $errors = $form->getErrors(true, false);
        
        if ($form->isSubmitted() && $form->isValid() && $errors->count() === 0) {

            // copy the data to the original user
            $user->setLastName($userClone->getLastName());
            $user->setFirstName($userClone->getFirstName());
            $user->setEmail($userClone->getEmail());

            // delete the clone
            unset($userClone);
    
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, AppCustomAuthenticator::class, 'main');
        }
        return $this->render('security/profile_edit.html.twig', [
            'editForm' => $form, 'errorCount' => $errors->count(),
        ]);
    }

    #[Route(path: '/profile/password', name: 'app_profile_password')]
    public function profilePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfilePasswordFormType::class);
        $form->handleRequest($request);

        $errors = $form->getErrors(true, false);

        if ($form->isSubmitted() && $form->isValid() && $errors->count() === 0) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, AppCustomAuthenticator::class, 'main');
        }

        return $this->render('security/profile_password.html.twig', [
            'passwordForm' => $form, 'errorCount' => $errors->count(),
        ]);
    }
}
