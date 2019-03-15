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

  $id_gehoeft = $_SESSION["id_gehoeft"];

?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>HRP - Lieferungen</title>

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
        <li class="nav-item active">
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
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="dashboard.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
              <a href="gueter.php">Güter</a>
            </li>
            <li class="breadcrumb-item active">
              Lieferungen
            </li>            
          </ol>
          <h1 class="float-left">Lieferungen</h1>
          <div class="float-right">
          <a class="btn btn-secondary" role="button" href="gueter.php">Zu den Gütern</a>
          </div>
          <br>
          <br>
          <hr>
          <div class="d-flex flex-row-reverse">
            <div class="p-2">
              <a class="btn btn-success" role="button" href="gut-edit.php?id_verbrauchsgut=0">Hinzufügen</a>
            </div>
          </div>
          
          
          
          <p>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-light">
            <tr>
              <th>Gut</th>
              <th>Lieferung</th>  
              <th>Lieferdatum</th>
              <th>Menge in kg</th>
              <th>Einkaufspreis in €/kg</th>
              <th>Lieferant</th>
              <th></th>
            </tr>
            </thead>          
            <tbody>
            <?php  
              // SQL-Anfrage: Ergebnis ist stets eine Tabelle
              $verbrauchsgut = "SELECT verbrauchsguttyp.verbrauchsguttypbez, verbrauchsgut.verbrauchsgutbez, verbrauchsgut.menge, verbrauchsgut.einkaufspreis, verbrauchsgut.id_person, verbrauchsgut.id_verbrauchsgut, DATE_FORMAT(verbrauchsgut.lieferdatum, '%d.%m.%Y') as lieferdatum FROM verbrauchsgut, verbrauchsguttyp WHERE verbrauchsgut.id_verbrauchsguttyp = verbrauchsguttyp.id_verbrauchsguttyp AND id_gehoeft = $id_gehoeft";
              $query = $conn->query($verbrauchsgut) or die(mysql_error());

              while($fetch = mysqli_fetch_assoc($query)){
                echo '<tr>';
                  echo '<td>' . $fetch['verbrauchsguttypbez'] . '</td>';
                  echo '<td>' . $fetch['verbrauchsgutbez'] . '</td>';
                  echo '<td>' . $fetch['lieferdatum'] . '</td>';
                  echo '<td>' . $fetch['menge'] . '</td>';
                  echo '<td>' . $fetch['einkaufspreis'] . '</td>';
                  $lieferant = 'SELECT person.vorname, person.nachname From person, verbrauchsgut  WHERE verbrauchsgut.id_person = person.id_person AND verbrauchsgut.id_person = '.$fetch['id_person'];
                  $query1 = $conn->query($lieferant) or die (mysql_error());
                    if($fetch1 = mysqli_fetch_assoc($query1)){
                      echo '<td>' . $fetch1['vorname'] . ' ' . $fetch1['nachname'] . '</td>'  ;
                    }
                                
                  echo '<td>
                  <div class="d-sm-flex flex-row">
                  <div><a class="btn btn-sm btn-primary" role="button" href="gut-edit.php?id_verbrauchsgut=' . $fetch["id_verbrauchsgut"] . '" >Bearbeiten</a></div>
                  <div class="ml-0 ml-sm-2 mt-1 mt-sm-0"><a class="btn btn-sm btn-danger" role="button" href="gut-delete.php?id_verbrauchsgut=' . $fetch["id_verbrauchsgut"] . '" >Löschen</a></div>
                  </div>
                  </td>
                  </tr>';
                }

            ?>
            </tbody>                

          </table>
          <br>
          </div>          
        

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

    <!-- Page level plugin JavaScript-->
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script>
    $(document).ready(function() {
    $('#dataTable').DataTable( {
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
        }
    } );
} );
    </script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>


    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>

      <!-- Demo scripts for this page-->
  <script src="js/demo/datatables-demo.js"></script>

  </body>
</html>

<?php
}

else {

  header('location:login.php');

}

?>