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

// Prüfung, ob User eingeloggt ist
if($_SESSION["logged"] == true) {

  // Setzen der Variable aus der Session
  $id_gehoeft = $_SESSION['id_gehoeft'];
  // Authorisierungs-Boolean erst false setzen
  $auth = false;

  // Setzen des Get-Parameters als Variable
  $id_pferd = $_GET["id_pferd"];

  // SQL-Abfrage für das Gehöft des Pferdes
  $query = "SELECT id_gehoeft FROM box WHERE id_pferd = ?";
  $auth_sql = $conn->prepare($query);
  $auth_sql->bind_param("i", $id_pferd);
  $auth_sql->execute();
  $result = $auth_sql->get_result();
  $auth_result = $result->fetch_assoc();
  
  // Wenn Gehöft übereinstimmt, ist Authorisierung positiv
  if ($auth_result["id_gehoeft"] == $id_gehoeft) {
    $auth = true;
  }
  $auth_sql->close();

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
      $pferdename_sql = "SELECT pferdename FROM pferd WHERE id_pferd = ?";
      $pferdename_result = $conn->prepare($pferdename_sql);
      $pferdename_result->bind_param("i", $_GET['id_pferd']);
      $pferdename_result->execute();
      $pferdename_result = $pferdename_result->get_result();
      $pferdename_result = $pferdename_result->fetch_assoc();
      $name = $pferdename_result['pferdename'];
    ?>

    <title>HRP - <?php echo $name; ?></title>

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
            <a class="nav-link" href="logout.php" data-toggle="modal" data-target="#logoutModal">Logout</a>
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
        <li class="nav-item active">
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

          // Authorisierung positiv
          if ($auth == true) {

            // SQL Abfrage Attribute des Pferdes
            $pferd_query = "SELECT * FROM pferd WHERE id_pferd = ?";
            $pferd_sql = $conn->prepare($pferd_query);
            $pferd_sql->bind_param("i", $id_pferd);
            $pferd_sql->execute();
            $pferd_result = $pferd_sql->get_result();
            while ($pferd_fetch = $pferd_result->fetch_assoc()) {

              echo "<ol class=\"breadcrumb\">
                    <li class=\"breadcrumb-item\">
                      <a href=\"dashboard.php\">Dashboard</a>
                    </li>
                    <li class=\"breadcrumb-item\">
                      <a href=\"pferd.php\">Pferde</a>
                    </li>
                    <li class=\"breadcrumb-item active\">
                      Pferd anzeigen
                    </li>
                  </ol>";
              echo "<h1>" . $pferd_fetch['pferdename'] . "</h1> <hr>";
              
              echo "<p>Geschlecht: ";
              if( $pferd_fetch['geschlecht'] =='s') {
                echo "Stute";
              }
              else if( $pferd_fetch['geschlecht'] =='w') {
                echo "Wallach";
              }
              else {
                echo "Hengst";
              }
              echo "</p>";
              
              // Speichern in Variable, in der das Datumsformat geändert werden kann
              $geburtsdatum_pferd = new DateTime($pferd_fetch['geburtsdatum_pferd']);
              $ankunft = new DateTime($pferd_fetch['ankunft']);

              echo "<p>Gewicht: " . $pferd_fetch['gewicht'] . " kg</p>
                    <p>Größe: " . $pferd_fetch['groesse'] . " cm</p>
                    <p>Passnummer: " . $pferd_fetch['passnr'] . "</p>
                    <p>Geburtsdatum: " . $geburtsdatum_pferd->format('d.m.Y') . "</p>
                    <p>Ankunft: " . $ankunft->format('d.m.Y') . "</p>";

            // Gewicht in Variable speichern für Bedarfsberechnung
            $gewicht = $pferd_fetch['gewicht'];
            }
            
              // SQL-Abfrage für zugewiesene Box
              $box_query = "SELECT boxentyp.boxenbez, box.boxenpreis FROM box, boxentyp WHERE box.id_pferd = ? AND box.id_boxentyp = boxentyp.id_boxentyp";
              $box_sql = $conn->prepare($box_query);
              $box_sql->bind_param("i", $id_pferd);
              $box_sql->execute();
              $box_result = $box_sql->get_result();
              while ($box_fetch = $box_result->fetch_assoc()) {  
                echo "<p>Boxentyp: " . $box_fetch['boxenbez'] . "</p>
                      <p>Boxenpreis: " . $box_fetch['boxenpreis'] .  " €/Monat</p>";
              }
              ?>

              <hr>
              <h3>Verbrauch</h3>

              <div class='table-responsive'>
              <table class='table table-bordered table-hover display' id='dataTable1' width='100%' cellspacing='0'>
              <thead class='thead-light'>
                <tr>
                  <th>Verbrauchsgut</th>
                  <th>Verbrauch in kg/Tag</th>
                </tr>
              </thead>
              
              <tbody>

              <?php

              // SQL-Abfrage für Bedarf des Pferdes
              $bedarf1_query = "SELECT verbrauchsguttypbez, (koeffizient * $gewicht / 100) as bedarf FROM verbrauchsguttyp WHERE id_verbrauchsguttyp <= 2";
              $bedarf1_sql = $conn->query($bedarf1_query);

              while($bedarf1_fetch = $bedarf1_sql->fetch_assoc()) {
                echo "<tr><td>" . $bedarf1_fetch['verbrauchsguttypbez'] . "</td><td>" . $bedarf1_fetch['bedarf'] . "</td></tr>";
              }

              $bedarf1_query = "SELECT verbrauchsguttypbez, (koeffizient) as bedarf FROM verbrauchsguttyp WHERE id_verbrauchsguttyp > 2";
              $bedarf1_sql = $conn->query($bedarf1_query);

              while($bedarf1_fetch = $bedarf1_sql->fetch_assoc()) {
                echo "<tr><td>" . $bedarf1_fetch['verbrauchsguttypbez'] . "</td><td>" . $bedarf1_fetch['bedarf'] . "</td></tr>";
              }

              ?>
              
              </tbody>
              </table>
              </div>
              
              <hr>
              <h3>Personen</h3>
                        
              <div class='table-responsive'>
              <table class='table table-bordered table-hover display' id='dataTable2' width='100%' cellspacing='0'>
              <thead class='thead-light'>
                <tr>
                  <th>Vorname</th>
                  <th>Nachname</th>
                  <th>Funktion</th>
                  <th></th>
                </tr>
              </thead>
              
              <tbody>

              <?php
              // SQL-Abfrage für zugewiesene Person zu diesem Pferd
              $person_query = "SELECT person.id_person, vorname, nachname, funktionsbez FROM person, funktion, beziehung WHERE beziehung.id_pferd = ? AND person.id_person = beziehung.id_person AND beziehung.id_funktion = funktion.id_funktion";
              $person_sql = $conn->prepare($person_query);
              $person_sql->bind_param("i", $id_pferd);
              $person_sql->execute();
              $person_result = $person_sql->get_result();
              while ($person_fetch = $person_result->fetch_assoc()) {  

                  echo "<tr>";
                  echo "<td>" . $person_fetch['vorname'] .  "</td>";
                  echo "<td>" . $person_fetch['nachname'] .  "</td>";
                  echo "<td>" . $person_fetch['funktionsbez'] . "</td>";

                  echo '<td>
                    <div class="d-sm-flex flex-row">
                    <div><a class="btn btn-sm btn-dark" role="button" href="person-show.php?id_person=' . $person_fetch["id_person"] . '" >Anzeigen</a></div>
                    <div class="ml-0 ml-sm-2 mt-1 mt-sm-0"><a class="btn btn-sm btn-primary" role="button" href="person-edit.php?id_person=' . $person_fetch["id_person"] . '" >Bearbeiten</a></div>
                    </div>
                    </td>
                    </tr>';
                  }
                  
              echo "
              </tbody>
              </table>
              </div>";
              
              
              echo "<hr>";

              echo "
              <div class=\"form-group\">
              <a class=\"btn btn-primary\" href=\"pferd-edit.php?id_pferd=" . $id_pferd . "\" >Bearbeiten</a>
              <a class=\"btn btn-danger\" href=\"pferd-delete.php?id_pferd=" . $id_pferd . "\" onclick='return checkDelete()'>Löschen</a>
              <a class=\"btn btn-secondary\" href=\"pferd.php\" >Zurück zur Übersicht</a></div>";
            
            
       
            echo "
            </tbody>
            </table>
            </div>";
                
          }
                

        else {
          echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für dieses Pferd!</div><hr><br>';
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

    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

    <!-- JavaScript für Delete-Confirm -->
    <script>
      function checkDelete(){
        return confirm('Pferd endgültig löschen?')
      }
    </script>
    
    <!-- JavaScript für mehrere DataTables auf einer Seite -->
    <script>
      $(document).ready(function() {
      $('table.display').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
        }
      });
      });
    </script>

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>