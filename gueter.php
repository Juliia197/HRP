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

  /* Berechnung der Durchschnittspreise der Verbrauchsgüter */
  $preis_verbrauchsguttyp1_sql = "SELECT ROUND(avg(verbrauchsgut.einkaufspreis),2) as preis_verbrauchsguttyp2 from verbrauchsgut WHERE id_gehoeft=$id_gehoeft AND id_verbrauchsguttyp=1";
  $preis_verbrauchsguttyp1_result = $conn->query($preis_verbrauchsguttyp1_sql);
  if ($preis_verbrauchsguttyp1_result->num_rows > 0) {
    while($row = $preis_verbrauchsguttyp1_result->fetch_assoc()) {
      $preis_verbrauchsguttyp1 = $row["preis_verbrauchsguttyp2"];
    }
  }

  $preis_verbrauchsguttyp2_sql = "SELECT ROUND(avg(verbrauchsgut.einkaufspreis),2) as preis_verbrauchsguttyp2 from verbrauchsgut WHERE id_gehoeft=$id_gehoeft AND id_verbrauchsguttyp=2";
  $preis_verbrauchsguttyp2_result = $conn->query($preis_verbrauchsguttyp2_sql);
  if ($preis_verbrauchsguttyp2_result->num_rows > 0) {
    while($row = $preis_verbrauchsguttyp2_result->fetch_assoc()) {
      $preis_verbrauchsguttyp2 = $row["preis_verbrauchsguttyp2"];
    }
  }

  $preis_verbrauchsguttyp3_sql = "SELECT ROUND(avg(verbrauchsgut.einkaufspreis),2) as preis_verbrauchsguttyp3 from verbrauchsgut WHERE id_gehoeft=$id_gehoeft AND id_verbrauchsguttyp=3";
  $preis_verbrauchsguttyp3_result = $conn->query($preis_verbrauchsguttyp3_sql);
  if ($preis_verbrauchsguttyp3_result->num_rows > 0) {
    while($row = $preis_verbrauchsguttyp3_result->fetch_assoc()) {
      $preis_verbrauchsguttyp3 = $row["preis_verbrauchsguttyp3"];
    }
  }

  $preis_verbrauchsguttyp4_sql = "SELECT ROUND(avg(verbrauchsgut.einkaufspreis),2) as preis_verbrauchsguttyp4 from verbrauchsgut WHERE id_gehoeft=$id_gehoeft AND id_verbrauchsguttyp=4";
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

    <title>HRP - Güter</title>

  <!-- Favicon -->
  <link rel="shortcut icon" type="image/icon" href="images/favicon-16x16.png"/>

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
            <li class="breadcrumb-item active">
              Güter
            </li>            
          </ol>
          
          <h1 class="float-left">Güter</h1>
          <div class="d-flex flex-row-reverse">
            <div class="p-2"><a class="btn btn-secondary" role="button" href="lieferung.php">Zu den Lieferungen</a></div>
            <div class="p-2"><a class="btn btn-success" role="button" href="gut-edit.php?id_verbrauchsgut=0">Lieferung hinzufügen</a></div>
          </div>
          <hr>
          <br>

          <div class="d-flex flex-row-reverse">
            <div class="p-2"><a class="btn btn-secondary" role="button" href="gueter-bestand.php">Zur Bestandsaktualisierung</a></div>
          </div>
          <hr>
          <br>

          <!-- Tabelle über Verbrauchsgüter im Überblick -->
          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
              <thead class="thead-light">
              <tr>
                <th>Typ</th>
                <th>Bestand in kg</th>
                <th>Durchschnittspreis in €/kg</th>
                <th></th>
              </tr>
              </thead>          
              <tbody>
              <?php  
                // SQL-Anfrage: Ergebnis ist stets eine Tabelle
                $verbrauchsguttyp = "SELECT verbrauchsguttyp.id_verbrauchsguttyp, verbrauchsguttyp.verbrauchsguttypbez, gehoeft_besitzt_verbrauchsguttyp.bestand FROM verbrauchsguttyp, gehoeft_besitzt_verbrauchsguttyp WHERE gehoeft_besitzt_verbrauchsguttyp.id_gehoeft = $id_gehoeft AND verbrauchsguttyp.id_verbrauchsguttyp = gehoeft_besitzt_verbrauchsguttyp.id_verbrauchsguttyp";
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
                    echo '<td> <a class="btn btn-sm btn-dark" href="gut-show.php?id_verbrauchsguttyp=' . $fetch["id_verbrauchsguttyp"] . '" >Anzeigen</a> <br> </td>';                  
                    }
              ?>
              </tbody>                
            </table>
          </div>  
        </div>

        <!-- Sticky Footer -->
        <footer class="sticky-footer">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright © HRP-Projekt 2018/19 | <a href="impressum.html" target="_blank">Impressum & Datenschutzerklärung</a></span>
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