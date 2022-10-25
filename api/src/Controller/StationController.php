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
        $url_information = 'https://api.bsmsa.eu/ext/api/bsm/gbfs/v2/en/station_information';
        $get_json_information = file_get_contents($url_information);
        $json_information = json_decode($get_json_information);
        $json_information = (array)$json_information->data;

        //Get Json of bicing temporary
        $url_status = 'https://api.bsmsa.eu/ext/api/bsm/gbfs/v2/en/station_status';
        $get_json_status = file_get_contents($url_status);
        $json_status = json_decode($get_json_status);
        $json_status = (array)$json_status->data;

        $iterator = 0;
        foreach($json_information['stations'] as $item) { //foreach element in $json
            $id_relation = $item->station_id;
            $latitude = $item->lat;
            $longitude = $item->lon;
            $adress = $item->address;
            $capacity = $item->capacity;
            $mechanical = $json_status['stations'][$iterator]->num_bikes_available_types->mechanical;
            $electrical = $json_status['stations'][$iterator]->num_bikes_available_types->ebike;
            $availableSlots = $json_status['stations'][$iterator]->num_docks_available;

            $station = new BicingStation($latitude,$longitude,true,$adress,$capacity,$mechanical,$electrical,$availableSlots);

            //persist changes to db
            $entityManager->persist($station);
            //EXECUTE THE ACTUAL QUERIES
            $entityManager->flush();

            ++$iterator;
        }
/*
        $url_vehicles = 'https://analisi.transparenciacatalunya.cat/resource/tb2m-m33b.json';
        $get_json_vehicles = file_get_contents($url_vehicles);
        $json_vehicles = json_decode($get_json_vehicles);
        $json_vehicles = (array)$json_vehicles;

        foreach($json_vehicles as $item) { //foreach element in $json

            $tipus_velocitat = $item->tipus_velocitat;
            $tipus_connexio = $item->tipus_connexi;
            $latitud = $item->latitud;
            $longitud = $item->longitud;
            $potencia = $item->kw;
            $tipus_corrent = $item->ac_dc;
            $address = $item->adre_a;
            $places = $item->nplaces_estaci;

          //  $estacio_recarrega = new RechargeStation($tipus_velocitat,$tipus_connexio,$latitud,$longitud,
                    //$potencia,$tipus_corrent,$address,$places);

            //persist changes to db
            //$entityManager->persist($estacio_recarrega);

            //EXECUTE THE ACTUAL QUERIES
            //$entityManager->flush();


        }
*/
        return new Response();
    }
}
