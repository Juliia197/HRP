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
      $personname_sql = "SELECT vorname, nachname FROM person WHERE id_person = ?";
      $personname_result = $conn->prepare($personname_sql);
      $personname_result->bind_param('i', $_GET['id_person']);
      $personname_result->execute();
      $personname_result = $personname_result->get_result();
      $personname_result = $personname_result->fetch_assoc();
      $name = $personname_result['vorname'] . " " . $personname_result['nachname'];
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

          $id_person = $_GET["id_person"];

          //Daten zur Person werden abgerufen

          $personquery = "SELECT *, DATE_FORMAT(person.geburtsdatum, '%d.%m.%Y') as geburtsdatum FROM person, adresse WHERE adresse.id_adresse = person.id_adresse AND person.id_person = ?";
          $person_sql = $conn->prepare($personquery);
          $person_sql->bind_param("i",$_GET["id_person"]);
          $person_sql->execute();
          $person = $person_sql->get_result();

          while($row_p = $person->fetch_assoc()){
            //Leiste zur Darstellung der aktuellen Position auf der Seite
            echo "<ol class=\"breadcrumb\">
                  <li class=\"breadcrumb-item\">
                    <a href=\"dashboard.php\">Dashboard</a>
                  </li>
                  <li class=\"breadcrumb-item\">
                    <a href=\"person.php\">Personen</a>
                  </li>
                  <li class=\"breadcrumb-item active\">
                    Person anzeigen
                  </li>
                </ol>";
            //Überschrift
            echo "<h1>" . $row_p['vorname'] ." " . $row_p['nachname'] . "</h1> <hr><br>";

            //Darstellung der Person            
            echo "<p>E-Mail: " . $row_p['email'] . "</p>";
            echo "<p>Telefonnummer: " . $row_p['telefonnr'] . "</p>";
            echo "<p>Geburtsdatum: " . $row_p['geburtsdatum'] . "</p>";

            echo "<br><h3> Adresse </h3>";

            echo "<p>" . $row_p['strasse'] . " " . $row_p['hausnr'] . "</p>";
            echo "<p>" . $row_p['plz'] . " " . $row_p['ort'] . "</p>";
            echo "<p>"; 
            if ($row_p['land'] == 'DE'){
              $land = 'Deutschland';
            } elseif ($row_p['land'] == 'AT'){
              $land = 'Österreich';
            } else {
              $land = 'Schweiz';
            };
            echo $land;
            echo "</p>"; 
            
            echo "<hr><br>";

            
            
            //Abfrage, ob diese Person Beziehungen hat
            $funktion_query = 'SELECT funktion.funktionsbez FROM beziehung, funktion WHERE beziehung.id_person = ? AND beziehung.id_funktion = funktion.id_funktion';
            $funktion_sql = $conn->prepare($funktion_query);
            $funktion_sql->bind_param("i",$_GET["id_person"]);
            $funktion_sql->execute();
            $query1 = $funktion_sql->get_result();

            $lieferant= 5;
            $funktion_query = 'SELECT funktion.funktionsbez FROM beziehung, funktion WHERE beziehung.id_person = ? AND beziehung.id_funktion = funktion.id_funktion AND beziehung.id_funktion < ?' ;
            $funktion_sql = $conn->prepare($funktion_query);
            $funktion_sql->bind_param("ii",$_GET["id_person"],$lieferant);
            $funktion_sql->execute();
            $funktion = $funktion_sql->get_result();

            $lieferung_query= "SELECT verbrauchsgutbez, lieferdatum, menge, einkaufspreis, id_verbrauchsguttyp FROM verbrauchsgut WHERE id_person =? AND id_gehoeft = $id_gehoeft";
            $lieferung_sql = $conn->prepare($lieferung_query);
            $lieferung_sql->bind_param("i",$_GET["id_person"]);
            $lieferung_sql->execute();
            $lieferung = $lieferung_sql->get_result();
            $lieferant_sql = "SELECT id_beziehung FROM beziehung WHERE id_funktion = 5 AND id_person =" . $_GET['id_person'];
            $lieferant = $conn->query($lieferant_sql);

              if($query1->num_rows>0 ){
                
                $pferd_query= "SELECT pferd.id_pferd, pferdename, funktionsbez FROM pferd, funktion, beziehung WHERE beziehung.id_person = ? AND pferd.id_pferd = beziehung.id_pferd AND beziehung.id_funktion = funktion.id_funktion";
                $pferd_sql = $conn->prepare($pferd_query);
                $pferd_sql->bind_param("i",$_GET["id_person"]);
                $pferd_sql->execute();
                $pferd_bez = $pferd_sql->get_result();
  
                while($fetch = mysqli_fetch_assoc($pferd_bez)){ //für jede Beziehung wird eine Zeile erzeugt
                  //Tabelle wird erzeugt
                  echo "
                  <h3>Beziehungen zu dieser Person</h3><br>";
  
                  echo "
                  <div class='table-responsive'>
                  <table class='table table-bordered table-hover display' id='dataTable1' width='100%' cellspacing='0'>
                  <thead class='thead-light'>
                    <tr>
                    <th>Pferdename</th>
                    <th>Funktion</th>
                    <th></th>
                    </tr>
                  </thead>                  
                  <tbody>";

                  echo '<tr>';
                  echo '<td>' . $fetch['pferdename'] .  '</td>';
                  echo '<td>' . $fetch['funktionsbez'] . '</td>';
  
                  //Links mit welchen die Id des Pferdes übergeben wird

                  echo '<td>
                  <div class="d-sm-flex flex-row">
                  <div><a class="btn btn-sm btn-dark" role="button" href="pferd-show.php?id_pferd=' . $fetch["id_pferd"] . '" >Anzeigen</a></div>
                  <div class="ml-0 ml-sm-2 mt-1 mt-sm-0"><a class="btn btn-sm btn-primary" role="button" href="pferd-edit.php?id_pferd=' . $fetch["id_pferd"] . '" >Bearbeiten</a></div>
                  </div>
                  </td>
                  </tr>';

              
                echo "
                  </tbody>
                  </table>
                  </div>
                  <hr>
                  <br>";

                }
              }
              
              if($lieferant->num_rows>0){
                echo "<h3 class='float-left'>Lieferungen zu dieser Person</h3>";
                echo "
                  <div class='table-responsive'>
                  <table class='table table-bordered table-hover display' id='dataTable2' width='100%' cellspacing='0'>
                  <thead class='thead-light'>
                    <tr>
                    <th>Verbrauchsgut</th>
                    <th>Bezeichnung</th>
                    <th>Lieferdatum</th>
                    <th>Menge</th>
                    <th>Einkaufspreis in € pro kg</th>
                    </tr>
                  </thead>
                  <tbody>";

                  while($lief = mysqli_fetch_assoc($lieferung)){ //für jede Lieferung wird eine Zeile erzeugt
                    echo '<tr>';
                    $verbrauchsguttyp_q = "SELECT verbrauchsguttypbez FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = " . $lief['id_verbrauchsguttyp'];
                    $verbrauchsguttyp = $conn->query($verbrauchsguttyp_q);

                    while ($bez = mysqli_fetch_assoc($verbrauchsguttyp)){
                      echo '<td>' . $bez['verbrauchsguttypbez'] . '</td>';
                    }
                    echo '<td>' . $lief['verbrauchsgutbez'] .  '</td>';
                    echo '<td>' . $lief['lieferdatum'] .  '</td>';
                    echo '<td>' . $lief['menge'] .  '</td>';
                    echo '<td>' . $lief['einkaufspreis'] .  '</td>';    
                    echo '</tr>';
  
                  }
                  echo "
                    </tbody>
                    </table>
                    </div>
                    <hr>";
              }
              if($lieferung->num_rows>0 or $funktion->num_rows>0){
                echo "
                <div class=\"form-group\">
                  <a class=\"btn btn-primary\" href=\"person-edit.php?id_person=" . $id_person . "\" >Bearbeiten</a>
                  <a class=\"btn btn-outline-danger disabled\" href=\"#\" onclick='return checkDelete()'>Löschen nicht möglich*</a>
                  <a class=\"btn btn-secondary\" href=\"person.php\" >Zurück zur Übersicht</a></div>
                  *Diese Person kann nicht gelöscht werden, da ihr entweder mindestens ein Pferd und/oder eine Lieferung zugeordnet ist.";
              } else{ // wird ausgeführt wenn die Person keine Beziehungen hat,also gelöscht werden kann
               echo "<div class=\"form-group\"></div>
               <div class=\"form-group\">
                <a class=\"btn btn-primary\" href=\"person-edit.php?id_person=" . $id_person . "\" >Bearbeiten</a>
                <a class=\"btn btn-danger\" href=\"person-delete.php?id_person=" . $id_person . "\" onclick='return checkDelete()'>Löschen</a>
                <a class=\"btn btn-secondary\" href=\"person.php\" >Zurück zur Übersicht</a> </div>";

              }         
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

    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

    <!-- JavaScript für Delete-Confirm -->
    <script>
      function checkDelete(){
        return confirm('Person endgültig löschen?')
      }
    </script>
    <!-- JavaScript für mehrere DataTables auf einer Seite -->
    <script>
    <script>
      function checkDelete(){
        return confirm('Person endgültig löschen?')
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