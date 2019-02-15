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

$preis_verbrauchsguttyp1_sql = "SELECT avg(verbrauchsgut.einkaufspreis) as preis_verbrauchsguttyp2 from verbrauchsgut WHERE id_gehoeft=1 AND id_verbrauchsguttyp=1";
$preis_verbrauchsguttyp1_result = $conn->query($preis_verbrauchsguttyp1_sql);
if ($preis_verbrauchsguttyp1_result->num_rows > 0) {
  while($row = $preis_verbrauchsguttyp1_result->fetch_assoc()) {
    $preis_verbrauchsguttyp1 = $row["preis_verbrauchsguttyp2"];
  }
}

$preis_verbrauchsguttyp2_sql = "SELECT avg(verbrauchsgut.einkaufspreis) as preis_verbrauchsguttyp2 from verbrauchsgut WHERE id_gehoeft=1 AND id_verbrauchsguttyp=2";
$preis_verbrauchsguttyp2_result = $conn->query($preis_verbrauchsguttyp2_sql);
if ($preis_verbrauchsguttyp2_result->num_rows > 0) {
  while($row = $preis_verbrauchsguttyp2_result->fetch_assoc()) {
    $preis_verbrauchsguttyp2 = $row["preis_verbrauchsguttyp2"];
  }
}

$preis_verbrauchsguttyp3_sql = "SELECT avg(verbrauchsgut.einkaufspreis) as preis_verbrauchsguttyp3 from verbrauchsgut WHERE id_gehoeft=1 AND id_verbrauchsguttyp=3";
$preis_verbrauchsguttyp3_result = $conn->query($preis_verbrauchsguttyp3_sql);
if ($preis_verbrauchsguttyp3_result->num_rows > 0) {
  while($row = $preis_verbrauchsguttyp3_result->fetch_assoc()) {
    $preis_verbrauchsguttyp3 = $row["preis_verbrauchsguttyp3"];
  }
}

$preis_verbrauchsguttyp4_sql = "SELECT avg(verbrauchsgut.einkaufspreis) as preis_verbrauchsguttyp4 from verbrauchsgut WHERE id_gehoeft=1 AND id_verbrauchsguttyp=4";
$preis_verbrauchsguttyp4_result = $conn->query($preis_verbrauchsguttyp4_sql);
if ($preis_verbrauchsguttyp4_result->num_rows > 0) {
  while($row = $preis_verbrauchsguttyp4_result->fetch_assoc()) {
    $preis_verbrauchsguttyp4 = $row["preis_verbrauchsguttyp4"];
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
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="dashboard.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">
              Güter
            </li>            
          </ol>
          <div class="container-fluid">
          <div class="row justify-content-end">
          <a class="btn btn-success" role="button" href="lieferung.php?id_verbrauchsgut=0">Lieferungen</a>
          </div>
          </div>
          
          <p>
          <div class="table-responsive">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
              <th>Typ</th>
              <th>Bestand</th>
              <th>Durchschnittspreis je kg</th>
              <th>Aktion</th>
            </tr>
            </thead>          
            <tbody>
            <?php  
              // SQL-Anfrage: Ergebnis ist stets eine Tabelle
              $verbrauchsguttyp = "SELECT * FROM verbrauchsguttyp";
              
              $query = $conn->query($verbrauchsguttyp) or die(mysql_error());

              while($fetch = mysqli_fetch_assoc($query)){
                echo '<tr>';
                  echo '<td>' . $fetch['verbrauchsguttypbez'] . '</td>';
                  echo '<td>' . $fetch['bestand'] . '</td>';
                  echo '<td>' ;
                    if( $fetch['id_verbrauchsguttyp'] == 1)
                      {
                        echo $preis_verbrauchsguttyp1;
                      }
                    else if( $fetch['id_verbrauchsguttyp'] == 2)
                      {
                        echo $preis_verbrauchsguttyp2;
                      }
                    else if( $fetch['id_verbrauchsguttyp'] == 3)
                      {
                        echo $preis_verbrauchsguttyp3;
                      }
                    else
                      {
                        echo $preis_verbrauchsguttyp4;
                      }
                  echo '</td>';
                  echo '<td> <a href="gut-show.php?id_verbrauchsguttyp=' . $fetch["id_verbrauchsguttyp"] . '" >Anzeigen</a> <br> </td>';                  
                  }
            ?>
            </tbody>                
          </table>
          </div>  
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

    <!-- Page level plugin JavaScript-->
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>


    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>

      <!-- Demo scripts for this page-->
  <script src="js/demo/datatables-demo.js"></script>

  </body>
</html>