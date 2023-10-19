<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Car;
use DateTime;
use DateTimeZone;
use App\Entity\Option;
use App\Entity\Reservation;
use App\Entity\PersonalData;
use App\Form\AddressType;
use App\Form\CarType;
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
        if (!isset($_COOKIE['road'])) setcookie('road', md5(uniqid()), time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if (isset($_COOKIE['road'])) {
            $code_road = $_COOKIE['road'];
            unset($_COOKIE['road']);
            setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        } else {
            return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);
        }

        $title = 'Réservation';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);

        if ($reservation === null) {
            $reservation = new Reservation();
            $extra = 0;
        } else {
            if ($reservation->getOption() === null) {
                $extra = 0;
            } else {
                $extra = $reservation->getOption()->getExtra();
            }
        }

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

                $extra1 ? $extra = $extra + 1 : $extra;
                $extra2 ? $extra = $extra + 2 : $extra;
                $extra3 ? $extra = $extra + 4 : $extra;

                $option = new Option();
                $option->setExtra($extra);

                $option->setReservation($reservation);
                $entityManager->persist($option);
                $entityManager->flush();

                $reservation->setOption($option);
                $entityManager->persist($reservation);
                $entityManager->flush();
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
        if (isset($_COOKIE['road'])) {
            $code_road = $_COOKIE['road'];
            unset($_COOKIE['road']);
            setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        } else {
            return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);
        }

        $title = 'Information Personnel';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);

        if ($reservation === null) {
            return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        } else {
            $personalData = $reservation->getPersonalData();

            if ($personalData === null) {
                $personalData = new PersonalData();
                $genre = 'Homme';
            } else {
                $genre = $personalData->getGender();
            }
        }

        $form = $this->createForm(PersonalDataType::class, $personalData, compact('genre'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->get('genre')->getData() ? $personalData->setGender('Femme') : $personalData->setGender('Homme');
            $form->get('type')->getData() ? $personalData->setCompanyName($form->get('company')->getData()) : $personalData->setCompanyName(null);

            $entityManager->persist($personalData);
            $entityManager->flush();

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
        if (isset($_COOKIE['road'])) {
            $code_road = $_COOKIE['road'];
            unset($_COOKIE['road']);
            setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        } else {
            return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);
        }

        $title = 'Voiture';
        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);

        if ($reservation === null) {
            return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        } elseif ($reservation->getPersonalData() === null) {
            return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);
        } else {
            $car = $reservation->getPersonalData()->getCar();

            if ($car === null) {
                $car = new car();
            }
        }

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

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }

    #[Route('/adresse', name: 'app_adresse')]
    public function adress(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if (isset($_COOKIE['road'])) {
            $code_road = $_COOKIE['road'];
            unset($_COOKIE['road']);
            setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        } else {
            return $this->redirectToRoute('app_code', [], Response::HTTP_SEE_OTHER);
        }

        $reservation = $reservationRepository->findOneBy(['code' => $code_road]);
        $title = 'Adresse';

        if ($reservation === null) {
            return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        } elseif ($reservation->getPersonalData() === null) {
            return $this->redirectToRoute('app_infos_persos', [], Response::HTTP_SEE_OTHER);
        } elseif ($reservation->getPersonalData()->getCar() === null) {
            return $this->redirectToRoute('app_car', [], Response::HTTP_SEE_OTHER);
        }

        $address = $reservation->getPersonalData()->getAddress();
        $invoice = $reservation->getPersonalData()->getInvoice();

        if ($address === null) {
            $address = new Address();
            $checkbox = false;
            $iAddress = null;
            $iCity = null;
            $iZip = null;
        } else {
            if ($address == $invoice) {
                $checkbox = false;
                $iAddress = null;
                $iCity = null;
                $iZip = null;
            } else {
                $checkbox = true;
                $iAddress = $invoice->getAddress();
                $iCity = $invoice->getCity();
                $iZip = $invoice->getZipCode();
            }
        }

        $form = $this->createForm(AddressType::class, $address, compact('checkbox', 'iAddress', 'iCity', 'iZip'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            if ($form->get('diff')->getData()) {
                $invoice = new Address();

                $addIn = $form->get('addressInvoice');
                $addressInvoice = $addIn->get('address_invoice')->getData();
                $cityInvoice = $addIn->get('city_invoice')->getData();
                $zipInvoice = $addIn->get('zipCode_invoice')->getData();

                $invoice->setAddress($addressInvoice);
                $invoice->setCity($cityInvoice);
                $invoice->setZipCode($zipInvoice);

                $entityManager->persist($invoice);
                $entityManager->flush();

                $reservation->getPersonalData()->setInvoice($invoice);
            } else {
                $reservation->getPersonalData()->setInvoice($address);
            }

            $reservation->getPersonalData()->setAddress($address);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_adresse', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }
}
