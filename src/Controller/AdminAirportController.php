<?php

namespace App\Controller;

use App\Entity\Airport;
use App\Form\AdminAirportType;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/airport')]
class AdminAirportController extends AbstractController
{
    #[Route('/', name: 'app_admin_airport_index')]
    public function index(AirportRepository $airportRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = 'Admin Airport';
        $airports = $airportRepository->findAll();

        $airport = new Airport();
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


    #[Route('/delete/{id}', name: 'app_admin_airport_delete')]
    public function delete(Airport $airport, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $airport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($airport);
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/edit/{id}', name: 'app_admin_airport_edit')]
    public function edit(Airport $airport, AirportRepository $airportRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = 'Admin Airport Edit';
        $button_label = 'Modifier';
        $airports = $airportRepository->findAll();

        $form = $this->createForm(AdminAirportType::class, $airport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport modifié avec succès');

            return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_airport/index.html.twig', compact('airports', 'form', 'title', 'button_label'));
    }


    #[Route('/show/{id}', name: 'app_admin_airport_show')]
    public function show(Airport $airport): Response
    {
        $title = 'Admin Airport Show';

        return $this->render('admin_airport/show.html.twig', compact('airport', 'title'));
    }


    private function region(int $valeur): string
    {
        switch ($valeur) {
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
