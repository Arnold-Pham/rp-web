<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $title = 'Accueil';
        return $this->render('main/index.html.twig', compact('title'));
    }


    #[Route('/mentions', name: 'app_mentions')]
    public function mention(): Response
    {
        $title = 'Mentions légales';
        return $this->render('main/mentions.html.twig', compact('title'));
    }


    #[Route('/reservation', name: 'app_reservation')]
    public function reservation(): Response
    {
        $title = 'Ma réservation';
        return $this->render('main/reservation.html.twig', compact('title'));
    }


    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $title = 'Contact';
        $button_label = 'Envoyer';

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $resa = $form->get('resa')->getData();
            $text = $form->get('text')->getData();
            $sujet = $form->get('sujet')->getData();

            $this->addFlash('envoyer', $email . ' ' . $sujet . ' ' . $text . ' ' . $resa);
            /*
                $this->addFlash('envoyer', 'Email: ' . $email);
                $this->addFlash('envoyer', 'Reservation: ' . $resa);
                $this->addFlash('envoyer', 'Texte: ' . $text);
                $this->addFlash('envoyer', 'Sujet: ' . $sujet);
            */

            return $this->redirectToRoute('app_contact', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/contact.html.twig', compact('title', 'button_label', 'form'));
    }
}
