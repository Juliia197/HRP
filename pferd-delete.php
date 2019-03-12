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

if($_SESSION["logged"] == true) {

  $id_gehoeft = $_SESSION['id_gehoeft'];
  $auth = false;

  $id_pferd = $_GET["id_pferd"];

  $query = "SELECT id_gehoeft FROM box WHERE id_pferd = ?";
  $auth_sql = $conn->prepare($query);
  $auth_sql->bind_param("i", $id_pferd);
  $auth_sql->execute();
  $result = $auth_sql->get_result();
  $auth_result = $result->fetch_assoc();
  
  if ($auth_result["id_gehoeft"] == $id_gehoeft) {
    $auth = true;

    $pferd_delete1_query = "DELETE FROM beziehung WHERE id_pferd =  ?";
    $pferd_delete1_sql = $conn->prepare($pferd_delete1_query);
    $pferd_delete1_sql->bind_param("i", $id_pferd);
    $pferd_delete1_sql->execute();
    $pferd_delete1_sql->close();

    $pferd_delete2_query = "DELETE FROM pferd_frisst_verbrauchsguttyp WHERE id_pferd =  ?";
    $pferd_delete2_sql = $conn->prepare($pferd_delete2_query);
    $pferd_delete2_sql->bind_param("i", $id_pferd);
    $pferd_delete2_sql->execute();
    $pferd_delete2_sql->close();

    $pferd_delete3_query = "UPDATE box SET id_pferd = NULL WHERE id_pferd = ?";
    $pferd_delete3_sql = $conn->prepare($pferd_delete3_query);
    $pferd_delete3_sql->bind_param("i", $id_pferd);
    $pferd_delete3_sql->execute();
    $pferd_delete3_sql->close();

    $pferd_delete4_query = "DELETE FROM pferd WHERE id_pferd =  ?";
    $pferd_delete4_sql = $conn->prepare($pferd_delete4_query);
    $pferd_delete4_sql->bind_param("i", $id_pferd);
    $pferd_delete4_sql->execute();
    $pferd_delete4_sql->close();
  }

  $auth_sql->close();


?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>HRP - Pferd gelöscht</title>

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
        <li class="nav-item active">
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

          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="dashboard.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
              <a href="pferd.php">Pferde</a>
            </li>
            <li class="breadcrumb-item active">
              Pferd löschen
            </li>
          </ol>

          <?php 
          
          if ($auth == true) {
          echo '<div class="alert alert-success" role="alert"> Das Pferd wurde gelöscht!</div><hr>
          <a class="btn btn-secondary" href="pferd.php">Zurück zur Übersicht</a>';
          }
          else {
            echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für dieses Pferd!</div><hr>';
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

    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>