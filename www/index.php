<?php

require_once("database.php");

if (($_GET['action'] != "new") && !$_GET['id']) die("No ID provided!");

switch ($_GET["action"]) {
    case "new":
        $db = new DataBase();

        $result = $db->get_new_id();

        die($result['id']);
    break;

    case "get":
        $db = new DataBase();

        $result = $db->select((int) $_GET['id']);
        if ($result) {
            $json = array();
            $json['lat'] = (float) $result['latitude'];
            $json['lng'] = (float) $result['longitude'];;

            // This doesn't work in my arsys hosting
            //$encoded = json_encode($json);
            $encoded = '{"lat":' . $json['lat']. ',"lng":' . $json['lng'] . '}';
        }

        die($encoded);
    break;

    case "post":
        $db = new DataBase();

        $id = (int) $_GET['id'];
        $lat = (float) $_GET['lat'];
        $lng = (float) $_GET['lng'];
        
        $db->insert($id, $lat, $lng);
        die();
    break;

    case "show":
        $id = $_GET['id'];
    break;

    default:
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"> 
  <head> 
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/> 
    <title>Show me!</title> 
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAIUiRh9vGTU8GIfuQooImRxReysCIw65eY2HcdIK__B4VQ4C-DBTUJ6fZDI5YGR6Jutf1WNG8Ogs3UA" type="text/javascript"></script>
    <script src="prototype.js" type="text/javascript"></script>
    <script type="text/javascript"> 
    
    function initialize() {
      if (GBrowserIsCompatible()) {
        this.map = new GMap2(document.getElementById("map_canvas"));

        new Ajax.Request("index.php?action=get&id=<?=$_GET['id']?>", {
            method: "get",
            onSuccess: function(transport) {
                var json = transport.responseText.evalJSON();
                this.map.setCenter(new GLatLng(parseFloat(json.lat), parseFloat(json.lng)), 13);
                this.map.setUIToDefault();
                this.marker = new GMarker(new GLatLng(json.lat, json.lng));
                this.map.addOverlay(this.marker);
            }
        });

        setInterval("updater()", 1000);
      }
    }
    
    function updater() {
        new Ajax.Request("index.php?action=get&id=<?=$_GET['id']?>", {
            method: "get",
            onSuccess: function(transport) {
                var json = transport.responseText.evalJSON();
                reposition(parseFloat(json.lat), parseFloat(json.lng));
            }
        });
    }

    function reposition(lat, lng) {
        this.marker.setLatLng(new GLatLng(lat, lng));
        //To work with WebOS
        //this.map.clearOverlays();
        //this.map.addOverlay(this.marker);
        this.map.panTo(new GLatLng(lat, lng));
    }
 
    </script> 
  </head> 
  <body onload="initialize()" onunload="GUnload()"> 
    <style>
        html, body { height: 100%; overflow: hidden; }
        body { margin: 0; }
        #map_canvas { height: 100%; }
    </style>
    <div id="map_canvas"></div>
  </body> 
</html>
<?php } ?>
