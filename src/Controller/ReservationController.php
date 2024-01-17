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
use App\Form\CodeSearchType;
use App\Form\ReservationType;
use App\Form\PersonalDataType;
use App\Form\AddressInvoiceType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ReservationController extends AbstractController
{
    //§ ---------------------------------------------------------------------------------------------------------------
    //! ================================================== FONCTIONS ==================================================
    //§ ---------------------------------------------------------------------------------------------------------------

    private function codeCookie(Request $request): string
    {
        if (isset($_COOKIE['road'])) {
            if ($_COOKIE['road'] == '') {
                unset($_COOKIE['road']);
                $code_road = strtoupper(hash('xxh64', uniqid()));
            } else {
                $code_road = $_COOKIE['road'];
                unset($_COOKIE['road']);
            }
        } else {
            $code_road = strtoupper(hash('xxh64', uniqid()));
        }

        setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        return $code_road;
    }

    private function codeDel(Request $request): string
    {
        $code_road = $_COOKIE['road'];
        unset($_COOKIE['road']);
        setcookie('road', '', time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        return $code_road;
    }

    //§ ------------------------------------------------------------------------------------------------------------------
    //! ================================================== VÉRIFICATION ==================================================
    //§ ------------------------------------------------------------------------------------------------------------------

    #[Route('/verification', name: 'verif')]
    public function verif(Request $request, ReservationRepository $reservationRepository): Response
    {
        $title = 'Réservation';
        $form = $this->createForm(CodeSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();

            $reservation = $reservationRepository->findOneBy(['code' => strtoupper($code)]);

            if ($reservation instanceof Reservation) {
                return $this->redirectToRoute('resa', ['code' => strtoupper($code)], Response::HTTP_FOUND);
            } else {
                $this->addFlash('error', 'Code de réservation incorrect.');
            }
        }

        return $this->render('main/code.html.twig', compact('title', 'form'));
    }

    #[Route('/reservation/{code}', name: 'resa')]
    public function recap(string $code, ReservationRepository $reservationRepository): Response
    {
        $title = 'Récapitulatif';

        $verif = $reservationRepository->findOneBy(['code' => strtoupper($code)]);
        if ($verif === null) return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        $prenom = $verif->getPersonalData()->getFirstname() . '.';
        $option = $verif->getOption() ? $verif->getOption()->getExtra() : null;
        $parking = $verif->getParking() != null ? $verif->getParking()->getName() : '';
        $place = $verif->getPlace() != null ? $verif->getPlace()->getLabel() : '';

        return $this->render('main/resume.html.twig', compact('title', 'verif', 'prenom', 'option', 'parking', 'place'));
    }

    //§ ------------------------------------------------------------------------------------------------------------------
    //! ================================================== RÉSERVATIONS ==================================================
    //§ ------------------------------------------------------------------------------------------------------------------

    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $code_road = $this->codeCookie($request);

        $title = 'Réservation';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]) ?? new Reservation();
        $extra = 0;

        if ($reservation->getOption() !== null) $extra = $reservation->getOption()->getExtra();

        $form = $this->createForm(ReservationType::class, $reservation, compact('extra'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setDateC(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $reservation->setStatus('Awaiting');
            $reservation->setCode($code_road);

            if ($form->get('valet')->getData()) {
                $extra = 0;

                $extra1 = $form->get('extra1')->getData();
                $extra2 = $form->get('extra2')->getData();
                $extra3 = $form->get('extra3')->getData();
                $extra1 ? $extra += 1 : $extra;
                $extra2 ? $extra += 2 : $extra;
                $extra3 ? $extra += 4 : $extra;

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

        return $this->render('main/resa/index.html.twig', compact('title', 'form'));
    }

    #[Route('/information', name: 'app_infos_persos')]
    public function infos(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $code_road = $this->codeCookie($request);

        $title = 'Informations Personnels';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);
        if ($reservation === null) return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);

        $personalData = $reservation->getPersonalData();
        $genre = 'Homme';
        $company = null;

        $personalData === null ? $personalData = new PersonalData() : $genre = $personalData->getGender();
        if ($personalData->getCompanyName() !== null) $company = $personalData->getCompanyName();

        $form = $this->createForm(PersonalDataType::class, $personalData, compact('genre', 'company'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->get('genre')->getData() ? $personalData->setGender('Homme') : $personalData->setGender('Femme');
            $personalData->setCompanyName(null);

            if ($form->get('type')->getData()) {
                if ($form->get('company')->getData() === null) {
                    return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);
                }
                $personalData->setCompanyName($form->get('company')->getData());
            }

            $entityManager->persist($personalData);
            $entityManager->flush();
            $reservation->setPersonalData($personalData);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_car', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/resa/index.html.twig', compact('title', 'form'));
    }

    #[Route('/voiture', name: 'app_car')]
    public function car(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $code_road = $this->codeCookie($request);

        $title = 'Voiture';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);

        if ($reservation === null) return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        if ($reservation->getPersonalData() === null) return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);

        $car = $reservation->getPersonalData()->getCar() ?? new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();
            $reservation->getPersonalData()->setCar($car);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_adresse', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/resa/index.html.twig', compact('title', 'form'));
    }

    #[Route('/adresse', name: 'app_adresse')]
    public function address(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $code_road = $this->codeCookie($request);

        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);
        $title = 'Adresse';

        if ($reservation === null) return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        if ($reservation->getPersonalData() === null) return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);
        if ($reservation->getPersonalData()->getCar() === null) return $this->redirectToRoute('app_car', [], Response::HTTP_SEE_OTHER);

        $address = $reservation->getPersonalData()->getAddress() ?? new Address();
        $checkbox = 0;

        if ($reservation->getPersonalData()->getInvoice() !== null && $reservation->getPersonalData()->getInvoice() !== $address) $checkbox = 1;

        $form = $this->createForm(AddressType::class, $address, compact('checkbox'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            $reservation->getPersonalData()->setAddress($address);
            $reservation->getPersonalData()->setInvoice($address);

            $entityManager->persist($reservation);
            $entityManager->flush();

            if ($form->get('diff')->getData()) return $this->redirectToRoute('app_invoice', [], Response::HTTP_SEE_OTHER);

            return $this->redirectToRoute('yeah', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/resa/index.html.twig', compact('title', 'form'));
    }

    #[Route('/facture', name: 'app_invoice')]
    public function invoice(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $code_road = $this->codeCookie($request);

        $title = 'Adresse de Facturation';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);
        $address = $reservation->getPersonalData()->getAddress();
        $invoice = $reservation->getPersonalData()->getInvoice();

        if ($reservation === null) return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        if ($reservation->getPersonalData() === null) return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);
        if ($reservation->getPersonalData()->getCar() === null) return $this->redirectToRoute('app_car', [], Response::HTTP_SEE_OTHER);
        if ($address === null || $invoice === null) return $this->redirectToRoute('app_address', [], Response::HTTP_SEE_OTHER);
        if ($invoice == $address) $invoice = new Address();

        $form = $this->createForm(AddressInvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($invoice);
            $entityManager->flush();
            $reservation->getPersonalData()->setInvoice($invoice);

            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('yeah', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/resa/index.html.twig', compact('title', 'form'));
    }

    #[Route('/paiement', name: 'yeah')]
    public function yeah(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $code = strtoupper($this->codeDel($request));
        $reservation = $reservationRepository->findOneBy(['code' => $code]);
        $reservation->setDateC(new DateTime('now', new DateTimeZone('Europe/Paris')));
        $entityManager->persist($reservation);
        $entityManager->flush();

        $title = 'Paiement';


        return $this->render('main/resa/paiement.html.twig', compact('title', 'code'));
    }
}
