<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Form\AdminUser2Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/users')]
class AdminUsersController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index')]
    public function index(EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $title = 'Admin Users';
        $button_label = 'Ajouter';
        $users = $userRepository->findAll();

        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, '123456'));
            $user->setPicture('defaultPicture.png');
            $user->setRoles(["ROLE_USER"]);
            $user->setZone($this->region($form->get('region')->getData()));
            $user->setDateC(new DateTime('now', new DateTimeZone('Europe/Paris')));

            $form->get('genre')->getData() ? $user->setGender('Homme') : $user->setGender('Femme');

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('crud', 'Utilisateur ajouté avec succès');

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_user/index.html.twig', compact('users', 'form', 'title', 'button_label'));
    }


    #[Route('/show/{id}', name: 'app_admin_user_show')]
    public function show(User $user): Response
    {
        $title = 'Admin Users Show';

        return $this->render('admin_user/show.html.twig', compact('user', 'title'));
    }


    #[Route('/edit/{id}', name: 'app_admin_user_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, User $user, UserRepository $userRepository): Response
    {
        $title = 'Admin Users Edit';
        $button_label = 'Modifier';
        $users = $userRepository->findAll();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $form = $this->createForm(AdminUser2Type::class, $user);
            if ($user->getRoles()[0] == 'ROLE_SUPER_ADMIN') {
                $this->addFlash('error', 'Modification interdite');
                return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            $form = $this->createForm(AdminUserType::class, $user);
            if ($user->getRoles()[0] == 'ROLE_ADMIN' || $user->getRoles()[0] == 'ROLE_SUPER_ADMIN') {
                $this->addFlash('error', 'Modification interdite');
                return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setZone($this->region($form->get('region')->getData()));
            $form->get('genre')->getData() ? $user->setGender('Homme') : $user->setGender('Femme');

            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                $form->get('role')->getData() ? $user->setRoles(["ROLE_ADMIN"]) : $user->setRoles(["ROLE_USER"]);
            }

            $entityManager->flush();
            $this->addFlash('crud', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_user/index.html.twig', compact('users', 'form', 'title', 'button_label'));
    }


    #[Route('/delete/{id}', name: 'app_admin_user_delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('error', 'Utilisateur supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    private function region(int $value): string
    {
        switch ($value) {
            case 0:
                $region = 'Auvergne-Rhône-Alpes';
                break;
            case 1:
                $region = 'Bourgogne-Franche-Comté';
                break;
            case 2:
                $region = 'Bretagne';
                break;
            case 3:
                $region = 'Centre-Val de Loire';
                break;
            case 4:
                $region = 'Corse';
                break;
            case 5:
                $region = 'Grand Est';
                break;
            case 6:
                $region = 'Hauts-de-France';
                break;
            case 7:
                $region = 'Ile-de-France';
                break;
            case 8:
                $region = 'Normandie';
                break;
            case 9:
                $region = 'Nouvelle-Aquitaine';
                break;
            case 10:
                $region = 'Occitanie';
                break;
            case 11:
                $region = 'Pays de la Loire';
                break;
            case 12:
                $region = 'Provence-Alpes-Côte d\'Azur';
                break;
            case 13:
                $region = 'Guadeloupe';
                break;
            case 14:
                $region = 'Guyane';
                break;
            case 15:
                $region = 'Martinique';
                break;
            case 16:
                $region = 'La Réunion';
                break;
            case 17:
                $region = 'Mayotte';
                break;
        }

        return $region;
    }
}
