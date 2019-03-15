<?php
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

// User-E-Mail aus der Session (vom Login) holen und setzen des Arrays, mit zugelassenen Admin-Mail-Adressen
$admin_mail  = $_SESSION["mail"];
$admin_mail_array = array("alisa@hrp-projekt.de", "henrik@hrp-projekt.de", "jan@hrp-projekt.de", "julia@hrp-projekt-de", "kerstin@hrp-projekt.de", "demo_admin@hrp-projekt.de");

?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>HRP - Admin Gehöft hinzufügen</title>

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

      <a class="navbar-brand mr-1" href="dashboard.php">HRP - Admin</a>

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
        <li class="nav-item active">
          <a class="nav-link" href="admin.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Admin</span>
          </a>
        </li>
      </ul>

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Page Content -->
          <h1>Admin</h1>
          <hr>

          <?php 
          // Prüfung, ob Benutzer mit einer hinterlegten Admin-Mail angemeldet ist
          if (in_array($admin_mail, $admin_mail_array)) {

            // Setzen der Post-Parameter als Variablen
            $gehoeftname = $_POST["gehoeftname"];
            $strasse = $_POST['strasse'];
            $hausnr = $_POST['hausnr'];
            $plz = $_POST['plz'];
            $ort = $_POST['ort'];
            $land = $_POST['land'];

            // SQL-Abfrage für eingebene Adresse
            $adresse_vorhanden_query = "SELECT id_adresse FROM adresse WHERE strasse = ? AND  hausnr = ? AND plz = ? AND ort = ? AND land = ?";
            $adresse_vorhanden_sql = $conn->prepare($adresse_vorhanden_query);
            $adresse_vorhanden_sql->bind_param("siiss", $strasse, $hausnr, $plz, $ort, $land);
            $adresse_vorhanden_sql->execute();
            $adresse_vorhanden_result = $adresse_vorhanden_sql->get_result();
            $adresse_vorhanden_fetch = $adresse_vorhanden_result->fetch_assoc();

            // Prüfung ob Adresse schon vorhanden
            if ($adresse_vorhanden_result->num_rows == 0) {
              // Insert von neu anzulegender Adresse
              $adresse_insert_query = "INSERT INTO adresse (strasse, hausnr, plz, ort, land) VALUES (?, ?, ?, ?, ?)";
              $adresse_insert_sql = $conn->prepare($adresse_insert_query);
              $adresse_insert_sql->bind_param("siiss", $strasse, $hausnr, $plz, $ort, $land);
              $adresse_insert_sql->execute();

              $id_adresse = $adresse_insert_sql->insert_id;

              // Insert von neuem Gehöft mit gerade angelegter Adresse
              $gehoeft_insert_query = "INSERT INTO gehoeft (gehoeftname, id_adresse) VALUES (?, ?)";
              $gehoeft_insert_sql = $conn->prepare($gehoeft_insert_query);
              $gehoeft_insert_sql->bind_param("si", $gehoeftname, $id_adresse);
              $gehoeft_insert_sql->execute();
              $id_gehoeft = $gehoeft_insert_sql->insert_id;
              
              // Insert von Bestand für dieses Gehöft
              for ($i=1; $i<=4; $i++) {
              $insert_bestand_sql = " INSERT INTO gehoeft_besitzt_verbrauchsguttyp (id_verbrauchsguttyp, id_gehoeft, bestand, datum) VALUES ('$i', '$id_gehoeft', '0', '0000-00-00') ";
              $insert_bestand = $conn->query($insert_bestand_sql);
              }
              echo '<div class="alert alert-success" role="alert">Das Gehöft wurde hinzugefügt</div><hr>';
            }

            // Adresse nicht vorhanden, Adressen-ID wird übernommen
            else {
              $id_adresse = $adresse_vorhanden_fetch["id_adresse"];

              // SQL-Abfrage für Gehöfte zu dieser Adresse
              $check_sql = "SELECT COUNT(id_adresse) AS count FROM gehoeft WHERE id_adresse =  $id_adresse ";
              $check = $conn->query($check_sql);
              $check = $check->fetch_assoc();
              
              // Prüfung, ob für diese Adresse schon ein Gehöft existiert
              if ($check['count'] > 0) {
                echo '<div class="alert alert-danger" role="alert">Zu dieser Adresse gibt es bereits ein Gehöft!</div><hr>';
              }
              
              else {
                $gehoeft_insert_query = "INSERT INTO gehoeft (gehoeftname, id_adresse) VALUES (?, ?)";
                $gehoeft_insert_sql = $conn->prepare($gehoeft_insert_query);
                $gehoeft_insert_sql->bind_param("si", $gehoeftname, $id_adresse);
                $gehoeft_insert_sql->execute();
                $id_gehoeft = $gehoeft_insert_sql->insert_id;
                
                // Insert von Bestand für dieses Gehöft
                for ($i=1; $i<=4; $i++) {
                $insert_bestand_sql = " INSERT INTO gehoeft_besitzt_verbrauchsguttyp (id_verbrauchsguttyp, id_gehoeft, bestand, datum) VALUES ('$i', '$id_gehoeft', '0', '0000-00-00') ";
                $insert_bestand = $conn->query($insert_bestand_sql);
                }
                echo '<div class="alert alert-success" role="alert">Das Gehöft wurde hinzugefügt</div><hr>';
              }
              
            }
            ?>
            
            <a class="btn btn-secondary" href="admin.php" >Zurück zur Übersicht</a>
            
            <?php
          }

          else {
            echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für die Admin-Funktionen!</div>';
          }

        ?>

        </div>
        <!-- /.container-fluid -->

        <!-- Sticky Footer -->
        <footer class="sticky-footer">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright © HRP-Projekt 2018/19 | <a href="impressum.html">Impressum & Datenschutzerklärung</a></span>
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