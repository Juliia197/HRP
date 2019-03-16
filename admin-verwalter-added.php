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
$admin_mail_array = array("alisa@hrp-projekt.de", "henrik@hrp-projekt.de", "jan@hrp-projekt.de", "julia@hrp-projekt.de", "kerstin@hrp-projekt.de", "demo_admin@hrp-projekt.de");

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

    <a class="navbar-brand mr-1" href="admin.php">HRP-Projekt</a>

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
            <i class="fas fa-fw fa-cogs"></i>
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
          // Prüfung ob Benutzer mit einer hinterlegten Admin-Mail angemeldet ist
          if (in_array($admin_mail, $admin_mail_array)) {

            // Setzen der Post-Paramter als Variabelen
            $email = $_POST['email'];
            $id_gehoeft = $_POST["id_gehoeft"];

            // SQL-Abfrage für Benutzer zu dieser E-Mail
            $id_benutzer_query = "SELECT id_benutzer FROM benutzer WHERE email = ?";
            $id_benutzer_sql = $conn->prepare($id_benutzer_query);
            $id_benutzer_sql->bind_param("s", $email);
            $id_benutzer_sql->execute();
            $id_benutzer_result = $id_benutzer_sql->get_result();
            $id_benutzer_fetch = $id_benutzer_result->fetch_assoc();

            // Prüfung, ob Benuter mit dieser E-Mail existiert
            if ($id_benutzer_result->num_rows == 0) {
              echo '<div class="alert alert-danger" role="alert">Die E-Mail ist keinem Benutzer zugeordnet!</div><hr>';
            }

            else {

            $id_benutzer = $id_benutzer_fetch["id_benutzer"];

            // SQL-Abfrage für Benutzer mit dieser E-Mail und dem Gehöft
            $check_sql = "SELECT COUNT(*) AS count FROM benutzer_verwaltet_gehoeft WHERE id_benutzer = $id_benutzer AND id_gehoeft =  ? ";
            $check = $conn->prepare($check_sql);
            $check->bind_param('i', $id_gehoeft);
            $check->execute();
            $check = $check->get_result();
            $check = $check->fetch_assoc();

            // Prüfung, ob Benutzer bereits Gehöftverwalter ist
            if ($check['count'] > 0) {
              echo '<div class="alert alert-danger" role="alert">Der Benutzer ist dem Gehöft bereits zugeordnet!</div><hr>';
            }
            
            else {

              // Insert für diesen Benutzer als Gehöftverwalter zu diesem Gehöft
              $insert_query = "INSERT INTO benutzer_verwaltet_gehoeft (id_benutzer, id_gehoeft) VALUES (?, ?)";
              $insert_sql = $conn->prepare($insert_query);
              $insert_sql->bind_param("ii", $id_benutzer, $id_gehoeft);
              $insert_sql->execute();

              echo '<div class="alert alert-success" role="alert">Der Benutzer wurde dem Gehöft zugeordnet!</div><hr>';
            }

            }

            echo '<a class="btn btn-secondary" href="admin-verwalter.php?id_gehoeft='. $id_gehoeft .'" >Zurück zur Übersicht</a>';

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