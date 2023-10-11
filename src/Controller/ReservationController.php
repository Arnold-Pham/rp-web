<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Option;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $title = 'Réservation';
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setDateC(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $reservation->setCode(md5(uniqid()));
            $reservation->setStatus('En cours');

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

            return $this->redirectToRoute('yeah', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/index.html.twig', compact('title', 'form'));
    }

    #[Route('/youhou', name: 'yeah')]
    public function youhou(): Response
    {
        $title = 'Yeah';

        return $this->render('reservation/index.html.twig', compact('title'));
    }
}
