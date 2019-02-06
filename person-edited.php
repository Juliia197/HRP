<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hrppr_db1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

    <title>HRP-Projekt</title>

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

            $schonvorhanden_sql = "SELECT * FROM person WHERE vorname = '$vorname' AND nachname = '$nachname' AND geburtsdatum = '$geburtsdatum' ";
            $schonvorhanden = $conn->query($schonvorhanden_sql);
//             echo $schonvorhanden_sql;

  	        if($schonvorhanden->num_rows==0){


              if ($update > 0){
                echo "<ol class=\"breadcrumb\">
                  <li class=\"breadcrumb-item\">
                    <a href=\"dashboard.php\">Dashboard</a>
                  </li>
                  <li class=\"breadcrumb-item\">
                    <a href=\"person.php\">Personen</a>
                  </li>
                  <li class=\"breadcrumb-item active\">
                    Person bearbeiten
                  </li>
                </ol>"; 

                $update_sql = "SELECT * FROM person, adresse WHERE id_person=" . $_GET["id_person"] . " AND person.id_adresse = adresse.id_adresse AND person.id_adresse=".  $_GET["id_adresse"];
                $update_result = $conn->query($update_sql);

                while($row_u = $update_result->fetch_assoc()){
                  echo "<h1>Änderung der Person: " . $row_u["vorname"] . " " . $row_u["nachname"] . "</h1>";

                  $adresseupdate_sql = "UPDATE adresse SET strasse ='$strasse', hausnr = '$hausnr', plz='$plz', ort='$ort', land='$land' WHERE id_adresse=$update2 ";
                  $adresseupdate_result = $conn->query($adresseupdate_sql);

                  $personupdate_sql = "UPDATE person SET vorname = '$vorname', nachname = '$nachname', email ='$email', telefonnr = '$telefonnr', geburtsdatum = '$geburtsdatum' WHERE id_person=$update";
                  $personupdate_result = $conn->query($personupdate_sql);

                  echo "<p>Ihre Person wurde geändert!</p>";

                }
              }
              
              else {
                echo "<ol class=\"breadcrumb\">
                      <li class=\"breadcrumb-item\">
                        <a href=\"dashboard.php\">Dashboard</a>
                      </li>
                      <li class=\"breadcrumb-item\">
                        <a href=\"person.php\">Personen</a>
                      </li>
                      <li class=\"breadcrumb-item active\">
                        Person erstellen
                      </li>
                    </ol>";
                echo "<h1>Neue Person hinzufügen</h1>";

                $id_adresse_sql = "SELECT id_adresse FROM adresse WHERE strasse = '$strasse' AND hausnr='$hausnr' AND plz='$plz' AND ort='$ort' AND land='$land'";
                $id_adresse_result = $conn->query($id_adresse_sql);

                  if($id_adresse_result->num_rows>0){
                    while($row_i = $id_adresse_result->fetch_assoc()){

                      $id_adresse_übergabe = $row_i['id_adresse'];
                      $personnew_sql = "INSERT INTO person (id_person, vorname, nachname, email, telefonnr, geburtsdatum, id_adresse) VALUES (NULL, '$vorname', '$nachname', '$email', $telefonnr, '$geburtsdatum', '$id_adresse_übergabe')";
                      $personnew_result = $conn->query($personnew_sql);
                    }
                  }


                  else{    
                    $adressenew_sql = "INSERT INTO adresse (id_adresse, strasse, hausnr, plz, ort, land) VALUES (NULL, '$strasse', '$hausnr', $plz, '$ort', '$land')";
                    $adressenew_result = $conn->query($adressenew_sql);

                    $id_adresse_sql1 = "SELECT id_adresse FROM adresse WHERE strasse = '$strasse' AND hausnr='$hausnr' AND plz='$plz' AND ort='$ort' AND land='$land'";
                    $id_adresse_result1 = $conn->query($id_adresse_sql1);

                    while($row_a = $id_adresse_result1->fetch_assoc()){   
                      $id_adresseh = $row_a['id_adresse'];
                      $personnew_sql = "INSERT INTO person (id_person, vorname, nachname, email, telefonnr, geburtsdatum, id_adresse) VALUES (NULL, '$vorname', '$nachname', '$email', $telefonnr, '$geburtsdatum', '$id_adresseh')";
                      $personnew_result = $conn->query($personnew_sql);
                    }

                  }
                echo 'die Person wurde hinzugefügt';
                }

            }
            else{

              // $personvsql = "SELECT vorname, nachname,geburtstag FROM person WHERE vorname = '$vorname' AND nachname = '$nachname' AND geburtsdatum = '$geburtsdatum'";
              // $personv = $conn->query($personvsql);
              // echo '<br>else<br>';
              // echo $personvsql;
    
              while($row_v = $schonvorhanden->fetch_assoc()){
                echo "<h1>Diese Person ist schon vorhanden</h1><hr>";
                echo "<p>" . $row_v['vorname'] ." " . $row_v['nachname'] . "<br>Geburtsdatum: " . $row_v['geburtsdatum'] . " </p> <hr>";

                echo "<div class=\"form-group\"></div>
                  <div class=\"form-group\">
                    <a class=\"btn btn-secondary\" href=\"person-show.php?id_person=" . $row_v['id_person'] . "\" >Person anzeigen</a>
                    <a class=\"btn btn-secondary\" href=\"person.php\" >zurück zur Übersicht</a>     
                    <a class=\"btn btn-secondary\" href=\"person-edit.php?id_person=0\" >eine andere Person anlegen</a>    
                  </div>";
              }

            }
            

            
          ?>

        </div>
        <!-- /.container-fluid -->

        <!-- Sticky Footer -->
        <footer class="sticky-footer">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright © HRP-Projekt 2018/19 | <a href="/impressum.html">Impressum & Datenschutzerklärung</a></span>
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
            <a class="btn btn-primary" href="login.html">Ja</a>
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