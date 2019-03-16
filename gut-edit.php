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
  $auth = false;
    
  $auth_sql = "SELECT id_gehoeft FROM verbrauchsgut WHERE id_verbrauchsgut = ?";
  $auth_result = $conn->prepare($auth_sql);
  $auth_result->bind_param('i', $_GET['id_verbrauchsgut']);
  $auth_result->execute();
  $auth_result = $auth_result->get_result();
  $auth_result = $auth_result->fetch_assoc();
    
  if ($auth_result['id_gehoeft'] == $id_gehoeft) {
    $auth = true;
  }
  else if ($_GET['id_verbrauchsgut'] == 0) {
    $auth = true;
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

    <?php
      if ($_GET['id_verbrauchsgut'] == 0){
        $lieferungname = "Lieferung erstellen";
      } else {
        $lieferungname_sql = "SELECT verbrauchsgutbez FROM verbrauchsgut WHERE id_verbrauchsgut = ?";
        $lieferungname_result = $conn->prepare($lieferungname_sql);
        $lieferungname_result->bind_param('i', $_GET['id_verbrauchsgut']);
        $lieferungname_result->execute();
        $lieferungname_result = $lieferungname_result->get_result();
        $lieferungname_result = $lieferungname_result->fetch_assoc();
        $lieferungname = $lieferungname_result['verbrauchsgutbez'] . "bearbeiten";
      }

    ?>
    <title>HRP - <?php echo $lieferungname; ?></title>

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
            <?php

              if ($auth == true) {

              $verbrauchsgut_sql = "SELECT * FROM verbrauchsgut WHERE id_verbrauchsgut=?";
              $verbrauchsgut_result = $conn->prepare($verbrauchsgut_sql);
              $verbrauchsgut_result->bind_param('i', $_GET['id_verbrauchsgut']);
              $verbrauchsgut_result->execute();
              $verbrauchsgut_result = $verbrauchsgut_result->get_result();

              if($verbrauchsgut_result->num_rows > 0){
                while($row_g = $verbrauchsgut_result->fetch_assoc()){   
                  echo "<ol class=\"breadcrumb\">
                          <li class=\"breadcrumb-item\">
                            <a href=\"dashboard.php\">Dashboard</a>
                          </li>
                          <li class=\"breadcrumb-item\">
                            <a href=\"gueter.php\">Güter</a>
                          </li>
                          <li class=\"breadcrumb-item\">
                            <a href=\"lieferung.php\">Lieferungen</a>
                          </li>
                          <li class=\"breadcrumb-item active\">
                            Lieferung bearbeiten
                          </li>
                        </ol>
                        <h1>Lieferung bearbeiten</h1>
                        <hr><br>
                        <form action=\"gut-edited.php?id_verbrauchsgut=" . $row_g["id_verbrauchsgut"] . "\" method=\"post\">";

                  /* Formular zur Bearbeitung von Lieferungen */
                  echo "<div class=\"form-group\"><label>Verbrauchsgütertyp</label>";
                  echo "<select class=\"form-control custom-select\" name=\"id_verbrauchsguttyp\" required>";

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
                  echo "</select></div><br>";
                  echo "<div class=\"form-group\"><label>Bezeichnung</label>";
                  echo "<input  class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_g["verbrauchsgutbez"] . "\" name=\"verbrauchsgutbez\" required></div><br>";
                  echo "<div class=\"form-group\"><label>Lieferdatum</label>";
                  echo "<input required class=\"form-control\" type=\"date\" min=\"2018-10-01\" max=\"" . date("Y-m-d") . "\" value=\"" . $row_g["lieferdatum"] . "\" name=\"lieferdatum\"></div><br>";
                  echo "<div class=\"form-group\"><label>Lieferant</label>";
                  echo "<select required class=\"form-control custom-select\" name=\"id_person\">";
                  $lieferant_sql = "SELECT * FROM person, beziehung WHERE person.id_person =" .$row_g["id_person"] . "person.id_person = beziehung.id_person AND id_funktion = 5";
                  $lieferant_result = $conn->query($lieferant_sql);
                  if($lieferant_result->num_rows > 0){
                    while($row_l = $lieferant_result->fetch_assoc()){
                      echo "<option value=\"" . $row_l["id_person"] . "\" selected>" . $row_l["vorname"] . " " . $row_l["nachname"] . "</option>";
                    }
                  }
                  $lieferanten_sql = "SELECT * FROM person, beziehung WHERE person.id_person = beziehung.id_person AND beziehung.id_funktion = 5";
                  $lieferanten_result = $conn->query($lieferanten_sql);
                  if($lieferanten_result->num_rows > 0) {
                    while($row_al = $lieferanten_result->fetch_assoc()){
                      echo "<option value=\"" . $row_al['id_person'] . "\">" . $row_al['vorname'] . " " . $row_al['nachname'] . "</option>";
                    }
                  }
                  echo "</select></div><br>";
                  echo "<div class=\"form-group\"><label>Menge in kg</label>";
                  echo "<input required class=\"form-control\" type=\"number\" min=\"1\" max=\"10000\" value=\"" . $row_g["menge"] . "\" name=\"menge\"></div><br>";
                  echo "<div class=\"form-group\"><label>Einkaufspreis in €/kg</label>";
                  echo "<input required class=\"form-control\" type=\"number\" min=\"0.01\" max=\"300\" step=\"0.01\" value=\"" . $row_g["einkaufspreis"] . "\" name=\"einkaufspreis\"></div><br>";
                  echo "
                      <div class=\"form-group\">
                        <button type=\"submit\" class=\"btn btn-success\"  href=\"gut-edited.php?id_verbrauchsgut=" . $row_g["id_verbrauchsgut"] . "\" role=\"button\">Abschicken</button>
                        <a class=\"btn btn-secondary\" href=\"lieferung.php\" >Abbrechen</a> </div>";
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
                          <li class=\"breadcrumb-item\">
                            <a href=\"lieferung.php\">Lieferungen</a>
                          </li>
                          <li class=\"breadcrumb-item active\">
                            Lieferung erstellen
                          </li>
                        </ol>
                        <h1>Lieferung erstellen</h1>
                        <hr><br>
                      <form action=\"gut-edited.php?id_verbrauchsgut=0\" method=\"post\">";
                      
                /* Formular zur Erstellung von Lieferungen */
                echo "<div class=\"form-group\"><label>Verbrauchsgütertyp</label>";
                echo "<select required class=\"form-control custom-select\" name=\"id_verbrauchsguttyp\">";
                $verbrauchsguttypall_sql = "SELECT * FROM verbrauchsguttyp";
                $verbrauchsguttypall_result = $conn->query($verbrauchsguttypall_sql);
                if ($verbrauchsguttypall_result->num_rows > 0){
                  while($row_vgtall = $verbrauchsguttypall_result->fetch_assoc()){
                    echo "<option value=\"" . $row_vgtall["id_verbrauchsguttyp"] . "\">" . $row_vgtall["verbrauchsguttypbez"] . "</option>";
                  }
                }
                echo "</select></div><br>";
                echo "<div class=\"form-group\"><label>Bezeichnung</label>";
                echo "<input required class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"verbrauchsgutbez\"></div><br>";
                echo "<div class=\"form-group\"><label>Lieferdatum</label>";
                echo "<input required class=\"form-control\" type=\"date\" min=\"2018-10-01\" max=\"" . date("Y-m-d") . "\" name=\"lieferdatum\"></div><br>";
                echo "<div class=\"form-group\"><label>Lieferant</label>";
                echo "<select required class=\"form-control custom-select\" name=\"id_person\">";
                $lieferanten_sql = "SELECT * FROM person, beziehung WHERE person.id_person = beziehung.id_person AND beziehung.id_funktion = 5";
                  $lieferanten_result = $conn->query($lieferanten_sql);
                  if($lieferanten_result->num_rows > 0) {
                    while($row_al = $lieferanten_result->fetch_assoc()){
                      echo "<option value=\"" . $row_al['id_person'] . "\">" . $row_al['vorname'] . " " . $row_al['nachname'] . "</option>";
                    }
                  }
                echo "</select></div><br>";
                echo "<div class=\"form-group\"><label>Menge in kg</label>";
                echo "<input required class=\"form-control\" type=\"number\" min=\"1\" max=\"10000\" name=\"menge\"></div><br>";
                echo "<div class=\"form-group\"><label>Einkaufspreis in €/kg</label>";
                echo "<input required class=\"form-control\" type=\"number\" min=\"0.01\" max=\"300\" step=\"0.01\" name=\"einkaufspreis\"></div><br>";
                echo "
                    <div class=\"form-group\">
                    <button type=\"submit\" class=\"btn btn-success\"  href=\"gut-edited.php?id_verbrauchsgut=" . $_GET["id_verbrauchsgut"] . "\" role=\"button\">Abschicken</button>
                    <a class=\"btn btn-secondary\" href=\"lieferung.php\" >Abbrechen</a> </div>";
              }

            }

            else {
              echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für diese Lieferung!</div><hr><br>';
            }
              ?>
          </form>

        </div>

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

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>
