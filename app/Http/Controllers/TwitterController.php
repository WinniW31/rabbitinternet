<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwitterController extends Controller
{
    function __construct() {

		//Twitter Bearer Token
		$this->bearer = 'Bearer AAAAAAAAAAAAAAAAAAAAANk%2FgAAAAAAAjbBkI%2FhYaOGS1VzPcLTSw48SOlQ%3Djg6qQtN0eB4KZrj98bjb3vdrDE0X3b0b4zJhW5qhGfDYuP4zFe';
		
		//HTTP GET content Setup
		$this->opts = array(
          'http'=>array(
            'method'=>"GET",
            'header'=>"Authorization: ".$this->bearer."\r\n"
          )
        );

        $this->context = stream_context_create($this->opts);

        $this->twitter_api_url = 'https://api.twitter.com/1.1/search/tweets.json';
	}

	/* 
	* Get Tweet from Lat Lng
	*
	*/

	public function getTweet($lat, $lng, $search_url = null) {

		//set center lat lgn with 50km
        $center = $lat.",".$lng.",50km";
        $search_url = ($search_url == null) ? '?geocode='.$center.'&result_type=recent&include_entities=true&count=100' : $search_url;
        $twitter_url = $this->twitter_api_url.$search_url;
        $twitter_result = "";

        //If have cache exist use cache
        if (file_exists('twitter_result.data')) {
        	// Read cache data
		    $data = unserialize(file_get_contents('twitter_result.data'));
		    if ($data['timestamp'] > time() - 60 * 60) {
		        $twitter_result = $data['twitter_result'];
		        $count = $this->check_coordinates($twitter_result);
		        $twitter_result = ($count > 0 ) ? $twitter_result : "";
		    }
		}

		// cache doesn't exist or is older than 60 mins
		if ($twitter_result == "") { 
		    $json = file_get_contents($twitter_url, false, $this->context);
        	$twitter_result = json_decode($json);

        	//Save cache data
		    $data = array ('twitter_result' => $twitter_result, 'timestamp' => time());
		    file_put_contents('twitter_result.data', serialize($data));
		}


		$result = $this->set_array_tweet($twitter_result, $lat, $lng);

		return $result;

	}


    /* 
	* Given an address, return the longitude and latitude using The Google Geocoding API V3
	*
	*/

	public function getLatLong($address) {
	    $address = urlencode($address);

	    $url = "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false";

	    // Make the HTTP request
	    $data = @file_get_contents($url);

	    // Parse the json response
	    $jsondata = json_decode($data,true);

	    // If the json data is invalid, return empty array
	    if (!$this->check_status($jsondata))   return array();

	    $LatLng = array(
	        'lat' => $jsondata["results"][0]["geometry"]["location"]["lat"],
	        'lng' => $jsondata["results"][0]["geometry"]["location"]["lng"],
	    );

	    return $LatLng;
	}

	/* 
	* Check if the json data from Google Geo is valid 
	*/

	function check_status($jsondata) {
	    if ($jsondata["status"] == "OK") return true;
	    return false;
	}

	/*
	* Count coordinates data
	*
	*/

	function check_coordinates($tweets) {
		$i = 0;

		foreach($tweets as $tweet) {
			foreach ($tweet as $t) {
				//Check that tweets has coordiantaes or not
				if (!empty($t->coordinates)) {
					$i++;
				}
			}
		}

		return $i;
	}

	/* 
	* Return tweet object as array
	*/

	function set_array_tweet($tweets, $lat, $lng){
    	
    	$temp_array = array();
    	foreach($tweets as $tweet) {
			foreach ($tweet as $t) {
				$temp = array();
				//Check that tweets has coordiantaes or not
				if (!empty($t->coordinates)) {
					$temp['text']  = $t->text;
					$temp['image'] = $t->user->profile_image_url;	
					$temp['lat']   = $t->coordinates->coordinates[1];
					$temp['lng']   = $t->coordinates->coordinates[0];
					array_push($temp_array, $temp);
				}
			}
		}

		return $temp_array;
    }
}
