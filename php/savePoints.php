<?php
session_start();
header("Content-type: application/json");
include_once('luokka.php');
include_once 'config.php';

if ( $_SESSION['julkisuus']>10 ){

$input = filter_input_array(INPUT_POST);

$arr = explode("-", $input['joukkueID']);
$kisa = $arr[0];
$joukkue = $arr[1];



if ($input['action'] == 'edit') {
    if (isset( $input['nimi'])){
        $conn -> muutaNimi( $kisa, $joukkue, $input['nimi'] ); 
    }elseif (isset( $input['era1'])){
        $conn -> muutaEra1( $kisa, $joukkue, $input['era1'] ); 
    }elseif (isset( $input['era2'])){
        $conn -> muutaEra2( $kisa, $joukkue, $input['era2'] ); 
    }elseif (isset( $input['era3'])){
        $conn -> muutaEra3( $kisa, $joukkue, $input['era3'] ); 
    }


} 
 



}

//echo json_encode($input);
echo json_encode(array(  $input['nimi'] ));


