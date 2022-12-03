<?php

namespace App\Controller;

use App\Repository\RechargeStationRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class ReservationController extends AbstractController
{
    #[Route('/reservations', name: 'create_reservation', methods: 'POST')]
    public function index(Request $request, RechargeStationRepository $rechargeStationRepository, ReservationRepository $reservationRepository): JsonResponse
    {
        $requestBodyAsJSON = json_decode($request->getContent(), true);

        $di = $requestBodyAsJSON['dataIni'];
        $df = $requestBodyAsJSON['dataFi'];
        $dataIni = date("d-m-Y H:i:s", strtotime($di));
        $dataFi = date("d-m-Y H:i:s", strtotime($df));
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
        foreach ($reservations as $reservation){
            if ($this->sameSpot($reservation, $dataIni, $dataFi)) {
                $returnMessage = json_encode(["message"=>"BicingStation does not exist"],$reservation);
                $response->setContent($returnMessage);
                $response->setStatusCode(403);
                return $response;
            }
        }
        dd("hola");
    }

    private function sameSpot(\App\Entity\Reservation $reservation, mixed $dataIni, mixed $dataFi)
    {
        $dataIniRes = $reservation->getDataIni();
        $dataFiRes = $reservation->getDataFi();
        dd($dataIni,$dataIniRes,($dataIni <= $dataIniRes),($dataIniRes < $dataFi));
        if ((($dataIni <= $dataIniRes) && ($dataIniRes < $dataFi)) || (($dataIni <= $dataFiRes) && ($dataFiRes < $dataFi))) return true;
        return false;
    }
}
