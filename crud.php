<?php

$data = fetchAll();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  header("Access-Control-Allow-Origin: *");

//This will be create and update
  //POST then update $data
  //echo "POST request detected";
  $device = array(
    'id' => count($data),
    'name' => $_POST['name'],
    'values' => array(
      $_POST['values']),
    'date_created' => $_POST['date_created']
    );

  $data = $data.push(json_encode($device));
  file_put_contents("data.txt", $data);
  //$data = file_get_contents("data.txt");
  echo $data;


}elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'){
  echo "PUT request detected";
  //TODO: Update data entry

}elseif ($_SERVER['REQUEST_METHOD'] == 'GET'){
  header("Access-Control-Allow-Origin: *");
  if (isset($_GET['id'])){
  $devices = json_decode($data, true);
   foreach($devices as $device){
     if($device[id] == $_GET['id']){
      echo json_encode($device);
      return;
     }
  }

  }else{
  echo $data;
  }
  

}elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
  echo "DELETE request detected";
  $id = $_REQUEST['id'];
  echo $id;
}else{
  echo "Nothing";

}
function fetchAll(){

  if (file_exists("data.txt")){

    return file_get_contents("data.txt");

  }else{

    $dummydata= '[
    {
        "id": 1,
        "name": "Light",
        "values": [
            {
                "title": "lumens",
                "value": "8000"
            },
            {
                "title": "wattage",
                "value": "16"
            }
        ],
        "date_created": "2014-04-08"
    },
    {
        "id": 2,
        "name": "Water",
        "values": [
            {
                "title": "pressure",
                "value": "50"
            },
            {
                "title": "temperature",
                "value": "27"
            },
            {
                "title": "pollution",
                "value": "0.4"
            }
        ],
        "date_created": "2014-04-08"
    },
    {
        "id": 3,
        "name": "Door",
        "values": [
            {
                "title": "state",
                "value": "locked"
            }
        ],
        "date_created": "2014-04-08"
    },
    {
        "id": 4,
        "name": "Window",
        "values": [
            {
                "title": "state",
                "value": "locked"
            }
        ],
        "date_created": "2014-04-08"
    }
]';
file_put_contents("data.txt", $dummydata);
return $dummydata;
  }
  //return file_get_contents("http://uwi-has.appspot.com/data/");

}
?> 