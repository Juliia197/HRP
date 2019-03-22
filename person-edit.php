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
	  }
	
	  else if ($_GET['id_person'] == 0) {
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
      if ($_GET['id_person'] == 0){
        $name = "Person hinzufügen";
      } else {
        $personname_sql = "SELECT vorname, nachname FROM person WHERE id_person = ?";
        $personname_result = $conn->prepare($personname_sql);
        $personname_result->bind_param('i', $_GET['id_person']);
        $personname_result->execute();
        $personname_result = $personname_result->get_result();
        $personname_result = $personname_result->fetch_assoc();
        $name = $personname_result['vorname'] . " " . $personname_result['nachname'] . " bearbeiten";
      }
    ?>

    <title>HRP - <?php echo $name; ?></title>

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
        <?php

          if ($auth == true) {

          //Übergebene Daten werden in Variablen gespeichert
          $personid = $_GET["id_person"];

          if($personid>0){ //wird ausgeführt wenn eine Person bearbeitet werden will
            
            $personquery = "SELECT * FROM adresse, person WHERE adresse.id_adresse = person.id_adresse AND person.id_person =? ";
            $person_sql = $conn->prepare($personquery);
            $person_sql->bind_param("i",$_GET["id_person"]);
            $person_sql->execute();
            $person = $person_sql->get_result();
  

            while($row_p = $person->fetch_assoc()){
              // Leiste zur Darstellung der aktuellen Position auf der Seite
              echo "<ol class=\"breadcrumb\">
                    <li class=\"breadcrumb-item\">
                      <a href=\"dashboard.php\">Dashboard</a>
                    </li>
                    <li class=\"breadcrumb-item\">
                      <a href=\"person.php\">Personen</a>
                    </li>
                    <li class=\"breadcrumb-item active\">
                      Person bearbeiten
                    </li>
                  </ol>";

              //Überschrift
              echo "<h1>" . $row_p['vorname'] ." " . $row_p['nachname'] . "</h1> <hr><br>";

              //Formular
              echo "<form action=\"person-edited.php?id_person=" . $row_p["id_person"] . "&amp;id_adresse=" . $row_p["id_adresse"] . "\" method=\"post\">";


              echo "<label>Vorname</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["vorname"] . "\" name=\"vorname\" required><br>";
              
              echo "<label>Nachname</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["nachname"] . "\" name=\"nachname\" required><br>";
              
              echo "<label>E-Mail</label>";
              echo "<input class=\"form-control\" type=\"email\" maxlength=\"45\" value=\"" . $row_p["email"] . "\" name=\"email\"  required><br>";
              
              echo "<label>Telefonnummer</label>";
              echo "<input class=\"form-control\" type=\"number\" min=\"100000000\" max=\"99999999999999999999\" value=\"" . $row_p["telefonnr"] . "\" name=\"telefonnr\" required ><br>";
              
              echo "<label>Geburtsdatum</label>";
              echo "<input class=\"form-control\" type=\"date\" min=\"1900-01-01\" max=\"" . date("Y-m-d") . "\" value=\"" . $row_p["geburtsdatum"] . "\" name=\"geburtsdatum\" required ><br>";


              echo "<hr><br><h3> Adresse </h3>";

              echo "<label>Straße</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["strasse"] . "\" name=\"strasse\" required ><br>";

              echo "<label>Hausnummer</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["hausnr"] . "\" name=\"hausnr\" required ><br>";

              echo "<label>Postleitzahl</label>";
              echo "<input class=\"form-control\" type=\"number\" min=\"1000\" max=\"99999\" value=\"" . $row_p["plz"] . "\" name=\"plz\" required ><br>";

              echo "<label>Ortschaft</label>";
              echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" value=\"" . $row_p["ort"] . "\" name=\"ort\" required ><br>";

              echo "<label>Land</label>";
              echo "<select class=\"custom-select\" name=\"land\"  required ><option value=\"DE\"";
                    if($row_p['land'] == 'DE'){echo "selected";};
              echo ">Deutschland</option><option value=\"AT\"";
                    if($row_p['land'] == 'AT'){echo "selected";};
              echo ">Österreich</option><option value=\"CH\"";
                    if($row_p['land'] == 'CH'){echo "selected";}
              echo ">Schweiz</option></select>";

              $checkbox_query = "SELECT lieferant FROM person WHERE id_person =" . $row_p['id_person'];
              $checkbox=$conn->query($checkbox_query);
              $checkbox_fetch = $checkbox->fetch_assoc();

              $disabled_query = "SELECT * FROM verbrauchsgut WHERE id_person = " . $row_p['id_person'] . " AND id_gehoeft = $id_gehoeft";
              $disabled_result = $conn->query($disabled_query);

              if ($disabled_result->num_rows > 0){
                echo '<br><hr><br><div class="form-check">
                <input class="form-check-input" type="checkbox" name="lieferant_checkbox" value="1" id="lieferant" checked disabled>
                <label class="form-check-label" for="lieferant_checkbox">
                  Hat die Rolle eines Lieferanten
                </label>
                <input type="hidden" name="lieferant" value="1">
                </div><br>';
                echo "<hr><br>"; 
              }
              else if ($disabled_result->num_rows == 0 && $checkbox_fetch["lieferant"] == 1){
                echo '<br><hr><br><div class="form-check">
                  <input class="form-check-input" type="checkbox" name="lieferant" value="1" id="lieferant" checked>
                  <label class="form-check-label" for="lieferant">
                    Hat die Rolle eines Lieferanten
                  </label>
                </div><br>';
                echo "<hr><br>";
              }
              else if ($checkbox_fetch["lieferant"] == 0){
                echo '<br><hr><br><div class="form-check">
                  <input class="form-check-input" type="checkbox" name="lieferant" value="1" id="lieferant">
                  <label class="form-check-label" for="lieferant">
                    Hat die Rolle eines Lieferanten
                  </label>
                </div><br>';
                echo "<hr><br>";
              }



              //Buttons
              echo "<div class=\"form-group\"></div>
              <div class=\"form-group\">
                <button type=\"submit\" class=\"btn btn-success\">Abschicken</button>
                <a class=\"btn btn-secondary\" href=\"person.php\" >Abbrechen</a>
              </div>";
              echo "</form>";

            }
          }
          else{ //wird ausgeführt wenn die Person hinzugefügt werden soll
            // Leiste zur Darstellung der aktuellen Position auf der Seite
            echo "<ol class=\"breadcrumb\">
                <li class=\"breadcrumb-item\">
                  <a href=\"dashboard.php\">Dashboard</a>
                </li>
                <li class=\"breadcrumb-item\">
                  <a href=\"person.php\">Personen</a>
                </li>
                <li class=\"breadcrumb-item active\">
                  Person hinzufügen
                </li>
              </ol>";

            //Überschrift
            echo "<h1>Person hinzufügen </h1><hr><br>";

            //Formular
            echo "<form action=\"person-edited.php?id_person=0&amp;id_adresse=0\" method=\"post\" required >";
            
            echo "<label>Vorname</label>";
            echo "<input class=\"form-control\" type=\"text\"  name=\"vorname\" maxlength=\"45\" required ><br>";
            
            echo "<label>Nachname</label>";
            echo "<input class=\"form-control\" type=\"text\"  name=\"nachname\" maxlength=\"45\" required ><br>";
            
            echo "<label>E-Mail</label>";
            echo "<input class=\"form-control\" type=\"email\" maxlength=\"45\" name=\"email\" required ><br>";
            
            echo "<label>Telefonnummer</label>";
            echo "<input class=\"form-control\" type=\"number\" name=\"telefonnr\" min=\"100000000\" max=\"99999999999999999999\" required ><br>";
            
            echo "<label>Geburtsdatum</label>";
            echo "<input class=\"form-control\" type=\"date\" min=\"1900-01-01\" max=\"" . date("Y-m-d") . "\" name=\"geburtsdatum\" required ><br>";

            echo "<hr><br><h3> Adresse </h3>";

            echo "<label>Straße</label>";
            echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"strasse\" required ><br>";

            echo "<label>Hausnummer</label>";
            echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"hausnr\" required ><br>";

            echo "<label>Postleitzahl</label>";
            echo "<input class=\"form-control\" type=\"number\" min=\"1000\" max=\"99999\" name=\"plz\" required ><br>";

            echo "<label>Ortschaft</label>";
            echo "<input class=\"form-control\" type=\"text\" maxlength=\"45\" name=\"ort\" required ><br>";

            echo "<label>Land </label>";
            echo "<select class=\"custom-select\" name=\"land\" required ><option value=\"DE\">Deutschland</option><option value=\"AT\">Österreich</option><option value=\"CH\">Schweiz</option></select><br>";

            echo '<br><hr><br><div class="form-check">
            <input class="form-check-input" type="checkbox" name="lieferant" value="1" id="lieferant">
            <label class="form-check-label" for="lieferant">
              Hat die Rolle eines Lieferanten
            </label>
          </div><br>';
          
            echo "<hr><br>";

            //Buttons
            echo "<div class='form-group'></div>
            <div class=\"form-group\">
              <button type=\"submit\" class=\"btn btn-success\">Abschicken</button>
              <a class=\"btn btn-secondary\" href=\"person.php\" >Abbrechen</a>
            </div></form>";
          }
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