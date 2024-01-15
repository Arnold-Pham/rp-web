<?php

namespace App\Controller;


use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/reservation/{code}', name: 'api_reservation')]
    public function getReservationIdByCode(ReservationRepository $repository, string $code): Response
    {
        $reservation = $repository->findOneBy(['code' => $this->clear($code)]);
        if (!$reservation) return new JsonResponse(['message' => 'Reservation not found'], Response::HTTP_NOT_FOUND);

        return new JsonResponse(['id' => $reservation->getId()]);
    }

    private function clear(string $clear): string
    {
        return htmlspecialchars(strtoupper($clear));
    }
}
