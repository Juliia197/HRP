<?php
//Logindaten
$servername = "localhost";
$username = "hrppr_1";
$password = "J49Wj7wUbSsKmNC5";
$dbname = "hrppr_db1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

session_start();

if($_SESSION["logged"] == true) {

  $id_gehoeft = $_SESSION['id_gehoeft'];
	  $auth = false;
	    
    $query = "SELECT id_gehoeft FROM person WHERE id_person = ?";
    $auth_sql = $conn->prepare($query);
    $auth_sql->bind_param("i", $_GET['id_person']);
    $auth_sql->execute();
    $result = $auth_sql->get_result();
    $auth_result = $result->fetch_assoc();
	    
	  if ($auth_result['id_gehoeft'] == $id_gehoeft) {
	      $auth = true;
	  }
	
	  else if ($_GET['id_person'] == 0) {
	    $auth = true;
	  }

?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <?php
      if ($_GET['id_person'] == 0){
        $name = "Person hinzufügen";
      } else {
        $personname_sql = "SELECT vorname, nachname FROM person WHERE id_person = ?";
        $personname_result = $conn->prepare($personname_sql);
        $personname_result->bind_param('i', $_GET['id_person']);
        $personname_result->execute();
        $personname_result = $personname_result->get_result();
        $personname_result = $personname_result->fetch_assoc();
        $name = $personname_result['vorname'] . " " . $personname_result['nachname'] . " bearbeiten";
      }
    ?>

    <title>HRP - <?php echo $name; ?></title>

  <!-- Favicon -->
  <link rel="shortcut icon" type="image/icon" href="images/favicon-16x16.png"/>

    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">

  </head>

  <body id="page-top">

    <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

      <a class="navbar-brand mr-1" href="dashboard.php">HRP-Projekt</a>

      <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
      </button>

      <!-- Navbar -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item no-arrow mx-1">
          <a class="nav-link" href="passwort.php">Passwort ändern</a>
        </li>
        <li class="nav-item no-arrow mx-1">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
        </li>
      </ul>

    </nav>

    <div id="wrapper">

      <!-- Sidebar -->
      <ul class="sidebar navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="gehoeft.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Gehöft</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="gueter.php">
            <i class="fas fa-fw fa-calculator"></i>
            <span>Güter</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="pferd.php">
            <i class="fas fa-fw fa-book"></i>
            <span>Pferde</span>
          </a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="person.php">
            <i class="fas fa-fw fa-address-book"></i>
            <span>Personen</span>
          </a>
        </li>
      </ul>

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Page Content -->
          <?php

            if ($auth == true) {

            $vorname = $_POST["vorname"];
            $nachname = $_POST["nachname"];
            $email = $_POST["email"];
            $telefonnr = $_POST["telefonnr"];
            $geburtsdatum = $_POST["geburtsdatum"];
            $strasse = $_POST["strasse"];
            $hausnr = $_POST["hausnr"];
            $plz = $_POST["plz"];
            $ort = $_POST["ort"];
            $land = $_POST["land"];
            $update = $_GET["id_person"];
            $update2 = $_GET["id_adresse"]; 

            // $test = $_POST['id_funktion'];
            
            // if (isset($_POST["id_funktion"]) && $_POST["id_funktion"] == 5){
            //   $test = $_POST['id_funktion'];
            // }


            $schonvorhanden_query = "SELECT * FROM person WHERE vorname = ? AND nachname = ? AND geburtsdatum = '$geburtsdatum' AND id_gehoeft = ? ";
            $schonvorhanden_sql = $conn->prepare($schonvorhanden_query);
            $schonvorhanden_sql->bind_param("ssi",$vorname, $nachname, $id_gehoeft);
            $schonvorhanden_sql->execute();
            $schonvorhanden = $schonvorhanden_sql->get_result();


              if ($update > 0){ //wird ausgeführt wenn die Person geändert wird
                $erfolg = 1;


                $adresseschonda_query = "SELECT id_adresse FROM adresse WHERE strasse = ? AND hausnr =? AND plz = '$plz' AND ort=? AND land='$land'";
                $adresseschonda_sql = $conn->prepare($adresseschonda_query);
                $adresseschonda_sql -> bind_param("sss",$strasse,$hausnr,$ort);
                $adresseschonda_sql->execute();
                $adresseschonda=$adresseschonda_sql->get_result();
                
                  $adressevergeben_query ="SELECT id_person FROM person WHERE id_adresse = ?";
                  $adressevergeben_sql = $conn->prepare($adressevergeben_query);
                  $adressevergeben_sql -> bind_param("i",$update2);
                  $adressevergeben_sql->execute();
                  $adressevergeben=$adressevergeben_sql->get_result();

                if($adresseschonda->num_rows==0){//wird ausgeführt die neue Adresse nicht vorhanden ist
                    if($adressevergeben->num_rows==1){ //wird ausgeführt wenn die alte Adresse nur einer Person zugeordnet ist
                      //adresse wird geändert
                      $adresseupdate_query = "UPDATE adresse SET strasse =?, hausnr = ?, plz='$plz', ort=?, land='$land' WHERE id_adresse=? ";
                      $adresseupdate_sql = $conn->prepare($adresseupdate_query);
                      $adresseupdate_sql -> bind_param("sssi",$strasse,$hausnr,$ort,$update2);
                      $adresseupdate_sql->execute();
                      $adresseupdate_result=$adresseupdate_sql->get_result();
  
                      //person wird geändert
                      $personupdate_query = "UPDATE person SET vorname = ?, nachname = ? , email =?, telefonnr = '$telefonnr', geburtsdatum = '$geburtsdatum' WHERE id_person=?";
                      $personupdate_sql = $conn->prepare($personupdate_query);
                      $personupdate_sql -> bind_param("sssi",$vorname,$nachname,$email,$update);
                      $personupdate_sql->execute();
                      $personupdate_result=$personupdate_sql->get_result();
                    }

                    else{//wird durchgeführt wenn die Adresse mehr als einer Person zugeordnet ist

                      //adresse wird neu hinzugefügt
                      $adressenew_query = "INSERT INTO adresse (id_adresse, strasse, hausnr, plz, ort, land) VALUES (NULL, ?, ?, $plz, ?, '$land')";
                      $adressenew_sql = $conn->prepare($adressenew_query);
                      $adressenew_sql -> bind_param("sss",$strasse,$hausnr,$ort);
                      $adressenew_sql->execute();
                      $adressenew_result=$adressenew_sql->get_result();       

                      //id der neuen Adresse wird abgerufen
                      $id_adresse_query1 = "SELECT id_adresse FROM adresse WHERE strasse = '$strasse' AND hausnr='$hausnr' AND plz='$plz' AND ort='$ort' AND land='$land'";
                      $id_adresse_sql1 = $conn->prepare($id_adresse_query1);
                      $id_adresse_sql1 -> bind_param("sss",$strasse,$hausnr,$ort);
                      $id_adresse_sql1->execute();
                      $id_adresse_result1=$id_adresse_sql1->get_result();

                      while($row_a = $id_adresse_result1->fetch_assoc()){   
                        $id_adresseh = $row_a['id_adresse'];

                        //Person wird geändert (mit neuer id_adresse)
                        $personupdate_query = "UPDATE person SET vorname = ?, nachname = ?, email =?, telefonnr = '$telefonnr', geburtsdatum = '$geburtsdatum' , id_adresse = ? WHERE id_person=? ";
                        $personupdate_sql = $conn->prepare($personupdate_query);
                        $personupdate_sql -> bind_param("sssii",$vorname,$nachname,$email,$id_adresseh,$update);
                        $personupdate_sql->execute();
                        $personupdate_result=$personupdate_sql->get_result();
                      }
                    }
                  }
                  else{//wird ausgefhürt wenn die neue Adresse im System vorhaden is

                    //person wird geändert mit der id der neuen adresse
                    $personupdate_query = "UPDATE person SET vorname =?, nachname = ?, email =?, telefonnr = '$telefonnr', geburtsdatum = '$geburtsdatum' , id_adresse = ? WHERE id_person=?";
                    $personupdate_sql = $conn->prepare($personupdate_query);
                    $personupdate_sql -> bind_param("sssii",$vorname,$nachname,$email,$update2,$update);
                    $personupdate_sql->execute();
                    $personupdate_result=$personupdate_sql->get_result();

                    if($adressevergeben->num_rows<2){ //wird ausgeführt wenn die alte Adresse nur einer Person zugeordnet ist
                      //alte Adresse wird gelöscht
                      $adresseloeschen_query = "DELETE FROM adresse WHERE id_adresse= ?";
                      $adresseloeschen_sql = $conn->prepare($adresseloeschen_query);
                      $adresseloeschen_sql -> bind_param("i",$update2);
                      $adresseloeschen_sql->execute();
                      $adresseloeschen_result=$adresseloeschen_sql->get_result();
                    }

                    else{//wird durchgeführt wenn die alte Adresse mehr als einer Person zugeordnet ist
                    }

                  }
                  $id_person_query = "SELECT id_person FROM person WHERE vorname = ? AND nachname = ? AND geburtsdatum = '$geburtsdatum' ";
                  $id_person_sql = $conn->prepare($id_person_query);
                  $id_person_sql -> bind_param("ss",$vorname,$nachname);
                  $id_person_sql->execute();
                  $id_person=$id_person_sql->get_result();

                  while ($id = $id_person->fetch_assoc()){

                    if (isset($_POST["id_funktion"]) && $_POST["id_funktion"] == 5){

                      $lieferant_query = "INSERT INTO beziehung (id_beziehung, id_person, id_funktion, id_pferd) VALUES(NULL,?, ?, NULL)";
                      $lieferant_sql = $conn->prepare($lieferant_query);
                      $lieferant_sql -> bind_param("ii", $id['id_person'], $_POST["id_funktion"]);
                      $lieferant_sql -> execute();
                      $lieferant = $lieferant_sql ->get_result();                          

                    }
                    else{
                      $istlieferant_query="SELECT id_beziehung FROM beziehung WHERE id_funktion = 5 AND id_person =?";
                      $istlieferant_sql = $conn->prepare($istlieferant_query);
                      $istlieferant_sql -> bind_param("i", $id['id_person']);
                      $istlieferant_sql -> execute();
                      $istlieferant = $istlieferant_sql ->get_result();

                      if($istlieferant->num_rows==1){
                        $deletelieferant_query = "DELETE FROM beziehung WHERE id_funktion= 5 AND id_person =?";
                        $deletelieferant_sql = $conn->prepare($deletelieferant_query);
                        $deletelieferant_sql -> bind_param("i", $id['id_person']);
                        $deletelieferant_sql -> execute();
                        $deletelieferant = $deletelieferant_sql ->get_result();
     
                      }
                    }
                  }
              }
              
              else { //wird bei hinzufügen einer Person ausgeführt

                if($schonvorhanden->num_rows==0){

                $id_adresse_query = "SELECT id_adresse FROM adresse WHERE strasse = ? AND hausnr=? AND plz='$plz' AND ort=? AND land='$land'";
                $id_adresse_sql = $conn->prepare($id_adresse_query);
                $id_adresse_sql -> bind_param("sss",$strasse,$hausnr,$ort);
                $id_adresse_sql->execute();
                $id_adresse_result=$id_adresse_sql->get_result();

                  if($id_adresse_result->num_rows==1){ //Adresse ist bereits vorhanden
                    while($row_i = $id_adresse_result->fetch_assoc()){
                      $id_adresse_übergabe = $row_i['id_adresse'];

                      $personnew_query = "INSERT INTO person (id_person, vorname, nachname, email, telefonnr, geburtsdatum, id_adresse, id_gehoeft) VALUES (NULL, ?, ?, ?, $telefonnr, '$geburtsdatum', '$id_adresse_übergabe','$id_gehoeft')";
                      $personnew_sql = $conn->prepare($personnew_query);
                      $personnew_sql -> bind_param("sss",$vorname,$nachname,$email);
                      $personnew_sql->execute();
                      $personnew_result=$personnew_sql->get_result();

                      $erfolg = 2;
                    }
                  }

                  else{    //Adresse muss hinzugefügt werden
                    $adressenew_query = "INSERT INTO adresse (id_adresse, strasse, hausnr, plz, ort, land) VALUES (NULL, ?,?, $plz, ?, '$land')";
                    $adressenew_sql = $conn->prepare($adressenew_query);
                    $adressenew_sql -> bind_param("sss",$strasse,$hausnr,$ort);
                    $adressenew_sql->execute();
                    $adressenew_result=$adressenew_sql->get_result();

                    $id_adresse_query1 = "SELECT id_adresse FROM adresse WHERE strasse = ? AND hausnr=? AND plz='$plz' AND ort=? AND land='$land'";
                    $id_adresse_sql1 = $conn->prepare($id_adresse_query1);
                    $id_adresse_sql1 -> bind_param("sss",$strasse,$hausnr,$ort);
                    $id_adresse_sql1->execute();
                    $id_adresse_result1=$id_adresse_sql1->get_result();

                    while($row_a = $id_adresse_result1->fetch_assoc()){  
                      $id_adresseh = $row_a['id_adresse'];
                      $personnew_query = "INSERT INTO person (id_person, vorname, nachname, email, telefonnr, geburtsdatum, id_adresse, id_gehoeft) VALUES (NULL, ?,?,?, $telefonnr, '$geburtsdatum', '$id_adresseh','$id_gehoeft')";
                      $personnew_sql = $conn->prepare($personnew_query);
                      $personnew_sql-> bind_param("sss",$vorname,$nachname,$email);
                      $personnew_sql->execute();
                      $personnew_result=$personnew_sql->get_result();


                      $erfolg = 2;
                    }
                  }
                  $id_person_query = "SELECT id_person FROM person WHERE vorname = ? AND nachname = ? AND geburtsdatum = '$geburtsdatum' ";
                  $id_person_sql = $conn->prepare($id_person_query);
                  $id_person_sql -> bind_param("ss",$vorname,$nachname);
                  $id_person_sql->execute();
                  $id_person=$id_person_sql->get_result();

                  while ($id = $id_person->fetch_assoc()){

                    if (isset($_POST["id_funktion"]) && $_POST["id_funktion"] == 5){

                      $lieferant_query = "INSERT INTO beziehung (id_beziehung, id_person, id_funktion, id_pferd) VALUES(NULL,?, ?, NULL)";
                      $lieferant_sql = $conn->prepare($lieferant_query);
                      $lieferant_sql -> bind_param("ii", $id['id_person'], $_POST["id_funktion"]);
                      $lieferant_sql -> execute();
                      $lieferant = $lieferant_sql ->get_result();                          

                    }
                  }

                }
                else{
                  $erfolg = 3;

                }
              }

//wiederanzeige der Bearbeiten

      if($erfolg==1){
        
        $person_query = "SELECT * FROM adresse, person WHERE adresse.id_adresse = person.id_adresse AND person.id_person = ?";
        $personsql = $conn->prepare($person_query);
        $personsql -> bind_param("i",$update);
        $personsql->execute();
        $person=$personsql->get_result();

        while($row_p = $person->fetch_assoc()){
          echo "<ol class=\"breadcrumb\">
                <li class=\"breadcrumb-item\">
                  <a href=\"dashboard.php\">Dashboard</a>
                </li>
                <li class=\"breadcrumb-item\">
                  <a href=\"person.php\">Personen</a>
                </li>
                <li class=\"breadcrumb-item active\">
                  Person ändern
                </li>
              </ol>";
          echo "<div class=\"alert alert-success\" role=\"alert\">Diese Person wurde geändert!</div>";
              echo "<h1>" . $row_p['vorname'] ." " . $row_p['nachname'] . "</h1> <hr><br>";
              echo "<form action=\"person-edited.php?id_person=" . $row_p["id_person"] . "&amp;id_adresse=" . $row_p["id_adresse"] . "\" method=\"post\">";


              echo "<label>Vorname</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["vorname"] . "\" name=\"vorname\" required ><br>";
              
              echo "<label>Nachname</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\"  value=\"" . $row_p["nachname"] . "\" name=\"nachname\" required ><br>";
              
              echo "<label>E-Mail</label>";
              echo "<input class=\"form-control\" type=\"email\" maxlength=\"45\" value=\"" . $row_p["email"] . "\" name=\"email\" required ><br>";
              
              echo "<label>Telefonnummer</label>";
              echo "<input class=\"form-control\" type=\"number\"  min=\"100000000\" max=\"99999999999999999999\" value=\"" . $row_p["telefonnr"] . "\" name=\"telefonnr\" required ><br>";
              
              echo "<label>Geburtsdatum</label>";
              echo "<input class=\"form-control\" type=\"date\" value=\"" . $row_p["geburtsdatum"] . "\" name=\"geburtsdatum\" required ><br>";

              echo "<hr>";

              echo "<br><h3> Adresse </h3>";

              echo "<label>Straße</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["strasse"] . "\" name=\"strasse\" required ><br>";

              echo "<label>Hausnummer</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["hausnr"] . "\" name=\"hausnr\" required ><br>";

              echo "<label>Postleitzahl</label>";
              echo "<input class=\"form-control\" type=\"number\"  min=\"1000\" max=\"99999\" value=\"" . $row_p["plz"] . "\" name=\"plz\" required ><br>";

              echo "<label>Ortschaft</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["ort"] . "\" name=\"ort\" required ><br>";

              echo "<label>Land</label>";
              echo "<select class=\"custom-select\" name=\"land\" required ><option value=\"DE\">Deutschland</option><option value=\"AT\">Österreich</option><option value=\"CH\">Schweiz</option></select>";

              $checkbox_query = "SELECT id_beziehung FROM beziehung WHERE id_funktion = 5 AND id_person =" . $row_p['id_person'];
              $checkbox=$conn->query($checkbox_query);

              $disabled_query = "SELECT * FROM verbrauchsgut WHERE id_person = " . $row_p['id_person'] . " AND id_gehoeft = $id_gehoeft";
              $disabled_result = $conn->query($disabled_query);
              if($disabled_result->num_rows > 0){
                $disabled = 'disabled="disabled"';
              } else {
                $disabled = '';
              }

              if ($checkbox->num_rows==1){
                echo '<br><hr><br><div class="form-check">
                <input class="form-check-input" type="checkbox" name="id_funktion" value="5" id="id_funktion" ' . $disabled . 'checked>
                <label class="form-check-label" for="id_funktion">
                  Hat die Rolle eines Lieferanten
                </label>
                </div><br>';
                echo "<hr><br>"; 
              }
              else{
                echo '<br><hr><br><div class="form-check">
                  <input class="form-check-input" type="checkbox" name="id_funktion" value="5" id="id_funktion">
                  <label class="form-check-label" for="id_funktion">
                    Hat die Rolle eines Lieferanten
                  </label>
                </div><br>';
                echo "<hr><br>";
              }
              
              echo "<div class=\"form-group\"></div>
              <div class=\"form-group\">
                <button type=\"submit\" class=\"btn btn-success\">Abschicken</button>
                <a class=\"btn btn-secondary\" href=\"person.php\" >Abbrechen</a>
              </div>";
              echo "</form>";

            }
        }

        else if($erfolg==3){
          while($row_v = $schonvorhanden->fetch_assoc()){
            echo "<h1>Diese Person ist bereits vorhanden!</h1><hr><br>";
            echo "<p>" . $row_v['vorname'] ." " . $row_v['nachname'] . "<br>Geburtsdatum: " . $row_v['geburtsdatum'] . " </p> <hr><br>";

            echo "<div class=\"form-group\"></div>
            <div class=\"form-group\">
            <a class=\"btn btn-secondary\" href=\"person-show.php?id_person=" . $row_v['id_person'] . "\" >Person anzeigen</a>
            <a class=\"btn btn-secondary\" href=\"person.php\" >zurück zur Übersicht</a>     
            <a class=\"btn btn-success\" href=\"person-edit.php?id_person=0\" >eine andere Person anlegen</a>    
            </div>";
          }
        }

        else{
          echo "<ol class=\"breadcrumb\">
                <li class=\"breadcrumb-item\">
                  <a href=\"dashboard.php\">Dashboard</a>
                </li>
                <li class=\"breadcrumb-item\">
                  <a href=\"person.php\">Personen</a>
                </li>
                <li class=\"breadcrumb-item active\">
                  Person hinzufügen
                </li>
              </ol>";
            echo "<div class=\"alert alert-success\" role=\"alert\">" . $vorname . " " . $nachname . " wurde hinzugefügt</div>";
            echo "<h1>Person hinzufügen </h1><hr><br>";
            echo "<form action=\"person-edited.php?id_person=0&amp;id_adresse=0\" method=\"post\">";
            
            
            echo "<label>Vorname</label>";
            echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"vorname\" required ><br>";
            
            echo "<label>Nachname</label>";
            echo "<input class=\"form-control\" type=\"text\"  maxlength=\"45\" name=\"nachname\" required ><br>";
            
            echo "<label>E-Mail</label>";
            echo "<input class=\"form-control\" type=\"email\" maxlength=\"45\" name=\"email\" required ><br>";
            
            echo "<label>Telefonnummer</label>";
            echo "<input class=\"form-control\" type=\"number\"  min=\"100000000\" max=\"99999999999999999999\" name=\"telefonnr\" required ><br>";
            
            echo "<label>Geburtsdatum</label>";
            echo "<input class=\"form-control\" type=\"date\"   min=\"1900-01-01\" max=\"" . date("Y-m-d") . "\" name=\"geburtsdatum\" required ><br>";

            echo "<hr>";

            echo "<br><h3> Adresse </h3>";

            echo "<label>Straße</label>";
            echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"strasse\" required ><br>";

            echo "<label>Hausnummer</label>";
            echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"hausnr\" required ><br>";

            echo "<label>Postleitzahl</label>";
            echo "<input class=\"form-control\" type=\"number\"  min=\"1000\" max=\"99999\" name=\"plz\" required ><br>";

            echo "<label>Ortschaft</label>";
            echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"ort\" required ><br>";

            echo "<label>Land</label>";
            echo "<select class=\"custom-select\" name=\"land\" required ><option value=\"DE\">Deutschland</option><option value=\"AT\">Österreich</option><option value=\"CH\">Schweiz</option></select><br>";
          
            echo '<br><hr><br><div class="form-check">
              <input class="form-check-input" type="checkbox" name="id_funktion" value="5" id="id_funktion">
              <label class="form-check-label" for="id_funktion">
                Hat die Rolle eines Lieferanten
              </label>
            </div><br>';

            echo "<div class=\"form-group\"></div>
            <div class=\"form-group\">
              <button type=\"submit\" class=\"btn btn-success\">Abschicken</button>
              <a class=\"btn btn-secondary\" href=\"person.php\" >Abbrechen</a>
            </div>";
        }
      }

      else {
        echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für diese Person!</div><br>';
      }
      
        ?>

        </div>
        <!-- /.container-fluid -->

        <!-- Sticky Footer -->
        <footer class="sticky-footer">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright © HRP-Projekt 2018/19 | <a href="impressum.html" target="_blank">Impressum & Datenschutzerklärung</a></span>
            </div>
          </div>
        </footer>

      </div>
      <!-- /.content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Logout</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Möchten Sie sich wirklich ausloggen?</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Nein</button>
            <a class="btn btn-primary" href="logout.php">Ja</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>