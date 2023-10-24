<?php

namespace App\Controller;

use App\Entity\Parking;
use App\Form\AdminParkingType;
use App\Form\AdminParking2Type;
use App\Repository\ParkingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/parking')]
class AdminParkingController extends AbstractController
{
    #[Route('/', name: 'app_admin_parking_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, ParkingRepository $parkingRepository): Response
    {
        $title = 'Admin Parking';
        $parkings = $parkingRepository->findAll();
        $parking = new Parking();
        $form = $this->createForm(AdminParkingType::class, $parking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parking->setAvailable(TRUE);
            $entityManager->persist($parking);
            $entityManager->flush();
            $this->addFlash('crud', 'Parking ajouté avec succès');

            return $this->redirectToRoute('app_admin_parking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_parking/index.html.twig', compact('parkings', 'form', 'title'));
    }


    #[Route('/delete/{id}', name: 'app_admin_parking_delete')]
    public function delete(Request $request, Parking $parking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $parking->getId(), $request->request->get('_token'))) {
            $entityManager->remove($parking);
            $entityManager->flush();
            $this->addFlash('error', 'Parking supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_parking_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/edit/{id}', name: 'app_admin_parking_edit')]
    public function edit(Request $request, Parking $parking, EntityManagerInterface $entityManager, ParkingRepository $parkingRepository): Response
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
}
