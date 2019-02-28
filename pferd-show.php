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
          $pferdsql = "SELECT * FROM pferd WHERE id_pferd = " . $_GET['id_pferd'];
          $pferd = $conn->query($pferdsql);

         // echo $pferdsql;

          while($row_p = $pferd->fetch_assoc()){
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
            echo "<h1>" . $row_p['pferdename'] . "</h1> <hr>";
            
            echo "<p>Geschlecht: ";
            if( $row_p['geschlecht'] =='s')
                {
                  echo "Stute";
                }
            else if( $row_p['geschlecht'] =='w')
                {
                  echo "Wallach";
                }
            else
                {
                  echo "Hengst";
                }
            echo "</p>";
            
            echo "<p>Gewicht: " . $row_p['gewicht'] . " kg</p>";
            echo "<p>Größe: " . $row_p['groesse'] . " cm</p>";
            echo "<p>Passnummer: " . $row_p['passnr'] . "</p>";
            echo "<p>Geburtsdatum: " . $row_p['geburtsdatum_pferd'] . "</p>";
            echo "<p>Ankunft: " . $row_p['ankunft'] . "</p>";
          
          }

            $boxsql = "SELECT boxentyp.boxenbez FROM box, boxentyp WHERE box.id_pferd = " . $_GET['id_pferd'].' AND box.id_boxentyp = boxentyp.id_boxentyp';
            $box = $conn->query($boxsql) or die (mysql_error());
            while($fetch1 = mysqli_fetch_assoc($box)){
              echo "<p>Boxentyp: " . $fetch1['boxenbez'] . "</p>";
            }
            
            echo "
            <hr>
            <h3>Verbrauch</h3>";


            $verbrauchstypsql = "SELECT verbrauchsguttyp.verbrauchsguttypbez FROM pferd_frisst_verbrauchsguttyp, verbrauchsguttyp WHERE pferd_frisst_verbrauchsguttyp.id_pferd = " . $_GET['id_pferd']. " AND pferd_frisst_verbrauchsguttyp.id_verbrauchsguttyp = verbrauchsguttyp.id_verbrauchsguttyp";
            $verbrauchstyp = $conn->query($verbrauchstypsql) or die (mysql_error());

            $bedarfsql = "SELECT id_verbrauchsguttyp, bedarf FROM pferd_frisst_verbrauchsguttyp WHERE id_pferd = " . $_GET['id_pferd']. "";
            $bedarf = $conn->query($bedarfsql) or die (mysql_error());

            
            echo "
            <div class='table-responsive'>
            <table class='table table-bordered' id='dataTable' width='100%' cellspacing='0'>
            <thead>
              <tr>
                <th>Verbrauchsgut</th>
                <th>Bedarf</th>
              </tr>
            </thead>
            
            <tbody>";
            while($fetch2 = mysqli_fetch_assoc($verbrauchstyp) and $fetch3 = mysqli_fetch_assoc($bedarf)){
              echo "<tr><td>" . $fetch2['verbrauchsguttypbez'] . "</td><td>" . $fetch3['bedarf'] . "</td></tr>";
            
            }
            
            echo "
            </tbody>
            </table>
            </div>
            
            <hr>
            <h3>Personen</h3>";

            $personsql = "SELECT person.id_person, vorname, nachname, funktionsbez FROM person, funktion, beziehung WHERE beziehung.id_pferd = " . $_GET['id_pferd'] . " AND person.id_person = beziehung.id_person AND beziehung.id_funktion = funktion.id_funktion";
            $personbez = $conn->query($personsql);

            echo "
            <div class='table-responsive'>
            <table class='table table-bordered' id='dataTable' width='100%' cellspacing='0'>
            <thead>
              <tr>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Funktion</th>
                <th>Aktion</th>
              </tr>
            </thead>
            
            <tbody>";

              while($fetch = mysqli_fetch_assoc($personbez)){
                echo "<tr>";
                echo "<td>" . $fetch['vorname'] .  "</td>";
                echo "<td>" . $fetch['nachname'] .  "</td>";
                echo "<td>" . $fetch['funktionsbez'] . "</td>";

                echo '<td> 
                  <a href="person-show.php?id_person=' . $fetch["id_person"] . '" >Anzeigen</a><br>
                  <a href="person-edit.php?id_person=' . $fetch["id_person"] . '" >Bearbeiten</a></td></tr>';              
              }
                
            echo "
            </tbody>
            </table>
            </div>";
            
            
            echo "<hr>";

            echo "<div class=\"form-group\"></div>
            <div class=\"form-group\">
            <a class=\"btn btn-primary\" href=\"pferd-edit.php?id_pferd=" . $row_p['id_pferd'] . "\" >Bearbeiten</a>
            <a class=\"btn btn-danger\" href=\"pferd-deleted.php?id_pferd=" . $row_p['id_pferd'] . "\" onclick='return checkDelete()'>Löschen</a>
            <a class=\"btn btn-secondary\" href=\"pferd.php\" >zurück zur Übersicht</a></div>";

        ?>


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

    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

    <!-- JavaScript for Delete-Confirmation -->
    <script>
      function checkDelete(){
        return confirm('Pferd endgültig löschen?')
      }
    </script>

  </body>

</html>
