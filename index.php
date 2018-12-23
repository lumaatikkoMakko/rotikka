<?php
session_start();
include_once("php/luokka.php");
$myFile = $_SERVER['DOCUMENT_ROOT'] + "/../salasanat/fll.txt";

echo $myFile;

//if file_exists($myFile) { 
//   $fh = fopen($myFile, 'r');
//   $password = fgets($fh);
//   fclose($fh);
//} else die("No password file");
$conn = new Testi( $password );

?>
<!DOCTYPE html>
<html lang="fi">
<head>


<!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Uniboard.</title>
  <meta name="description" content="">
  <meta name="author" content="@MarkkuOpe">

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/skeleton.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery.tabledit.min.js"></script>
  <link rel="stylesheet" href="css/default.css">


  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="images/favicon.png">

  
<style>

.h1{
	font-size:30px;
}

</style>



</head>
<body>
	
	
<?php
//include 'php/getIPs.php';
?>
	
	

<div id="container">
	
<div class="kpl">
  <div id="kirjautumisform">

  <form name="login" method="post" action="php/logTsekkaa.php">
  <div class="row">
    <div class="six columns">
      <label for="myusername">Käyttäjätunnus</label>
      <input class="u-full-width" placeholder="test@mailbox.com" id="myusername" name="myusername" type="text">
    </div>
    <div class="six columns">
      <label for="mypassword">Salasana</label>
       <input class="u-full-width" placeholder="test@mailbox.com" id="mypassword" name="mypassword" type="password">
    </div>
  <input class="button-primary" value="Kirjaudu sisään" type="submit">
  </div>
</form>
</div>

</div> <!-- Kirjaudu -->
</div>
	


<?php
//
// Luetaan kisavaihtoehdot
//

//$kisat = $conn -> haeKisat( $_SESSION['julkisuus'] );
$kisat = $conn -> haeKisat( 99 );

//
// 
//
$kisaID = 0;
$joukkueet = [];
$kisaNimi = "";
if ($_GET['kisaID']>0){
  if ($_SESSION['julkisuus']>0){
    $kisaID = $_GET['kisaID'];
    $joukkueet = $conn -> haeJoukkueet( $kisaID );
    $kisaNimi = $conn -> haeKisa( $kisaID );
}
}
?>



    <div class="kpl">
		<h3>Kisa</h3>	
<div class="row">
<div class="six columns">
<p>Valitse kisa. Listassa on joitain kisoja. Helppo lisätä: kerro Markulle.</p>
</div>
<div class="six columns">

    <form action="index.php" method="GET">

              <label for="kisa">Valitse kisa</label>
              <select name="kisaID" class="u-full-width" id="kisa">

<?php

foreach( $kisat as $kisa){
    
    echo '<option value="'.$kisa->kisaID .'">'.$kisa->nimi.'</option>';

}
?>


              </select>
<input class="button-primary" type="submit" value="Valitse">
            </div>


	</div>
</div>
	
		<div class="kpl">
<h2>Pistetaulukko &mdash;
<?php
echo $kisaNimi[0]->nimi;
?>

</h2>	
<p>Tallenna painamalla <em>enteriä</em>, klikkaamalla muualle tarkoittaa perumista.<P>

<?php

if ( $_SESSION['julkisuus']>10 ){




echo '<table id="my-table" class="u-full-width">
    <thead><tr>
      <th>Id</th>
      <th>Nimi</th>
      <th>Erä 1</th>
      <th>Erä 2</th>
      <th>Erä 3</th></tr></thead><tbody>' . "\n";

    foreach( $joukkueet as $joukkue ){
        echo '<tr>';

        echo '<td>' . $kisaID . '-' . $joukkue -> ID .'</td>';
        echo '<td>' . $joukkue -> nimi .'</td>';
        
        echo '<td>' . $joukkue -> era1 .'</td>';
        echo '<td>' . $joukkue -> era2 .'</td>';
        echo '<td>' . $joukkue -> era3 .'</td>';
        echo '</tr>' . "\n";

    }

echo '</tbody></table>';

echo ' <input class="button-primary" id="lisaa" type="submit" value="Lisää joukkue">';

}else{

echo '<h2>Kirjaudu sisään jatkaaksesi</h2>';
}
?>





</div>
</div>
</body>

<script>

$(document).ready(function(){ 

$("#lisaa").click( function(event){
    event.preventDefault();

    $.ajax({
        url: "php/lisaaJoukkue.php", 
        type: "POST",
        data: {
             kisaID: <?php echo $kisaID?>,
        }, success: function(result){
            console.log( result );
        }, error: function(XMLHttpRequest, textStatus, errorThrown) { 
            alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    } 
    });

    
    return false;

});


});

$('#my-table').Tabledit({

    url: 'php/savePoints.php',
    editButton: false,
    deleteButton: false,
    saveButton: true,
    hideIdentifier: true,
    buttons: {
        save: {
            class: 'btn btn-sm btn-primary',
            html: '<span class="glyphicon glyphicon-pencil"></span> &nbsp EDIT',
            action: 'edit'
        },
        edit: {
            class: 'btn btn-sm btn-primary',
            html: '<span class="glyphicon glyphicon-pencil"></span> &nbsp EDIT',
            action: 'edit'
        }
    },
    columns: {
        identifier: [0, 'joukkueID'],
            editable: [[1, 'nimi'], [2, 'era1'], [3, 'era2'], [4, 'era3']]
    },
        onDraw: function() {
        console.log('onDraw()');
    },
    onSuccess: function(data, textStatus, jqXHR) {
        console.log('onSuccess(data, textStatus, jqXHR)');
        console.log(data);
        console.log(textStatus);
        console.log(jqXHR);
    },
    onFail: function(jqXHR, textStatus, errorThrown) {
        console.log('onFail(jqXHR, textStatus, errorThrown)');
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    },
    onAlways: function() {
        console.log('onAlways()');
    },
    onAjax: function(action, serialize) {
        console.log('onAjax(action, serialize)');
        console.log(action);
        console.log(serialize);
    }


	});
 
</script>

</html>

