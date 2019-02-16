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

$gehoeft_name_sql = "SELECT * FROM gehoeft WHERE id_gehoeft=1";
$gehoeft_name_result = $conn->query($gehoeft_name_sql);

$gehoeft_adresse_sql = "SELECT gehoeft.id_adresse, adresse.id_adresse, adresse.strasse, adresse.hausnr, adresse.plz, adresse.ort FROM adresse, gehoeft WHERE gehoeft.id_gehoeft=1 AND adresse.id_adresse=gehoeft.id_adresse";
$gehoeft_adresse_result = $conn->query($gehoeft_adresse_sql);

$anzahl_paddockbox_sql = "SELECT COUNT(id_box) as anzahl_paddockbox FROM box WHERE id_gehoeft=1 AND id_boxentyp=2";
$anzahl_paddockbox_result = $conn->query($anzahl_paddockbox_sql);
if ($anzahl_paddockbox_result->num_rows > 0) {
  while($row = $anzahl_paddockbox_result->fetch_assoc()) {
    $anzahl_paddockbox = $row["anzahl_paddockbox"];
  }
}

$anzahl_innenbox_sql = "SELECT COUNT(id_box) as anzahl_innenbox FROM box WHERE id_gehoeft=1 AND id_boxentyp=1";
$anzahl_innenbox_result = $conn->query($anzahl_innenbox_sql);
if ($anzahl_innenbox_result->num_rows > 0) {
  while($row = $anzahl_innenbox_result->fetch_assoc()) {
    $anzahl_innenbox = $row["anzahl_innenbox"];
  }
}

$anzahl_boxbelegt_sql = "SELECT COUNT(id_box) as anzahl_belegt FROM box WHERE id_gehoeft=1 AND id_pferd IS NOT NULL";
$anzahl_boxbelegt_result = $conn->query($anzahl_boxbelegt_sql);
if ($anzahl_boxbelegt_result->num_rows > 0) {
  while($row = $anzahl_boxbelegt_result->fetch_assoc()) {
    $anzahl_boxbelegt = $row["anzahl_belegt"];
  }
}

$anzahl_boxfrei_sql = "SELECT COUNT(id_box) as anzahl_frei FROM box WHERE id_gehoeft=1 AND id_pferd IS NULL";
$anzahl_boxfrei_result = $conn->query($anzahl_boxfrei_sql);
if ($anzahl_boxfrei_result->num_rows > 0) {
  while($row = $anzahl_boxfrei_result->fetch_assoc()) {
    $anzahl_boxfrei = $row["anzahl_frei"];
  }
}

$anzahl_boxbelegt_paddock_sql = "SELECT COUNT(id_box) as anzahl_belegt FROM box WHERE id_gehoeft=1 AND id_boxentyp=2 AND id_pferd IS NOT NULL";
$anzahl_boxbelegt_paddock_result = $conn->query($anzahl_boxbelegt_paddock_sql);
if ($anzahl_boxbelegt_paddock_result->num_rows > 0){
  while($row = $anzahl_boxbelegt_paddock_result->fetch_assoc()){
    $anzahl_boxbelegt_paddock = $row["anzahl_belegt"];
  }
}

$anzahl_boxfrei_paddock = $anzahl_paddockbox-$anzahl_boxbelegt_paddock;

$anzahl_boxbelegt_innen_sql = "SELECT COUNT(id_box) as anzahl_belegt FROM box WHERE id_gehoeft=1 AND id_boxentyp=1 AND id_pferd IS NOT NULL";
$anzahl_boxbelegt_innen_result = $conn->query($anzahl_boxbelegt_innen_sql);
if ($anzahl_boxbelegt_innen_result->num_rows > 0){
  while($row = $anzahl_boxbelegt_innen_result->fetch_assoc()){
    $anzahl_boxbelegt_innen = $row["anzahl_belegt"];
  }
}

$anzahl_boxfrei_innen = $anzahl_innenbox-$anzahl_boxbelegt_innen;

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

    <script>
      window.onload = function() {
        var anzahl_belegt = <?php echo $anzahl_boxbelegt ?>;
        var anzahl_frei = <?php echo $anzahl_boxfrei ?>;
        var anzahl_boxbelegt_paddock = <?php echo $anzahl_boxbelegt_paddock ?>;
        var anzahl_boxfrei_paddock = <?php echo $anzahl_boxfrei_paddock ?>;
        var anzahl_boxbelegt_innen = <?php echo $anzahl_boxbelegt_innen ?>;
        var anzahl_boxfrei_innen = <?php echo $anzahl_boxfrei_innen ?>;
        var anzahl_paddockbox = <?php echo $anzahl_paddockbox ?>;
        var anzahl_innenbox = <?php echo $anzahl_innenbox ?>;

        var boxen_belegt_frei = new CanvasJS.Chart("boxen_belegt_frei", {
          animationEnabled: true,
          title: {
            fontFamily: "Segoe UI",
            fontWeight: "bold",
            text: "Belegung der Boxen"
          },
          data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "##0",
            indexLabel: "{label} {y}",
            dataPoints: [
              {y: anzahl_belegt, label: "Belegte Boxen"},
              {y: anzahl_frei, label: "Freie Boxen"}
            ]
          }]
        });
        boxen_belegt_frei.render();
        
        var verhaeltnis_frei_belegt_paddock_innen = new CanvasJS.Chart("verhaeltnis_frei_belegt_paddock_innen", {
          animationEnabled: true,
          theme: "light2",
          title:{
            fontFamily: "Segoe UI",
            fontWeight: "bold",
            text: "Freie und Belegte Boxen anhand des Boxentyps"
          },
          axisY: {
            title: "Anzahl der Boxen"
          },
          data: [{        
            type: "column",
            dataPoints: [      
              { y: anzahl_paddockbox, label: "Paddockboxen gesamt" },
              { y: anzahl_boxbelegt_paddock,  label: "Paddockboxen belegt" },
              { y: anzahl_innenbox,  label: "Innenboxen gesamt" },
              { y: anzahl_boxbelegt_innen,  label: "Innenboxen belegt" }
            ]
          }]
        });
        verhaeltnis_frei_belegt_paddock_innen.render();
      } 
    </script>

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
          <a class="nav-link active" href="gehoeft.php">
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
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Dashboard</a>
          </li>
          <li class="breadcrumb-item active">
            Gehöft
          </li>
        </ol>

          <!-- Page Content -->

          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="dashboard.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">
              Gehöft
            </li>
          </ol>

          <h1>Gehöft: 
            <?php if ($gehoeft_name_result->num_rows > 0) {
                // output data of each row
                while($row = $gehoeft_name_result->fetch_assoc()) {
                    echo $row["gehoeftname"];
                }
            } else {
                echo "Nicht gefunden.";
            }?> 
          </h1>
          <hr>
          <p>
            <?php if ($gehoeft_adresse_result->num_rows > 0) {
              while($row = $gehoeft_adresse_result->fetch_assoc()) {
                echo $row["strasse"] . " " . $row["hausnr"] . "<br>" . $row["plz"] . " " . $row["ort"];
              }
            }
            ?>
          </p>
          <hr>
          <div class="card mb-3">
            <div class="card-header">
            <i class="fas fa-chart-pie"></i>
            Belegung der Boxen
            </div>
            <div class="card-body">
              <div class="row justify-content-end">
                <a class="btn btn-success" href="box-edit.php">Box hinzufügen</a>
              </div>
              <br>
              <div class="row justify-content-end">
                <a class="btn btn-danger" href="box-delete.php">Box löschen</a>
              </div>
              <div class="row">
                <div id="boxen_belegt_frei" style="height: 300px; width: 100%;"></div>
              </div>
            </div>
          </div>
          <hr>
          <div class="card mb-3">
          <div class="card-header">
          <i class="fas fa-chart-area"></i>
          Verhältnis der freien zu belegten Boxen
          </div>
          <div class="card-body">
            <div id="verhaeltnis_frei_belegt_paddock_innen" style="height: 300px; width: 100%"></div>
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

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

  </body>

</html>