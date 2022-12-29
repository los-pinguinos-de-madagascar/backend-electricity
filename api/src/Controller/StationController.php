<?php

namespace App\Controller;

use App\Entity\BicingStation;
use App\Entity\RechargeStation;
use App\Entity\Station;
use App\Helpers\RouteAlgorithm;
use App\Repository\BicingStationRepository;
use App\Repository\RechargeStationRepository;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Exception;
use PhpParser\Node\Expr\Empty_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/stations', name: 'refresh_stations')]
    public function index(ManagerRegistry $doctrine, BicingStationRepository $bicingStationRepository, RechargeStationRepository $rechargeStationRepository): Response
    {
        $this->getRechargeStations($doctrine,$rechargeStationRepository);
        $this->getBicingStations($doctrine, $bicingStationRepository);
        return new Response();
    }

    private function getBicingStations(ManagerRegistry $doctrine, BicingStationRepository $bicingStationRepository): void
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

        $bicingStations = $bicingStationRepository->findAll();

        if (empty($bicingStations)){

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

        else{

            $iterator = 0;
            foreach ($jsonInformation['stations'] as $item) { //foreach element in $json

                $latitude = $item->lat;
                $longitude = $item->lon;

                $bicingStation = $bicingStationRepository->findOneBy([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);

                $address = $item->address;
                $capacity = $item->capacity;
                $mechanical = $jsonStatus['stations'][$iterator]->num_bikes_available_types->mechanical;
                $electrical = $jsonStatus['stations'][$iterator]->num_bikes_available_types->ebike;
                $availableSlots = $jsonStatus['stations'][$iterator]->num_docks_available;

                if ($bicingStation !== null){

                    if($bicingStation->getAddress() !== $address) $bicingStation->setAddress($address);
                    if($bicingStation->getCapacity() !== $capacity) $bicingStation->setCapacity($capacity);
                    if($bicingStation->getMechanical() !== $mechanical) $bicingStation->setMechanical($mechanical);
                    if($bicingStation->getElectrical() !== $electrical) $bicingStation->setElectrical($electrical);
                    if($bicingStation->getAvailableSlots() !== $availableSlots) $bicingStation->setAvailableSlots($availableSlots);


                    //EXECUTE THE ACTUAL QUERIES
                    $entityManager->flush();
                }

                else {
                    $bicingStation = new BicingStation();

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

                }

                ++$iterator;
            }

        }

    }

    /**
     * @throws Exception
     */
    private function getRechargeStations(ManagerRegistry $doctrine, RechargeStationRepository $rechargeStationRepository): void
    {
        $entityManager = $doctrine->getManager();
        $urlRechargeStations = 'https://analisi.transparenciacatalunya.cat/resource/tb2m-m33b.json';
        $getJsonRechargeStations = file_get_contents($urlRechargeStations);
        $jsonRechargeStation = json_decode($getJsonRechargeStations);
        $jsonRechargeStation = (array)$jsonRechargeStation;

        $adapter["latitud"] = ["funcName" => "setLatitude", "propertyName" => "latitude"];
        $adapter["longitud"] = ["funcName" => "setLongitude", "propertyName" => "longitude"];
        $adapter["adre_a"] = ["funcName" => "setAddress", "propertyName" => "adress"];
        $adapter["tipus_velocitat"] = ["funcName" => "setSpeedType", "propertyName" => "speedType"];
        $adapter["tipus_connexi"] = ["funcName" => "setConnectionType", "propertyName" => "connectionType"];
        $adapter["kw"] = ["funcName" => "setPower", "propertyName" => "power"];
        $adapter["ac_dc"] = ["funcName" => "setCurrentType", "propertyName" => "currentType"];
        $adapter["nplaces_estaci"] = ["funcName" => "setSlots", "propertyName" => "slots"];

        $rechargeStations = $rechargeStationRepository->findAll();
        if (empty($rechargeStations)){
            foreach ($jsonRechargeStation as $item) { //foreach element in $json
                $this->iterationsThroughJson($item, $entityManager, $adapter);
            }
        }

        else{
            foreach ($jsonRechargeStation as $item) { //foreach element in $json
                $latStr = $item->latitud;
                $lonStr = $item->longitud;

                $latitude = (float)$latStr;
                $longitude = (float)$lonStr;

                $rechargeStation= $rechargeStationRepository->findOneBy([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);

                if ($rechargeStation !== null){
                    foreach ($item as $attNameRaw => $value) {
                        $attName = trim($attNameRaw);
                        if (isset($adapter[$attName]["propertyName"])) {
                            if (property_exists(RechargeStation::class, $adapter[$attName]["propertyName"]) || property_exists(Station::class, $adapter[$attName]["propertyName"])) {
                                $functionName = $adapter[$attName]["funcName"];

                                if ($adapter[$attName]["propertyName"] === "power") $value = (float)$value;
                                else if ($adapter[$attName]["propertyName"] === "slots") $value = (int)$value;

                                try {
                                    $rechargeStation->$functionName($value);
                                } catch (Exception $e) {
                                    throw new Exception($e);
                                }
                            }
                        }
                    }

                    $existsLatitude = $rechargeStation->getLatitude();
                    $existsLongitude = $rechargeStation->getLongitude();
                    if (isset($existsLatitude) && isset($existsLongitude)) {
                        $entityManager->flush();
                    }
                }

                else{
                    $this->iterationsThroughJson($item, $entityManager, $adapter);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    private function iterationsThroughJson($item, $entityManager, $adapter): void
    {
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
                        throw new Exception($e);
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

    #[Route('/potusInformationRecharge', name: 'refresh_potusInfo_recharge', methods: ["PUT"])]
    public function updatePotusInfoRecharge(ManagerRegistry $doctrine, BicingStationRepository $bicingStationRepository, RechargeStationRepository $rechargeStationRepository): JsonResponse
    {
        $response = new JsonResponse();

        $rechargeStations = $rechargeStationRepository->findAll();

        if (!empty($rechargeStations)){
            foreach ($rechargeStations as $station){

                $lat = $station->getLatitude();
                $lon = $station->getLongitude();

                $latitudestr = strval($lat);
                $lengthstr = strval($lon);
                $boolpolution = false;
                $boolgases = false;

                $potusInformation = $this->getPotusInformation($latitudestr,$lengthstr);

                foreach($potusInformation as $key=>$value){
                    if ($key === 0){
                        $polution = $value;
                        $boolpolution = true;
                    }

                    else if ($key === 1){
                        $dangerousGases = $value;
                        $boolgases = true;
                    }
                }
                if ($boolpolution) $station->setPolution($polution);
                if ($boolgases) $station->setDangerousGases($dangerousGases);
            }
        }

        else {
            $returnMessage = json_encode(["message"=>"The DB does not have recharge stations"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(503);
            return $response;
        }

        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        $returnMessage = json_encode(["message"=>"The DB has been updated"]);
        $response->setContent($returnMessage);
        $response->setStatusCode(200);

        return $response;
    }

    #[Route('/potusInformationBicing', name: 'refresh_potusInfo_bicing', methods: ["PUT"])]
    public function updatePotusInfoBicing(ManagerRegistry $doctrine, BicingStationRepository $bicingStationRepository, RechargeStationRepository $rechargeStationRepository): JsonResponse
    {
        $response = new JsonResponse();

        $bicingStations = $bicingStationRepository->findAll();

        if (!empty($bicingStations)){
            foreach ($bicingStations as $station){

                $lat = $station->getLatitude();
                $lon = $station->getLongitude();

                $latitudestr = strval($lat);
                $lengthstr = strval($lon);
                $boolpolution = false;
                $boolgases = false;

                $potusInformation = $this->getPotusInformation($latitudestr,$lengthstr);

                foreach($potusInformation as $key=>$value){
                    if ($key === 0){
                        $polution = $value;
                        $boolpolution = true;
                    }

                    else if ($key === 1){
                        $dangerousGases = $value;
                        $boolgases = true;
                    }
                }
                if ($boolpolution) $station->setPolution($polution);
                if ($boolgases) $station->setDangerousGases($dangerousGases);
            }
        }

        else {
            $returnMessage = json_encode(["message"=>"The DB does not have bicing stations"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(503);
            return $response;
        }

        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        $returnMessage = json_encode(["message"=>"The DB has been updated"]);
        $response->setContent($returnMessage);
        $response->setStatusCode(200);

        return $response;
    }
    private function getPotusInformation($latitude, $length)
    {
        // Potus Information
        $url = 'https://potusback-production-b295.up.railway.app/api/external/airquality/region';
        $data = array(
            'latitude' => $latitude,
            'length' => $length,
        );

        //var_dump($data);
        $final = $url . "?" . http_build_query($data);
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header'=>"Authorization: token BD2GNpAHy0pQbpXvGyLoaSEYxSghpMKRBx79X3K4Q7DUQNJloggzmi3yGqiEVP084eY1yXN8a073dEdTb5UNUusL5thCqflqCJJRHYicf2bjVaKd7vI9EpQTEpxJ2HmWxXlidTiYkqK1icwVw1jG4LxuV4d359rqY149ArkOb2om1PVVyrI1qdt28o3Ps3hfIZp8L7rzNoLT10kL2cEDGo4HLawTfnmEOoJmravYrGCuSUcnrhP4DWZ9\r\n"
            )
        );

        $context = stream_context_create($options);
        $file = file_get_contents($final, false, $context);
        $jsonInformation = json_decode($file);

        //var_dump($jsonInformation);
        $gases = $jsonInformation->registry;

        $levelDanger = 0;
        $lowDanger = false;
        $moderateDanger = false;
        $dangerousGases = array();

        foreach ($gases as $gas){
            if ($gas->dangerLevel === "Low"){
                $lowDanger = true;

                if ($gas->value !== null){
                    $gasInfo[$gas->name] = ["dangerLevel" => "Low" , "value" => $gas->value];
                }
                else{
                    $gasInfo[$gas->name] = ["dangerLevel" => $gas->dangerLevel];
                }

                $dangerousGases[] = $gasInfo[$gas->name];
            }
            else if ($gas->dangerLevel === "Moderate"){
                $moderateDanger = true;

                if ($gas->value !== null){
                    $gasInfo[$gas->name] = ["dangerLevel" => "Moderate" , "value" => $gas->value];
                }
                else {
                    $gasInfo[$gas->name] = ["dangerLevel" => $gas->dangerLevel];
                }

                $dangerousGases[] = $gasInfo[$gas->name];
            }
        }

        if ($moderateDanger) $levelDanger = 2;
        else if ($lowDanger) $levelDanger = 1;

        $potusInformation[] = $levelDanger;
        $potusInformation[] = $dangerousGases;

        return $potusInformation;
    }
}

