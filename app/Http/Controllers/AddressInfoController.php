<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class AddressInfoController extends Controller
{
    // Postcode JSON Service API Url
    private $postcodesApiUrl = "https://api.postcodes.io";
    // Google Cloud Platform API Key
    private $googleApiUrl = "https://maps.googleapis.com/maps/api/distancematrix/json";
    // Variables
    private $origin;
    private $destination;
    private $distanceInfo;
    public $distance = 0;
    public $duration = 0;

    public function __construct($originPostCode, $destinationPostCode)
    {
        // Get Latitude & Longitude informations by postcode
        $originAddress = self::getPostCodeInfo($originPostCode);
        $destinationAddress = self::getPostCodeInfo($destinationPostCode);

        // Prepare variables for getting distance & duration informations from Google Distance API
        $this->origin = implode(',', [$originAddress["latitude"], $originAddress["longitude"]]);
        $this->destination = implode(',', [$destinationAddress["latitude"], $destinationAddress["longitude"]]);

        // Get Distance information from Google API by origin & destination
        self::getDistanceInfo();
    }

    public function getPostCodeInfo($postcode) {
        return Http::get($this->postcodesApiUrl . '/postcodes/' . $postcode)["result"];
    }

    /**
     * @param $origin string
     * @param $destination string
     * @throws \Exception
     * @return mixed
     */
    public function getDistanceInfo() {
        $this->distanceInfo = Http::get($this->googleApiUrl, [
            'origins'      => $this->origin,
            'destinations' => $this->destination,
            'key'          => env('GOOGLE_MAPS_DISTANCE_API_KEY'),
            'random'       => random_int(1, 100)
        ])->json();

        if (isset($this->distanceInfo["error_message"])) {
            return ApiResponses::send(401, "Please provide an API key for Google Distance Matrix API.");
        }

        $this->distance = $this->distanceInfo["rows"][0]["elements"][0]["distance"];
        $this->duration = $this->distanceInfo["rows"][0]["elements"][0]["duration"];

        return true;
    }
}
