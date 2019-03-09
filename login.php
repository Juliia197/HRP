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

        
        $bestandsaenderungnoetig_sql = "SELECT datum FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_gehoeft = 1 LIMIT 1";
        $bestandsaenderungnoetig_result = $conn->query($bestandsaenderungnoetig_sql);
        $bestandsaenderungnoetig_result = $bestandsaenderungnoetig_result->fetch();
        $letzteaenderung_datum = $bestandsaenderungnoetig_result['datum'];

        $heute_datum = date("Y-m-d");
        
        if ($letzteaenderung_datum != $heute_datum){

          $letzteaenderung_datum_jahr = intval(substr($letzteaenderung_datum, 0,4));
          $letzteaenderung_datum_monat = intval(substr($letzteaenderung_datum,5,2));
          $letzteaenderung_datum_tag = intval(substr($letzteaenderung_datum,8,2));

          $heute_datum_jahr = intval(substr($heute_datum,0,4));
          $heute_datum_monat = intval(substr($heute_datum,5,2));
          $heute_datum_tag = intval(substr($heute_datum,8,2));

          $anzahl_tage = ($heute_datum_jahr - $letzteaenderung_datum_jahr) * 365 + ($heute_datum_monat - $letzteaenderung_datum_monat) * 30 + ($heute_datum_tag - $letzteaenderung_datum_tag);

          $bestand_veraenderung = 0;
          $gewichtpferd_sql = "SELECT SUM(pferd.gewicht) as gesamtgewicht FROM pferd,box WHERE pferd.id_pferd = box.id_pferd AND box.id_gehoeft = $id_gehoeft";
          $gewichtpferd_result = $conn->query($gewichtpferd_sql);
          $gewichtpferd_result = $gewichtpferd_result->fetch();
          $gesamtgewichtpferd = $gewichtpferd_result['gesamtgewicht'];

          $anzahlbox_sql = "SELECT COUNT(id_box) as anzahlbox FROM box WHERE id_gehoeft = $id_gehoeft AND id_pferd IS NOT NULL";
          $anzahlbox_result = $conn->query($anzahlbox_sql);
          $anzahlbox_result = $anzahlbox_result->fetch();
          $anzahlboxen = $anzahlbox_result['anzahlbox'];

          $haferkoeff_sql = "SELECT koeffizient FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 1";
          $haferkoeff_result = $conn->query($haferkoeff_sql);
          $haferkoeff_result = $haferkoeff_result->fetch();
          $koeffhafer = $haferkoeff_result['koeffizient'];

          $heukoeff_sql = "SELECT koeffizient FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 2";
          $heukoeff_result = $conn->query($heukoeff_sql);
          $heukoeff_result = $heukoeff_result->fetch();
          $koeffheu = $heukoeff_result['koeffizient'];

          $strohkoeff_sql = "SELECT koeffizient FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 3";
          $strohkoeff_result = $conn->query($strohkoeff_sql);
          $strohkoeff_result = $strohkoeff_result->fetch();
          $koeffstroh = $strohkoeff_result['koeffizient'];

          $spaenekoeff_sql = "SELECT koeffizient FROM verbrauchsguttyp WHERE id_verbrauchsguttyp = 4";
          $spaenekoeff_result = $conn->query($spaenekoeff_sql);
          $spaenekoeff_result = $spaenekoeff_result->fetch();
          $koeffspaene = $spaenekoeff_result['koeffizient'];

          $haferbestand_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp = 1 AND id_gehoeft = $id_gehoeft";
          $haferbestand_result = $conn->query($haferbestand_sql);
          $haferbestand_result = $haferbestand_result->fetch();
          $bestand_hafer = $haferbestand_result['bestand'];

          $heubestand_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp = 2 AND id_gehoeft = $id_gehoeft";
          $heubestand_result = $conn->query($heubestand_sql);
          $heubestand_result = $heubestand_result->fetch();
          $bestand_heu = $heubestand_result['bestand'];

          $strohbestand_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp = 3 AND id_gehoeft = $id_gehoeft";
          $strohbestand_result = $conn->query($strohbestand_sql);
          $strohbestand_result = $strohbestand_result->fetch();
          $bestand_stroh = $strohbestand_result['bestand'];

          $spaenebestand_sql = "SELECT bestand FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_verbrauchsguttyp = 4 AND id_gehoeft = $id_gehoeft";
          $spaenebestand_result = $conn->query($spaenebestand_sql);
          $spaenebestand_result = $spaenebestand_result->fetch();
          $bestand_spaene = $spaenebestand_result['bestand'];

          $bestand_veraenderung_heu = $anzahl_tage * $koeffheu * ($gesamtgewichtpferd / 100);
          $bestand_veraenderung_hafer = $anzahl_tage * $koeffhafer * ($gesamtgewichtpferd / 100);
          $bestand_veraenderung_spaene = $anzahl_tage * $koeffspaene * $anzahlboxen;
          $bestand_veraenderung_stroh = $anzahl_tage * $koeffstroh * $anzahlboxen;
          
          $bestandneu_hafer = $bestand_hafer - $bestand_veraenderung_hafer;
          $bestandneu_heu = $bestand_heu - $bestand_veraenderung_heu;
          $bestandneu_spaene = $bestand_spaene - $bestand_veraenderung_spaene;
          $bestandneu_stroh = $bestand_stroh - $bestand_veraenderung_stroh;

          $bestandneu_hafer_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_hafer . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 1 AND id_gehoeft = 1";
          $bestandneu_heu_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_heu . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 2 AND id_gehoeft = 1";
          $bestandneu_spaene_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_spaene . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 3 AND id_gehoeft = 1";
          $bestandneu_stroh_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_stroh . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 4 AND id_gehoeft = 1";

          $bestandneu_hafer_result = $conn->query($bestandneu_hafer_sql);
          $bestandneu_heu_result = $conn->query($bestandneu_heu_sql);
          $bestandneu_spaene_result = $conn->query($bestandneu_spaene_sql);
          $bestandneu_stroh_result = $conn->query($bestandneu_stroh_sql);

        }
        
        
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
                          <input type="text" class="form-control" name="fullname" placeholder="Full name">
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
                          <p class="term-policy text-muted small">I agree to the <a href="#">privacy policy</a> and <a href="#">terms of service</a>.</p>
                        </div>
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up with email</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div> <!-- ./panel-signup -->
                <!-- panel-forget start -->
                <div id="forgot-pwd" class="authfy-panel panel-forgot tab-pane fade">
                  <div class="row">
                    <div class="col-xs-12 col-sm-12">
                      <div class="authfy-heading">
                        <h3 class="auth-title">Recover your password</h3>
                        <p>Fill in your e-mail address below and we will send you an email with further instructions.</p>
                      </div>
                      <form name="forgetForm" class="forgetForm" action="#" method="POST">
                        <div class="form-group wrap-input">
                          <input type="email" class="form-control" name="username" placeholder="Email address">
                          <span class="focus-input"></span>
                        </div>
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block" type="submit">Recover your password</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div> <!-- ./panel-forgot -->
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
