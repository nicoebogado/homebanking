<script src=https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry&sensor=false%22''></script>
<script>

var markers = [];
var map;
var latString;
var longString;

function initialize() {
var mapDiv = document.getElementById('map-canvas');
map = new google.maps.Map(mapDiv, {
  center: new google.maps.LatLng(-25.2887732,-57.6328424),
  zoom: 17,
  mapTypeId: google.maps.MapTypeId.ROADMAP
});

google.maps.event.addListener(map, 'bounds_changed', function() {

    var lat1 = -19.175697;
    var lat2 = -27.875042;
    var lng1 = -62.808092;
    var lng2 = -53.224717;

    var rectangle = new google.maps.Polygon({
       paths : [
         new google.maps.LatLng(lat1, lng1),
         new google.maps.LatLng(lat2, lng1),
         new google.maps.LatLng(lat2, lng2),
         new google.maps.LatLng(lat1, lng2)
       ],
      strokeOpacity: 1,
      fillOpacity : 0,
      map : map
    });
    google.maps.event.addListener(rectangle, 'click', function(args) {
       latString=args.latLng.lat();
       longString=args.latLng.lng();
       placeMarker(args.latLng,map);
    });
});
}

google.maps.event.addDomListener(window, 'load', initialize);

function placeMarker(location,map) {
    clearMarkers()
    var marker = new google.maps.Marker({
        position: location,
        map: map
    });
    markers.push(marker);
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
  setMapOnAll(null);
}

function CloseMySelf(lat,long) {
    try {
        window.opener.HandlePopupResult(lat,long);
    }
    catch (err) {}
    window.close();
    return false;
}

jQuery(document).ready(function($) {
  $('#enviar').click(function(event) {
    CloseMySelf(latString,longString);
  })
});

</script>
<style>
#map-canvas{
  position:relative;
  width: 800px;
  height: 565px;
}
#enviar{width:100px; margin-left:700px; height:35px;}
</style>

<div id="map-canvas"></div>
<button id="enviar" class="btn btn-primary" type="button">Enviar</button>
