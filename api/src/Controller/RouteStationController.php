<?php

namespace App\Controller;

use App\Helpers\RouteAlgorithm;
use App\Repository\RechargeStationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteStationController extends AbstractController
{
    #[Route('/route/station', name: 'app_route_station', methods: ["GET"])]
    public function index(RechargeStationRepository $rechargeStationRepository, Request $request , RouteAlgorithm $routeAlgorithm): Response
    {

        $requestBodyAsJSON = json_decode($request->getContent(), true);

        $latitudeA = $requestBodyAsJSON['latitudeA'];
        $longitudeA = $requestBodyAsJSON['longitudeA'];
        $latitudeB = $requestBodyAsJSON['latitudeB'];
        $longitudeB = $requestBodyAsJSON['longitudeB'];
        $numStations = $requestBodyAsJSON['numStations'];


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if (!($numStations >= 1 && $numStations <= 3)) {
            $returnMessage = json_encode(["message"=>"numStations should be 1, 2 or 3"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(412);
            return $response;
        }

        $rechargeStations = $rechargeStationRepository->findAll();

        $arrayStops = $routeAlgorithm->getArrayStops($numStations,$latitudeA,$longitudeA,$latitudeB, $longitudeB,$rechargeStations);

        $presentation =  json_encode($arrayStops, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $response = new Response($presentation);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
