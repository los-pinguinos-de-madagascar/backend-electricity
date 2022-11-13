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

        //dd($latitudeA,$longitudeA,$latitudeB,$longitudeB,$numStations);

        //$rechargeStationRepository = new RechargeStationRepository();
        $rechargeStations = $rechargeStationRepository->findAll();
        //dd($rechargeStations);

        //$routeAlgorithm = new RouteAlgorithm();
        $arrayStops = $routeAlgorithm->getArrayStops($numStations,$latitudeA,$longitudeA,$latitudeB, $longitudeB,$rechargeStations);

        $presentation =  json_encode($arrayStops, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $response = new Response($presentation);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
