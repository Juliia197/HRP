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


$register_email = $_POST['register_email'];
$register_password = $_POST['register_password'];
$aktivierungscode = uniqid();
$aktiviert = 0;
$date = date('Y-m-d H:i:s');

$register_password_hash = md5($register_password);
$aktivierungscode_hash = md5($aktivierungscode);

$register_query = "INSERT INTO benutzer (email, passwort, aktivierungscode, aktiviert, registrierungsdatum) VALUES (?, ?, ?, ?, ?)";

$register_sql = $mysqli->prepare($register_query);
$register_sql->bind_param("sssis", $register_email, $register_password_hash, $aktivierungscode_hash, $aktiviert, $date);
$register_sql->execute();

$id_benutzer = $mysqli->insert_id;

//Nicht-Passende Links auskommentieren

$link_base = "https://www.hrp-projekt.de/activate.php";
//$link_base = "localhost/HRP/activate.php";
//$link_base = "henriks-macbook-pro.local/HRP/activate.php";

$link_parameter = "?id_benutzer=$id_benutzer&aktivierungscode=$aktivierungscode";

$aktivierungslink = $link_base . $link_parameter;

$_SESSION["register_email"] = $register_email;
$_SESSION["aktivierungslink"] = $aktivierungslink;

header('location:activate.php');
exit();

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
	
  <title>HRP - Login</title>

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
                    <p>Ihr Account wurde registriert. Zur Verifizierung müssen Sie Ihre E-Mail-Adresse bestätigen. Hierzu senden wir Ihnen einen Bestätigungslink an die angebene E-Mail-Adresse.</p> <br />
                    <div class="col-xs-12 col-sm-12">
                      <form action="activate.php" method="POST">
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block">E-Mail Adresse jetzt bestätigen</button>
                        </div>
                      </form>
                    </div>
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
