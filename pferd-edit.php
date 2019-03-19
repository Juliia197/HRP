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

  else if ($_GET['id_pferd'] == 0) {
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
      if ($_GET['id_pferd'] == 0){
        $pferdenamebearbeiten = "Pferd hinzufügen";
      } else {
        $pferdename_sql = "SELECT pferdename FROM pferd WHERE id_pferd = ?";
        $pferdename_result = $conn->prepare($pferdename_sql);
        $pferdename_result->bind_param('i', $_GET['id_pferd']);
        $pferdename_result->execute();
        $pferdename_result = $pferdename_result->get_result();
        $pferdename_result = $pferdename_result->fetch_assoc();
        $pferdenamebearbeiten = $pferdename_result['pferdename'] . " bearbeiten";
      }

    ?>

    <title>HRP - <?php echo $pferdenamebearbeiten; ?></title>

    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">

    <!-- Jan Custom styles for this template-->
    <link href="css/jan.css" rel="stylesheet">

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
            <li class="breadcrumb-item">
              <a href="pferd.php">Pferde</a>
            </li>
            <li class="breadcrumb-item active">
              <?php if ($_GET['id_pferd'] == 0){
                echo "Pferd hinzufügen";
              } else {
                echo "Pferd bearbeiten";
              }?>
            </li>
          </ol>
          

            <!-- Feststellung, ob Pferd bearbeitet/erstellt werden muss in DB oder nicht -->
            <?php

              if($auth)
              {
              if (isset($_GET['saved'])){
                $saved = $_GET['saved'];
              } else {
                $saved = false;
              }
              $id_pferd = $_GET['id_pferd'];
              if ($id_pferd === 0){
                $pferdename = '';
                $geschlecht = '';
                $gewicht = 0;
                $groesse = 0;
                $passnr = 0;
                $geburtsdatum = '';
                $ankunft = '';
              } else {
                $pferdtoedit_sql = "SELECT * FROM pferd WHERE id_pferd = ?";
                $pferdtoedit_result = $conn->prepare($pferdtoedit_sql);
                $pferdtoedit_result->bind_param('i', $_GET['id_pferd']);
                $pferdtoedit_result->execute();
                $pferdtoedit_result = $pferdtoedit_result->get_result();
                $pferdtoedit_result = $pferdtoedit_result->fetch_assoc();
                $pferdename = $pferdtoedit_result['pferdename'];
                $geschlecht = $pferdtoedit_result['geschlecht'];
                $gewicht = $pferdtoedit_result['gewicht'];
                $groesse = $pferdtoedit_result['groesse'];
                $passnr = $pferdtoedit_result['passnr'];
                $geburtsdatum = $pferdtoedit_result['geburtsdatum_pferd'];
                $ankunft = $pferdtoedit_result['ankunft'];
              }
              
              if ($saved){
                $pferdename = $_POST['pferdename'];
                $geschlecht = $_POST['geschlecht'];
                $gewicht = $_POST['gewicht'];
                $groesse = $_POST['groesse'];
                $passnr = $_POST['passnr'];
                $geburtsdatum = $_POST['geburtsdatum'];
                $ankunft = $_POST['ankunft'];
                $idbesitzer = $_POST['besitzerId'];
                $idrb = $_POST['rbId'];
                $idtierarzt = $_POST['tierarztId'];
                $idhufschmied = $_POST['hufschmiedId'];
                $idbox = $_POST['id_box'];

                if ($id_pferd == 0){
                  $neuespferd_sql = "INSERT INTO pferd (pferdename, geschlecht, gewicht, groesse, passnr, geburtsdatum_pferd, ankunft) VALUES (?, '$geschlecht', $gewicht, $groesse, $passnr, '$geburtsdatum', '$ankunft')";
                  $neuespferd_result = $conn->prepare($neuespferd_sql);
                  $neuespferd_result->bind_param('s', $pferdename);
                  $neuespferd_result->execute();

                  $newpferdid_sql = "SELECT id_pferd FROM pferd WHERE passnr = $passnr";
                  $newpferdid_result = $conn->query($newpferdid_sql);
                  $newpferdid_result = $newpferdid_result->fetch_assoc();
                  $id_pferd = $newpferdid_result['id_pferd'];

                  $besitzernew_sql = "INSERT INTO beziehung (id_pferd, id_funktion, id_person) VALUES ($id_pferd, 1, $idbesitzer)";
                  $besitzernew_result = $conn->query($besitzernew_sql);

                  if ($idrb != 0){
                    $rbnew_sql = "INSERT INTO beziehung (id_pferd, id_funktion, id_person) VALUES ($id_pferd, 2, $idrb)";
                    $rbnew_result = $conn->query($rbnew_sql);
                  }

                  if ($idtierarzt != 0){
                    $tierarztnew_sql = "INSERT INTO beziehung (id_pferd, id_funktion, id_person) VALUES ($id_pferd, 3, $idtierarzt)";
                    $tierarztnew_result = $conn->query($tierarztnew_sql);
                  }

                  if ($idhufschmied != 0){
                    $hufschmiednew_sql = "INSERT INTO beziehung (id_pferd, id_funktion, id_person) VALUES ($id_pferd, 4, $idhufschmied)";
                    $hufschmiednew_result = $conn->query($hufschmiednew_sql);
                  }
                  
                  $boxzuweisen_sql = "UPDATE box SET id_pferd = $id_pferd WHERE id_box = $idbox";
                  $boxzuweisen_result = $conn->query($boxzuweisen_sql);

                  echo "<div class=\"alert alert-success\" role=\"alert\">Dieses Pferd wurde erstellt!</div>";


                } else {
                  $pferdedit_sql = "UPDATE pferd SET pferdename = ?, geschlecht = '$geschlecht', gewicht = $gewicht, groesse = $groesse, passnr = $passnr, geburtsdatum_pferd = '$geburtsdatum', ankunft = '$ankunft' WHERE id_pferd = ?";
                  $pferdedit_result = $conn->prepare($pferdedit_sql);
                  $pferdedit_result->bind_param('si', $pferdename, $id_pferd);
                  $pferdedit_result->execute();

                  $pferdzubesitzer_sql = "SELECT * FROM beziehung WHERE id_pferd = $id_pferd AND id_funktion = 1";
                  $pferdzubesitzer_result = $conn->query($pferdzubesitzer_sql);
                  while ($pferdzubesitzer = $pferdzubesitzer_result->fetch_assoc()){
                    if ($idbesitzer != $pferdzubesitzer['id_person']){
                      $besitzerupdate_sql = "UPDATE beziehung SET id_person = $idbesitzer WHERE id_beziehung = " . $pferdzubesitzer['id_beziehung'];
                      $besitzerupdate_result = $conn->query($besitzerupdate_sql);
                    }
                  }

                  $pferdzurb_sql = "SELECT * FROM beziehung WHERE id_pferd = $id_pferd AND id_funktion = 2";
                  $pferdzurb_result = $conn->query($pferdzurb_sql);
                  if ($pferdzurb_result->num_rows > 0){
                    while ($pferdzurb = $pferdzurb_result->fetch_assoc()){
                      if ($idrb != $pferdzurb['id_person']){
                        if($idrb == 0){
                          $rbupdate_sql = "DELETE FROM beziehung WHERE id_beziehung = " . $pferdzurb['id_beziehung'];
                          $rbupdate_result = $conn->query($rbupdate_sql);
                        } else {
                          $rbupdate_sql = "UPDATE beziehung SET id_person = $idrb WHERE id_beziehung = " . $pferdzurb['id_beziehung'];
                          $rbupdate_result = $conn->query($rbupdate_sql);
                        }
                      }
                    }

                   

                  } else {
                    if ($idrb != 0){
                      $rbnew_sql = "INSERT INTO beziehung (id_pferd, id_funktion, id_person) VALUES ($id_pferd, 2, $idrb)";
                      $rbnew_result = $conn->query($rbnew_sql);
                    }
                  }
                  
                  $pferdzutierarzt_sql = "SELECT * FROM beziehung WHERE id_pferd = $id_pferd AND id_funktion = 3";
                  $pferdzutierarzt_result = $conn->query($pferdzutierarzt_sql);
                  if ($pferdzutierarzt_result->num_rows > 0){
                    while ($pferdzutierarzt = $pferdzutierarzt_result->fetch_assoc()){
                      if ($idtierarzt != $pferdzutierarzt['id_person']){
                        if($idtierarzt == 0){
                          $tierarztupdate_sql = "DELETE FROM beziehung WHERE id_beziehung = " . $pferdzutierarzt['id_beziehung'];
                          $tierarztupdate_result = $conn->query($tierarztupdate_sql);
                        } else {
                          $tierarztupdate_sql = "UPDATE beziehung SET id_person = $idtierarzt WHERE id_beziehung = " . $pferdzutierarzt['id_beziehung'];
                          $tierarztupdate_result = $conn->query($tierarztupdate_sql);
                        }
                      }
                    }

                  } else {
                    if ($idtierarzt != 0){
                      $tierarztnew_sql = "INSERT INTO beziehung (id_pferd, id_funktion, id_person) VALUES ($id_pferd, 3, $idtierarzt)";
                      $tierarztnew_result = $conn->query($tierarztnew_sql);
                    }
                  }

                  
                  $pferdzuhufschmied_sql = "SELECT * FROM beziehung WHERE id_pferd = $id_pferd AND id_funktion = 4";
                  $pferdzuhufschmied_result = $conn->query($pferdzuhufschmied_sql);
                  if ($pferdzuhufschmied_result->num_rows > 0){
                    while ($pferdzuhufschmied = $pferdzuhufschmied_result->fetch_assoc()){
                      if ($idhufschmied != $pferdzuhufschmied['id_person']){
                        if($idhufschmied == 0){
                          $hufschmiedupdate_sql = "DELETE FROM beziehung WHERE id_beziehung = " . $pferdzuhufschmied['id_beziehung'];
                          $hufschmiedupdate_result = $conn->query($hufschmiedupdate_sql);
                        } else {
                          $hufschmiedupdate_sql = "UPDATE beziehung SET id_person = $idhufschmied WHERE id_beziehung = " . $pferdzuhufschmied['id_beziehung'];
                          $hufschmiedupdate_result = $conn->query($hufschmiedupdate_sql);
                        }
                      }
                    }

                  } else {
                    if ($idhufschmied != 0){
                      $hufschmiednew_sql = "INSERT INTO beziehung (id_pferd, id_funktion, id_person) VALUES ($id_pferd, 4, $idhufschmied)";
                      $hufschmiednew_result = $conn->query($hufschmiednew_sql);
                    }
                  }

                  $boxaktuell_sql = "SELECT * FROM box WHERE id_pferd = $id_pferd";
                  $boxaktuell_result = $conn->query($boxaktuell_sql);
                  while ($boxaktuell = $boxaktuell_result->fetch_assoc()){
                    if($boxaktuell['id_box'] != $idbox){
                      $boxzuweisen_sql = "UPDATE box SET id_pferd = $id_pferd WHERE id_box = $idbox";
                      $boxzuweisen_result = $conn->query($boxzuweisen_sql);
                      $boxleeren_sql = "UPDATE box SET id_pferd = NULL WHERE id_box = " . $boxaktuell['id_box'];
                      $boxleeren_result = $conn->query($boxleeren_sql);
                    }
                  }

                  echo "<div class=\"alert alert-success\" role=\"alert\">Dieses Pferd wurde geändert!</div>";
                  


                }





                

              }
            ?>

          <h1>
          <?php  if ($_GET['id_pferd'] == 0){
            echo "Pferd hinzufügen";
          } else {
            echo "Pferd bearbeiten";
          } ?>
          </h1>
          <hr>
          <br>
          <p>Auf dieser Seite können Sie alle Informationen rund um das Pferd bearbeiten. Außerdem die Personen, die mit dem Pferd in Verbindung stehen, einsehen und ändern.</p>
            <!-- Formular zur Eingabe -->
            <form action="pferd-edit.php?id_pferd=<?php echo $id_pferd; ?>&saved=true"  method="post">
                <label>Pferdename</label>
                <input class="form-control" type="text" maxlength="45" value="<?php echo $pferdename ?>" name="pferdename" required> <br />
                <label>Geschlecht</label><br />
                <select class="custom-select" name="geschlecht" required>
                    <option <?php if ($geschlecht === 's') {echo 'selected';} ?> value="s">Stute</option>
                    <option <?php if ($geschlecht === 'h') {echo 'selected';} ?> value="h">Hengst</option>
                    <option <?php if ($geschlecht === 'w') {echo 'selected';} ?> value="w">Wallach</option>
                </select><br /><br />
                <label>Gewicht (in kg)</label>
                <input class="form-control" type="number" min="3" max="1543" value="<?php echo $gewicht ?>" name="gewicht" required><br />
                <label>Größe (in cm)</label>
                <input class="form-control" type="number" min="35" max="210" value="<?php echo $groesse ?>" name="groesse" required><br />
                <label>Passnummer</label>
                <input class="form-control" type="number" min="100000000" max="999999999999999" value="<?php echo $passnr ?>" name="passnr" required><br />
                <label>Geburtsdatum (tt.mm.jjjj)</label>
                <input class="form-control" type="date" min="1980-01-01" max="<?php echo date("Y-m-d"); ?>" value="<?php echo $geburtsdatum ?>" name="geburtsdatum" required><br />
                <label>Ankunft des Pferdes am Hof (tt.mm.jjjj)</label>
                <input class="form-control" type="date" min="1980-01-01" max="<?php echo date("Y-m-d"); ?>" value="<?php echo $ankunft ?>" name="ankunft" required><br />

                <br />
                <hr> 
                <!-- Beziehungen zu Pferd als Auswahl in Tabelle -->
                <?php
                  $allepersonen_sql = "SELECT id_person, vorname, nachname FROM person WHERE id_gehoeft = $id_gehoeft";
                  $allepersonen_result = $conn->query($allepersonen_sql);
                  
                  $id_besitzer = 0;
                  $id_rb = 0;
                  $id_tierarzt = 0;
                  $id_hufschmied = 0;
                  if ($_GET['id_pferd']>0){
                    $allebeziehungen_sql = "SELECT person.vorname, person.nachname, person.id_person, beziehung.id_funktion FROM person, beziehung WHERE person.id_person = beziehung.id_person AND id_pferd = ?";
                    $allebeziehungen_result = $conn->prepare($allebeziehungen_sql);
                    $allebeziehungen_result->bind_param('i', $_GET['id_pferd']);
                    $allebeziehungen_result->execute();
                    $allebeziehungen_result = $allebeziehungen_result->get_result();
                    while ($beziehung = $allebeziehungen_result->fetch_assoc()){
                      $beziehungid = $beziehung['id_funktion'];
                      if ($beziehungid == 1){
                        $id_besitzer = $beziehung['id_person'];
                      } elseif ($beziehungid == 2){
                        $id_rb = $beziehung['id_person'];
                      } elseif ($beziehungid == 3){
                        $id_tierarzt = $beziehung['id_person'];
                      } elseif ($beziehungid == 4){
                        $id_hufschmied = $beziehung['id_person'];
                      }
                    }
                  } 

                ?>
                <h2>Beziehungen zu Pferd</h2>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover display" id="dataTable1" width="100%" cellspacing="0">
                      <thead>
                        <th>Beziehung</th>
                        <th>Person</th>
                      </thead>
                      <tbody>
                        <tr>
                          <td>Besitzer</td>
                          <td>
                            <select class="custom-select" name="besitzerId">  
                            <?php
                              foreach($allepersonen_result as $person){ ?>
                                <option value = "<?php echo $person['id_person'] ?>" <?php if($person['id_person']==$id_besitzer){ echo 'selected';}?>><?php echo $person['vorname'] . " " . $person['nachname']; ?></option>
                              <?php } 

                            ?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Reitbeteiligung</td>
                          <td>
                            <select class="custom-select" name="rbId">
                              <option value="0"></option>
                              <?php
                                foreach($allepersonen_result as $person){ ?>
                                  <option value = "<?php echo $person['id_person'] ?>" <?php if($person['id_person']==$id_rb){ echo 'selected';} ?>><?php echo $person['vorname'] . " " . $person['nachname'];?></option>
                                <?php }
                              ?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Tierarzt</td>
                          <td>
                            <select class="custom-select" name="tierarztId">
                              <option value="0"></option>
                              <?php
                                foreach($allepersonen_result as $person){?>
                                  <option value = "<?php echo $person['id_person']?>" <?php if($person['id_person']==$id_tierarzt){ echo 'selected';} ?>><?php echo $person['vorname'] . " " . $person['nachname']; ?></option>
                                <?php }
                              ?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Hufschmied</td>
                          <td>
                            <select class="custom-select" name="hufschmiedId">
                              <option value="0"></option>
                              <?php
                                foreach($allepersonen_result as $person){?>
                                  <option value = "<?php echo $person['id_person'] ?>" <?php if($person['id_person']==$id_hufschmied){ echo 'selected';} ?>><?php echo $person['vorname'] . " " . $person['nachname'];?></option>
                                <?php }
                              ?>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                
                <hr>
                <br> 
                <!-- Tabelle zum Zuweisen einer Box -->
                <h2>Box zuweisen</h2>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover display" id="dataTable2" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Auswahl</th>
                        <th>Boxentyp</th>
                        <th>Boxenpreis</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                      $boxenfrei_sql = "SELECT box.id_box as id_box, boxentyp.boxenbez as boxenbez, box.boxenpreis as boxenpreis, box.id_pferd as id_pferd 
                          FROM box, boxentyp WHERE (box.id_pferd IS NULL AND box.id_boxentyp = boxentyp.id_boxentyp AND box.id_gehoeft = $id_gehoeft) OR 
                          (box.id_pferd = ? AND box.id_boxentyp = boxentyp.id_boxentyp AND box.id_gehoeft = $id_gehoeft)";
                      $boxenfrei_result = $conn->prepare($boxenfrei_sql);
                      $boxenfrei_result->bind_param('i', $id_pferd);
                      $boxenfrei_result->execute();
                      $boxenfrei_result = $boxenfrei_result->get_result();
                      while ($boxfrei = $boxenfrei_result->fetch_assoc()){
                        if ($boxfrei['id_pferd'] == $_GET['id_pferd']){
                          echo "<tr><td><input type=\"radio\" name=\"id_box\" value=\"" . $boxfrei['id_box'] . "\" checked required></td><td>" . $boxfrei['boxenbez'] . "</td><td>" . $boxfrei['boxenpreis'] . "</td></tr>";
                        } else {
                        echo "<tr><td><input type=\"radio\" name=\"id_box\" value=\"" . $boxfrei['id_box'] . "\" required></td><td>" . $boxfrei['boxenbez'] . "</td><td>" . $boxfrei['boxenpreis'] . "</td></tr>";
                        }
                      } 
                    ?>
                    </tbody>
                  </table>
                </div>

                <br>
                <hr>

                <button type="submit" class="btn btn-success" id="sendButton">Abschicken</button>
                <a class="btn btn-secondary" href="pferd.php">Abbrechen</a>
            </form>
            <?php } else {
              echo '<div class="alert alert-danger" role="alert">Keine Berechtigung für dieses Pferd!</div><hr><br>';
            }
            ?>

        </div>

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
