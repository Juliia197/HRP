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


$admin_mail  = $_SESSION["mail"];
$admin_mail_array = array("alisa@hrp-projekt.de", "henrik@hrp-projekt.de", "jan@hrp-projekt.de", "julia@hrp-projekt-de", "kerstin@hrp-projekt.de", "demo_admin@hrp-projekt.de");

if (isset($_GET["id_gehoeft"])) {
  $id_gehoeft = $_GET["id_gehoeft"];
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

      <a class="navbar-brand mr-1" href="dashboard.php">HRP - Admin</a>

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
        <li class="nav-item active">
          <a class="nav-link" href="admin.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Admin</span>
          </a>
        </li>
      </ul>

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Page Content -->
          <h1>Admin</h1>
          <hr>
          <br>

          <?php 
          if (in_array($admin_mail, $admin_mail_array)) {
          ?>

          <h2>Benutzer als Gehöftverwalter hinzufügen</h2>

          <form action= "admin-verwalter-added.php" method="post">
          <div class="form-group">
            <label for="email">E-Mail</label>
            <input class="form-control" id="email" name="email" type="email">
          </div>
          <input value="<?php echo $id_gehoeft ?>" name="id_gehoeft" type="hidden">
          <button type="submit" class="btn btn-success">Benutzer zum Gehöft hinzufügen</button>
          </form>

          <hr>
          <br>

          <h2>Gehöftverwalter</h2>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>E-Mail</th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              <?php

              $gehoeftverwalter_query = "SELECT benutzer.email, benutzer.id_benutzer FROM benutzer, benutzer_verwaltet_gehoeft WHERE benutzer.id_benutzer = benutzer_verwaltet_gehoeft.id_benutzer AND benutzer_verwaltet_gehoeft.id_gehoeft = ?";
              $gehoeftverwalter_sql = $conn->prepare($gehoeftverwalter_query);
              $gehoeftverwalter_sql->bind_param("i", $id_gehoeft);
              $gehoeftverwalter_sql->execute();
              $gehoeftverwalter_result = $gehoeftverwalter_sql->get_result();

              $nummer=1;
              while ($gehoeftverwalter_fetch = $gehoeftverwalter_result->fetch_assoc()) {
                echo '<tr>
                <td>' . $nummer . '</td>
                <td>' . $gehoeftverwalter_fetch['email'] . '</td>
                <td> <a class="btn btn-sm btn-danger" href="admin-verwalter-delete.php?id_benutzer=' . $gehoeftverwalter_fetch["id_benutzer"] . '&id_gehoeft='. $id_gehoeft .'" onclick="return checkDelete()">Benutzer entfernen</a></td>
                </tr>';
                $nummer += 1;
              } 
              
              ?>

            </tbody>
          </table>
          </div>
          <hr>

          <div class="form-group">
          <a class="btn btn-secondary" href="admin.php">Zurück zur Übersicht</a>
          </div>
          
          <?php
          }

          else {
            echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für die Admin-Funktionen!</div>';
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

  <!-- JavaScript for Delete-Confirmation -->
  <script>
    function checkDelete(){
      return confirm('Benutzer als Gehöftverwalter entfernen?')
    }
  </script>

  </body>

</html>