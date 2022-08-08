<?php
function gps2Num($coordPart){
    $parts = explode('/', $coordPart);
    if(count($parts) <= 0)
    return 0;
    if(count($parts) == 1)
    return $parts[0];
    return floatval($parts[0]) / floatval($parts[1]);
}

/**
 * get_image_location
 * Returns an array of latitude and longitude from the Image file
 * @param $image file path
 * @return multitype:array|boolean
 */
function get_image_location($image = ''){
    $exif = exif_read_data($image, 0, true);
    if($exif && isset($exif['GPS'])){
        $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
        $GPSLatitude    = $exif['GPS']['GPSLatitude'];
        $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
        $GPSLongitude   = $exif['GPS']['GPSLongitude'];
        
        $lat_degrees = count($GPSLatitude) > 0 ? gps2Num($GPSLatitude[0]) : 0;
        $lat_minutes = count($GPSLatitude) > 1 ? gps2Num($GPSLatitude[1]) : 0;
        $lat_seconds = count($GPSLatitude) > 2 ? gps2Num($GPSLatitude[2]) : 0;
        
        $lon_degrees = count($GPSLongitude) > 0 ? gps2Num($GPSLongitude[0]) : 0;
        $lon_minutes = count($GPSLongitude) > 1 ? gps2Num($GPSLongitude[1]) : 0;
        $lon_seconds = count($GPSLongitude) > 2 ? gps2Num($GPSLongitude[2]) : 0;
        
        $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
        $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;
        
        $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
        $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

        return array('latitude'=>$latitude, 'longitude'=>$longitude);
    }else{
        return false;
    }
}

    //image file path
    $imageURL = "1.jpg";

    //get geolocation of image
    $imgLocation = get_image_location($imageURL);

    if(!empty($imgLocation))
    {
        $imgLat = $imgLocation['latitude'];
        $imgLng = $imgLocation['longitude'];
        echo '<p>Latitude: '.$imgLat.' | Longitude: '.$imgLng.'</p>';
    }
    else
    {
        echo '<p>Geotags not found.</p>';
    }

    //get image taken date
        $exif_data = exif_read_data ($imageURL);
    if (!empty($exif_data['DateTimeOriginal'])) {
        $exif_date = $exif_data['DateTimeOriginal'];
        echo '<p>Date & Time: '.$exif_date.'</p>';
    }

?>

<!-- Display Image Geolocation on Google Maps -->

<style>
    #map
    {
        width: 50%;
        height: 400px;
    }
    #image
    {
        width: 35%;
        height:400px;
        float:left;
    }
    #image img{width:100%;height:400px;}
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCUOzfEMYXPD8rEgJpJEbBFxhJ9GuBS0-8"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    var myCenter = new google.maps.LatLng(<?php echo $imgLat; ?>, <?php echo $imgLng; ?>);
    function initialize()
    {
        var mapProp = 
        {
            center:myCenter,
            zoom:19,
            mapTypeId:google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("map"),mapProp);

        var marker = new google.maps.Marker({
            position:myCenter,
            animation:google.maps.Animation.BOUNCE
        });

        marker.setMap(map);
    }
    google.maps.event.addDomListener(window, 'load', initialize);

    console.log($('#map .dismissButton'));
</script>
<!-- <script>
    $(document).ready(function () {
        // if ($('#map .dismissButton').length > 0) $('#map .dismissButton').trigger('click');
        // $('#map .dismissButton').trigger('click')   
        console.log($('#map .dismissButton').length);    
    });
</script> -->
<div style="width:100%">
    <!-- <div id="image"><img src="<?php //echo $imageURL; ?>"/></div> -->
    <div id="map"></div>
</div>
