<?php
//Logindaten
$servername = "localhost";
$username = "hrppr_1";
$password = "J49Wj7wUbSsKmNC5";
$dbname = "hrppr_db1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$mysqli = new mysqli($servername, $username, $password, $dbname);
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

    <title>HRP - Personen</title>

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

          <!-- Leiste zur Darstellung der aktuellen Position auf der Seite -->
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="dashboard.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">
              Personen
            </li>
          </ol>
          
          <!-- Überschrift -->
          <h1>Übersicht Personen</h1>
          <hr>
          <br>

          <!-- Hinzufügen Button -->
          <div class="container-fluid">
          <div class="row justify-content-end">
          <a class="btn btn-success" role="button" href="person-edit.php?id_person=0">Hinzufügen</a>
          </div>
          </div>

          <!-- Tabelle mit den Personen in der Datenbank -->
          <p>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
           <thead class="thead-light">
            <tr >
              <th >Vorname</th>
              <th >Name</th>
              <th >Beziehung</th>
              <th></th>
            </tr>
            </thead>
                          
            <?php
              //Abrufen aller Personen
              $person_query = "SELECT * FROM person WHERE id_gehoeft = ?";
              $person_sql = $conn->prepare($person_query);
              $person_sql -> bind_param("i",$id_gehoeft);
              $person_sql->execute();
              $query=$person_sql->get_result();
              
              //While erzeugt für jede Zeile der Datenbank eine Tabellenzeile
              while($fetch = mysqli_fetch_assoc($query)){
                $funktion_query = 'SELECT funktion.funktionsbez FROM beziehung, funktion WHERE beziehung.id_person = ? AND beziehung.id_funktion = funktion.id_funktion GROUP BY funktion.funktionsbez';
                $funktion_sql = $conn->prepare($funktion_query);
                $funktion_sql->bind_param("i",$fetch["id_person"]);
                $funktion_sql->execute();
                $query3= $funktion_sql->get_result();
                
                echo '<tr>';
                  echo'<td>' . $fetch['vorname'] .  '</td>';
                  echo'<td>' . $fetch['nachname'] . '</td>';
                  echo '<td>';
                  
                  //Abrufen der verschiedenen Beziehungen die die Person mit einem oder mehreren Pferden hat
                  $funktion_query = 'SELECT funktion.funktionsbez FROM beziehung, funktion WHERE beziehung.id_person = ? AND beziehung.id_funktion = funktion.id_funktion  GROUP BY funktion.funktionsbez';
                  $funktion_sql = $conn->prepare($funktion_query);
                  $funktion_sql->bind_param("i",$fetch["id_person"]);
                  $funktion_sql->execute();
                  $query1 = $funktion_sql->get_result();
                  
                  while($fetch1 = mysqli_fetch_assoc($query1)){
                    echo'<p>' . $fetch1['funktionsbez'] . '</p>'; 
                  }
                  $istlieferant = $fetch['lieferant'];
                  if ($istlieferant == 1){
                    echo '<p>Lieferant</p>';
                  }
                  echo '</td>';

                  //Links zum verweisen auf die anderen Seiten, mit übergabe der Id des Pferdes
                  echo '
                  <td>
                  <div class="d-sm-flex flex-row">
                    <div><a class="btn btn-sm btn-dark" href="person-show.php?id_person=' . $fetch["id_person"] . '" >Anzeigen</a></div>
                    
                    <div class="ml-0 ml-sm-2 mt-1 mt-sm-0"><a class="btn btn-sm btn-primary" href="person-edit.php?id_person=' . $fetch["id_person"] . '" >Bearbeiten</a></div>';
                    
                    $lieferung_query= "SELECT id_verbrauchsgut FROM verbrauchsgut WHERE id_person =? AND id_gehoeft = $id_gehoeft";
                    $lieferung_sql = $conn->prepare($lieferung_query);
                    $lieferung_sql->bind_param("i",$fetch["id_person"]);
                    $lieferung_sql->execute();
                    $lieferung = $lieferung_sql->get_result();

                  if($query3->num_rows>0 or $lieferung->num_rows>0){  //Link zum Löschen wird nur angezeigt wenn löschen möglich ist
                    echo '<div class="ml-0 ml-sm-2 mt-1 mt-sm-0"><a class="btn btn-sm btn-outline-danger disabled" href=\"#\">Löschen nicht möglich*</a></div>';
                  }
                  else{
                    
                    echo '<div class="ml-0 ml-sm-2 mt-1 mt-sm-0"><a class="btn btn-sm btn-danger" role="button" href="person-delete.php?id_person=' . $fetch['id_person'] . '" onclick="return checkDelete()">Löschen</a></div>';
                    //echo '<div></div>';
                  }

                echo "</td></tr>";
              }


              ?>
          </table>
            </div>
        </p>
        *Diese Person kann nicht gelöscht werden, da ihr entweder mindestens ein Pferd und/oder eine Lieferung zugeordnet ist.


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
  <script> function checkDelete(){ return confirm('Person endgültig löschen?') } </script>

  </body>

</html>

<?php
}

else {

  header('location:login.php');

}

?>
