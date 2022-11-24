<?php

namespace App\Helpers;

class
RouteAlgorithm
{
    public function getArrayStops($numStations, $latitudeA,$longitudeA, $latitudeB, $longitudeB, $rechargeStations): array
    {
        $arrayStops = array();
        $paramlatitude = 0.0;
        $paramlongitude = 0.0;

        for ($i = 0; $i < $numStations; $i++){
            if ($numStations === 1){

                $mediumlatitude = ($latitudeA + $latitudeB)/2;
                $mediumlongitude = ($longitudeA + $longitudeB)/2;

                $paramlatitude = $mediumlatitude;
                $paramlongitude = $mediumlongitude;
            }

            else if ($numStations === 2){

                if ($i === 0){

                    $num1 = $latitudeA + ((1/2)*$latitudeB);
                    $num2 = $longitudeA + ((1/2)*$longitudeB);
                    $den = 1 + (1/2);

                    $paramlatitude = $num1/$den;
                    $paramlongitude = $num2/$den;

                }
                else if ($i === 1){

                    $num1 = $latitudeA + (2*$latitudeB);
                    $num2 = $longitudeA + (2*$longitudeB);
                    $den = 1 + 2;

                    $paramlatitude = $num1/$den;
                    $paramlongitude = $num2/$den;
                }
            }

            else if($numStations === 3){

                $mediumlatitude = ($latitudeA + $latitudeB)/2;
                $mediumlongitude = ($longitudeA + $longitudeB)/2;

                if ($i === 0){
                    $paramlatitude = ($latitudeA + $mediumlatitude)/2;
                    $paramlongitude = ($longitudeA + $mediumlongitude)/2;
                }

                else if ($i === 1){
                    $paramlatitude = $mediumlatitude;
                    $paramlongitude = $mediumlongitude;
                }

                else if ($i === 2){

                    $paramlatitude = ($latitudeB + $mediumlatitude)/2;
                    $paramlongitude = ($longitudeB + $mediumlongitude)/2;


                }
            }

            $pairLatLon = $this->getBestRechargeStation($paramlatitude,$paramlongitude,$rechargeStations,$arrayStops);
            $arrayStops[] = $pairLatLon;
        }

        return $arrayStops;
    }

    private function getBestRechargeStation($latitudePoint, $longitudePoint, $rechargeStations, $arrayStops): array
    {
        $first = true;
        $bestLatitude = 0.0;
        $bestLongitude = 0.0;


        foreach ($rechargeStations as $station){

            $actLatitude = $station->getLatitude();
            $actLongitude = $station->getLongitude();

            $pow1 = ($actLatitude - $latitudePoint) ** 2;
            $pow2 = ($actLongitude - $longitudePoint) ** 2;
            $distance = sqrt($pow1 + $pow2);

            if ($first) {
                $bestDistance = $distance;
                $bestLatitude = $actLatitude;
                $bestLongitude = $actLongitude;
                $first = false;
            }

            else {
                if ($distance < $bestDistance && $this->puntNoRepetit($arrayStops, $actLatitude, $actLongitude)){
                    $bestDistance = $distance;
                    $bestLatitude = $actLatitude;
                    $bestLongitude = $actLongitude;
                }
            }
        }

        $pairLatLon["latitude"] = $bestLatitude;
        $pairLatLon["longitude"] = $bestLongitude;

        return $pairLatLon;
    }

    private function puntNoRepetit($arrayStops, mixed $bestLatitude, mixed $bestLongitude)
    {
        $pairLatLon["latitude"] = $bestLatitude;
        $pairLatLon["longitude"] = $bestLongitude;
        return (!in_array($pairLatLon, $arrayStops));
    }

}
