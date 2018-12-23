<?php
	class MySQL_naama {
		var $host_name = '';
		var $user_name = '';
		var $password = '';
		var $db_name = '';
		var $conn_id = 0; //Not actually a variable but an object or something
		var $errstr = '';
		var $halt_on_error = 1;
		var $query_pieces = array();
		var $result_id = 0;
		var $num_rows = 0;
		var $row = array();
		function connect() {
			$this->errno  = 0; #Tyhjää virhemuuttuja
			$this->errstr = '';
			if ( $this->conn_id == 0 ) // Yhdistä tietokantaan, jollei ole jo yhteydessä
			{
				try {
                    $this->conn_id = new PDO( 
                        "mysql:host=" . $this->host_name . 
                        ";dbname=" . $this->db_name . 
                        ";charset=utf8" .
                        "", $this->user_name, 
                        $this->password );
					//Persistent connections for faster db application 
				}
				catch ( PDOException $e ) {
					$this->error( $e->getMessage() );
				}
				return ( $this->conn_id );
			}
		}
		function disconnect() {
			if ( $this->conn_id != 0 ) {
				$this->conn_id = null;
			}
		}
		function error( $msg ) {
			if ( !$this->halt_on_error )
				return;
			$msg .= "\n";
			$this->errstr = $msg;
			echo "X1: VIRHE!" . $this->errstr . "</br>";
			// die (nl2br (htmlspecialchars ($msg)) );
			die();
		}
    }


	class Testi extends MySQL_naama {
		var $host_name = '';
		var $user_name = '';
		var $password = '';
		var $db_name = '';
		function __construct( $pwd ) {
			// Rakentaja. 
			$this->set_database( $pwd );
		}
		function set_database( $pwd ) {
			// Haetaan kone/ leinolamsa.org vai localhost
			$url = "http://" . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
			//echo $url;
			if ( strlen( strstr( $url, "fllsuomi.org" ) ) > 0 )
			{
				$this->host_name = '';
				$this->user_name = '';
				$this->password  = $pwd; 
				$this->db_name   = '';
			}
			elseif ( strlen( strstr( $url, "localhost" ) ) > 0 )
			{
				$this->host_name = 'localhost';
				$this->user_name = 'root';
				$this->password  = 'root'; //$pwd;
                $this->db_name   = 'finfll';
				//echo "LOCALHOST"; 
			}
		}
        


        function kirjaudusisaan($user, $pwd){

            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("SELECT kID, nimi, julkID  FROM kayttajat WHERE nimi = :userid AND ssana = :password LIMIT 1");

                $sql->setFetchMode(PDO::FETCH_INTO, new kayttaja);
                $sql->execute(array(
                    ':userid' => $user,
                    ':password' => $pwd
                ));
                $result = $sql->fetchAll();
                //print_r($result);
            }
            catch (PDOException $e) {
                $this->error($e->getMessage());
            }
            return $result;
        }


    function haeKisa( $id ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * FROM kisat WHERE kisaID = :id");

                $sql->setFetchMode(PDO::FETCH_INTO, new kisat);
                $sql->execute( array( ":id" => $id  ) ); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }
    function haeKisat( $julkisuus ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * FROM kisat WHERE tunnus <= :tunnus");

                $sql->setFetchMode(PDO::FETCH_INTO, new kisat);
                $sql->execute( array( ":tunnus" => $julkisuus  ) ); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }


    function haeJoukkueet( $id ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT *  FROM erapisteet 
                    WHERE kisaID = :id
                    ORDER by nimi
                    ");

                $sql->setFetchMode(PDO::FETCH_INTO, new joukkueet);
                $sql->execute( array( ":id" => $id  ) ); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }


	function lisaaJoukkue( $ID ){
		if ( empty( $this->conn_id ) ) // Not connected
    		$this->connect();
        try{
                $sql = $this->conn_id->prepare( "INSERT INTO 
				    erapisteet 	
                    (kisaID)
					VALUES
                    (:ID)
                " );//WHERE
                if( !$sql->execute(array(
                    ":ID" => $ID, 
                )) ){
					print_r($sql->errorInfo());
				}
            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
			return $this -> conn_id -> lastInsertId();
	}

    function muutaNimi( $kisa, $ID, $nimi ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    UPDATE  erapisteet 
                    SET nimi= :nimi
                    WHERE kisaID= :kisaID AND ID= :ID 
                    ");

                $sql->execute( array( 
                    ":nimi"      => $nimi,
                    ":ID" => $ID,
                    ":kisaID"    => $kisa
                )); 
                }                 
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
            }
    function muutaEra1( $kisa, $ID, $era ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    UPDATE  erapisteet 
                    SET era1= :era
                    WHERE kisaID= :kisaID AND ID= :ID 
                    ");

                $sql->execute( array( 
                    ":era"      => $era,
                    ":ID" => $ID,
                    ":kisaID"    => $kisa
                )); 
                }                 
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
            }
    function muutaEra2( $kisa, $ID, $era ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    UPDATE  erapisteet 
                    SET era2= :era
                    WHERE kisaID= :kisaID AND ID= :ID 
                    ");

                $sql->execute( array( 
                    ":era"      => $era,
                    ":ID" => $ID,
                    ":kisaID"    => $kisa
                )); 
                }                 
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
            }
    function muutaEra3( $kisa, $ID, $era ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    UPDATE  erapisteet 
                    SET era3= :era
                    WHERE kisaID= :kisaID AND ID= :ID 
                    ");

                $sql->execute( array( 
                    ":era"      => $era,
                    ":ID" => $ID,
                    ":kisaID"    => $kisa
                )); 
                }                 
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
            }







    function haeAiheet(){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * FROM aiheet ORDER by nimi DESC");

                $sql->setFetchMode(PDO::FETCH_INTO, new aiheet);
                $sql->execute(); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }

    function haeMateriaalit( $id ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT ID, pvm, nimi, palauttajaID FROM materiaalit WHERE aiheID=:id ORDER by pvm DESC");

                $sql->setFetchMode(PDO::FETCH_INTO, new aiheet);
                $sql->execute( array(
                        ':id' => $id )); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }


        function talletaPisteet( $era, $joukkueID, $kisaID, $tuomariID, $points, $summa  ){
            if ( empty( $this->conn_id ) ) // Not connected
                $this->connect();
            try{
                $sql = $this->conn_id->prepare( "INSERT INTO pisteet 
                    (joukkueID, kisaID, eraID, muutettu, tuomariID, pisteet, summa)
                    VALUES 
                    (:joukkueID, :kisaID, :eraID, NOW(), :tuomariID, :pisteet, :summa )
                " );//WHERE
                $sql->execute(array(
                    ':joukkueID' => $joukkueID , 
                    ':kisaID' => $kisaID, 
                    ':eraID' => $era, 
                    ':tuomariID' => $tuomariID, 
                    ':summa' => $summa, 
                    ':pisteet' => $points
                ));


            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }

            //echo "MOI"; 



        }



        function haePisteet( $eraID, $joukkueID ){
            if ( empty( $this->conn_id ) ) // Not connected
                $this->connect();
            try{
                $sql = $this->conn_id->prepare( "SELECT ID, pisteet, summa from pisteet 
                    WHERE (joukkueID = :joukkueID AND eraID = :eraID ) 
                    ORDER BY ID DESC
                    " );//WHERE
                $sql->setFetchMode(PDO::FETCH_INTO, new pisteet); 
                $sql->execute(array(
                    ':joukkueID' => $joukkueID , 
                    ':eraID' => $eraID
                ));

                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                  }
              }
            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }

            return $result;
        }




            function   PalautaTehtava($aiheID, $userID, $content, $nimi){
                if ( empty( $this->conn_id ) ) // Not connected
                $this->connect();
            try{
                $sql = $this->conn_id->prepare( "INSERT INTO materiaalit 
                    (palauttajaID, aiheID, data,nimi, pvm)
                    VALUES 
                    (:kayttajaID, :aiheID, :data, :nimi, NOW())
                " );//WHERE
                $sql->execute(array(
                    ':kayttajaID' => $userID , 
                    ':aiheID' => $aiheID, 
                    ':nimi' => $nimi, 
                    ':data' => $content 
                ));


            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }

            //echo "MOI"; 

        }

            function lueTiedosto($tiedostoID){
                if ( empty( $this->conn_id ) ) // Not connected
                    $this->connect();
                try{

                    $sql = $this->conn_id->prepare( "SELECT data, nimi 
                        FROM materiaalit 
                        WHERE
                        ID=:tiedostoID 
                        " );
                    $sql->setFetchMode(PDO::FETCH_INTO, new materiaalit); 
                    $sql->execute(array(
                        ':tiedostoID' => $tiedostoID,
                    ));

                    if ($sql -> rowCount() < 2){
                        $result = $sql -> fetchAll();
                    } else{
                        while ($object = $sql->fetch()) {
                            $result[] = clone $object;
                        }
                    }
                } catch (PDOException $e) {
                    $this->error($e->getMessage());
                }

                return $result;
            }



     function haeTapahtuma( $id ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * FROM kalenteri WHERE ID = :id");

                $sql->setFetchMode(PDO::FETCH_INTO, new tapahtuma);
                $sql->execute(array(
                    ':id' => $id, 
                ));

                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result[0];

            }



     function haeKaikkiKalenteriIdOts(){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT ID as value, otsikko as label FROM kalenteri 
                    ");

                $sql->setFetchMode(PDO::FETCH_INTO, new tapahtumaID);
                $sql->execute(); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }
     function haeVanhaKalenteri(){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * FROM kalenteri 
                    WHERE pvm <= CURDATE()
                    ORDER by pvm DESC
                    ");

                $sql->setFetchMode(PDO::FETCH_INTO, new tapahtuma);
                $sql->execute(); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }

     function haeKalenteri(){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * FROM kalenteri 
                    WHERE pvm >= CURDATE()
                    ORDER by pvm ASC
                    ");

                $sql->setFetchMode(PDO::FETCH_INTO, new tapahtuma);
                $sql->execute(); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }

     function paivitaTapahtuma( $tap ){

            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    UPDATE kalenteri SET
                        otsikko = :otsikko,
                        karttalinkki = :klinkki,
                        osoite = :osoite,
                        kuvaus = :kuvaus,
                        tmpnimi = :tmpnimi,
                        kuvanimi = :kuvanimi,
                        pvm = :pvm,
                        loppupvm = :loppupvm,
                        youtube = :youtube,
                        twitter = :twitter,
                        instagram = :instagram,
                        facebook = :facebook,
                        tapahtumalinkki = :tapahtumalinkki
                        WHERE ID=:id
                    ");

                $sql->execute( array( 
                    ":otsikko" => $tap -> otsikko,
                    ":klinkki" => $tap -> karttalinkki,
                    ":osoite" => $tap -> osoite,
                    ":kuvaus" => $tap -> kuvaus,
                    ":tmpnimi" => $tap -> tmpnimi,
                    ":kuvanimi" => $tap -> kuvanimi,
                    ":pvm" => $tap -> pvm,
                    ":loppupvm" => $tap -> loppupvm,
                    ":id" => $tap -> id,
                    ":youtube" => $tap -> youtube,
                    ":twitter" => $tap -> twitter,
                    ":instagram" => $tap -> instagram,
                    ":facebook" => $tap -> facebook,
                    ":tapahtumalinkki" => $tap -> tapahtumalinkki
                )); 

  //          echo "\nPDO::errorCode(): ";
  //              print $sql->errorCode();
            if ( $sql -> errorCode() > 0 ){
                print_r($sql->errorInfo());
            }

            }
            catch (PDOException $e){
                $this -> error( $e->getMessage()  ); 
            }

         }
       
      function lisaaTapahtuma( $tap ){

            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    INSERT INTO kalenteri 
                        (otsikko, karttalinkki,
                        osoite, kuvaus, tmpnimi, kuvanimi,
                        pvm, loppupvm, youtube, twitter, instagram, facebook, tapahtumalinkki)
                        VALUES
                        (:otsikko, :klinkki, :osoite,
                        :kuvaus, :tmpnimi, :kuvanimi,
                        :pvm, :loppupvm, :youtube, :twitter, :instagram, :facebook, :tapahtumalinkki)
                    ");

                $sql->execute( array( 
                    ":otsikko" => $tap -> otsikko,
                    ":klinkki" => $tap -> karttalinkki,
                    ":osoite" => $tap -> osoite,
                    ":kuvaus" => $tap -> kuvaus,
                    ":tmpnimi" => $tap -> tmpnimi,
                    ":kuvanimi" => $tap -> kuvanimi,
                    ":pvm" => $tap -> pvm,
                    ":youtube" => $tap -> youtube,
                    ":twitter" => $tap -> twitter,
                    ":instagram" => $tap -> instagram,
                    ":facebook" => $tap -> facebook,
                    ":tapahtumalinkki" => $tap -> tapahtumalinkki,
                    ":loppupvm" => $tap -> loppupvm
                )); 

//            echo "\nPDO::errorCode(): ";
//                print $sql->errorCode();
            if ( $sql -> errorCode() > 0 ){
                print_r($sql->errorInfo());
            }

            }
            catch (PDOException $e){
                $this -> error( $e->getMessage()  ); 
            }

         }
		 






    function   LisaaKuva($nimi, $nimirnd, $hakemisto, $kuvaaja, $tapahtumaID ){
            if ( empty( $this->conn_id ) ) // Not connected
                $this->connect();
            try{
                $sql = $this->conn_id->prepare( "INSERT INTO kuvat 
                    (nimi, nimirnd, hakemisto, lisayspvm, kuvaaja, tapahtuma)
                    VALUES 
                    (:nimi, :nimirnd, :hakemisto, NOW(), :kuvaaja, :tapahtuma) 
                " );
                $sql->execute(array(
                    ':nimi' => $nimi,
                    ':nimirnd' => $nimirnd,
                    ':hakemisto' => $hakemisto,
                    ':tapahtuma' => $tapahtumaID,
                    ':kuvaaja' => $kuvaaja
                ));

                if ( $sql -> errorCode() > 0 ){
                  error_log(" -+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-");
                  //error_log ( echo ($sql->errorInfo()) );
                  //eerror_log ( print_r ($sql->errorInfo()) )
                  error_log (  ($sql->errorCode()) );
                  error_log ( ( $errr  )  );
                }



            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
    }

    function haeKuvat(){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * FROM kuvat ");

                $sql->setFetchMode(PDO::FETCH_INTO, new kuvat);
                $sql->execute(); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }


    function haeKuvatTapahtumista(){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT 
                      nimirnd, hakemisto, otsikko, tapahtuma
                    FROM kuvat 
                       INNER join kalenteri on kuvat.tapahtuma=kalenteri.ID 
                    ORDER BY kalenteri.ID
                ");

                $sql->setFetchMode(PDO::FETCH_INTO, new kuvat);
                $sql->execute(); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

            }




    function haeKuvatTapahtumasta( $ID ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT 
                      nimirnd, hakemisto, otsikko, tapahtuma
                    FROM kuvat 
                       INNER join kalenteri on kuvat.tapahtuma=kalenteri.ID 
                    WHERE kalenteri.ID = :ID
                    ORDER BY kalenteri.ID
                ");

                $sql->setFetchMode(PDO::FETCH_INTO, new kuvat);
                $sql->execute(array(
                   ':ID' => $ID
                )); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;

      }





    function getCurrenthtmlID( $currentPageString  ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT 
                     sivuID, nimi
                    FROM htmlsivut 
                    WHERE nimi LIKE :value
                ");

                $sql->setFetchMode(PDO::FETCH_INTO, new sivut);
                $sql->execute(array(
                   ":value" => "%" .  $currentPageString  . "%"
                
                )); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;
    }




    function   saveIPs($sivuID, $remoteIP, $forwardedIP, $referer ){
            if ( empty( $this->conn_id ) ) // Not connected
                $this->connect();
            try{
                $sql = $this->conn_id->prepare( "INSERT INTO lastVisithtml 
                    (sivuID, remoteIP, forwardedIP, pvm, referer)
                    VALUES 
                    (:sivuID, :remoteIP, :forwardedIP, NOW(), :referer) 
                " );
                $sql->execute(array(
                    ':sivuID' => $sivuID,
                    ':remoteIP' => $remoteIP,
                    ':referer' => $referer,
                    ':forwardedIP' => $forwardedIP
                ));

                if ( $sql -> errorCode() > 0 ){
                  error_log(" -+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-");
                  //error_log ( echo ($sql->errorInfo()) );
                  //error_log ( print_r ($sql->errorInfo()) )
                  error_log (  ($sql->errorCode()) );
                  //error_log ( ( $sql->  )  );
                }

            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
    }


    function getAllPages(   ){
            if (empty($this->conn_id))
                $this->connect();
            try {
                $sql = $this->conn_id->prepare("
                    SELECT * 
                    FROM htmlsivut 
                ");

                $sql->setFetchMode(PDO::FETCH_INTO, new sivut);
                $sql->execute(array(
                )); 
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;
    }




    function modifiedDate( $sivuID,  $pvm ){
            if ( empty( $this->conn_id ) ) // Not connected
                $this->connect();
            try{
                $sql = $this->conn_id->prepare( "INSERT IGNORE INTO lastChangehtml 
                    SET 
                    sivuID = :sivuID,
                    pvm = :pvm
                " );
                $sql->execute(array(
                    ':sivuID' => $sivuID,
                    ':pvm' => $pvm
                ));

                if ( $sql -> errorCode() > 0 ){
                  error_log(" *+*+*+*+*+*?*?*+*+*?*?*?*?*+*+*+*");
                  //error_log ( echo ($sql->errorInfo()) );
                  //error_log ( print_r ($sql->errorInfo()) )
                  error_log (  ($sql->errorCode()) );
                  //error_log ( ( $sql->  )  );
                }

            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
    
    
    }





    function getLastModified( ){
            if ( empty( $this->conn_id ) ) // Not connected
                $this->connect();
            try{
                $sql = $this->conn_id->prepare( "SELECT * FROM lastChangehtml 
                    INNER JOIN htmlsivut 
                    ON htmlsivut.sivuID  = lastChangehtml.sivuID
                    ORDER BY pvm DESC
                    LIMIT 3
                " );
                $sql->setFetchMode(PDO::FETCH_INTO, new sivut);
                $sql->execute(array(
                ));
                if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
                }
                catch (PDOException $e) {
                    $this->error($e->getMessage());
                }
                return $result;
    
    }











	function lisaaHenkilo( $hnimi, $posti, $yksikko, $lahiosoite, $postinro, $postitoimipaikka ){
		if ( empty( $this->conn_id ) ) // Not connected
    			$this->connect();
            try{
                $sql = $this->conn_id->prepare( "INSERT INTO 
					osallistujat
                    (ilmopvm, nimi, sposti, yksikko, lahiosoite, postinro, postitoimipaikka)
					VALUES
                    (CURDATE(), :nimi, :sposti, :yksikko, :lahiosoite, :postinro, :postitoimipaikka)

                " );//WHERE
                if( !$sql->execute(array(
                    ":nimi" => $hnimi, 
					":sposti" => $posti, 
					":yksikko" => $yksikko, 
					":lahiosoite" => $lahiosoite, 
					":postinro" => $postinro, 
					":postitoimipaikka" => $postitoimipaikka
                )) ){
					print_r($sql->errorInfo());
				}
            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
			return $this -> conn_id -> lastInsertId();
	}

	function lisaaIlmo( $ID, $ilmo){
		if ( empty( $this->conn_id ) ) // Not connected
    		$this->connect();
        try{
                $sql = $this->conn_id->prepare( "INSERT INTO 
					osallistujailmo
                    (oID, iID)
					VALUES
                    (:ID, :ilmo)

                " );//WHERE
                if( !$sql->execute(array(
                    ":ID" => $ID, 
					":ilmo" => $ilmo
                )) ){
					print_r($sql->errorInfo());
				}
            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
			return $this -> conn_id -> lastInsertId();
	}
	function lisaaToimi( $ID, $toimi){
		if ( empty( $this->conn_id ) ) // Not connected
    		$this->connect();
        try{
                $sql = $this->conn_id->prepare( "INSERT INTO 
					osallistujatoimi
                    (oID, tID)
					VALUES
                    (:ID, :toimi)

                " );//WHERE
                if( !$sql->execute(array(
                    ":ID" => $ID, 
					":toimi" => $toimi
                )) ){
					print_r($sql->errorInfo());
				}
            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
			return $this -> conn_id -> lastInsertId();
	}

	function ilmoittautuneetTanaan( ){
		if ( empty( $this->conn_id ) ) // Not connected
    		$this->connect();
        try{
                $sql = $this->conn_id->prepare( "
					SELECT count(ID) AS lkm FROM osallistujat WHERE 
					ilmopvm = DATE(NOW())
                " );//WHERE
                if( !$sql->execute(array(
                )) ){
					print_r($sql->errorInfo());
				}
				if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
			return $result;
	}
	function ilmoittautuneetEilen( ){
		if ( empty( $this->conn_id ) ) // Not connected
    		$this->connect();
        try{
                $sql = $this->conn_id->prepare( "
					SELECT count(ID) AS lkm FROM osallistujat WHERE 
					ilmopvm = CURDATE() - INTERVAL 1 DAY
                " );//WHERE
                if( !$sql->execute(array(
                )) ){
					print_r($sql->errorInfo());
				}
				if ($sql -> rowCount() < 2){
                    $result = $sql -> fetchAll();
                } else{
                    while ($object = $sql->fetch()) {
                        $result[] = clone $object;
                   }  
                }
            } catch (PDOException $e) {
                $this->error($e->getMessage());
            }
			return $result;
	}
	
    }  

    class kuvat{


    }
    class kisat{}
    class pisteet{}
    class joukkueet{}
    class aiheet{}
    class sivut{}
    class materiaalit{}
    class kayttaja{}
    class tapahtumaID{}
    class tapahtuma{
  
       public $ID;
       public $pvm;
       public $tarkkapvm;
       public $loppupvm;
       public $otsikko;
       public $osoite;
       public $karttalinkki;
       public $kuvanimi;
       public $tmpnimi;
       public $kuvaus;
       public $tyyppi; 
       public $twitter; 
       public $facebook; 
       public $instagram; 
       public $tapahtumalinkki; 
       public $youtube; 



       function printLinkRow( $ots, $var ){
        $ret = '';
        if ( $this -> $var  ){      
          $ret .= "<div class='row'>";
          $ret .= "<div class='three columns'>";
          $ret .= "<strong>";
          $ret .= $ots;
          $ret .= "</strong>";
          $ret .= "</div>";
          $ret .= "<div class='nine columns'>";
          $ret .= "<a href='" . $this -> $var . "'>";
          $ret .=  $this -> $var;
          $ret .= "</a>";
          $ret .= "</div>";
          $ret .= "</div >\n";
        }
        return $ret;
       }

       function printRow( $ots, $var ){
        $ret = '';
        if ( $this -> $var  ){      
          $ret .= "<div class='row'>";
          $ret .= "<div class='three columns'>";
          $ret .= "<strong>";
          $ret .= $ots;
          $ret .= "</strong>";
          $ret .= "</div>";
          $ret .= "<div class='nine columns'>";
          $ret .=  $this -> $var;
          $ret .= "</div>";
          $ret .= "</div >\n";
        }
        return $ret;
       }

       function printSocialMediaRow( ){
        $ret = '';

          $ret .= "<div class='row'>";
          $ret .= "<div class='three columns'>";
          $ret .= "<strong>Social</strong>";
          $ret .= "</div>";

        if ( $this -> twitter  ){      
          $ret .= "<div class='two columns'>";
          $ret .= "<a href=' ". $this -> twitter  . " '>";
          $ret .= "Twitter"; 
          $ret .= "</a>";
          $ret .= "</div>";
        }
        if ( $this -> facebook  ){      
          $ret .= "<div class='two columns'>";
          $ret .= "<a href=' ". $this -> facebook  . " '>";
          $ret .= "Facebook"; 
          $ret .= "</a>";
          $ret .= "</div>";
        }
        if ( $this -> youtube  ){      
          $ret .= "<div class='two columns'>";
          $ret .= "<a href=' ". $this -> youtube  . " '>";
          $ret .= "Youtube"; 
          $ret .= "</a>";
          $ret .= "</div>";
        }
        if ( $this -> instagram  ){      
          $ret .= "<div class='two columns'>";
          $ret .= "<a href=' ". $this -> instagram  . " '>";
          $ret .= "Instagram"; 
          $ret .= "</a>";
          $ret .= "</div>";
        }

          $ret .= "</div >\n";
        return $ret;
       }

       function printDateBegRow( ){
        $ret = '';
          $ret .= "<div class='row'>";
          $ret .= "<div class='three columns'>";
          $ret .= "<strong>". "Päivämäärä:" . "</strong>";
          $ret .= "</div>";
          $ret .= "<div class='nine columns'>";
          $ret .=  $this -> alkupvm() . ' kello ' . $this -> alkuaika()   ;
          $ret .= "</div>";
          $ret .= "</div >\n";
        return $ret;
       }


       function printDateEndRow( ){
        $ret = '';
          $ret .= "<div class='row'>";
          $ret .= "<div class='three columns'>";
          $ret .= "<strong>". "Loppuu: " . "</strong>";
          $ret .= "</div>";
          $ret .= "<div class='nine columns'>";
          $ret .=  $this -> loppupvm(). ' kello ' . $this -> loppuaika() . '</p>'  ;;
          $ret .= "</div>";
          $ret .= "</div >\n";
        return $ret;
       }


       function printHappening(  ){

  
         $ret = '';
         $ret .= $this -> printLinkRow( 'Linkki tapahtumaan:', 'tapahtumalinkki'  );
         $ret .= $this -> printDateBegRow(   );
         $ret .= $this -> printDateEndRow(   );
         $ret .= $this -> printRow( 'Osoite:', 'osoite'  );
         $ret .= $this -> printLinkRow( 'Linkki karttaan:', 'karttalinkki'  );

         $ret .= $this -> printSocialMediaRow( );


         $ret .= "<div class='row'>";
         $ret .= "<div class='three columns'>";
         $ret .= "<strong>Kuvaus</strong>";
         $ret .= "</div>";
         $ret .= "<div class='nine columns'>";
         $ret .=  $this -> kuvaus; 
         $ret .= "</div>";
         $ret .= "</div >\n";
        return $ret;

  //echo '  <p>' . $tapahtuma -> osoite . '</p>';


         return $ret; 
       }
    

        function alkupvm(  ){
            return date( 'd.m.Y' , strtotime( $this -> pvm ));
        }
        function alkuaika(  ){
            return date( 'H.i' , strtotime( $this -> pvm ));
        }
        function alkutunti(  ){
            return date( 'H' , strtotime( $this -> pvm ));
        }
        function alkumin(  ){
            return date( 'i' , strtotime( $this -> pvm ));
        }
        function loppupvm(  ){
            return date( 'd.m.Y' , strtotime( $this -> loppupvm ));
        }
        function loppuaika(  ){
            return date( 'H.i' , strtotime( $this -> loppupvm ));
        }
        function lopputunti(  ){
            return date( 'H' , strtotime( $this -> loppupvm ));
        }
        function loppumin(  ){
            return date( 'i' , strtotime( $this -> loppupvm ));
        }

        function lisaakuva( $arg ){
            if ( !empty( $this -> kuvanimi )){

                echo '<img ' . $arg  .   '  src="kalenterikuvat/' . $this -> tmpnimi . '">';
            }
        }

    }
    

?>
