<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\RechargeStationRepository;
use App\Repository\ReservationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReservationController extends AbstractController
{
    #[Route('/reservations', name: 'create_reservation', methods: 'POST')]
    public function index(Request $request, RechargeStationRepository $rechargeStationRepository, SerializerInterface $serializer,ReservationRepository $reservationRepository): JsonResponse
    {
        $requestBodyAsJSON = json_decode($request->getContent(), true);

        $di = $requestBodyAsJSON['dataIni'];
        $df = $requestBodyAsJSON['dataFi'];

        $format = 'Y-m-d H:i:s';
        $dataIni = DateTimeImmutable::createFromFormat($format, $di);
        $dataFi = DateTimeImmutable::createFromFormat($format, $df);

        $idStation = $requestBodyAsJSON['idStation'];
        $user = $this->getUser();

        $response = new JsonResponse();

        $station = $rechargeStationRepository->find($idStation);
        if ($station == null) {
            $returnMessage = json_encode(["message"=>"Recharge station does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        else if ($dataIni >= $dataFi) {
            $returnMessage = json_encode(["message"=>"Wrong dates input"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(403);
            return $response;
        }

        $reservations = $reservationRepository->findBy(array('rechargeStation' => $idStation));
        $count = 0;
        $finalReservations = array();
        $slots = $station->getSlots();
        foreach ($reservations as $reservation){
            if ($this->sameSpot($reservation, $dataIni, $dataFi)) {
                $count++;
                $finalReservations[] = $reservation;
                if($count === $slots || (($slots === null) && ($count === 1))) {
//                    ["message" => "There are not availale slots for this date-time to reserve"]
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setContent($serializer->serialize($finalReservations,'json'));
                    $response->setStatusCode(403);
                    return $response;
                }
            }
        }
        $newReservation = new Reservation();
        $newReservation->setDataIni($dataIni);
        $newReservation->setDataFi($dataFi);
        $newReservation->setRechargeStation($station);
        $newReservation->setUserReservation($user);

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($serializer->serialize($newReservation,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    private function sameSpot(\App\Entity\Reservation $reservation, mixed $dataIni, mixed $dataFi)
    {
        $dataIniRes = $reservation->getDataIni();
        $dataFiRes = $reservation->getDataFi();
        if ((($dataIni <= $dataIniRes) && ($dataIniRes < $dataFi)) || (($dataIni <= $dataFiRes) && ($dataFiRes < $dataFi))) return true;
        return false;
    }

    #[Route('/reservations/update', name: 'update_reservation', methods: 'DELETE')]
    public function actualitzaDB(ReservationRepository $reservationRepository, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $reservations = $reservationRepository->findAll();

        $at = date('Y-m-d H:i:s');
        $format = 'Y-m-d H:i:s';
        $actualTime = DateTimeImmutable::createFromFormat($format, $at);
        foreach ($reservations as $reservation) {
            if ($reservation->getDataFi() < $actualTime) {
                $reservationRepository->remove($reservation);
                $entityManager->flush();
            }
        }
    }
}
