<?php

namespace App\Controller;

use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test/option')]
class TestOptionController extends AbstractController
{
    #[Route('/', name: 'app_test_option_index', methods: ['GET'])]
    public function index(OptionRepository $optionRepository): Response
    {
        return $this->render('test_option/index.html.twig', [
            'options' => $optionRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_test_option_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $id, OptionRepository $optionRepository): Response
    {
        $option = $optionRepository->findBy(['id' => $id]);

        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $entityManager->persist($option);
            $entityManager->flush();

            return $this->redirectToRoute('app_test_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('test_option/new.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_test_option_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_test_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('test_option/edit.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_test_option_delete', methods: ['POST'])]
    public function delete(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $option->getId(), $request->request->get('_token'))) {
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_test_option_index', [], Response::HTTP_SEE_OTHER);
    }
}
