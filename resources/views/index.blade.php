<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Map Search Based</title>

        <!-- Bootstrap & CSS-->
        <link href="{{ URL::asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('css/map.css') }}" rel="stylesheet">

    </head>
    <body>

        <div class="flex-center position-ref full-height">
            <div class="container">
                <div id="map"></div>
                <form action="/" method="post" class="form-inline" id="city-form">
                    <div class="row">
                        <div class="form-group col-xs-6 col-md-10">
                            <input type="text" class="form-control" id="city_name" name="city_name" value="{{ $city_name }}" placeholder="City Name">
                        </div>
                        <button type="submit" class="btn btn-primary col-xs-6 col-md-2">Search</button>
                    </div>
                     <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="{{ URL::asset('bootstrap/js/bootstrap.min.js') }}"></script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtATFwli-lRI7qfuX4nvsfG43UV8sLSJQ&callback=initMap">
        </script>
    
         <!-- Google MAP JS Script -->
        <script>
            var map;
            var markers = {!! $tweets !!};
            var marks = [];
         
            function initMap() {
                
                var lat = parseFloat({!! $lat !!});
                var lng = parseFloat({!! $lng !!});
                

                var city = {lat: lat, lng: lng};
                map = new google.maps.Map(document.getElementById('map'), {
                  zoom: 8,
                  center: city
                });

                if(markers != null && markers.length > 0) {
                    for(var i = 0; i < markers.length; i++){
                       addMarker(markers[i]);
                    }
                }
            }

            function addMarker(marker) {

                var markerLatlng = new google.maps.LatLng(parseFloat(marker.lat),parseFloat(marker.lng));

                var mark = new google.maps.Marker({
                    map: map,
                    position: markerLatlng,
                    icon: marker.image
                });

                var html = marker.text;

                var infoWindow = new google.maps.InfoWindow;
                google.maps.event.addListener(mark, 'click', function(){
                    infoWindow.setContent(html);
                    infoWindow.open(map, mark);
                });

            }

        </script>
    </body>
</html>