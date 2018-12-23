<?php
session_start();
header("Content-type: application/json");
include_once('luokka.php');
include_once 'config.php';


if ( $_SESSION['julkisuus']>10 ){
  $input = filter_input_array(INPUT_POST);

  $id = $input['kisaID'];

  $conn -> lisaaJoukkue( $id ); 

  echo json_encode($input);
}

?> 

