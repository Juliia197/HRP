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
        <li class="nav-item">
          <a class="nav-link" href="person.php">
            <i class="fas fa-fw fa-address-book"></i>
            <span>Personen</span>
          </a>
        </li>
      </ul>

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Page Content -->
          <h1>Admin</h1>
          <hr>

          <?php 
            $email = $_POST['email'];
            $id_gehoeft = $_POST["id_gehoeft"];

            $id_benutzer_query = "SELECT id_benutzer FROM benutzer WHERE email = ?";
            $id_benutzer_sql = $mysqli->prepare($id_benutzer_query);
            $id_benutzer_sql->bind_param("s", $email);
            $id_benutzer_sql->execute();
            $id_benutzer_result = $id_benutzer_sql->get_result();
            $id_benutzer_fetch = $id_benutzer_result->fetch_assoc();

            $id_benutzer = $id_benutzer_fetch["id_benutzer"];

            $check_sql = "SELECT COUNT(*) AS count FROM benutzer_verwaltet_gehoeft WHERE id_benutzer = $id_benutzer AND id_gehoeft =  $id_gehoeft ";
            $check = $conn->query($check_sql);
            $check = $check->fetch_assoc();

            if ($check['count'] > 0) {
              echo '<div class="alert alert-danger" role="alert">Der Benutzer ist dem Gehöft bereits zugeordnet!</div><hr>';
            }
            
            else {
              $insert_sql = " INSERT INTO benutzer_verwaltet_gehoeft (id_benutzer, id_gehoeft) VALUES ('$id_benutzer', '$id_gehoeft')";
              $insert = $conn->query($insert_sql);

              echo '<div class="alert alert-success" role="alert">Der Benutzer wurde dem Gehöft zugeordnet!</div><hr>';
            }

            echo '<a class="btn btn-secondary" href="admin.php" >zurück zur Übersicht</a>';

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
            <a class="btn btn-primary" href="login.php">Ja</a>
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