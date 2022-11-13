<?php

namespace App\Controller;

use App\Repository\RechargeStationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteStationController extends AbstractController
{
    #[Route('/route/station', name: 'app_route_station', methods: ["GET"])]
    public function index(Request $request): Response
    {
        //dd("HOLA");
        $requestBodyAsJSON = json_decode($request->getContent(), true);
        //dd("HOLA");
        $latitudeA = $requestBodyAsJSON['latitudeA'];
        $longitudeA = $requestBodyAsJSON['longitudeA'];
        $latitudeB = $requestBodyAsJSON['latitudeB'];
        $longitudeB = $requestBodyAsJSON['longitudeB'];
        $numStations = $requestBodyAsJSON['numStations'];

        $mediumlatitude = ($latitudeA + $latitudeB)/2;
        $mediumlongitude = ($longitudeA + $longitudeB)/2;

        $RechargeStationRepository = new RechargeStationRepository();
        $RechargeStationRepository->findAll();

        dd($RechargeStationRepository);

        return new Response();
    }
}
