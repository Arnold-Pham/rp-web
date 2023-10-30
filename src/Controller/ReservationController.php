<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Car;
use App\Form\CarType;
use App\Entity\Option;
use App\Entity\Address;
use App\Form\AddressType;
use App\Entity\Reservation;
use App\Entity\PersonalData;
use App\Form\ReservationType;
use App\Form\PersonalDataType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    #[Route('/donneruncodedereservation', name: 'app_code')]
    public function code(Request $request)
    {
        // Si le cookie est non-existant, on définies un nouveau cookie avec une genération d'un code en md5 et uniqid basé sur l'heure et la date et on définie le temps du cookie (time : est configuré pour 3 j) la 4 ème options ('/') permet de definir le cookie sur tout le site , GetHost permet de ciblé le nom domaine du site , et le dernier (secure) permet de sécuriser le cookie 
        if (!isset($_COOKIE['road'])) setcookie('road', md5(uniqid()), time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);

        return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        //si le cookie road n'est pas défini l'utilisateur est redirigé vers le chemin ou est situé le cookie
        if (!isset($_COOKIE['road'])) return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);

        // Introduit la valeur du cookie dans la variable code_road
        $code_road = $_COOKIE['road'];

        // Permet de supprimer l'ancien cookie 
        unset($_COOKIE['road']);

        // Remplace l'ancien cookie avec la même valeur (code_road) sauf avec une nouvelle date d'expiration 
        setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);

        $title = 'Réservation';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);
        $extra = 0;

        // Si il y'a pas de réservation celà va crée une nouvelle réservation 
        if ($reservation === null) $reservation = new Reservation();

        // Si dans reservation l'option est pas null la commande récupère les extra des options de la reservation
        if ($reservation->getOption() !== null) $extra = $reservation->getOption()->getExtra();

        // Affiche un nouveau formulaire avec les extra 
        $form = $this->createForm(ReservationType::class, $reservation, compact('extra'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setDateC(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $reservation->setStatus('Awaiting');
            $reservation->setCode($code_road);

            // Si l'utilisateur a coché la case valet (il affichera les extra dans la ligne de commande suivante)
            if ($form->get('valet')->getData()) {
                $extra = 0;

                // Récupère dans une variable les valeur des informations Extra qui sont envoyés du formulaire 
                $extra1 = $form->get('extra1')->getData();
                $extra2 = $form->get('extra2')->getData();
                $extra3 = $form->get('extra3')->getData();

                // On fonction des extra choisie par l'utilisateur il va définir un nombre 
                $extra1 ? $extra += 1 : $extra;
                $extra2 ? $extra += 2 : $extra;
                $extra3 ? $extra += 4 : $extra;

                //si l'option est null on crée une nouvelle option sinon on prend l'option existante pour la mettre dans la variable option
                $reservation->getOption() === null ? $option = new Option() : $option = $reservation->getOption();

                $option->setExtra($extra);
                $option->setReservation($reservation);

                $entityManager->persist($option);
                $entityManager->flush();
                $reservation->setOption($option);
            }

            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }



    #[Route('/information', name: 'app_infos_persos')]
    public function infos(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if (!isset($_COOKIE['road'])) return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);

        $code_road = $_COOKIE['road'];
        unset($_COOKIE['road']);
        setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);

        $title = 'Information Personnel';

        //dans la resevation on recupère par le critère code le code_road(le cookie et ses informations)
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);

        // Si il y'a pas de réservation l'utilisateur sera redirigé vers la page de réservation 
        if ($reservation === null) return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        $personalData = $reservation->getPersonalData();
        $genre = 'Homme';
        $company = null;
        // Crée une nouvelle instance PersonnalData si il y a pas de donnée récupérer par la variable $personnalData sinon il récupère les informations de $genre
        $personalData === null ? $personalData = new PersonalData() : $genre = $personalData->getGender();
        if ($personalData->getCompanyName() !== null) $company = $personalData->getCompanyName();

        $form = $this->createForm(PersonalDataType::class, $personalData, compact('genre', 'company'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si les donnée dans le genre sont récupérer et qu'il est true, les donnée sont modifier en "femme" sinon ils reste par défaut en "Homme"
            $form->get('genre')->getData() ? $personalData->setGender('Femme') : $personalData->setGender('Homme');

            // Si les donnée dans le type sont récupérer et qu'il sont == a 1 OU TRUE, la fonction récupère les donnée company sinon elle les considère comme null et "désactive" le setCompanyName
            $form->get('type')->getData() ? $personalData->setCompanyName($form->get('company')->getData()) : $personalData->setCompanyName(null);

            $entityManager->persist($personalData);
            $entityManager->flush();

            // Liaison entre PersonnalData & Réservation
            $reservation->setPersonalData($personalData);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_car', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }

    #[Route('/voiture', name: 'app_car')]
    public function car(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if (!isset($_COOKIE['road'])) return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);
        $code_road = $_COOKIE['road'];
        unset($_COOKIE['road']);
        setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);

        $title = 'Voiture';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);

        // Si il y'a pas de Réservation l'utilisateur sera redirigé vers la page réservation
        if ($reservation === null) return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);

        // Si il y a pas de données personnelles enregistré dans la reservation, il y a une redirection vers la page information
        if ($reservation->getPersonalData() === null) return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);

        // Permet de récupérer des Informations sur la voiture grace à la variable $reservation
        $car = $reservation->getPersonalData()->getCar();

        // si voiture est vide definir une nouvelle donnée car dans voiture
        if ($car === null) $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

            // La Liaison de PersonnalData & Réservation permet de mettre à jour des informations sur le Véhicule lié à une réservation 
            $reservation->getPersonalData()->setCar($car);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_adresse', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }


    #[Route('/adresse', name: 'app_adresse')]
    public function address(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if (!isset($_COOKIE['road'])) return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);
        $code_road = $_COOKIE['road'];
        unset($_COOKIE['road']);
        setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);

        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);
        $title = 'Adresse';
        if ($reservation === null) return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        if ($reservation->getPersonalData() === null) return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);

        // Si les donnée de car sont vide on redirige vers la page voiture
        if ($reservation->getPersonalData()->getCar() === null) return $this->redirectToRoute('app_car', [], Response::HTTP_SEE_OTHER);

        // Permet de récupérer l'adresse associée aux données personnelles d'une réservation 
        $address = $reservation->getPersonalData()->getAddress();

        // Permet de récupérer l'adresse de facturation associée aux données personnelles d'une réservation 
        $invoice = $reservation->getPersonalData()->getInvoice();

        // Valeur par défaut de addresse invoice
        $checkbox = 0;
        $iadd = null;
        $icit = null;
        $izip = null;

        // Crée une nouvelle instance Address si elle est null 
        if ($address === null) $address = new Address();

        // Vérifie si l'addresse de facturation existe et est différente de l'Adresse alors ca permet d'obtenir l'adresse de facturation associée à la réservation 
        if ($invoice !== null && $invoice != $address) {
            $checkbox = 1;
            $iadd = $invoice->getAddress();
            $icit = $invoice->getCity();
            $izip = $invoice->getZipCode();
        }

        // Crée le formulaire AddressType dans form et récupere  les options suivantes ( $checkbox , $iadd, $icit, $izip)
        $form = $this->createForm(AddressType::class, $address, compact('checkbox', 'iadd', 'icit', 'izip'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();
            $reservation->getPersonalData()->setAddress($address);
            $reservation->getPersonalData()->setInvoice($address);

            // Vérifie si le champ ('diff') dans le form à été coché 
            if ($form->get('diff')->getData()) {
                // Si invoice est null on donne une nouvelle valeur a la variable invoice: new address
                if ($invoice === null) $invoice = $address;

                // Récupère la section ('adressInvoice') à partir du form (AddressType) et de le stocker dans la variable $addIn
                $addIn = $form->get('addressInvoice');

                // Récupère les données adresse, ville et code postal des champs de la section "AdresseInvoice" et les stocker dans la variable $invoice
                $addressInvoice = $addIn->get('address_invoice')->getData();
                $cityInvoice = $addIn->get('city_invoice')->getData();
                $zipInvoice = $addIn->get('zipCode_invoice')->getData();

                $invoice->setAddress($addressInvoice);
                $invoice->setCity($cityInvoice);
                $invoice->setZipCode($zipInvoice);

                $entityManager->persist($invoice);
                $entityManager->flush();
                $reservation->getPersonalData()->setInvoice($invoice);
            }

            $entityManager->persist($reservation);
            $entityManager->flush();

            // dd($reservation->getPersonalData(), $address, $invoice);

            return $this->redirectToRoute('yeah', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }

    #[Route('/youhou', name: 'yeah')]
    public function yeah(): Response
    {
        $title = 'Yeah ça a redirigé';
        return $this->render('main/index.html.twig', compact('title'));
    }
}
