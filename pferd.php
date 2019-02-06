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

          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="dashboard.php">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">
              Pferde
            </li>
          </ol>

          <h1>Übersicht Pferde</h1>
          <hr>

          <div class="container-fluid">
          <div class="row justify-content-end">
          <a class="btn btn-success" role="button" href="pferd-edit.php?id_pferd=0">Hinzufügen</a>
          </div>
          </div>
          
          <p>
          <div class="table-responsive">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
              <th>Name</th>
              <th>Geschlecht</th>
              <th>Besitzer</th>
              <th>Boxentyp</th>
              <th>Aktion</th>
            </tr>
          </thead>

            <?php

              $servername = "localhost";
              $username = "root";
              $password = "";
              $dbname = "hrppr_db1";

              // Create connection
              $conn = new mysqli($servername, $username, $password, $dbname);
              $mysqli = new mysqli($servername, $username, $password, $dbname);
              // Check connection
              if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
              } 
                        
              // SQL-Anfrage: Ergebnis ist stets eine Tabelle
              $pferd = "SELECT * FROM pferd";
              
              $query = $conn->query($pferd) or die(mysql_error());

              while($fetch = mysqli_fetch_assoc($query)){
                echo '<tr>';
                  echo '<td>' . $fetch['pferdename'] . '</td>';
                  echo '<td>' ;
                    if( $fetch['geschlecht'] =='s')
                      {
                        echo "Stute";
                      }
                    else if( $fetch['geschlecht'] =='w')
                      {
                        echo "Wallach";
                      }
                    else
                      {
                        echo "Hengst";
                      }
              
                  echo '</td>';

                  $besitzer = 'SELECT person.vorname, person.nachname From person, beziehung  WHERE beziehung.id_pferd = '.$fetch['id_pferd'].' AND beziehung.id_funktion = 1 AND beziehung.id_person=person.id_person';
                    $query1 = $conn->query($besitzer) or die (mysql_error());
                    while($fetch1 = mysqli_fetch_assoc($query1)){
                      echo '<td>' . $fetch1['vorname'] . ' ' . $fetch1['nachname'] . '</td>'  ;
                    }
                  
                  $boxentyp = 'SELECT boxentyp.boxenbez From box, boxentyp WHERE box.id_pferd = '.$fetch['id_pferd'].' AND box.id_boxentyp = boxentyp.id_boxentyp' ;
                    $query2 = $conn->query($boxentyp) or die (mysql_error());
                    while($fetch2 = mysqli_fetch_assoc($query2)){
                      echo '<td>' . $fetch2['boxenbez'] . '</td>'  ;
                    }

                  echo '<td> 
                    <a href="pferd-show.php?id_pferd=' . $fetch["id_pferd"] . '">Anzeigen</a> <br> 
                    <a href="pferd-edit.php?id_pferd=' . $fetch["id_pferd"] . '" >Bearbeiten</a> <br>
                    <a href="pferd-delet.php?id_pferd=' . $fetch["id_pferd"] . '" >Löschen</a> <br>
                  </td>';

                echo '</tr>';
              }

              ?>
                          

          </table>
            </div>
            </p>
      

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

    <!-- Page level plugin JavaScript-->
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>


    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>

      <!-- Demo scripts for this page-->
  <script src="js/demo/datatables-demo.js"></script>

  </body>

</html>
