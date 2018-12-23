<?php

session_start();
header("Content-type: application/json");
include_once('luokka.php');
$myFile = $_SERVER['DOCUMENT_ROOT'] + "/../salasanat/fllResultTable.txt";
if file_exists($myFile) { 
   $fh = fopen($myFile, 'r');
   $password = fgets($fh);
   fclose($fh);
} else die("No password file");
$conn = new Testi( $password );





$myusername = strtoupper( $_POST['myusername'] );
//$mypassword = $_POST['mypassword'];
$mypassword = sha1( $_POST['mypassword'] );
//MYSQL update kayttajat set salasana=sha1("SALASANA") WHERE kayttajaID=1;

$head = "location: ../index.php";
//Tsekataan, onko ope-sivulta logattu:
if ( !empty(  $_POST['Submit-ope'] ) ){
    $head = "location: ../materiaalit.php";
}

//print_r($_POST);


//exit();

$varaaja = $conn -> kirjaudusisaan($myusername, $mypassword);

//print_r($varaaja);


if ((empty($varaaja)) || ($varaaja=="")){
  session_destroy(); 
  header('HTTP/1.1 401 Unauthorized');
  //print "{\"status\":\"error\",\"errorcode\":\"1\"}";


}else{

    //Kirjautuminen ok
        
  //print "{\"status\":\"ok\",\"name\":\"".$varaaja[0] -> ktunnus . "\"}";
  $_SESSION['userid'] =  $varaaja[0] -> kID;
  $_SESSION['nimi'] =  $_POST['myusername'];  
  $_SESSION['julkisuus'] =  $varaaja[0] -> julkID;

    
    //
    //Alkuun
    //

}
header( $head );




?>


