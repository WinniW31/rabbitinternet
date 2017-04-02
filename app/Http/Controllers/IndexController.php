<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\TwitterController;

class IndexController extends Controller
{

	public function __construct() {
		
		//Call TwitterController to use in IndexController
        $this->twitterController = new TwitterController;
	}

	/*
	*Default Function for GET index page
	*
	*/
	public function view() {

		$tweets_array = $this->twitterController->getTweet(13.7244426, 100.3529157);
		return view('index', [  'tweets' => json_encode($tweets_array)
							  , 'lat' => 13.7244426
							  , 'lng' => 100.3529157 
							  , 'city_name' => 'bangkok' ]);
	}

	/*
	*Function for POST index page
	*
	*/
    public function search(Request $request) {

    	$city_name 	= (!empty($request->input('city_name')) && $request->input('city_name') != "") ? $request->input('city_name') : 'bangkok';
    	$latlng     = $this->twitterController->getLatLong($city_name);
    	$lat 		= ($latlng['lat'] != "") ? $latlng['lat'] : 13.7244426;
        $lng 	   	=	 ($latlng['lng'] != "") ? $latlng['lng'] : 100.3529157;

		$tweets_array = $this->twitterController->getTweet($latlng['lat'], $latlng['lng']);

		return view('index', [  'tweets' => json_encode($tweets_array)
							  , 'lat' => $lat
							  , 'lng' => $lng 
							  , 'city_name' => $city_name ]);

    }


}