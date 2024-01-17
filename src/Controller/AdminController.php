<?php

namespace App\Controller;


use DateTime;
use DateTimeZone;
use App\Entity\Car;
use App\Entity\User;
use App\Entity\Place;
use App\Form\CarType;
use App\Entity\Address;
use App\Entity\Airport;
use App\Entity\Parking;
use App\Entity\Reservation;
use App\Form\AdminUserType;
use App\Entity\PersonalData;
use App\Form\AdminPlaceType;
use App\Form\AdminUser2Type;
use App\Form\AdminPlace2Type;
use App\Form\AdminAirportType;
use App\Form\AdminParkingType;
use App\Form\AdminParking2Type;
use App\Form\AddressInvoiceType;
use App\Repository\CarRepository;
use App\Form\AdminReservationType;
use App\Repository\UserRepository;
use App\Form\AdminPersonalDataType;
use App\Repository\PlaceRepository;
use App\Repository\AddressRepository;
use App\Repository\AirportRepository;
use App\Repository\ParkingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use App\Repository\PersonalDataRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/admin')]
class AdminController extends AbstractController
{
    //§ -----------------------------------------------------------------------------------------------------------
    //! ================================================== USERS ==================================================
    //§ -----------------------------------------------------------------------------------------------------------

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
            $user->setPassword($userPasswordHasher->hashPassword($user, '123456789@ABCdef'));
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

        return $this->render('admin/user.html.twig', compact('users', 'form', 'title', 'button_label'));
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
        $region = $this->region($user->getZone());
        $form = $this->createForm(AdminUserType::class, $user, compact('region'));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) $form = $this->createForm(AdminUser2Type::class, $user, compact('region'));

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

        return $this->render('admin/user.html.twig', compact('users', 'form', 'title', 'button_label'));
    }


    //§ -------------------------------------------------------------------------------------------------------------
    //! ================================================== AIRPORT ==================================================
    //§ -------------------------------------------------------------------------------------------------------------

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

        return $this->render('admin/airport.html.twig', compact('airports', 'title', 'form'));
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
        $form = $this->createForm(AdminAirportType::class, $airport, ['region' => $this->region($airport->getZone())]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport modifié avec succès');

            return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/airport.html.twig', compact('airports', 'form', 'title', 'button_label'));
    }


    //§ -------------------------------------------------------------------------------------------------------------
    //! ================================================== PARKING ==================================================
    //§ -------------------------------------------------------------------------------------------------------------

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

        return $this->render('admin/parking.html.twig', compact('parkings', 'form', 'title'));
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

        return $this->render('admin/parking.html.twig', compact('parkings', 'form', 'title', 'button_label'));
    }


    //§ ------------------------------------------------------------------------------------------------------------
    //! ================================================== PLACES ==================================================
    //§ ------------------------------------------------------------------------------------------------------------

    #[Route('/place', name: 'app_admin_place_index')]
    public function place(Request $request, EntityManagerInterface $entityManager, PlaceRepository $placeRepository): Response
    {
        $title = 'Admin Places';
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

        return $this->render('admin/place.html.twig', compact('title', 'form', 'places', 'button_label'));
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
        $title = 'Admin Places Edit';
        $button_label = 'Modifier';
        $places = $placeRepository->findAll();
        $form = $this->createForm(AdminPlace2Type::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/place.html.twig', compact('title', 'form', 'places', 'button_label'));
    }


    //§ ------------------------------------------------------------------------------------------------------------
    //! =============================================== RÉSERVATION ================================================
    //§ ------------------------------------------------------------------------------------------------------------

    #[Route('/resa', name: 'app_admin_resa_index')]
    public function resa(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $title = 'Admin Réservation';
        $button_label = 'Ajouter';
        $reservations = $reservationRepository->findAll();
        $reservation = new Reservation();
        $form = $this->createForm(AdminReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_resa_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/resa.html.twig', compact('title', 'form', 'reservations', 'button_label'));
    }

    #[Route('/resa/delete/{id}', name: 'app_admin_resa_delete')]
    public function rDelete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_resa_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/resa/edit/{id}', name: 'app_admin_resa_edit')]
    public function rEdit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $title = 'Admin Réservation Edit';
        $button_label = 'Modifier';
        $reservations = $reservationRepository->findAll();
        $extra = 0;
        if ($reservation->getOption() !== null) $extra = $reservation->getOption()->getExtra();
        $form = $this->createForm(AdminReservationType::class, $reservation, compact('extra'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_resa_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/resa.html.twig', compact('title', 'form', 'reservations', 'button_label'));
    }

    #[Route('/infos', name: 'app_admin_infos_index')]
    public function infos(Request $request, EntityManagerInterface $entityManager, PersonalDataRepository $personalDataRepository): Response
    {
        $title = 'Admin Infos';
        $button_label = 'Ajouter';
        $datas = $personalDataRepository->findAll();
        $data = new PersonalData();
        $genre = 'Homme';
        $company = null;

        $form = $this->createForm(AdminPersonalDataType::class, $data, compact('genre', 'company'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->get('genre')->getData() ? $data->setGender('Homme') : $data->setGender('Femme');
            $data->setCompanyName($form->get('company')->getData());

            $entityManager->persist($data);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_infos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/infos.html.twig', compact('title', 'form', 'datas', 'button_label'));
    }

    #[Route('/infos/delete/{id}', name: 'app_admin_infos_delete')]
    public function iDelete(Request $request, PersonalData $personalData, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $personalData->getId(), $request->request->get('_token'))) {
            $entityManager->remove($personalData);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_infos_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/infos/edit/{id}', name: 'app_admin_infos_edit')]
    public function iEdit(Request $request, PersonalData $personalData, EntityManagerInterface $entityManager, PersonalDataRepository $personalDataRepository): Response
    {
        $title = 'Admin Infos Edit';
        $button_label = 'Modifier';
        $datas = $personalDataRepository->findAll();

        if ($personalData === null)
            return $this->redirectToRoute('app_admin_infos_index', [], Response::HTTP_SEE_OTHER);
        $genre = $personalData->getGender();

        if ($personalData->getCompanyName() !== null) $company = $personalData->getCompanyName();
        $form = $this->createForm(AdminPersonalDataType::class, $personalData, compact('genre', 'company'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_infos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/infos.html.twig', compact('title', 'form', 'datas', 'button_label'));
    }

    #[Route('/voiture', name: 'app_admin_car_index')]
    public function voiture(Request $request, EntityManagerInterface $entityManager, CarRepository $carRepository): Response
    {
        $title = 'Admin Voiture';
        $button_label = 'Ajouter';
        $cars = $carRepository->findAll();
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/car.html.twig', compact('title', 'form', 'cars', 'button_label'));
    }

    #[Route('/voiture/delete/{id}', name: 'app_admin_car_delete')]
    public function cDelete(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $car->getId(), $request->request->get('_token'))) {
            $entityManager->remove($car);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_car_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/voiture/edit/{id}', name: 'app_admin_car_edit')]
    public function cEdit(Request $request, Car $car, EntityManagerInterface $entityManager, CarRepository $carRepository): Response
    {
        $title = 'Admin Voiture';
        $button_label = 'Ajouter';
        $cars = $carRepository->findAll();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/car.html.twig', compact('title', 'form', 'cars', 'button_label'));
    }

    #[Route('/adresse', name: 'app_admin_address_index')]
    public function adresse(Request $request, EntityManagerInterface $entityManager, AddressRepository $addressRepository): Response
    {
        $title = 'Admin Adresse';
        $button_label = 'Ajouter';
        $adresses = $addressRepository->findAll();
        $adresse = new Address();
        $form = $this->createForm(AddressInvoiceType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adresse);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_address_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/address.html.twig', compact('title', 'form', 'adresses', 'button_label'));
    }

    #[Route('/adresse/delete/{id}', name: 'app_admin_address_delete')]
    public function adDelete(Request $request, Address $address, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->request->get('_token'))) {
            $entityManager->remove($address);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_address_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/adresse/edit/{id}', name: 'app_admin_address_edit')]
    public function adEdit(Request $request, Address $adresse, EntityManagerInterface $entityManager, AddressRepository $addressRepository): Response
    {
        $title = 'Admin Adresse Edit';
        $button_label = 'Modifier';
        $adresses = $addressRepository->findAll();
        $form = $this->createForm(AddressInvoiceType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adresse);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_address_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/address.html.twig', compact('title', 'form', 'adresses', 'button_label'));
    }


    //§ ---------------------------------------------------------------------------------------------------------------
    //! ================================================== FONCTIONS ==================================================
    //§ ---------------------------------------------------------------------------------------------------------------

    private function region(int|string $valeur): string|int
    {
        $region = ['Auvergne-Rhône-Alpes', 'Bourgogne-Franche-Comté', 'Bretagne', 'Centre-Val de Loire', 'Corse', 'Grand Est', 'Hauts-de-France', 'Ile-de-France', 'Normandie', 'Nouvelle-Aquitaine', 'Occitanie', 'Pays de la Loire', 'Provence-Alpes-Côte d\'Azur', 'Guadeloupe', 'Guyane', 'Martinique', 'La Réunion', 'Mayotte'];

        if (gettype($valeur) == 'integer') return $region[$valeur];
        array_search($valeur, $region) !== false ? $search = array_search($valeur, $region) : $search = 'Inconnu';

        return $search;
    }
}
