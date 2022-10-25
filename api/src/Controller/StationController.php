<?php

namespace App\Controller;

use App\Entity\BicingStation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends AbstractController
{
    #[Route('/station_bicing', name: 'app_station')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        //Get Json of bicing permanent
        $url1 = 'https://api.bsmsa.eu/ext/api/bsm/gbfs/v2/en/station_information';
        $get_json1 = file_get_contents($url1);
        $json1 = json_decode($get_json1);
        $json1 = (array)$json1->data;

        //dd($json['stations'][0]->lat);

        //Get Json of bicing temporary
        $url2 = 'https://api.bsmsa.eu/ext/api/bsm/gbfs/v2/en/station_status';
        $get_json2 = file_get_contents($url2);
        $json2 = json_decode($get_json2);
        $json2 = (array)$json2->data;
        //dd($json2);

        $iterator = 0;
        foreach($json1['stations'] as $item) { //foreach element in $json
            $id_relation = $item->station_id;
            $latitude = $item->lat;
            $longitude = $item->lon;
            $adress = $item->address;
            $capacity = $item->capacity;
            $mechanical = $json2['stations'][$iterator]->num_bikes_available_types->mechanical;
            $electrical = $json2['stations'][$iterator]->num_bikes_available_types->ebike;
            $availableSlots = $json2['stations'][$iterator]->num_docks_available;


            $station = new BicingStation($latitude,$longitude,true,$adress,$capacity,$mechanical,$electrical,$availableSlots);
            //dd($station);

            //persist changes to db
            $entityManager->persist($station);
            //EXECUTE THE ACTUAL QUERIES
            $entityManager->flush();

            ++$iterator;
        }






     /*   return $this->render('station/index.html.twig', [
            'controller_name' => 'StationController',
        ]);*/

        return new Response();
    }
}
