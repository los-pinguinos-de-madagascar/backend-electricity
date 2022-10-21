<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends AbstractController
{
    #[Route('/station', name: 'app_station')]
    public function index(): Response
    {
        $url = 'https://api.bsmsa.eu/ext/api/bsm/gbfs/v2/en/station_information';
        $get_json = file_get_contents($url);
        $json = json_decode($get_json);


        return $this->render('station/index.html.twig', [
            'controller_name' => 'StationController',
        ]);
    }
}
