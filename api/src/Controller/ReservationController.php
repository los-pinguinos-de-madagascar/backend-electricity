<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\RechargeStationRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Request $request, RechargeStationRepository $rechargeStationRepository, EntityManagerInterface $entityManager ,SerializerInterface $serializer,ReservationRepository $reservationRepository): JsonResponse
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
        $response->headers->set('Content-Type', 'application/json');

        $station = $rechargeStationRepository->find($idStation);
        if ($station == null) {
            $returnMessage = json_encode(["message"=>"Recharge station does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        if ($dataIni >= $dataFi) {
            $returnMessage = json_encode(["message"=>"Wrong input, dataFi should be later than dataIni"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(403);
            return $response;
        }

        $diff = $dataIni->diff($dataFi);
        $hours = $diff->h;
        $minutes = $diff->i;

        if ($hours > 3 || ($hours === 3 && $minutes !== 0)) {
            $returnMessage = json_encode(["message"=>"The maximum time for a reservation is 3 hours"]);
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
                    $responseArray['message'] = "There are not available slots for this date-time to reserve";
                    $responseArray['data'] = $finalReservations;
                    $response->setContent($serializer->serialize($responseArray,'json'));
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

        $entityManager->persist($newReservation);
        $entityManager->flush();

        $response->setContent($serializer->serialize($newReservation,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    private function sameSpot(\App\Entity\Reservation $reservation, mixed $dataIni, mixed $dataFi)
    {
        $dataIniRes = $reservation->getDataIni();
        $dataFiRes = $reservation->getDataFi();
        if ((($dataIni <= $dataIniRes) && ($dataIniRes < $dataFi)) || (($dataIni < $dataFiRes) && ($dataFiRes <= $dataFi))) return true;
        return false;
    }

    #[Route('/reservations/actualization', name: 'update_reservation', methods: 'DELETE')]
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
