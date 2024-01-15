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
    //§ ---------------------------------------------------------------------------------------------------------------
    //! ================================================== FONCTIONS ==================================================
    //§ ---------------------------------------------------------------------------------------------------------------

    private function clear(string $clear): string
    {
        return htmlspecialchars(strtoupper($clear));
    }


    //§ ---------------------------------------------------------------------------------------------------------------
    //! =================================================== APPELS ====================================================
    //§ ---------------------------------------------------------------------------------------------------------------

    #[Route('/api/reservation/{code}', name: 'api_reservation', methods: ['GET'])]
    public function getReservationIdByCode(ReservationRepository $repository, string $code): Response
    {
        if (strlen($this->clear($code)) !== 16)
            return new JsonResponse([
                'success' => false,
                'error_code' => 403,
                'message' => 'Invalid code length'
            ], Response::HTTP_FORBIDDEN);

        $reservation = $repository->findOneBy(['code' => $this->clear($code)]);

        if ($reservation === null)
            return new JsonResponse([
                'success' => false,
                'error_code' => 404,
                'message' => 'Reservation not found'
            ], Response::HTTP_NOT_FOUND);

        return new JsonResponse([
            'success' => true,
            'error_code' => 200,
            'id' => $reservation->getId()
        ], Response::HTTP_OK);
    }
}
