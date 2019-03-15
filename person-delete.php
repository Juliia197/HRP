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

      //id_adresse der Person herausfinden
      $id_adresse_query = "SELECT id_adresse FROM person WHERE id_person=?";
      $id_adresse_sql = $conn->prepare($id_adresse_query);
      $id_adresse_sql->bind_param("i", $_GET['id_person']);
      $id_adresse_sql->execute();
      $id_adresse = $id_adresse_sql->get_result();
    

      //Löschen der Person aus der Datenbank
      $personloeschen_query = "DELETE FROM person WHERE id_person=?";
      $personloeschen_sql = $conn->prepare($personloeschen_query);
      $personloeschen_sql->bind_param("i", $_GET['id_person']);
      $personloeschen_sql->execute();
      $personloeschen_result = $personloeschen_sql->get_result();

      while($row_x = $id_adresse->fetch_assoc()){   
        $wieoftda_query = "SELECT id_person FROM person WHERE id_adresse = ?";
        $wieoftda_sql = $conn->prepare($wieoftda_query);
        $wieoftda_sql->bind_param("i", $row_x["id_adresse"]);
        $wieoftda_sql->execute();
        $wieoftda = $wieoftda_sql->get_result();

        if($wieoftda->num_rows==0){ //wird durchgeführt wenn die Adresse keiner weiteren Person zugeordnet ist
          $adressebeigehoeft_query = "SELECT id_gehoeft FROM gehoeft WHERE id_adresse = " . $row_x['id_adresse'];
          $adressebeigehoeft = $conn->query($adressebeigehoeft);

          if($gehoeftnichtda->num_rows==0){

            $adresseloeschen_query = "DELETE FROM adresse WHERE id_adresse=? ";
            $adresseloeschen_sql = $conn->prepare($adresseloeschen_query);
            $adresseloeschen_sql->bind_param("i", $row_x["id_adresse"]);
            $adresseloeschen_sql->execute();
            $adresseloeschen_result = $adresseloeschen_sql->get_result();

          }
          else{
          }
        }
        else{
        }
      }
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


    <title> HRP - Person gelöscht </title>

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
          if ($auth == true) {
            //Success Balken
            echo '<div class="alert alert-success" role="alert"> Die Person wurde gelöscht!</div><hr><br>';
            echo "<div class=\"form-group\"></div>
            <div class=\"form-group\">
            <a class=\"btn btn-secondary\" href=\"person.php\" >zurück zur Übersicht</a>
            </div";
          }
          else {
            echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für diese Person!</div><hr><br>';
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
