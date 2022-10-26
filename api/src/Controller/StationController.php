<?php

namespace App\Controller;

use App\Entity\BicingStation;
use App\Entity\RechargeStation;
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
        $rechargeStation = new RechargeStation();
        $rechargeStationClass = get_class($rechargeStation);
        var_dump(get_class($rechargeStation));
        $adapter["latitud"] = "latitude";
        foreach($json_vehicles as $item) { //foreach element in $json
            $rechargeStation = new RechargeStation();
            foreach($item as $attNameRaw => $value){
                $attName = trim($attNameRaw);
                $entityAttName = null;
                if(isset($adapter[$attName])) {
                    var_dump("\n");
                    var_dump("valor del adapter");
                    var_dump("\n");
                    var_dump($adapter[$attName]);
                    var_dump("key jsonr");
                    var_dump("  $attName ");
                    var_dump("\n");
                    var_dump($adapter[$attName]);

                    $entityAttName = $adapter[$attName];
                }
                if(property_exists($rechargeStationClass, $entityAttName)){
                    var_dump("-----------------------EXISTS----------------");
                    $rechargeStation->$entityAttName = $value;
                }
            }
            var_dump("FINAL");
            dd($rechargeStation);
            $entityManager->persist($rechargeStation);
            $entityManager->flush();

            $speed_type = null;
            $connection_type = null;
            $power = null;
            $current_type = null;
            $slots = null;

            if (false && isset($item->latitud)) {
                $latitude = (float)$item->latitud;
                if (isset($item->longitud)) {
                    $longitude = (float)$item->longitud;
                } if (isset($item->tipus_velocitat)) {
                    $speed_type = $item->tipus_velocitat;
                } if (isset($item->tipus_connexi)) {
                    $connection_type = $item->tipus_connexi;
                } if (isset($item->kw)) {
                    $power = (float)$item->kw;
                } if (isset($item->ac_dc)) {
                    $current_type = $item->ac_dc;
                } if (isset($item->adre_a)) {
                    $address = $item->adre_a;
                }
                else {
                    $address = "Not available";
                }
                if (isset($item->nplaces_estaci)) {
                    $slots = (int)$item->nplaces_estaci;
                }

                $recharge_station = new RechargeStation($latitude, $longitude, true, $address, $speed_type,
                    $connection_type, $power, $current_type, $slots);

                //persist changes to db
                $entityManager->persist($recharge_station);

                //EXECUTE THE ACTUAL QUERIES
                $entityManager->flush();
            }

        }

        return new Response();
    }
}
