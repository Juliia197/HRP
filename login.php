<?php
session_start();

if (isset($_SESSION['logged']) && $_SESSION['logged']) {
    header('location:dashboard.php');
    exit();
}

$servername = "localhost";
$username = "hrppr_1";
$password = "J49Wj7wUbSsKmNC5";
$dbname = "hrppr_db1";
$error = false;
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
              benutzer.passwort 
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
        $_SESSION['logged'] = true;
        $bestandsaenderungnoetig_sql = "SELECT letzteaenderung FROM verbrauchsguttypt";
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

          $hafer_sql = "SELECT koeffizient, bestand FROM verbrauchsguttypt WHERE id_verbrauchsguttyp = 2";
          $hafer_result = $conn->query($hafer_sql);
          $hafer_result = $hafer_result->fetch();
          $koeffhafer = $hafer_result['koeffizient'];
          $bestand_hafer = $hafer_result['bestand'];

          $heu_sql = "SELECT koeffizient, bestand FROM verbrauchsguttypt WHERE id_verbrauchsguttyp = 3";
          $heu_result = $conn->query($heu_sql);
          $heu_result = $heu_result->fetch();
          $koeffheu = $heu_result['koeffizient'];
          $bestand_heu = $heu_result['bestand'];

          $spaene_sql = "SELECT koeffizient, bestand FROM verbrauchsguttypt WHERE id_verbrauchsguttyp = 4";
          $spaene_result = $conn->query($spaene_sql);
          $spaene_result = $spaene_result->fetch();
          $koeffspaene = $spaene_result['koeffizient'];
          $bestand_spaene = $spaene_result['bestand'];

          $stroh_sql = "SELECT koeffizient, bestand FROM verbrauchsguttypt WHERE id_verbrauchsguttyp = 5";
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

          $bestandneu_hafer_sql = "UPDATE verbrauchsguttypt SET bestand = " . $bestandneu_hafer . " WHERE id_verbrauchsguttyp = 2";
          $bestandneu_heu_sql = "UPDATE verbrauchsguttypt SET bestand = " . $bestandneu_heu . " WHERE id_verbrauchsguttyp = 3";
          $bestandneu_spaene_sql = "UPDATE verbrauchsguttypt SET bestand = " . $bestandneu_spaene . " WHERE id_verbrauchsguttyp = 4";
          $bestandneu_stroh_sql = "UPDATE verbrauchsguttypt SET bestand = " . $bestandneu_stroh . " WHERE id_verbrauchsguttyp = 5";

          $bestandneu_hafer_result = $conn->query($bestandneu_hafer_sql);
          $bestandneu_heu_result = $conn->query($bestandneu_heu_sql);
          $bestandneu_spaene_result = $conn->query($bestandneu_spaene_sql);
          $bestandneu_stroh_result = $conn->query($bestandneu_stroh_sql);

        }

        header('location:dashboard.php');
        exit();
    } else {
        $error = true;
    }

}

?>

<!DOCTYPE html>
<html lang="de">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin - Login</title>

    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">

  </head>

  <body class="bg-dark">

    <div class="container">
      <div class="card card-login mx-auto mt-5">
        <div class="card-header">Login</div>
        <div class="card-body">
            <?php if ($error) { ?>
            <p>Ungültige Anmeldedaten. Versuchen Sie es noch einmal!</p>
            <?php } ?>
          <form action="login.php" method="post">
            <div class="form-group">
              <div class="form-label-group">
                <input type="email" value="<?php echo $mail; ?>" name="email" id="inputEmail" class="form-control" placeholder="Ihre E-Mail Adresse..." required="required" autofocus="autofocus">
                <label for="inputEmail">Ihre E-Mail Adresse...</label>
              </div>
            </div>
            <div class="form-group">
              <div class="form-label-group">
                <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Ihr Passwort..." required="required">
                <label for="inputPassword">Ihr Passwort...</label>
              </div>
            </div>
            <button class="btn btn-primary btn-block">Anmelden</button>
          </form>
          <div class="text-center">
            <a class="d-block small mt-3" href="register.php">Noch nicht registriert? Jetzt Konto anlegen!</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  </body>

</html>
