<?php
session_start();

if (isset($_SESSION['logged']) && $_SESSION['logged']) {
    header('location:dashboard.php');
    exit();
}

// Error reporting 
error_reporting(E_ALL);
ini_set('display_errors', 'on');

// Database connection
$servername = "localhost";
$username = "hrppr_1";
$password = "J49Wj7wUbSsKmNC5";
$dbname = "hrppr_db1";
$error = false;
$error_gehoeft = false;
$mail = '';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

if (isset($_POST['email'], $_POST['password'])) {
    $mail = trim($_POST['email']);
    $password = md5($_POST['password']);

    $sql = "SELECT 
              benutzer.passwort, benutzer.id_benutzer 
            FROM
              benutzer 
            LEFT JOIN 
              person
            ON 
              benutzer.id_person = person.id_person
            WHERE 
              person.email = '".$mail."'";

    $user = $conn->query($sql);
    $user = $user->fetch();

    if (isset($user['passwort']) && $user['passwort'] === $password) {
      
      $id_gehoeft_count_sql = " SELECT COUNT(*) AS count FROM benutzer_verwaltet_gehoeft WHERE id_benutzer =  '" . $user['id_benutzer'] . "'";
      $id_gehoeft_count = $conn->query($id_gehoeft_count_sql);
      $id_gehoeft_count = $id_gehoeft_count->fetch();

      if ($id_gehoeft_count['count'] == 1) {
            
        $id_gehoeft_sql = " SELECT id_gehoeft FROM benutzer_verwaltet_gehoeft WHERE id_benutzer = '" . $user['id_benutzer'] . "'";
        $id_gehoeft = $conn->query($id_gehoeft_sql);
        $id_gehoeft = $id_gehoeft->fetch();

        $_SESSION['id_gehoeft'] = $id_gehoeft['id_gehoeft'];
        $_SESSION['logged'] = true;
        /*$bestandsaenderungnoetig_sql = "SELECT letzteaenderung FROM verbrauchsguttyp";
        $bestandsaenderungnoetig_result = $conn->query($bestandsaenderungnoetig_sql);
        $bestandsaenderungnoetig_result = $bestandsaenderungnoetig_result->fetch();
        $letzteaenderung_datum = $bestandsaenderungnoetig_result['letzteaenderung'];

        $heute_datum = date("Y-m-d");
        
        if ($letzteaenderung_datum != $heute_datum){

          $id_gehoeft = 1;

          $letzteaenderung_datum_jahr = intval(substr($letzteaenderung_datum, 0,4));
          $letzteaenderung_datum_monat = intval(substr($letzteaenderung_datum,5,2));
          $letzteaenderung_datum_tag = intval(substr($letzteaenderung_datum,8,2));

          $heute_datum_jahr = intval(substr($heute_datum,0,4));
          $heute_datum_monat = intval(substr($letzteaenderung_datum,5,2));
          $heute_datum_tag = intval(substr($letzteaenderung_datum,8,2));

          $anzahl_tage = ($heute_datum_jahr - $letzteaenderung_datum_jahr) * 365 + ($heute_datum_monat - $letzteaenderung_datum_monat) * 30 + ($heute_datum_tag - $letzteaenderung_datum_tag);

          $bestand_veraenderung = 0;
          $gewichtpferd_sql = "SELECT SUM(pferd.gewicht) as gesamtgewicht FROM pferd,box WHERE pferd.id_pferd = box.id_pferd AND box.id_gehoeft = $id_gehoeft";
          $gewichtpferd_result = $conn->query($gewichtpferd_sql);
          $gewichtpferd_result = $gewichtpferd_result->fetch();
          $gesamtgewichtpferd = $gewichtpferd_result['gesamtgewicht'];

          $anzahlbox_sql = "SELECT COUNT(id_box) as anzahlbox FROM box WHERE id_gehoeft = 1";
          $anzahlbox_result = $conn->query($anzahlbox_sql);
          $anzahlbox_result = $anzahlbox_result->fetch();
          $anzahlboxen = $anzahlbox_result['anzahlbox'];

          $hafer_sql = "SELECT koeffizient, bestand FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 2";
          $hafer_result = $conn->query($hafer_sql);
          $hafer_result = $hafer_result->fetch();
          $koeffhafer = $hafer_result['koeffizient'];
          $bestand_hafer = $hafer_result['bestand'];

          $heu_sql = "SELECT koeffizient, bestand FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 3";
          $heu_result = $conn->query($heu_sql);
          $heu_result = $heu_result->fetch();
          $koeffheu = $heu_result['koeffizient'];
          $bestand_heu = $heu_result['bestand'];

          $spaene_sql = "SELECT koeffizient, bestand FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 4";
          $spaene_result = $conn->query($spaene_sql);
          $spaene_result = $spaene_result->fetch();
          $koeffspaene = $spaene_result['koeffizient'];
          $bestand_spaene = $spaene_result['bestand'];

          $stroh_sql = "SELECT koeffizient, bestand FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 5";
          $stroh_result = $conn->query($stroh_sql);
          $stroh_result = $stroh_result->fetch();
          $koeffstroh = $stroh_result['koeffizient'];
          $bestand_stroh = $stroh_result['bestand'];

          $bestand_veraenderung_heu = $anzahl_tage * $koeffheu * ($gesamtgewichtpferd / 100);
          $bestand_veraenderung_hafer = $anzahl_tage * $koeffhafer * ($gesamtgewichtpferd / 100);
          $bestand_veraenderung_spaene = $anzahl_tage * $koeffspaene * $anzahlboxen;
          $bestand_veraenderung_stroh = $anzahl_tage * $koeffstroh * $anzahlboxen;
          
          $bestandneu_hafer = $bestand_hafer - $bestand_veraenderung_hafer;
          $bestandneu_heu = $bestand_heu - $bestand_veraenderung_heu;
          $bestandneu_spaene = $bestand_spaene - $bestand_veraenderung_spaene;
          $bestandneu_stroh = $bestand_stroh - $bestand_veraenderung_stroh;

          $bestandneu_hafer_sql = "UPDATE verbrauchsguttyp SET bestand = " . $bestandneu_hafer . " WHERE id_verbrauchsguttyp = 2";
          $bestandneu_heu_sql = "UPDATE verbrauchsguttyp SET bestand = " . $bestandneu_heu . " WHERE id_verbrauchsguttyp = 3";
          $bestandneu_spaene_sql = "UPDATE verbrauchsguttyp SET bestand = " . $bestandneu_spaene . " WHERE id_verbrauchsguttyp = 4";
          $bestandneu_stroh_sql = "UPDATE verbrauchsguttyp SET bestand = " . $bestandneu_stroh . " WHERE id_verbrauchsguttyp = 5";

          $bestandneu_hafer_result = $conn->query($bestandneu_hafer_sql);
          $bestandneu_heu_result = $conn->query($bestandneu_heu_sql);
          $bestandneu_spaene_result = $conn->query($bestandneu_spaene_sql);
          $bestandneu_stroh_result = $conn->query($bestandneu_stroh_sql);

        } */

        header('location:dashboard.php');
        exit();
      }
      
      else {
        $error_gehoeft = true;
      }
      
    } else {
        $error = true;
    }

}

?>

<!DOCTYPE html>
<html lang="de">
  <head>
  <!-- Basic Page Needs
  ================================================== -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Mobile Specific Metas
  ================================================== -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- For Search Engine Meta Data  -->
  <meta name="pferdeverwalter" content="" />
  <meta name="JanFreymuth" content="hrp-projekt.de" />
	
  <title>Admin Login</title>

  <!-- Favicon -->
  <link rel="shortcut icon" type="image/icon" href="images/favicon-16x16.png"/>
   
  <!-- Main structure css file -->
  <link  rel="stylesheet" href="css/login5-style.css">
  <link  rel="stylesheet" href="css/jan.css">
 
  </head>
  
  <body>
    <!-- Start Preloader -->
    <div id="preload-block">
      <div class="square-block"></div>
    </div>
    <!-- Preloader End -->
    
    <div class="container-fluid">
      <div class="row">
        <div class="authfy-container col-xs-12 col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
          <div class="col-sm-5 authfy-panel-left">
            <div class="brand-col">
              <div class="headline">
                <!-- brand-logo start -->
                <div class="brand-logo">
                  <img src="images/brand-logo-white.png" alt="brand-logo" style="display:block; margin:auto;">
                </div><!-- ./brand-logo -->
                <p style="text-align: center;">Horse-Resourcing-Planner</p> <br /> <br /> <br /><br /><br /><br /><br /> 
                <p style="text-align: center; margin-bottom: -20px;">Copyright &copy; HRP 2019</p>
              </div>
            </div>
          </div>
          <div class="col-sm-7 authfy-panel-right">
            <!-- authfy-login start -->
            <div class="authfy-login">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs nav-justified" role="tablist">
                <li role="presentation" class="active"><a href="#login" data-toggle="tab">Bereits Mitglied</a></li>
                <li role="presentation"><a href="#signup" data-toggle="tab">Registrieren</a></li>
              </ul>
              <div class="tab-content">
                <!-- panel-login start -->
                <div id="login" class="authfy-panel panel-login text-center tab-pane fade in active">
                  <div class="row">
                    <?php if ($error) { ?>
                    <p>Ungültige Anmeldedaten. Versuchen Sie es noch einmal!</p>
                    <?php } ?>
                    <?php if ($error_gehoeft) { ?>
                    <p>Kein Gehöft zugeordnet!</p>
                    <?php } ?>
                    <div class="col-xs-12 col-sm-12">
                      <form action="login.php" method="POST">
                        <div class="form-group wrap-input">
                          <input type="email" value="<?php echo $mail; ?>" name="email" id="inputEmail" class="form-control email" placeholder="E-Mail Adresse" required="required" autofocus="autofocus">
                          <span class="focus-input"></span>
                        </div>
                        <div class="form-group wrap-input">
                          <div class="pwdMask">
                            <input type="password" name="password" id="inputPassword" class="form-control password" placeholder="Passwort" required="required">
                            <span class="focus-input"></span>
                            <span class="fa fa-eye-slash pwd-toggle"></span>
                          </div>
                        </div>
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block">Mit E-Mail einloggen</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div> <!-- ./panel-login -->
                <!-- panel-signup start -->
                <div id="signup" class="authfy-panel panel-signup text-center tab-pane fade">
                  <div class="row">
                    <div class="col-xs-12 col-sm-12">
                      <form name="signupForm" class="signupForm" action="#" method="POST">
                        <div class="form-group wrap-input">
                          <input type="email" class="form-control" name="username" placeholder="E-Mail Adresse">
                          <span class="focus-input"></span>
                        </div>
                        <div class="form-group wrap-input">
                          <input type="text" class="form-control" name="fullname" placeholder="Vor- und Zuname">
                          <span class="focus-input"></span>
                        </div>
                        <div class="form-group wrap-input">
                          <div class="pwdMask">
                            <input  type="password" class="form-control" name="password" placeholder="Passwort">
                            <span class="focus-input"></span>
                            <span class="fa fa-eye-slash pwd-toggle"></span>
                          </div>
                        </div>
                        <div class="form-group">
                          <p class="term-policy text-muted small">Ich stimme den <a href="#">AGB</a> und der <a href="#">Datenschutzerklärung</a> zu.</p>
                        </div>
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block" type="submit">Registrierung abschließen</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div> <!-- ./panel-signup -->
              </div> <!-- ./tab-content -->
            </div> <!-- ./authfy-login -->
          </div>
        </div>
      </div> <!-- ./row -->
    </div> <!-- ./container -->
    
    <!-- Javascript Files -->

    <!-- initialize jQuery Library -->
    <script src="js/jquery-2.2.4.min.js"></script>
  
    <!-- for Bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    
      <!-- Custom js-->
    <script src="js/custom.js"></script>
    
  </body>	
</html>
