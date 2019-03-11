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
$activated = false;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} 

if (isset($_GET["id_benutzer"]) && isset($_GET["aktivierungscode"])) {

  $id_benutzer = $_GET["id_benutzer"];
  $aktivierungscode = $_GET["aktivierungscode"];

  $check_activate_query = "SELECT aktivierungscode FROM benutzer WHERE id_benutzer = ?";
  $check_activate_sql = $mysqli->prepare($check_activate_query);
  $check_activate_sql->bind_param("i", $id_benutzer);
  $check_activate_sql->execute();
  $check_activate_result = $check_activate_sql->get_result();
  $check_activate_fetch = $check_activate_result->fetch_assoc();

  if ($check_activate_fetch["aktivierungscode"] == md5($aktivierungscode)) {

    $aktiviert = 1;

    $activate_query = "UPDATE benutzer SET aktiviert = ? WHERE id_benutzer = ?";
    $activate_sql = $mysqli->prepare($activate_query);
    $activate_sql->bind_param("ii", $aktiviert, $id_benutzer);
    $activate_sql->execute();

    $activated = true;
  }

}

else {

  $register_email = $_SESSION["register_email"];
  $aktivierungslink = $_SESSION["aktivierungslink"];

  // Ausgabe Aktivierungslink für localhost
  echo $aktivierungslink;

  // Mail wird gesendet 
  
  $to = $register_email;
  $subject = "Bestätigung Ihres Accounts bei hrp-projekt.de";
  $message ='
  <html>
  <head>
      <style>
          * {
              font-family: Helvetica, sans-serif
          }
          .button {
              padding: 8px;
              background-color: #a4bf6b;
              border: 1px solid black;
              border-radius: 4px;
              display: inline-block;
          }
          .button:hover {
              background-color: #8da35b;
          }
          .center {
              margin: 0 auto;
              text-align: center;
          }
      </style>
  </head>
  <body>
    <h1>Best&auml;tigung Ihres Accounts bei <a href="https://www.hrp-projekt.de">hrp-projekt.de</a></h1>
    <p>Zum Best&auml;tigen Ihres Accounts dr&uuml;cken Sie einfach auf den unten stehenden Button:</p>
    <p><div class="center"><div class="button">
    <a style="color: black; text-decoration: none;" href="'.$aktivierungslink.'">E-Mail-Adresse jetzt best&auml;tigen</a>
    </div></div></p>
    <p>Sollte dies nicht funktionieren, folgen Sie dem unten stehenden Link:</p>
    <p><a href="'.$aktivierungslink.'">'.$aktivierungslink.'</a></p>
  </body>
</html>
  ';
  $header  = "MIME-Version: 1.0\r\n";
  $header .= "Content-type: text/html; charset=utf-8\r\n";
  $header .= "From: bestaetigung@hrp-projekt.de" . "\r\n" .
  $header .= "Reply-to: info@hrp-projekt.de";

  mail($to, $subject, $message, $header);
  
  
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
                <p style="text-align: center;">Horse-Resource-Planning</p> <br /> <br /> <br /><br /><br /><br /><br /> 
                <p style="text-align: center; margin-bottom: -20px;">Copyright &copy; HRP 2019</p> <br />
                <p style="text-align: center;"><a href="impressum.html">Impressum & Datenschutz</a></p>
              </div>
            </div>
          </div>
          <div class="col-sm-7 authfy-panel-right">
            <!-- authfy-login start -->
            <div class="authfy-login">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs nav-justified" role="tablist">
                <!-- <li role="presentation" class="active"><a href="#login" data-toggle="tab">Bereits Mitglied</a></li>
                <li role="presentation"><a href="#signup" data-toggle="tab">Registrieren</a></li> -->
                <li role="presentation" class="active"><a href="#activate" data-toggle="tab">E-Mail Adresse bestätigen</a></li>
              </ul>
              <div class="tab-content">
                <!-- panel-activate start -->
                <div id="activate" class="authfy-panel panel-login text-center tab-pane fade in active">
                  <div class="row">
                  <?php if ($activated == false) { ?>
                    <p>Ihre E-Mail-Adresse wurde noch nicht bestätigt!</p>
                    <p>Wir haben Ihnen einen Bestätigungslink an <strong><?php echo $register_email?></strong> gesendet. 
                    Überprüfen Sie auch Ihren Spam-Ordner. 
                    Bei Problemen melden Sie sich bei <a href="mailto:info@hrp-projekt.de?subject=Bestätigung der E-Mail-Adresse">info@hrp-projekt.de</a></p> <br />
                    <div class="col-xs-12 col-sm-12">
                      <form action="activate.php" method="POST">
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block">E-Mail erneut versenden</button>
                        </div>
                      </form>
                    </div>
                    <?php } ?>
                  <?php if ($activated == true) { ?>
                    <p>Ihre E-Mail-Adresse wurde bestätigt!</p> <br />
                    <div class="col-xs-12 col-sm-12">
                      <form action="login.php" method="POST">
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block">Zum Login</button>
                        </div>
                      </form>
                    </div>
                    <?php } ?>
                  </div>
                </div> <!-- ./panel-activate -->
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
