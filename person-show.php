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
	
	$auth_sql = "SELECT id_gehoeft FROM person WHERE id_person = " . $_GET['id_person'] . "";
	$auth_result =  $conn->query($auth_sql);
	$auth_result = $auth_result->fetch_assoc();
	
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

          $personquery = "SELECT * FROM person, adresse WHERE adresse.id_adresse = person.id_adresse AND person.id_person = ?";
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
            echo "<h1>" . $row_p['vorname'] ." " . $row_p['nachname'] . "</h1> <hr>";

            //Darstellung der Person            
            echo "<p>E-Mail: " . $row_p['email'] . "</p>";
            echo "<p>Telefonnummer: " . $row_p['telefonnr'] . "</p>";
            echo "<p>Geburtsdatum: " . $row_p['geburtsdatum'] . "</p>";

            echo "<br><h3> Adresse </h3>";

            echo "<p>" . $row_p['strasse'] . " " . $row_p['hausnr'] . "</p>";
            echo "<p>" . $row_p['plz'] . " " . $row_p['ort'] . "</p>";
            echo "<p>" . $row_p['land'] . "</p>"; 
            
            echo "<hr>";

            
            
            //Abfrage, ob diese Person Beziehungen hat
            $funktion_query = 'SELECT funktion.funktionsbez FROM beziehung, funktion WHERE beziehung.id_person = ? AND beziehung.id_funktion = funktion.id_funktion';
            $funktion_sql = $conn->prepare($funktion_query);
            $funktion_sql->bind_param("i",$_GET["id_person"]);
            $funktion_sql->execute();
            $query1 = $funktion_sql->get_result();

              if($query1->num_rows==0){ //wird ausgeführt wenn die Person keine Beziehungen hat,also gelöscht werden kann
               echo "<div class=\"form-group\"></div>
               <div class=\"form-group\">
                <a class=\"btn btn-primary\" href=\"person-edit.php?id_person=" . $id_person . "\" >Bearbeiten</a>
                <a class=\"btn btn-danger\" href=\"person-delete.php?id_person=" . $id_person . "\" onclick='return checkDelete()'>Löschen</a>
                <a class=\"btn btn-secondary\" href=\"person.php\" >Zurück zur Übersicht</a> </div>";
              }
              else{ //wird ausgeführt wenn die Person Beziehungen hat also nicht gelöscht werden kann.
  
                //Tabelle wird erzeugt
                echo "
                <h3>Beziehungen zu dieser Person</h3><br>";

                echo "
                <div class='table-responsive'>
                <table class='table table-bordered table-hover display' id='dataTable2' width='100%' cellspacing='0'>
                <thead class='thead-light'>
                  <tr>
                  <th>Pferdename</th>
                  <th>Funktion</th>
                  <th></th>
                  </tr>
                </thead>
                
                <tbody>";

                
                $pferd_query= "SELECT pferd.id_pferd, pferdename, funktionsbez FROM pferd, funktion, beziehung WHERE beziehung.id_person = ? AND pferd.id_pferd = beziehung.id_pferd AND beziehung.id_funktion = funktion.id_funktion";
                $pferd_sql = $conn->prepare($pferd_query);
                $pferd_sql->bind_param("i",$_GET["id_person"]);
                $pferd_sql->execute();
                $pferd_bez = $pferd_sql->get_result();
  
                while($fetch = mysqli_fetch_assoc($pferd_bez)){ //für jede Beziehung wird eine Zeile erzeugt
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

                }
                echo "
                  </tbody>
                  </table>
                  </div>
                  <hr>";

                echo "
                <div class=\"form-group\">
                  <a class=\"btn btn-primary\" href=\"person-edit.php?id_person=" . $id_person . "\" >Bearbeiten</a>
                  <a class=\"btn btn-outline-danger disabled\" href=\"#\" onclick='return checkDelete()'>Löschen nicht möglich*</a>
                  <a class=\"btn btn-secondary\" href=\"person.php\" >Zurück zur Übersicht</a></div>
                  *Diese Person kann nicht gelöscht werden, da ihr mindestens ein Pferd zugeordnet ist.";
          
            }
            
          }

        }
	
          else {
            echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für diese Person!</div><hr>';
          }

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
    <script>
    $(document).ready(function() {
    $('#dataTable').DataTable( {
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
        }
    } );
} );
    </script>

    <!-- For this Page -->
    <script> function checkDelete(){ return confirm('Person endgültig löschen?') } </script>

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>
