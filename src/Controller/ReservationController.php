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
use App\Form\AddressInvoiceType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReservationController extends AbstractController
{
    private function codeCookie(Request $request): string
    {
        if (isset($_COOKIE['road'])) {
            $code_road = $_COOKIE['road'];
            unset($_COOKIE['road']);
        } else {
            $code_road = hash('xxh64', uniqid());
        }

        setcookie('road', $code_road, time() + (60 * 60 * 24 * 3), '/', $request->getHost(), true);
        return $code_road;
    }

    #[Route('/verif', name: 'verif')]
    public function verif(Request $request, ReservationRepository $reservationRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('code', TextType::class, [
                'label' => 'Code de réservation',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Vérifier',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();

            $reservation = $reservationRepository->findOneBy(['code' => $code, 'status' => 'Payé']);

            if ($reservation instanceof Reservation) {
                return $this->redirectToRoute('resa', ['code' => $code], Response::HTTP_FOUND);
            } else {
                $this->addFlash('erreur', 'Code de réservation incorrect.');
                return $this->redirectToRoute('verif');
            }
        }

        // faire la route
        return $this->render('main/index.html.twig', compact('title', 'form'));
    }

    #[Route('/reservation/{code}', name: 'resa')]
    public function recap(string $code, ReservationRepository $reservationRepository): Response
    {
        $title = 'Récapitulatif';

        $code = $reservationRepository->findOneBy(['code' => $code, 'status' => 'Payé']);
        if ($code === null) return $this->redirectToRoute('choix', [], Response::HTTP_SEE_OTHER);

        return $this->render('main/index.html.twig', compact('title'));
    }


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

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }


    #[Route('/information', name: 'app_infos_persos')]
    public function infos(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $code_road = $this->codeCookie($request);

        $title = 'Information Personnel';
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

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
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

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
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

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
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

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }

    #[Route('/youhou', name: 'yeah')]
    public function yeah(): Response
    {
        $title = 'Yeah ça a redirigé';

        return $this->render('main/index.html.twig', compact('title'));
    }
}
