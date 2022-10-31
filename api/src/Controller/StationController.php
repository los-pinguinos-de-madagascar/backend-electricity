<?php

namespace App\Controller;

use App\Entity\BicingStation;
use App\Entity\RechargeStation;
use App\Entity\Station;
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
        /*
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
        */
        $url_vehicles = 'https://analisi.transparenciacatalunya.cat/resource/tb2m-m33b.json';
        $get_json_vehicles = file_get_contents($url_vehicles);
        $json_vehicles = json_decode($get_json_vehicles);
        $json_vehicles = (array)$json_vehicles;


        $adapter["latitud"] = ["funcName" => "setLatitude", "propertyName" => "latitude"];
        $adapter["longitud"] = ["funcName" => "setLongitude", "propertyName" => "longitude"];
        $adapter["adre_a"] = ["funcName" => "setAddress", "propertyName" => "adress"];
        $adapter["tipus_velocitat"] = ["funcName" => "setSpeedType", "propertyName" => "speedType"];
        $adapter["tipus_connexi"] = ["funcName" => "setConnectionType", "propertyName" => "connectionType"];
        $adapter["kw"] = ["funcName" => "setPower", "propertyName" => "power"];
        $adapter["ac_dc"] = ["funcName" => "setCurrentType", "propertyName" => "currentType"];
        $adapter["nplaces_estaci"] = ["funcName" => "setSlots", "propertyName" => "slots"];


        foreach($json_vehicles as $item) { //foreach element in $json
            $rechargeStation = new RechargeStation();

            foreach($item as $attNameRaw => $value){
                $attName = trim($attNameRaw);

                if(isset($adapter[$attName]["propertyName"])) {
                    var_dump($value);

                    if(property_exists(RechargeStation::class, $adapter[$attName]["propertyName"]) || property_exists(Station::class, $adapter[$attName]["propertyName"])){
                        $function_name = $adapter[$attName]["funcName"];
                        $rechargeStation->$function_name($value);
                    }
                }
            }

            $exists_latitude = $rechargeStation->getLatitude();
            $exists_longitude = $rechargeStation->getLongitude();
            if (isset($exists_latitude) && isset($exists_longitude)){
                $rechargeStation->setStatus(true);

                $entityManager->persist($rechargeStation);
                $entityManager->flush();
            }
        }

        return new Response();
    }
}
