<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
        integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
        integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
        crossorigin=""></script>
    <style>
        #map { height: 360px; }
    </style>
    <title>Hello, world!</title>
  </head>
  <body>
    
    <div class="container pt-5">
        <div class="row">
            <div class="col">
            </div>
            <div class="col-8">
                <form>
                    <div class="form-group">
                        <label for="findPlaceInput">Place</label>
                        <input type="text" class="form-control form-control-lg" id="findPlaceInput" aria-describedby="findPlaceHelp" placeholder="Enter a place">
                        <small id="findPlaceHelp" class="form-text text-muted">Find all events on a place.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Find</button>
                </form>
                <div id="map" class="mt-5"></div>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
        
        var map = L.map('map').setView([{{ $long }}, {{ $lat }}], 13);

        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        //var cords = @json($cords);
        var places = @json($places);
        var event_places = @json($events);
        var events_and_places = @json($events_and_places);
        
        //for(i = 0; i < places.length; i += 2) 
        events_and_places.forEach(place => {
            
            //L.marker([ place['latitude'], place['longitude']]).addTo(map)
              //  .bindPopup(place['place_name'])
                //.openPopup();
        });
    </script>
</body>
</html>