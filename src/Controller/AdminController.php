<?php

namespace App\Controller;



use DateTime;
use DateTimeZone;
use App\Entity\User;
use App\Entity\Place;
use App\Entity\Airport;
use App\Entity\Parking;
use App\Form\AdminUserType;
use App\Form\AdminPlaceType;
use App\Form\AdminUser2Type;
use App\Form\AdminPlace2Type;
use App\Form\AdminAirportType;
use App\Form\AdminParkingType;
use App\Form\AdminParking2Type;
use App\Repository\UserRepository;
use App\Repository\PlaceRepository;
use App\Repository\AirportRepository;
use App\Repository\ParkingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/admin')]
class AdminController extends AbstractController
{
    //! =========================================================================== USERS ===========================================================================

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


    #[Route('/users/delete/{id}', name: 'app_admin_user_delete')]
    public function uDelete(EntityManagerInterface $entityManager, Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('error', 'Utilisateur supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/users/edit/{id}', name: 'app_admin_user_edit')]
    public function uEdit(EntityManagerInterface $entityManager, Request $request, User $user, UserRepository $userRepository): Response
    {
        $title = 'Admin Users Edit';
        $button_label = 'Modifier';
        $users = $userRepository->findAll();
        $form = $this->createForm(AdminUserType::class, $user);

        if ($this->isGranted('ROLE_SUPER_ADMIN')) $form = $this->createForm(AdminUser2Type::class, $user);
        if ($user->getRoles()[0] == 'ROLE_ADMIN' || $user->getRoles()[0] == 'ROLE_SUPER_ADMIN') {
            $this->addFlash('error', 'Modification interdite');
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/users/show/{id}', name: 'app_admin_user_show')]
    public function uShow(User $user): Response
    {
        $title = 'Admin Users Show';
        return $this->render('admin_user/show.html.twig', compact('user', 'title'));
    }



    //! =========================================================================== AIRPORT ===========================================================================

    #[Route('/airport', name: 'app_admin_airport_index')]
    public function airport(AirportRepository $airportRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = 'Admin Airport';
        $airport = new Airport();
        $airports = $airportRepository->findAll();
        $form = $this->createForm(AdminAirportType::class, $airport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $airport->setZone($this->region($form->get('region')->getData()));

            $entityManager->persist($airport);
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport ajouté avec succès');

            return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_airport/index.html.twig', compact('airports', 'title', 'form'));
    }

    #[Route('/airport/delete/{id}', name: 'app_admin_airport_delete')]
    public function aDelete(Airport $airport, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $airport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($airport);
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/airport/edit/{id}', name: 'app_admin_airport_edit')]
    public function aEdit(Airport $airport, AirportRepository $airportRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = 'Admin Airport Edit';
        $button_label = 'Modifier';
        $airports = $airportRepository->findAll();
        $form = $this->createForm(AdminAirportType::class, $airport, ['region' => $this->region($airport->getZone(), false)]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport modifié avec succès');

            return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_airport/index.html.twig', compact('airports', 'form', 'title', 'button_label'));
    }

    #[Route('/airport/show/{id}', name: 'app_admin_airport_show')]
    public function aShow(Airport $airport): Response
    {
        $title = 'Admin Airport Show';

        return $this->render('admin_airport/show.html.twig', compact('airport', 'title'));
    }



    //! =========================================================================== PARKING ===========================================================================

    #[Route('/parking', name: 'app_admin_parking_index')]
    public function parking(Request $request, EntityManagerInterface $entityManager, ParkingRepository $parkingRepository): Response
    {
        $title = 'Admin Parking';
        $parkings = $parkingRepository->findAll();
        $parking = new Parking();
        $form = $this->createForm(AdminParkingType::class, $parking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parking);
            $entityManager->flush();
            $this->addFlash('crud', 'Parking ajouté avec succès');

            return $this->redirectToRoute('app_admin_parking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_parking/index.html.twig', compact('parkings', 'form', 'title'));
    }

    #[Route('/parking/delete/{id}', name: 'app_admin_parking_delete')]
    public function pDelete(Request $request, Parking $parking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $parking->getId(), $request->request->get('_token'))) {
            $entityManager->remove($parking);
            $entityManager->flush();
            $this->addFlash('error', 'Parking supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_parking_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/parking/edit/{id}', name: 'app_admin_parking_edit')]
    public function pEdit(Request $request, Parking $parking, EntityManagerInterface $entityManager, ParkingRepository $parkingRepository): Response
    {
        $title = 'Admin Parking Edit';
        $button_label = 'Modifier';
        $parkings = $parkingRepository->findAll();
        $form = $this->createForm(AdminParking2Type::class, $parking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('crud', 'Parking modifié avec succès');

            return $this->redirectToRoute('app_admin_parking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_parking/index.html.twig', compact('parkings', 'form', 'title', 'button_label'));
    }


    //! =========================================================================== PLACES ===========================================================================

    #[Route('/place', name: 'app_admin_place_index')]
    public function place(Request $request, EntityManagerInterface $entityManager, PlaceRepository $placeRepository): Response
    {
        $title = 'Places de parking';
        $button_label = 'Ajouter';
        $places = $placeRepository->findAll();
        $place = new Place();

        $form = $this->createForm(AdminPlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $place->setAvailable(true);

            $entityManager->persist($place);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_place/index.html.twig', compact('title', 'form', 'places', 'button_label'));
    }

    #[Route('/place/delete/{id}', name: 'app_admin_place_delete')]
    public function plDelete(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $place->getId(), $request->request->get('_token'))) {
            $entityManager->remove($place);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_place_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/place/edit/{id}', name: 'app_admin_place_edit')]
    public function plEdit(Request $request, Place $place, EntityManagerInterface $entityManager, PlaceRepository $placeRepository): Response
    {
        $title = 'Places de parking';
        $button_label = 'Modifier';
        $places = $placeRepository->findAll();
        $form = $this->createForm(AdminPlace2Type::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_place/index.html.twig', compact('title', 'form', 'places', 'button_label'));
    }


    //! =========================================================================== FONCTIONS ===========================================================================

    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        $title = $this->region(0);
        return $this->render('admin/index.html.twig', compact('title'));
    }

    /**
     * Fonction permettant de transformer un nombre en sa région ou inversement, chercher la région pour en ressortir un nombre.
     *
     * @param integer|string $valeur
     * @return string|integer
     */
    private function region(int|string $valeur): string|int
    {
        $region = ['Auvergne-Rhône-Alpes', 'Bourgogne-Franche-Comté', 'Bretagne', 'Centre-Val de Loire', 'Corse', 'Grand Est', 'Hauts-de-France', 'Ile-de-France', 'Normandie', 'Nouvelle-Aquitaine', 'Occitanie', 'Pays de la Loire', 'Provence-Alpes-Côte d\'Azur', 'Guadeloupe', 'Guyane', 'Martinique', 'La Réunion', 'Mayotte'];

        if (gettype($valeur) == 'integer') return $region[$valeur];

        $search = array_search($valeur, $region) !== false ? array_search($valeur, $region) : 'Inconnu';
        return $search;
        /*
            foreach ($region as $i => $item) {
                if ($item === $valeur) return $i;
            }

            for ($i = 0; $i < count($region); $i++) {
                if ($region[$i] === $valeur) return $i;
            }

            $i = 50;
            while ($i < count($region)) {
                if ($region[$i] === $valeur) return $i;

                $i++;
            }

            do {
                if ($region[$i] === $valeur) return $i;
                $i++;
            } while ($i < count($region));
            */
    }
}
