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

    #[Route('/reservation/{code}', name: 'api_reservation', methods: ['GET'])]
    public function getResaDetailsByCode(ReservationRepository $repository, string $code): Response
    {
        $cleanedCode = $this->clear($code);

        if (strlen($cleanedCode) !== 16) {
            return new JsonResponse([
                'success' => false,
                'status' => 403,
                'message' => 'Invalid code length'
            ], Response::HTTP_FORBIDDEN);
        }

        $reservation = $repository->findOneBy(['code' => $cleanedCode]);

        if ($reservation === null) {
            return new JsonResponse([
                'success' => false,
                'status' => 404,
                'message' => 'Reservation not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $reservationDetails = [
            'id' => $reservation->getId(),
            'firstName' => $reservation->getPersonalData()->getFirstname(),
            'status' => $reservation->getStatus(),
            'dateA' => $reservation->getDateA(),
            'flightA' => $reservation->getFlightA(),
            'dateB' => $reservation->getDateB(),
            'flightB' => $reservation->getFlightB(),
            'valet' => $reservation->getOption() ? $reservation->getOption()->getExtra() : null,
            'parking' => $reservation->getParking() ? $reservation->getParking()->getName() : null,
            'place' => $reservation->getPlace() ? $reservation->getPlace()->getLabel() : null,
        ];

        return new JsonResponse([
            'success' => true,
            'status' => 200,
            'reservation' => $reservationDetails
        ], Response::HTTP_OK);
    }
}
