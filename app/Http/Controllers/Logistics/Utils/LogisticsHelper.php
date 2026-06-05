<?php

namespace App\Http\Controllers\Logistics\Utils;

class LogisticsHelper
{
    private $curl;
    private string $googleApiKey;
    private string $matrixApiKey;

    public function __construct()
    {
        $this->googleApiKey = env('GOOGLE_MAPS_API_KEY');
        $this->matrixApiKey = env('GOOGLE_MAPS_Matrix_KEY');
    }

    public function setInit($url)
    {
        $this->curl = curl_init($url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    }

    public function setTimeout($seconds = 30)
    {
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $seconds);
    }

    public function setSSL($flag = false)
    {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, $flag ? 2 : 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $flag);
    }

    public function execute()
    {
        $response = curl_exec($this->curl);
        $error = curl_error($this->curl);
        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        curl_close($this->curl);

        return [
            'response' => $response,
            'error' => $error,
            'status' => $status
        ];
    }

    /**
     * Google Places Autocomplete
     */
    public function getCitySuggestions($search)
    {
        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?" . http_build_query([
            'input'      => $search,
            'types'      => '(cities)',
            'components' => 'country:in',
            'key'        => $this->googleApiKey
        ]);

        $this->setInit($url);
        $this->setSSL(false);
        $this->setTimeout(30);

        $result = $this->execute();

        if ($result['status'] !== 200) {
            return [
                'error' => true,
                'message' => 'Google API Error'
            ];
        }

        $data = json_decode($result['response'], true);

        return $data['predictions'] ?? [];
    }


    public function getRouteMetrics($originPlaceId, $destinationPlaceId)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?" .
            http_build_query([
                'origins'      => 'place_id:' . $originPlaceId,
                'destinations' => 'place_id:' . $destinationPlaceId,
                'units'        => 'metric',
                'key'          => $this->googleApiKey
            ]);
    
        $this->setInit($url);
        $this->setSSL(false);
        $this->setTimeout(30);
    
        $result = $this->execute();
    
        if (!empty($result['error'])) {
            throw new \Exception($result['error']);
        }
    
        $data = json_decode($result['response'], true);
    
        if (
            !isset($data['rows'][0]['elements'][0]) ||
            $data['rows'][0]['elements'][0]['status'] !== 'OK'
        ) {
            throw new \Exception('Unable to calculate route metrics.');
        }
    
        $element = $data['rows'][0]['elements'][0];
    
        return [
            'distance' => $element['distance']['text'] ?? 'N/A',
            'distance_value' => $element['distance']['value'] ?? 0, // meters
            'duration' => $element['duration']['text'] ?? 'N/A',
            'duration_value' => $element['duration']['value'] ?? 0, // seconds
        ];
    }

    public function getAlternativeRoutes($originPlaceId, $destinationPlaceId)
    {
        $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';
    
        $payload = [
            'origin' => [
                'placeId' => $originPlaceId
            ],
            'destination' => [
                'placeId' => $destinationPlaceId
            ],
            'travelMode' => 'DRIVE',
            'computeAlternativeRoutes' => true,
            'routeModifiers' => [
                'avoidTolls' => false,
                'avoidHighways' => false,
                'avoidFerries' => true
            ]
        ];
    
        $this->setInit($url);
        $this->setSSL(false);
        $this->setTimeout(30);
    
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($payload));
    
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $this->matrixApiKey,
            'X-Goog-FieldMask: routes.distanceMeters,routes.duration,routes.description,routes.polyline'
        ]);
    
        $result = $this->execute();
    
        $data = json_decode($result['response'], true);

        echo json_encode($data);
        exit;
    
        return $data['routes'] ?? [];
    }

}