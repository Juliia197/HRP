<?php
//Logindaten
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
        <li class="nav-item active">
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

          <!-- Leiste zur Darstellung der aktuellen Position auf der Seite -->
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="dashboard.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
              <a href="person.php">Personen</a>
            </li>
            <li class="breadcrumb-item active">
              Person löschen
            </li>
          </ol>

          <?php  
            //Übergebene Daten werden in Variablen gespeichert
            $id_person = $_GET['id_person'];
            $id_delete = $_GET['id_delete'];

            //Daten zur Person werden aufgerufen          
            $personsql = "SELECT * FROM person, adresse WHERE adresse.id_adresse = person.id_adresse AND person.id_person = " . $_GET['id_person'];
            $person = $conn->query($personsql);

            if($id_delete==1){ //wird ausgeführt wenn die Person gelöscht werden kann

              //success Balken 
              echo '<div class="alert alert-success" role="alert"> Diese Person kann gelöscht werden!</div><hr>';

              while($row_p = $person->fetch_assoc()){
                echo "<h1>" . $row_p['vorname'] ." " . $row_p['nachname'] . "</h1> <hr>";
                
                echo "<p>E-Mail: " . $row_p['email'] . "</p>";
                echo "<p>Telefonnummer: " . $row_p['telefonnr'] . "</p>";
                echo "<p>Geburtsdatum: " . $row_p['geburtsdatum'] . "</p>";
    
                echo "<br><h3> Adresse </h3>";    
                echo "<p>Straße: " . $row_p['strasse'] . "</p>";
                echo "<p>Hausnummer: " . $row_p['hausnr'] . "</p>";
                echo "<p>Postleitzahl: " . $row_p['plz'] . "</p>";
                echo "<p>Ortschaft: " . $row_p['ort'] . "</p>";
                echo "<p>Land: " . $row_p['land'] . "</p>"; 
                
                echo "<hr>";

                echo "<div class=\"form-group\"></div>
                <div class=\"form-group\">
                <a class=\"btn btn-secondary\" href=\"person-edit.php?id_person=" . $row_p['id_person'] . "\" >Person bearbeiten</a>
                <a class=\"btn btn-secondary\" href=\"person-deleted.php?id_person=" . $row_p['id_person'] . "\" >Löschen</a>
                <a class=\"btn btn-secondary\" href=\"person.php\" >Abbrechen</a> </div>";
              }       
              


            }
            else{ //wird ausgeführt wenn die Person nicht gelöscht werden kann (wegen Beziehungen zu Pferden oder Lieferungen)
              //Fehlermeldung
              echo '<div class="alert alert-danger" role="alert"> Diese Person kann nicht gelöscht werden, da ihr Pferde oder Lieferungen zugeordnet sind!</div><hr>';
    
              while($row_p = $person->fetch_assoc()){

                echo "<h1>" . $row_p['vorname'] ." " . $row_p['nachname'] . "</h1> <hr>";
                
                echo "<p>E-Mail: " . $row_p['email'] . "</p>";
                echo "<p>Telefonnummer: " . $row_p['telefonnr'] . "</p>";
                echo "<p>Geburtsdatum: " . $row_p['geburtsdatum'] . "</p>";
    
                echo "<br><h3> Adresse </h3>";
    
                echo "<p>Straße: " . $row_p['strasse'] . "</p>";
                echo "<p>Hausnummer: " . $row_p['hausnr'] . "</p>";
                echo "<p>Postleitzahl: " . $row_p['plz'] . "</p>";
                echo "<p>Ortschaft: " . $row_p['ort'] . "</p>";
                echo "<p>Land: " . $row_p['land'] . "</p>"; 
                
                echo "<hr>";

                echo "<div class=\"form-group\"></div>
                <div class=\"form-group\">
                <a class=\"btn btn-secondary\" href=\"person-edit.php?id_person=" . $row_p['id_person'] . "\" >Person bearbeiten</a>
                <a class=\"btn btn-secondary\" href=\"person-pferd.php?id_person=" . $row_p['id_person'] . "\" >Pferde anzeigen</a>
                <a class=\"btn btn-secondary\" href=\"person.php\" >Abbrechen</a> </div>";

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

    <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
  <script src="js/demo/datatables-demo.js"></script>

  </body>

</html>
