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

$anzahl_stuten_sql = "SELECT COUNT(pferd.id_pferd) as anzahl FROM pferd, box WHERE pferd.geschlecht = 's' AND pferd.id_pferd = box.id_pferd AND box.id_gehoeft = $id_gehoeft";
$anzahl_stuten_result = $conn->query($anzahl_stuten_sql);
if ($anzahl_stuten_result->num_rows > 0){
  while($row_as = $anzahl_stuten_result->fetch_assoc()){
    $anzahl_stuten = $row_as["anzahl"]; 
  }
}

$anzahl_wallach_sql = "SELECT COUNT(pferd.id_pferd) as anzahl FROM pferd, box WHERE pferd.geschlecht = 'w' AND pferd.id_pferd = box.id_pferd AND box.id_gehoeft = $id_gehoeft";
$anzahl_wallach_result = $conn->query($anzahl_wallach_sql);
if ($anzahl_wallach_result->num_rows > 0){
  while($row_aw = $anzahl_wallach_result->fetch_assoc()){
    $anzahl_wallache = $row_aw["anzahl"]; 
  }
}

$anzahl_hengste_sql = "SELECT COUNT(pferd.id_pferd) as anzahl FROM pferd, box WHERE pferd.geschlecht = 'h' AND pferd.id_pferd = box.id_pferd AND box.id_gehoeft = $id_gehoeft";
$anzahl_hengste_result = $conn->query($anzahl_hengste_sql);
if ($anzahl_hengste_result->num_rows > 0){
  while($row_ah = $anzahl_hengste_result->fetch_assoc()){
    $anzahl_hengste = $row_ah["anzahl"]; 
  }
}

$anzahl_boxenfrei_sql = "SELECT COUNT(id_box) as anzahl FROM box WHERE id_pferd IS NULL AND id_gehoeft = $id_gehoeft";
$anzahl_boxenfrei_result = $conn->query($anzahl_boxenfrei_sql);
if ($anzahl_boxenfrei_result->num_rows > 0){
  while($row_abf = $anzahl_boxenfrei_result->fetch_assoc()){
    $anzahl_boxenfrei = $row_abf["anzahl"];
  }
}

$anzahl_boxenbelegt_sql = "SELECT COUNT(id_box) as anzahl FROM box WHERE id_pferd IS NOT NULL AND id_gehoeft = $id_gehoeft";
$anzahl_boxenbelegt_result = $conn->query($anzahl_boxenbelegt_sql);
if ($anzahl_boxenbelegt_result->num_rows > 0){
  while($row_abb = $anzahl_boxenbelegt_result->fetch_assoc()){
    $anzahl_boxenbelegt = $row_abb["anzahl"];
  }
}

$bestand_hafer_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp=1 AND id_gehoeft = $id_gehoeft";
$bestand_hafer_result = $conn->query($bestand_hafer_sql);
if ($bestand_hafer_result->num_rows > 0){
  while($row_bh = $bestand_hafer_result->fetch_assoc()){
    $bestand_hafer = $row_bh["bestand"];
  }
}

$bestand_heu_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp=2 AND id_gehoeft = $id_gehoeft";
$bestand_heu_result = $conn->query($bestand_heu_sql);
if ($bestand_heu_result->num_rows > 0){
  while($row_bheu = $bestand_heu_result->fetch_assoc()){
    $bestand_heu = $row_bheu["bestand"];
  }
}

$bestand_stroh_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp=3 AND id_gehoeft = $id_gehoeft";
$bestand_stroh_result = $conn->query($bestand_stroh_sql);
if ($bestand_stroh_result->num_rows > 0){
  while($row_bs = $bestand_stroh_result->fetch_assoc()){
    $bestand_stroh = $row_bs["bestand"];
  }
}

$bestand_saegespaene_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp=4 AND id_gehoeft = $id_gehoeft";
$bestand_saegespaene_result = $conn->query($bestand_saegespaene_sql);
if ($bestand_saegespaene_result->num_rows > 0){
  while($row_bss = $bestand_saegespaene_result->fetch_assoc()){
    $bestand_saegespaene = $row_bss["bestand"];
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

    <!-- Jan Custom styles for this template-->
    <link href="css/jan.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">
    <script>
      window.onload = function () {
        var boxen_belegt = <?php echo $anzahl_boxenbelegt ?>;
        var boxen_frei = <?php echo $anzahl_boxenfrei ?>;
        var stuten = <?php echo $anzahl_stuten ?>;
        var wallache = <?php echo $anzahl_wallache ?>;
        var hengste = <?php echo $anzahl_hengste ?>;
        var hafer = <?php echo $bestand_hafer ?>;
        var heu = <?php echo $bestand_heu ?>;
        var stroh = <?php echo $bestand_stroh ?>;
        var saegespaene = <?php echo $bestand_saegespaene ?>;

        CanvasJS.addColorSet("customColors", ["#7e5738", "#a4bf6b", "#473221", "a09189", "240c01"]);
        
        var boxen_belegt_frei = new CanvasJS.Chart("boxen_belegt_frei", {
          animationEnabled: true,
          colorSet: "customColors",
          title: {
            fontFamily: "Helvetica",
            fontWeight: "bold",
            text: "Belegung der Boxen"
          },
          data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "##0",
            indexLabel: "{label} {y}",
            dataPoints: [
              {y: boxen_belegt, label: "Belegte Boxen"},
              {y: boxen_frei, label: "Freie Boxen"}
            ]
          }]
        });
        boxen_belegt_frei.render();

        var anzahl_hws = new CanvasJS.Chart("anzahl_hws", {
          animationEnabled: true,
          
          title:{
            text:"Anzahl der Pferde",
            fontFamily: "Helvetica",
            fontWeight: "bold"
          },
          axisX:{
            interval: 1
          },
          data: [{
            type: "bar",
            name: "Pferdegeschlecht",
            axisYType: "secondary",
            color: "#a4bf6b",
            dataPoints: [
              { y: stuten, label: "Stuten" },
              { y: wallache, label: "Wallache" },
              { y: hengste, label: "Hengste" }
            ]
          }]
        });
        anzahl_hws.render();

        var verbrauchsguttypen_bestand = new CanvasJS.Chart("verbrauchsguttypen_bestand", {
          animationEnabled: true,
          
          title:{
            text:"Bestände der Verbrauchsgüter",
            fontFamily: "Helvetica",
            fontWeight: "bold"
          },
          axisX:{
            interval: 1
          },
          data: [{
            type: "bar",
            name: "Pferdegeschlecht",
            axisYType: "secondary",
            color: "#a4bf6b",
            dataPoints: [
              { y: heu, label: "Heu" },
              { y: hafer, label: "Hafer" },
              { y: stroh, label: "Stroh" },
              { y: saegespaene, label: "Sägespäne"}
            ]
          }]
        });
        verbrauchsguttypen_bestand.render();
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
        <li class="nav-item active">
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
          <ol class="breadcrumb">
            <li class="breadcrumb-item active">
              Dashboard
            </li>
          </ol>

            <?php
            if (isset($_GET['registered'])) {
             echo '<div id="myAlert" class="alert alert-success collapse">
                    <strong>Erfolgreich registriert!</strong>
                </div>';
            }
            ?>

          <!-- Page Content -->

          <h1>Dashboard</h1>
          <hr>
          <div class="row">
          <div class="col-md">
            <div class="card mb-3">
              <div class="card-header">
                <i class="fas fa-chart-pie"></i>
                Pferd
              </div>
              <div class="card-body">
                <div id="anzahl_hws" style="height: 300px; width: 100%;"></div>
              </div>
            </div>
          </div>
          <div class="col-md">
            <div class="card mb-3">
              <div class="card-header">
                <i class="fas fa-chart-pie"></i>
                Boxen
              </div>
              <div class="card-body">
                <div id="boxen_belegt_frei" style="height: 300px; width: 100%;"></div>
              </div>
            </div>
          </div>
          </div>
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-chart-pie"></i>
              Verbrauchsgüter
            </div>
            <div class="card-body">
              <div id="verbrauchsguttypen_bestand" style="height: 300px; width: 100%;"></div>
            </div>
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

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>
