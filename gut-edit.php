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

$verbrauchsgut_sql = "SELECT * FROM verbrauchsgut WHERE id_verbrauchsgut=" . $_GET["id_verbrauchsgut"];
$verbrauchsgut_result = $conn->query($verbrauchsgut_sql);

session_start();

if($_SESSION["logged"] == true) {


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
            <?php
              if($verbrauchsgut_result->num_rows > 0){
                while($row_g = $verbrauchsgut_result->fetch_assoc()){   
                  echo "<ol class=\"breadcrumb\">
                          <li class=\"breadcrumb-item\">
                            <a href=\"dashboard.php\">Dashboard</a>
                          </li>
                          <li class=\"breadcrumb-item\">
                            <a href=\"gueter.php\">Güter</a>
                          </li>
                          <li class=\"breadcrumb-item active\">
                            Lieferung bearbeiten
                          </li>
                        </ol>
                        <h1>Lieferung bearbeiten</h1>
                        <hr>
                        <form action=\"gut-edited.php?id_verbrauchsgut=" . $row_g["id_verbrauchsgut"] . "\" method=\"post\">";
                  echo "<div class=\"form-group\"><label>Verbrauchsgütertyp</label>";
                  echo "<select class=\"form-control\" name=\"id_verbrauchsguttyp\">";
                  $verbrauchsguttyp_sql = "SELECT * FROM verbrauchsguttyp WHERE id_verbrauchsguttyp=" . $row_g["id_verbrauchsguttyp"];
                  $verbrauchsguttyp_result = $conn->query($verbrauchsguttyp_sql);
                  if($verbrauchsguttyp_result->num_rows > 0){
                    while($row_vgt = $verbrauchsguttyp_result->fetch_assoc()){
                      echo "<option value=\"" . $row_vgt["id_verbrauchsguttyp"] . "\" selected>" . $row_vgt["verbrauchsguttypbez"] . "</option>";
                    }
                  }
                  $notverbrauchsguttyp_sql = "SELECT * FROM verbrauchsguttyp WHERE NOT id_verbrauchsguttyp=" . $row_g["id_verbrauchsguttyp"];
                  $notverbrauchsguttyp_result = $conn->query($notverbrauchsguttyp_sql);
                  if($notverbrauchsguttyp_result->num_rows > 0){
                    while($row_nvgt = $notverbrauchsguttyp_result->fetch_assoc()){
                      echo "<option value=\"" . $row_nvgt["id_verbrauchsguttyp"] . "\">" . $row_nvgt["verbrauchsguttypbez"] . "</option>";
                    }
                  }
                  echo "</select></div>";
                  echo "<div class=\"form-group\"><label>Bezeichnung</label>";
                  echo "<input class=\"form-control\" type=\"text\" value=\"" . $row_g["verbrauchsgutbez"] . "\" name=\"verbrauchsgutbez\"></div>";
                  echo "<div class=\"form-group\"><label>Lieferdatum</label>";
                  echo "<input class=\"form-control\" type=\"date\" value=\"" . $row_g["lieferdatum"] . "\" name=\"lieferdatum\"></div>";
                  echo "<div class=\"form-group\"><label>Lieferant</label>";
                  echo "<select class=\"form-control\" name=\"id_person\">";
                  $lieferant_sql = "SELECT * FROM person WHERE id_person =" .$row_g["id_person"];
                  $lieferant_result = $conn->query($lieferant_sql);
                  if($lieferant_result->num_rows > 0){
                    while($row_l = $lieferant_result->fetch_assoc()){
                      echo "<option value=\"" . $row_l["id_person"] . "\" selected>" . $row_l["vorname"] . " " . $row_l["nachname"] . "</option>";
                    }
                  }
                  $notlieferant_sql = "SELECT * FROM person WHERE id_person IS NOT NULL AND NOT id_person = " . $row_g["id_person"];
                  $notlieferant_result = $conn->query($notlieferant_sql);
                  if($notlieferant_result->num_rows > 0){
                    while($row_nl = $notlieferant_result->fetch_assoc()){
                      echo "<option value=\"" . $row_nl["id_person"] . "\">" . $row_nl["vorname"] . " " . $row_nl["nachname"] . "</option>";
                    }
                  }
                  echo "</select></div>";
                  echo "<div class=\"form-group\"><label>Menge</label>";
                  echo "<input class=\"form-control\" type=\"number\" value=\"" . $row_g["menge"] . "\" name=\"menge\"></div>";
                  echo "<div class=\"form-group\"><label>Einkaufspreis</label>";
                  echo "<input class=\"form-control\" type=\"number\" value=\"" . $row_g["einkaufspreis"] . "\" name=\"einkaufspreis\"></div>";
                  echo "
                      <div class=\"form-group\">
                        <button type=\"submit\" class=\"btn btn-success\">Abschicken</button>
                        <button class=\"btn btn-secondary\" href=\"gut-edited.php?id_verbrauchsgut=" . $row_g["id_verbrauchsgut"] . "\" role=\"button\">Abbrechen</button>
                      </div>";
                }
              }
              else {
                echo "<ol class=\"breadcrumb\">
                          <li class=\"breadcrumb-item\">
                            <a href=\"dashboard.php\">Dashboard</a>
                          </li>
                          <li class=\"breadcrumb-item\">
                            <a href=\"gueter.php\">Güter</a>
                          </li>
                          <li class=\"breadcrumb-item active\">
                            Lieferung erstellen
                          </li>
                        </ol>
                        <h1>Lieferung erstellen</h1>
                        <hr>
                      <form action=\"gut-edited.php?id_verbrauchsgut=0\" method=\"post\">";
                echo "<div class=\"form-group\"><label>Verbrauchsgütertyp</label>";
                echo "<select class=\"form-control\" name=\"id_verbrauchsguttyp\">";
                $verbrauchsguttypall_sql = "SELECT * FROM verbrauchsguttyp";
                $verbrauchsguttypall_result = $conn->query($verbrauchsguttypall_sql);
                if ($verbrauchsguttypall_result->num_rows > 0){
                  while($row_vgtall = $verbrauchsguttypall_result->fetch_assoc()){
                    echo "<option value=\"" . $row_vgtall["id_verbrauchsguttyp"] . "\">" . $row_vgtall["verbrauchsguttypbez"] . "</option>";
                  }
                }
                echo "</select></div>";
                echo "<div class=\"form-group\"><label>Bezeichnung</label>";
                echo "<input class=\"form-control\" type=\"text\" name=\"verbrauchsgutbez\"></div>";
                echo "<div class=\"form-group\"><label>Lieferdatum (yyyy-mm-dd)</label>";
                echo "<input class=\"form-control\" type=\"date\" name=\"lieferdatum\"></div>";
                echo "<div class=\"form-group\"><label>Lieferant</label>";
                echo "<select class=\"form-control\" name=\"id_person\">";
                $lieferantall_sql = "SELECT * FROM person";
                $lieferantall_result = $conn->query($lieferantall_sql);
                if($lieferantall_result->num_rows > 0){
                  while($row_lall = $lieferantall_result->fetch_assoc()){
                    echo "<option value=\"" . $row_lall["id_person"] . "\">" . $row_lall["vorname"] . " " . $row_lall["nachname"] . "</option>";
                  }
                }
                echo "</select></div>";
                echo "<div class=\"form-group\"><label>Menge</label>";
                echo "<input class=\"form-control\" type=\"number\" name=\"menge\"></div>";
                echo "<div class=\"form-group\"><label>Einkaufspreis</label>";
                echo "<input class=\"form-control\" type=\"number\" name=\"einkaufspreis\"></div>";
                echo "
                      <div class=\"form-group\">
                        <button type=\"submit\" class=\"btn btn-success\">Abschicken</button>
                        <button class=\"btn btn-secondary\" href=\"gut-edited.php?id_verbrauchsgut=0\" role=\"button\">Abbrechen</button>
                      </div>";
              }
              ?>
          </form>

        </div>

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

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>
