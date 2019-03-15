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

$user = false;
$admin = false;
$changed = false;
$wiederholung = false;
$passwort_falsch = false;

session_start();

$mail  = $_SESSION["mail"];
$admin_mail_array = array("alisa@hrp-projekt.de", "henrik@hrp-projekt.de", "jan@hrp-projekt.de", "julia@hrp-projekt-de", "kerstin@hrp-projekt.de", "demo_admin@hrp-projekt.de");

// Prüfung, ob User eingeloggt ist
if(isset($_SESSION["logged"]) && $_SESSION["logged"] == true) {
  $user = true;
}

// Oder ob User ein Admin ist
else if (in_array($mail, $admin_mail_array)) {
  $admin = true;
}

// Ansonsten Weiterleitung auf login
else {
  header('location:login.php');
}

// Prüfung, ob alle Felder gesetzt sind
if (isset($_POST['passwort_alt']) && isset($_POST['passwort_neu']) && isset($_POST['passwort_confirm'])) {

  // Prüfung, ob Passwort und Passwort-Wiederholung übereinstimmen
  if ($_POST['passwort_neu'] === $_POST['passwort_confirm']) {
    $wiederholung = false;

    $passwort_alt = $_POST["passwort_alt"];
    $passwort_neu = $_POST["passwort_neu"];
    $passwort_confirm = $_POST["passwort_confirm"];

    // SQL-Abfrage für aktuelles Passwort
    $passwort_query = "SELECT passwort FROM benutzer WHERE email = ?";
    $passwort_sql = $conn->prepare($passwort_query);
    $passwort_sql->bind_param("s", $mail);
    $passwort_sql->execute();
    $passwort_result = $passwort_sql->get_result();

      $passwort_fetch = $passwort_result->fetch_assoc();

      $passwort = $passwort_fetch["passwort"];
      $passwort_alt_hash = md5($passwort_alt);

      // Prüfung, ob eingebenes aktuelles Passwort mit aktuellem Passwort übereinstimmt
      if ($passwort === $passwort_alt_hash) {

        $passwort_falsch = false;

        // Hashen des Passworts und Update in der DB
        $passwort_neu_hash = md5($passwort_neu);

        $passwort_change_query = "UPDATE benutzer SET passwort = ? WHERE email = ?";
        $passwort_change_sql = $conn->prepare($passwort_change_query);
        $passwort_change_sql->bind_param("ss", $passwort_neu_hash, $mail);
        $passwort_change_sql->execute();

        $changed = true;
        // Löschen der Session und Logout
        session_destroy();
      }

      else {
        $passwort_falsch = true;
    }
  }
  else {
    $wiederholung = true;
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

    <title>HRP - Passwort ändern</title>

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

    <?php
    if ($user) {
    ?>

      <!-- Sidebar User -->
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
        <li class="nav-item">
          <a class="nav-link" href="person.php">
            <i class="fas fa-fw fa-address-book"></i>
            <span>Personen</span>
          </a>
        </li>
      </ul>
      
    <?php 
    }

    if ($admin) {
    ?>

    <!-- Sidebar Admin -->
    <ul class="sidebar navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="admin.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Admin</span>
          </a>
        </li>
    </ul>

    <?php
    }
    ?>

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Page Content -->
          
          <!-- Überschrift -->
          <h1>Passwort ändern</h1>
          <hr>
          <br>

          <?php
          if ($changed == true) {
          ?>

          <div class="alert alert-success" role="alert">Das Passwort wurde geändert</div>
          
          <div class="form-group">
          <a class="btn btn-secondary" href="login.php">Zum Login</a>
          </div>

          <?php
          }
          ?>

          <?php
          if ($changed == false) {

          if ($wiederholung) {
            echo '<div class="alert alert-danger" role="alert">Passwörter sind nicht identisch!</div><hr><br>';
          }

          if ($passwort_falsch) {
            echo '<div class="alert alert-danger" role="alert">Falsches Passwort!</div><hr><br>';
          }
          ?>

          <form action="passwort.php" method="post" onsubmit="return checkChange()">
            <div class="form-group">
            <label for="passwort_alt">
              Altes Passwort
            </label>
            <input class="form-control" type="password" id="passwort_alt" name="passwort_alt" required><br>
            <label for="passwort_neu">
              Neues Passwort
            </label>
            <input class="form-control" minlength="8" type="password" id="passwort_neu" name="passwort_neu" required><br>
            <label for="passwort_confirm">
              Neues Passwort wiederholen
            </label>
            <input class="form-control" minlength="8" type="password" id="passwort_confirm" name="passwort_confirm" required>
            </div>
            
            <div class="form-group">

              <button class="btn btn-success">
                Passwort ändern
              </button>
              <?php 
              if ($user) {
                echo '<a class="btn btn-secondary" href="dashboard.php" >Abbrechen</a>';
              }

              if ($admin) {
                echo '<a class="btn btn-secondary" href="admin.php" >Abbrechen</a>';
              }
              ?>

            </div>

          </form>
          <?php
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

  <!-- JavaScript for Change-Confirmation -->
  <script>
    function checkChange(){
      return confirm('Passwort ändern?')
    }
  </script>
    
  </body>

</html>