<?php

namespace App\Controller;

use App\Entity\BicingStation;
use App\Entity\RechargeStation;
use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/station_bicing', name: 'app_station')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->getRechargeStations($doctrine);
        $this->getBicingStations($doctrine);
        return new Response();
    }

    private function getBicingStations(ManagerRegistry $doctrine): void
    {
        $entityManager = $doctrine->getManager();
        //Get Json of bicing permanent
        $urlInformation = 'https://api.bsmsa.eu/ext/api/bsm/gbfs/v2/en/station_information';
        $getJsonInformation = file_get_contents($urlInformation);
        $jsonInformation = json_decode($getJsonInformation);
        $jsonInformation = (array)$jsonInformation->data;

        //Get Json of bicing temporary
        $urlStatus = 'https://api.bsmsa.eu/ext/api/bsm/gbfs/v2/en/station_status';
        $getJsonStatus = file_get_contents($urlStatus);
        $jsonStatus = json_decode($getJsonStatus);
        $jsonStatus = (array)$jsonStatus->data;

        $iterator = 0;
        foreach ($jsonInformation['stations'] as $item) { //foreach element in $json

            $bicingStation = new BicingStation();

            $latitude = $item->lat;
            $longitude = $item->lon;
            $address = $item->address;
            $capacity = $item->capacity;
            $mechanical = $jsonStatus['stations'][$iterator]->num_bikes_available_types->mechanical;
            $electrical = $jsonStatus['stations'][$iterator]->num_bikes_available_types->ebike;
            $availableSlots = $jsonStatus['stations'][$iterator]->num_docks_available;

            $bicingStation->setLatitude($latitude);
            $bicingStation->setLongitude($longitude);
            $bicingStation->setStatus(true);
            $bicingStation->setAddress($address);
            $bicingStation->setCapacity($capacity);
            $bicingStation->setMechanical($mechanical);
            $bicingStation->setElectrical($electrical);
            $bicingStation->setAvailableSlots($availableSlots);

            //persist changes to db
            $entityManager->persist($bicingStation);
            //EXECUTE THE ACTUAL QUERIES
            $entityManager->flush();

            ++$iterator;
        }
    }

    /**
     * @throws Exception
     */
    private function getRechargeStations(ManagerRegistry $doctrine): void
    {
        $entityManager = $doctrine->getManager();
        $urlVehicles = 'https://analisi.transparenciacatalunya.cat/resource/tb2m-m33b.json';
        $getJsonVehicles = file_get_contents($urlVehicles);
        $jsonVehicles = json_decode($getJsonVehicles);
        $jsonVehicles = (array)$jsonVehicles;

        $adapter["latitud"] = ["funcName" => "setLatitude", "propertyName" => "latitude"];
        $adapter["longitud"] = ["funcName" => "setLongitude", "propertyName" => "longitude"];
        $adapter["adre_a"] = ["funcName" => "setAddress", "propertyName" => "adress"];
        $adapter["tipus_velocitat"] = ["funcName" => "setSpeedType", "propertyName" => "speedType"];
        $adapter["tipus_connexi"] = ["funcName" => "setConnectionType", "propertyName" => "connectionType"];
        $adapter["kw"] = ["funcName" => "setPower", "propertyName" => "power"];
        $adapter["ac_dc"] = ["funcName" => "setCurrentType", "propertyName" => "currentType"];
        $adapter["nplaces_estaci"] = ["funcName" => "setSlots", "propertyName" => "slots"];

        foreach ($jsonVehicles as $item) { //foreach element in $json
            $rechargeStation = new RechargeStation();

            foreach ($item as $attNameRaw => $value) {
                $attName = trim($attNameRaw);

                if (isset($adapter[$attName]["propertyName"])) {

                    if (property_exists(RechargeStation::class, $adapter[$attName]["propertyName"]) || property_exists(Station::class, $adapter[$attName]["propertyName"])) {
                        $functionName = $adapter[$attName]["funcName"];

                        if ($adapter[$attName]["propertyName"] === "latitude" || $adapter[$attName]["propertyName"] === "longitude" || $adapter[$attName]["propertyName"] === "power") {
                            $value = (float)$value;
                        } else if ($adapter[$attName]["propertyName"] === "slots") {
                            $value = (int)$value;
                        }

                        try {
                            $rechargeStation->$functionName($value);
                        } catch (Exception $e) {
                            throw new Exception($e); //TODO
                        }
                    }
                }
            }

            $existsLatitude = $rechargeStation->getLatitude();
            $existsLongitude = $rechargeStation->getLongitude();
            if (isset($existsLatitude) && isset($existsLongitude)) {
                $rechargeStation->setStatus(true);
                $entityManager->persist($rechargeStation);
                $entityManager->flush();
            }
        }
    }

}

