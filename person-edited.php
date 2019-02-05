<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dhbw";

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
            $vorname = $_POST["vorname"];
            $nachname = $_POST["nachname"];
            $email = $_POST["email"];
            $telefonnr = $_POST["telefonnr"];
            $update = $_GET["id_person"];
            if ($update > 0){
              $update_sql = "SELECT * FROM person WHERE id_person=" . $update;
              $update_result = $conn->query($update_sql);
              if($update_result->num_rows > 0){
                while($row_u = $update_result->fetch_assoc()){
                  echo "<h1>Änderung Person: " . $row_u["vorname"] . " " . $row_u["nachname"] . "</h1>";
                  $personupdate_sql = "UPDATE person SET vorname = '$vorname', nachname = '$nachname', email ='$email', telefonnr = '$telefonnr', geburtsdatum = '$geburtsdatum' WHERE id_person=$update;
                  $personupdate_result = $conn->query($personupdate_sql)";
                  echo "<p>Ihre Person wurde geändert!</p>";
                }
              }
            }
            else {
              echo "<ol class=\"breadcrumb\">
                    <li class=\"breadcrumb-item\">
                      <a href=\"dashboard.php\">Dashboard</a>
                    </li>
                    <li class=\"breadcrumb-item\">
                      <a href=\"person.php\">Personen</a>
                    </li>
                    <li class=\"breadcrumb-item active\">
                      Person erstellen
                    </li>
                  </ol>";
              echo "<h1>Neue Person hinzufügen</h1>";
              $personnew_sql = "INSERT INTO person (id_person, vorname, nachname, email, telefonnr, geburtsdatum) VALUES (NULL, '$vorname', '$nachname', '$email', '$telefonnr', '$geburtsdatum')";
              $personnew_result = $conn->query($personnew_sql);
              echo "<p>Ihre Person wurde hinzugefügt!</p>";
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

  </body>

</html>
