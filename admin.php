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

    <title>HRP - Admin</title>

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
          
          <!--<div class="container">-->

          <!-- Page Content -->
          <h1>Admin</h1>
          <hr>
          <br>

          <?php 
          if (in_array($admin_mail, $admin_mail_array)) {
          ?>

          <h2>Gehöft hinzufügen</h2>
          <form action= "admin-gehoeft-added.php" method="post">
          <div class="form-group">
          <label for="gehoeftname">Gehöftname</label>
          <input class="form-control" id="gehoeftname" type="text" name="gehoeftname" required><br>
          <label for="strasse">Straße</label>
          <input class="form-control" id="strasse" type="text" name="strasse" required><br>
          <label for="hausnr">Hausnummer</label>
          <input class="form-control" id="hausnr" type="number" name="hausnr" required><br>
          <label for="plz">Postleitzahl</label>
          <input class="form-control" id="plz" type="number" name="plz" required><br>
          <label for="ort">Ortschaft</label>
          <input class="form-control" id="ort" type="text" name="ort" required><br>
          <label for="land">Land (als Kürzel, wie zum Beispiel Deutschland: DE)</label>
          <input class="form-control" id="land" type="text" name="land" required>
          </div>
          <button type="submit" class="btn btn-success">Gehöft hinzufügen</button>
          </form>
          
          <hr>
          <br>

          <h2>Gehöfte</h2>
          
          <div class="table-responsive">
          <table class="table table-bordered table-hover display" id="dataTable1" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr>
                <th>Gehöft-ID</th>
                <th>Gehöftname</th>
                <th>Ortschaft</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
          <?php

          $gehoeft_query = "SELECT id_gehoeft, gehoeftname, id_adresse FROM gehoeft";
          $gehoeft_sql = $conn->query($gehoeft_query);

          while ($gehoeft_fetch = $gehoeft_sql->fetch_assoc()) {
            $id_adresse = $gehoeft_fetch["id_adresse"];
            $adresse_query = "SELECT ort FROM adresse WHERE id_adresse = ?";
            $adresse_sql = $conn->prepare($adresse_query);
            $adresse_sql->bind_param("i", $id_adresse);
            $adresse_sql->execute();
            $adresse_result = $adresse_sql->get_result();
            $adresse_fetch = $adresse_result->fetch_assoc();

            echo '
            <tr>
            <td>'. $gehoeft_fetch["id_gehoeft"] .'</td>
            <td>'. $gehoeft_fetch["gehoeftname"] .'</td>
            <td>'. $adresse_fetch["ort"] .'</td>
            <td>
              <div class="d-sm-flex flex-row">
                <div><a class="btn btn-sm btn-primary" role="button" href="admin-verwalter.php?id_gehoeft=' . $gehoeft_fetch['id_gehoeft'] . '">Gehöftverwalter</a></div>
              </div>
            </td>
            
            </tr>
            ';
            
            $adresse_sql->close();
          }
          
          ?>
          
            </tbody>
          </table>
          </div> 

          <hr>
          <br>

          <h2>Benutzer</h2>

          <div class="table-responsive">
          <table class="table table-bordered table-hover display" id="dataTable2" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr>
                <th>Benutzer-ID</th>
                <th>E-Mail</th>
                <th>Aktiviert</th>
                <th>Registrierungsdatum</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
          <?php

          $benutzer_query = "SELECT id_benutzer, email, aktiviert, registrierungsdatum FROM benutzer";
          $benutzer_sql = $conn->query($benutzer_query);

          while ($benutzer_fetch = $benutzer_sql->fetch_assoc()) {

            $registrierungsdatum = new DateTime($benutzer_fetch["registrierungsdatum"]);

            echo '
            <tr>
            <td>'. $benutzer_fetch["id_benutzer"] .'</td>
            <td>'. $benutzer_fetch["email"] .'</td>
            <td>';
              if ($benutzer_fetch['aktiviert'] == 0) {
                echo 'Nein';
              }
              else {
                echo 'Ja';
              }
            echo '</td>
            <td>'. $registrierungsdatum->format('d.m.Y') .'</td>
            <td>
              <div class="d-sm-flex flex-row">
                <div><a class="btn btn-sm btn-danger" role="button" href="admin-benutzer-delete.php?id_benutzer=' . $benutzer_fetch['id_benutzer'] . '" onclick="return checkDelete()">Löschen</a></div>
              </div>
            </td>
            
            </tr>
            ';
            
          }
          
          ?>
          
            </tbody>
          </table>
          </div> 
          <br>

        <?php
          }

          else {
            echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für die Admin-Funktionen!</div>';
          }

        ?>
        
        <!-- </div> -->
        <!-- /.container -->
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

    <!-- Page level plugin JavaScript-->
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>


    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>

      <!-- Demo scripts for this page-->
  <script src="js/demo/datatables-demo.js"></script>

  <!-- JavaScript for Delete-Confirmation -->
  <script>
    function checkDelete(){
      return confirm('Benutzer endgültig löschen?')
    }
  </script>

  <!-- JavaScript für mehrere DataTables auf einer Seite -->
  <script>
      $(document).ready(function() {
      $('table.display').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
        }
      });
      });
    </script>

  </body>

</html>