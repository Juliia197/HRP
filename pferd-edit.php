<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hrppr_db1";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

session_start();

if($_SESSION["logged"] == true) {

  
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
          <a class="nav-link" href="personen.php">
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
            <li class="breadcrumb-item">
              <a href="pferd.php">Pferde</a>
            </li>
            <li class="breadcrumb-item active">
              Pferd bearbeiten
            </li>
          </ol>

          <h1>Pferd editieren</h1>
          <hr>
          <p>Auf dieser Seite können Sie alle Informationen rund um das Pferd bearbeiten. Außerdem die Personen, die mit dem Pferd in Verbindung stehen, einsehen und ändern.</p>

            <?php
                $pferdId = !isset($_GET['id_pferd']) ? 0 : (int) $_GET['id_pferd'];

                if (isset($_POST['pferdename'])) {
                    $pferdename = !isset($_POST['pferdename']) ? '' : $_POST['pferdename'];
                    $geschlecht = !isset($_POST['geschlecht']) ? '' : $_POST['geschlecht'];
                    $gewicht = !isset($_POST['gewicht']) ? '' : $_POST['gewicht'];
                    $groesse = !isset($_POST['groesse']) ? '' : $_POST['groesse'];
                    $passnr = !isset($_POST['passnr']) ? '' : $_POST['passnr'];
                    $gebursdatum = !isset($_POST['geburtsdatum_pferd']) ? '' : $_POST['geburtsdatum_pferd'];
                    $ankunft = !isset($_POST['ankunft']) ? '' : $_POST['ankunft'];

                    if ($pferdId === 0) {
                        $prepare = $conn->prepare('
                        INSERT INTO 
                            pferd 
                          ( 
                            pferdename,
                            geschlecht,
                            gewicht,
                            groesse,
                            passnr,
                            geburtsdatum_pferd,
                            ankunft
                          ) VALUES (
                            ?,?,?,?,?,?,?
                          )');
                        $bind = [$pferdename, $geschlecht, $gewicht, $groesse, $passnr, $gebursdatum, $ankunft];
                    } else {
                        $prepare = $conn->prepare(
                          "UPDATE
                            pferd
                          SET
                            pferdename ='$pferdename',
                            geschlecht = '$geschlecht',
                            gewicht='$gewicht',
                            groesse='$groesse',
                            passnr='$passnr',
                            geburtsdatum_pferd='$gebursdatum',
                            ankunft='$ankunft'
                          WHERE
                           id_pferd = ?"
                          );
                        $bind = [$pferdId];
                    }

                    if ($pferdId !== 0) {
                        foreach ($_POST as $key => $post) {
                            if (strpos($key, 'userId-') === 0) {
                                $connId = abs((int) filter_var($key, FILTER_SANITIZE_NUMBER_INT));
                                $userId = $_POST['userId-'.$connId];
                                $functionId = $_POST['functionId-'.$connId];
                                $prepareCon = $conn->prepare(
                                    "UPDATE
                                        beziehung
                                      SET
                                        id_person ='$userId',
                                        id_funktion = '$functionId',
                                        id_pferd='$pferdId'
                                      WHERE
                                       id_beziehung = ?"
                                        );
                                $bindCon = [$connId];
                                $prepareCon->execute($bindCon);
                            }
                            if (strpos($key, 'userIdNew-') === 0) {
                                $connId = abs((int) filter_var($key, FILTER_SANITIZE_NUMBER_INT));
                                $userId = $_POST['userIdNew-'.$connId];
                                $functionId = $_POST['functionIdNew-'.$connId];
                                $prepareCon = $conn->prepare('
                                    INSERT INTO 
                                        beziehung 
                                      ( 
                                        id_person,
                                        id_funktion,
                                        id_pferd
                                      ) VALUES (?,?,?)'
                                );
                                $bindCon = [(int) $userId, (int) $functionId, $pferdId];
                                $prepareCon->execute($bindCon);
                            }
                        }
                    }

                    if ($pferdId === 0 && $prepare->execute($bind)) {
                        $pferdId = $conn->lastInsertId();
                        foreach ($_POST as $key => $post) {
                            if (strpos($key, 'userIdNew-') === 0) {
                                $connId = abs((int) filter_var($key, FILTER_SANITIZE_NUMBER_INT));
                                $userId = $_POST['userIdNew-'.$connId];
                                $functionId = $_POST['functionIdNew-'.$connId];
                                $prepareCon = $conn->prepare('
                                INSERT INTO 
                                    beziehung 
                                  ( 
                                    id_person,
                                    id_funktion,
                                    id_pferd
                                  ) VALUES (?,?,?)'
                                );
                                $bindCon = [(int) $userId, (int) $functionId, $conn->lastInsertId()];
                                $prepareCon->execute($bindCon);
                            }
                        }
                        header('Location: pferd-edit.php?id_pferd=' . $pferdId);
                        exit();
                    }


                } else {
                    $sql = 'SELECT * FROM pferd WHERE id_pferd = ' . $_GET['id_pferd'];
                    $pferd = $conn->query($sql);

                    $pferd = $pferd->fetch(MYSQLI_ASSOC);

                    $pferdename = !isset($pferd['pferdename']) ? '' : $pferd['pferdename'];
                    $geschlecht = !isset($pferd['geschlecht']) ? '' : $pferd['geschlecht'];
                    $gewicht = !isset($pferd['gewicht']) ? '' : $pferd['gewicht'];
                    $groesse = !isset($pferd['groesse']) ? '' : $pferd['groesse'];
                    $passnr = !isset($pferd['passnr']) ? '' : $pferd['passnr'];
                    $gebursdatum = !isset($pferd['geburtsdatum_pferd']) ? '' : $pferd['geburtsdatum_pferd'];
                    $ankunft = !isset($pferd['ankunft']) ? '' : $pferd['ankunft'];
                }

                $connections = [];

                if ($pferdId !== 0) {
                    $sql = 'SELECT * FROM beziehung WHERE id_pferd = ' . $pferdId;
                    $connections = $conn->query($sql);
                    $connections = $connections->fetchAll();
                }

                $sql = 'SELECT id_person, vorname, nachname FROM person';
                $users = $conn->query($sql);
                $users = $users->fetchAll();

                $sql = 'SELECT * FROM funktion';
                $functions = $conn->query($sql);
                $functions = $functions->fetchAll();
            ?>

            <form action="pferd-edit.php?id_pferd=<?php echo $pferdId ?>"  method="post">
                <label>Pferdname:</label>
                <input class="form-control" type="text" value="<?php echo $pferdename ?>" name="pferdename" required> <br />
                <label>Geschlecht:</label>
                <select name="geschlecht">
                    <option <?php if ($geschlecht === 's') {echo 'selected';} ?> value="s">s</option>
                    <option <?php if ($geschlecht === 'h') {echo 'selected';} ?> value="h">h</option>
                    <option <?php if ($geschlecht === 'w') {echo 'selected';} ?> value="w">w</option>
                </select><br /><br />
                <label>Gewicht (in kg):</label>
                <input class="form-control" type="number" value="<?php echo $gewicht ?>" name="gewicht" required><br />
                <label>Größe (in cm):</label>
                <input class="form-control" type="number" value="<?php echo $groesse ?>" name="groesse" required><br />
                <label>Passnummer:</label>
                <input class="form-control" type="number" value="<?php echo $passnr ?>" name="passnr" required><br />
                <label>Geburtsdatum:</label>
                <input class="form-control" type="date" value="<?php echo $gebursdatum ?>" name="geburtsdatum_pferd" required><br />
                <label>Ankunft des Pferdes am Hof:</label>
                <input class="form-control" type="date" value="<?php echo $ankunft ?>" name="ankunft" required><br />

                <br />

                <?php
                    foreach ($connections as $connection) {
                ?>
                       <select name="userId-<?php echo $connection['id_beziehung'];?>" id="userId-<?php echo $connection['id_beziehung'];?>">
                           <?php
                                foreach ($users as $user) {
                           ?>
                                    <option <?php if ($connection['id_person'] === $user['id_person']) {echo 'selected';} ?> value="<?php echo $user['id_person']; ?>"><?php echo $user['vorname'] . ' ' . $user['nachname']; ?></option>
                           <?php
                                }
                           ?>
                        </select>
                        <select name="functionId-<?php echo $connection['id_beziehung'];?>" id="functionId-<?php echo $connection['id_beziehung'];?>">
                            <?php
                            foreach ($functions as $function) {
                                ?>
                                <option <?php if ($connection['id_funktion'] === $function['id_funktion']) {echo 'selected';} ?> value="<?php echo $function['id_funktion']; ?>"><?php echo $function['funktionsbez']; ?></option>
                                <?php
                            }
                            ?>
                        </select>

                        <span class="delete btn btn-danger btn-sm" id="delete-<?php echo $connection['id_beziehung'];?>" data-connid="<?php echo $connection['id_beziehung'];?>">Löschen</span>

                        <br />
                <?php
                    }
                ?>

                <span id="addNewConnection">Dem Pferd eine neue Person zuweisen...</span> <br /><br />

                <button type="submit" class="btn btn-success" id="sendButton">Speichern!</button>
            </form>

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

    <script>
        let i = 0;
        $('#addNewConnection').click(function(){
            i++;
            $('#addNewConnection').before('<select name="userIdNew-'+i+'" id="userIdNew-'+i+'">' +
                <?php
                foreach ($users as $user) {
                ?>
                '<option value="<?php echo $user['id_person']; ?>"><?php echo $user['vorname'] . ' ' . $user['nachname']; ?></option>' +
                <?php
                }
                ?>
                '</select>' +
                '<select name="functionIdNew-'+i+'" id="functionIdNew-'+i+'">' +
                <?php
                foreach ($functions as $function) {
                ?>
                '<option value="<?php echo $function['id_funktion']; ?>"><?php echo $function['funktionsbez']; ?></option>' +
                <?php
                }
                ?>
                '</select>' +
                '<span class="deleteNew btn btn-danger btn-sm" data-connid='+i+' id="deleteNew-'+i+'"> Löschen</span>'+
                '<br />'
                );
        });

        $('.delete').click(function(){
            let connId = $(this).data('connid');
            $.post( "pferd-delete.php", { connId: connId } );
            $('#userId-'+connId).remove();
            $('#functionId-'+connId).remove();
            $('#delete-'+connId).remove();
        });

        $("body").on("click", ".deleteNew", function(){
            let connId = $(this).data('connid');
            $('#userIdNew-'+connId).remove();
            $('#functionIdNew-'+connId).remove();
            $(this).remove();
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