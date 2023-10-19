<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserSettingsType;
use App\Form\UserSettings2Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $title = 'Profile';
        $ip = $request->getClientIp();

        return $this->render('user/index.html.twig', compact('user', 'title', 'ip'));
    }

    #[Route('/settings', name: 'app_user_settings')]
    public function settings(Request $request): Response
    {
        $user = $this->getUser();
        $title = 'Settings';

        $form = $this->createForm(UserSettingsType::class, $user);
        $form->handleRequest($request);

        return $this->render('user/settings.html.twig', compact('user', 'title', 'form'));
    }

    #[Route('/settings/{id}', name: 'app_user_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $title = 'Settings Edit';
        $submit = '';
        $button_label = 'Enregistrer';

        $form = $this->createForm(UserSettings2Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->get('genre')->getData() ? $user->setGender('Homme') : $user->setGender('Femme');
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_settings', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/settings.html.twig', compact('user', 'title', 'form', 'submit', 'button_label'));
    }
}
