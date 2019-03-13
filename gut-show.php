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

    <title>HRP-Projekt</title>

    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">
    <?php
      $lieferungen_sql = "SELECT DATE_FORMAT(lieferdatum, '%d.%m.%Y') as lieferdatum, einkaufspreis FROM verbrauchsgut WHERE id_verbrauchsguttyp = " . $_GET['id_verbrauchsguttyp'] . ' AND id_gehoeft = ' . $id_gehoeft;
      $lieferungen_result = $conn->query($lieferungen_sql);
      $dataPoints = '';
      if ($lieferungen_result->num_rows > 0){
        while ($row_l = $lieferungen_result->fetch_assoc()){
          $dataPoints = $dataPoints . '{label: "' . $row_l["lieferdatum"] . '" , y: ' . $row_l["einkaufspreis"] . '},';
        }
      }
      $dataPoints = "[" . $dataPoints . "]";

    ?>
        <script>
        window.onload = function () {
          var dataPoints_verbrauchsgut = <?php echo $dataPoints ?>;
var chart = new CanvasJS.Chart("preisentwicklung", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Preisentwicklung",
    fontWeight: "bold",
    fontFamily: "Helvetica"
	},
	axisY:{
		includeZero: false
	},
	data: [{        
		type: "line",    
    color: "#a4bf6b",   
		dataPoints: dataPoints_verbrauchsgut
	}]
});
chart.render();

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
              Gut anzeigen
            </li>            
          </ol>
          
          <?php

            $verbrauchsguttypbez_query = "SELECT verbrauchsguttypbez FROM verbrauchsguttyp WHERE id_verbrauchsguttyp=?";
            $verbrauchsguttypbez_sql= $conn->prepare($verbrauchsguttypbez_query);
            $verbrauchsguttypbez_sql -> bind_param("i", $_GET["id_verbrauchsguttyp"]);
            $verbrauchsguttypbez_sql ->execute();
            $verbrauchsguttypbez = $verbrauchsguttypbez_sql->get_result();

            while ($fetch = $verbrauchsguttypbez ->fetch_assoc()){
              echo "<h1> " . $fetch['verbrauchsguttypbez'] . "</h1>";
              echo "<hr>";
              echo "<h3> Lieferungen </h3>";
              echo "                
              <div class='table-responsive'>
              <table class='table table-bordered table-hover display' id='dataTable2' width='100%' cellspacing='0'>
              <thead class='thead-light'>
                <tr>
                <th>Lieferung</th>
                <th>Lieferdatum</th>
                <th>Menge in kg</th>
                <th>Einkaufpreis je kg</th>
                <th>Lieferant</th>
                </tr>
              </thead>              
              <tbody>";
            }
            $verbrauchsgut_query = "SELECT *, DATE_FORMAT(lieferdatum, '%d.%m.%Y') as lieferdatum FROM verbrauchsgut WHERE id_verbrauchsguttyp = ?";
            $verbrauchsgut_sql = $conn->prepare($verbrauchsgut_query);
            $verbrauchsgut_sql->bind_param("i", $_GET["id_verbrauchsguttyp"]);
            $verbrauchsgut_sql->execute();
            $verbrauchsgut = $verbrauchsgut_sql->get_result();
            while($row_v = mysqli_fetch_assoc($verbrauchsgut)){
              echo "<tr>";
              echo "<td>" . $row_v["verbrauchsgutbez"] . "</td>";
              echo "<td>" . $row_v["lieferdatum"] . "</td>";
              echo "<td>" . $row_v["menge"] . "</td>";
              echo "<td>" . $row_v["einkaufspreis"] . "</td>";

              $lieferant_query = "SELECT vorname , nachname FROM person WHERE id_person =?";
              $lieferant_sql = $conn->prepare($lieferant_query);
              $lieferant_sql -> bind_param("i", $row_v["id_person"]);
              $lieferant_sql->execute();
              $lieferant = $lieferant_sql->get_result();
              
              while($lief = mysqli_fetch_assoc($lieferant)){
                echo "<td>" . $lief["vorname"] . " " . $lief["nachname"] . "</td>";
              }
              echo "</tr>";
              
            }
          ?>
          </tbody>
          </table>
          <br>
          <hr>
          <h3> Preisentwicklung des Gutes</h3>
          
          <div class="card mb-3">
          <div class="card-header">
          </div>
          <div class="card-body">
          <div id="preisentwicklung" style="height: 300px; width: 100%;"></div>
          
          </div>
          </div>
          <a class="btn btn-secondary" href="gueter.php" >Zurück zur Übersicht</a>
          <br>
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

  <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

  </body>
</html>

<?php
}

else {

  header('location:login.php');

}

?>