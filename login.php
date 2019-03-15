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
$error_register_password = false;
$error_register_email = false;
$error_activated = false;
$gehoeft_auswaehlen = false;
$admin = false;
$ein_gehoeft = false;
$mail = '';
$register_email = '';
$passwort = '';

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} 

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
  echo "Connection failed: " . $e->getMessage();
}

// Prüfung, ob beide Felder gefüllt sind
if (isset($_POST['email'], $_POST['password'])) {
    $mail = trim($_POST['email']);
    $passwort_hash = md5($_POST['password']);

    // SQL-Abfrage der Benutzerdaten für Eingabe-E-Mail

    $user_query = "SELECT passwort, id_benutzer, aktiviert FROM benutzer WHERE email = ?";
    $user_sql = $mysqli->prepare($user_query);
    $user_sql->bind_param("s", $mail);
    $user_sql->execute();
    $user_result = $user_sql->get_result();
    $user = $user_result->fetch_assoc();

    // Prüfung, ob Eingabe-Passwort mit DB-Passwort übereinstimmt
    if (isset($user['passwort']) && $user['passwort'] === $passwort_hash) {

      $passwort = $_POST['password'];

      // Abfrage, wie vielen Gehöften ein User zugeordnet ist
      $id_gehoeft_count_sql = "SELECT COUNT(*) AS count FROM benutzer_verwaltet_gehoeft WHERE id_benutzer =  '" . $user['id_benutzer'] . "'";
      $id_gehoeft_count = $conn->query($id_gehoeft_count_sql);
      $id_gehoeft_count = $id_gehoeft_count->fetch();

      // Prüfung, ob User aktiviert ist
      if ($user['aktiviert'] == 1) {

        $id_benutzer = $user['id_benutzer'];
      
        // Auswahl des Gehöftes aus dem Select, wenn >1 Gehöft dem User zugeordnet ist
        if (isset($_POST['gehoeftauswaehlen'])) {

          $id_gehoeft = $_POST['gehoeftauswaehlen'];
          
          // Gehöft-ID, E-Mail des Benutzers und der Boolean logged wird in die Session übergeben
          $_SESSION['id_gehoeft'] = $id_gehoeft;
          $_SESSION['mail'] = $mail;
          $_SESSION['logged'] = true;

          /* BESTANDSÄNDERUNG AUTOMATISCH */
          /* Ermittlung des Datums der letzten Änderung */
          $bestandsaenderungnoetig_sql = "SELECT datum FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_gehoeft = $id_gehoeft LIMIT 1";
          $bestandsaenderungnoetig_result = $conn->query($bestandsaenderungnoetig_sql);
          $bestandsaenderungnoetig_result = $bestandsaenderungnoetig_result->fetch();
          $letzteaenderung_datum = $bestandsaenderungnoetig_result['datum'];

          $heute_datum = date("Y-m-d");
          
          /* Kontrolle ob Änderung nötig */
          if ($letzteaenderung_datum != $heute_datum && $heute_datum != "0000-00-00"){
            $letzteaenderung_datum_jahr = intval(substr($letzteaenderung_datum, 0,4));
            $letzteaenderung_datum_monat = intval(substr($letzteaenderung_datum,5,2));
            $letzteaenderung_datum_tag = intval(substr($letzteaenderung_datum,8,2));
            $heute_datum_jahr = intval(substr($heute_datum,0,4));
            $heute_datum_monat = intval(substr($heute_datum,5,2));
            $heute_datum_tag = intval(substr($heute_datum,8,2));

            /* Berechnung der Anzahl der Tage der letzten Veränderung */
            $anzahl_tage = ($heute_datum_jahr - $letzteaenderung_datum_jahr) * 365 + ($heute_datum_monat - $letzteaenderung_datum_monat) * 30 + ($heute_datum_tag - $letzteaenderung_datum_tag);
            
            $bestand_veraenderung = 0;
            
            /* Ermittlung Gesamtgewicht Pferd */
            $gewichtpferd_sql = "SELECT SUM(pferd.gewicht) as gesamtgewicht FROM pferd,box WHERE pferd.id_pferd = box.id_pferd AND box.id_gehoeft = $id_gehoeft";
            $gewichtpferd_result = $conn->query($gewichtpferd_sql);
            $gewichtpferd_result = $gewichtpferd_result->fetch();
            $gesamtgewichtpferd = $gewichtpferd_result['gesamtgewicht'];

            /* Ermittlung Gesamtanzahl Boxen */
            $anzahlbox_sql = "SELECT COUNT(id_box) as anzahlbox FROM box WHERE id_gehoeft = $id_gehoeft AND id_pferd IS NOT NULL";
            $anzahlbox_result = $conn->query($anzahlbox_sql);
            $anzahlbox_result = $anzahlbox_result->fetch();
            $anzahlboxen = $anzahlbox_result['anzahlbox'];

            /* Ermittlung der Koeffizienten für die Verbrauchsguttypen */
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

            /* Ermittlung der Bestände für die Verbrauchsguttypen je Gehöft */
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

            /* Berechnung der Bestandsveränderung */
            $bestand_veraenderung_heu = $anzahl_tage * $koeffheu * ($gesamtgewichtpferd / 100);
            $bestand_veraenderung_hafer = $anzahl_tage * $koeffhafer * ($gesamtgewichtpferd / 100);
            $bestand_veraenderung_spaene = $anzahl_tage * $koeffspaene * $anzahlboxen;
            $bestand_veraenderung_stroh = $anzahl_tage * $koeffstroh * $anzahlboxen;
            
            /* Neuberechnung der Bestände */
            $bestandneu_hafer = $bestand_hafer - $bestand_veraenderung_hafer;
            $bestandneu_heu = $bestand_heu - $bestand_veraenderung_heu;
            $bestandneu_spaene = $bestand_spaene - $bestand_veraenderung_spaene;
            $bestandneu_stroh = $bestand_stroh - $bestand_veraenderung_stroh;

            /* Einfügen der Bestände */
            $bestandneu_hafer_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_hafer . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 1 AND id_gehoeft = $id_gehoeft";
            $bestandneu_heu_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_heu . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 2 AND id_gehoeft = $id_gehoeft";
            $bestandneu_spaene_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_spaene . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 4 AND id_gehoeft = $id_gehoeft";
            $bestandneu_stroh_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_stroh . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 3 AND id_gehoeft = $id_gehoeft";
            $bestandneu_hafer_result = $conn->query($bestandneu_hafer_sql);
            $bestandneu_heu_result = $conn->query($bestandneu_heu_sql);
            $bestandneu_spaene_result = $conn->query($bestandneu_spaene_sql);
            $bestandneu_stroh_result = $conn->query($bestandneu_stroh_sql);
          
          }
          
            header('location:dashboard.php');
            exit();
          
        }

        else {

          if ($id_gehoeft_count['count'] == 1) {

            $gehoeft_auswaehlen = false;

            // Überprüfung, ob Benutzer ein Admin ist
            $_SESSION['mail'] = $mail;
            $admin_mail_array = array("alisa@hrp-projekt.de", "henrik@hrp-projekt.de", "jan@hrp-projekt.de", "julia@hrp-projekt-de", "kerstin@hrp-projekt.de", "demo_admin@hrp-projekt.de");
            if (in_array($mail, $admin_mail_array)) {
              // Setzen eines Booleans, wenn ein Admin einem Gehöft zugeordnet ist
              $ein_gehoeft = true;
              $admin = true;
              // Drückt der Admin nun auf "Einloggen", und nicht auf "Adminzugang", wird der boolean mitgeschickt und der Admin eingeloggt und zum Gehöft weitergeleitet
              if (isset($_POST['ein_gehoeft']) && $admin = true) {
                $id_gehoeft_sql = " SELECT id_gehoeft FROM benutzer_verwaltet_gehoeft WHERE id_benutzer = '" . $id_benutzer . "'";
                $id_gehoeft = $conn->query($id_gehoeft_sql);
                $id_gehoeft_fetch = $id_gehoeft->fetch();
                
                $id_gehoeft = $id_gehoeft_fetch["id_gehoeft"];

                // Gehöft-ID, E-Mail des Benutzers und der Boolean logged wird in die Session übergeben
                $_SESSION['id_gehoeft'] = $id_gehoeft;
                $_SESSION['mail'] = $mail;
                $_SESSION['logged'] = true;

                // Durchführung der Bestandsveränderung
                $bestandsaenderungnoetig_sql = "SELECT datum FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_gehoeft = $id_gehoeft LIMIT 1";
                $bestandsaenderungnoetig_result = $conn->query($bestandsaenderungnoetig_sql);
                $bestandsaenderungnoetig_result = $bestandsaenderungnoetig_result->fetch();
                $letzteaenderung_datum = $bestandsaenderungnoetig_result['datum'];
                $heute_datum = date("Y-m-d");
              
              if ($letzteaenderung_datum != $heute_datum && $heute_datum != "0000-00-00"){
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
                $bestandneu_hafer_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_hafer . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 1 AND id_gehoeft = $id_gehoeft";
                $bestandneu_heu_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_heu . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 2 AND id_gehoeft = $id_gehoeft";
                $bestandneu_spaene_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_spaene . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 3 AND id_gehoeft = $id_gehoeft";
                $bestandneu_stroh_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_stroh . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 4 AND id_gehoeft = $id_gehoeft";
                $bestandneu_hafer_result = $conn->query($bestandneu_hafer_sql);
                $bestandneu_heu_result = $conn->query($bestandneu_heu_sql);
                $bestandneu_spaene_result = $conn->query($bestandneu_spaene_sql);
                $bestandneu_stroh_result = $conn->query($bestandneu_stroh_sql);
              
              
              }

                header('location:dashboard.php');
                exit();
              }
            }

            else {
              $ein_gehoeft = false;
              $admin = false;

              $id_gehoeft_sql = " SELECT id_gehoeft FROM benutzer_verwaltet_gehoeft WHERE id_benutzer = '" . $id_benutzer . "'";
              $id_gehoeft = $conn->query($id_gehoeft_sql);
              $id_gehoeft_fetch = $id_gehoeft->fetch();
              
              $id_gehoeft = $id_gehoeft_fetch["id_gehoeft"];

              // Gehöft-ID, E-Mail des Benutzers und der Boolean logged wird in die Session übergeben
              $_SESSION['id_gehoeft'] = $id_gehoeft;
              $_SESSION['mail'] = $mail;
              $_SESSION['logged'] = true;

              $bestandsaenderungnoetig_sql = "SELECT datum FROM gehoeft_besitzt_verbrauchsguttyp WHERE id_gehoeft = $id_gehoeft LIMIT 1";
              $bestandsaenderungnoetig_result = $conn->query($bestandsaenderungnoetig_sql);
              $bestandsaenderungnoetig_result = $bestandsaenderungnoetig_result->fetch();
              $letzteaenderung_datum = $bestandsaenderungnoetig_result['datum'];
              $heute_datum = date("Y-m-d");
            
              // Durchführung der Bestandsveränderung
              if ($letzteaenderung_datum != $heute_datum && $heute_datum != "0000-00-00"){
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
                $bestandneu_hafer_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_hafer . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 1 AND id_gehoeft = $id_gehoeft";
                $bestandneu_heu_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_heu . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 2 AND id_gehoeft = $id_gehoeft";
                $bestandneu_spaene_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_spaene . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 3 AND id_gehoeft = $id_gehoeft";
                $bestandneu_stroh_sql = "UPDATE gehoeft_besitzt_verbrauchsguttyp SET bestand = " . $bestandneu_stroh . ", datum = '" . $heute_datum . "' WHERE id_verbrauchsguttyp = 4 AND id_gehoeft = $id_gehoeft";
                $bestandneu_hafer_result = $conn->query($bestandneu_hafer_sql);
                $bestandneu_heu_result = $conn->query($bestandneu_heu_sql);
                $bestandneu_spaene_result = $conn->query($bestandneu_spaene_sql);
                $bestandneu_stroh_result = $conn->query($bestandneu_stroh_sql);
              }

              header('location:dashboard.php');
              exit();
          
        }
        }

          // Triggern des Select-Feldes, wenn >1 Gehöft dem User zugeordnet ist
          else if ($id_gehoeft_count['count'] > 1) {
            $gehoeft_auswaehlen = true;
            $_SESSION['mail'] = $mail;
            $admin_mail_array = array("alisa@hrp-projekt.de", "henrik@hrp-projekt.de", "jan@hrp-projekt.de", "julia@hrp-projekt-de", "kerstin@hrp-projekt.de", "demo_admin@hrp-projekt.de");
            if (in_array($mail, $admin_mail_array)) {
              $admin = true;
            }
          }
    
          // Kein Gehöft zugeordnet, Fehlermeldung und Admin-Überprüfung
          else {
            $error_gehoeft = true;
            $_SESSION['mail'] = $mail;
            $admin_mail_array = array("alisa@hrp-projekt.de", "henrik@hrp-projekt.de", "jan@hrp-projekt.de", "julia@hrp-projekt-de", "kerstin@hrp-projekt.de", "demo_admin@hrp-projekt.de");
            if (in_array($mail, $admin_mail_array)) {
              $admin = true;
            }

          }
          
        }
    
    }
    else {
      $error_activated = true;
    }
      
    } 
    else {
      $error = true;
    }

}

// Prüfung, ob in der Registrierung alle Felder gesetzt sind
if (isset($_POST['register_email'], $_POST['register_password'], $_POST['register_confirm_password'])) {
  // Prüfung, ob in der Registrieung Passwort und Passwortwiederholung übereinstimmen
  if ($_POST['register_password'] == $_POST['register_confirm_password']) {
    $register_email = $_POST['register_email'];

    // Prüfung, ob E-Mail bereits vergeben ist
    $email_vergeben_query = "SELECT COUNT(email) as count FROM benutzer WHERE email = ?";
    $email_vergeben_sql = $mysqli->prepare($email_vergeben_query);
    $email_vergeben_sql->bind_param("s", $register_email);
    $email_vergeben_sql->execute();
    $email_vergeben_result = $email_vergeben_sql->get_result();
    $email_vergeben_fetch = $email_vergeben_result->fetch_assoc();

    if ($email_vergeben_fetch['count'] > 0) {
      $error_register_email = true;
    }

    // Wenn nicht, Übergabe der Parameter an register.php
    else {
      $register_password = $_POST['register_password'];
      ?>

      <form id="form_register" action="register.php" method="POST">
        <input type="hidden" value="<?php echo $register_email ?>"  name="register_email">
        <input type="hidden" value="<?php echo $register_password ?>"  name="register_password">
      </form>
      <script type="text/javascript">
        document.getElementById('form_register').submit();
      </script>

      <?php
    }
  }

  else {
    $error_register_password = true;
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
                <p style="text-align: center;"><a href="impressum.html" target="_blank">Impressum & Datenschutz</a></p>
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
                    <p>Wenden Sie sich hierzu an einen Gehöftverwalter.</p>
                    <?php } ?>
                    <?php if ($error_activated) { ?>
                    <p>Die E-Mail-Adresse ist noch nicht bestätigt!</p>
                    <p><a href="activate.php">Jetzt bestätigen!</a></p>
                    <?php } ?>
                    <div class="col-xs-12 col-sm-12">
                      <form action="login.php" method="POST">
                        <div class="form-group wrap-input">
                          <input type="email" value="<?php echo $mail; ?>" name="email" id="inputEmail" class="form-control email" placeholder="E-Mail Adresse" required="required" autofocus="autofocus">
                          <span class="focus-input"></span>
                        </div>
                        <div class="form-group wrap-input">
                          <div class="pwdMask">
                            <input type="password" value="<?php echo $passwort; ?>" name="password" id="inputPassword" class="form-control password" placeholder="Passwort" required="required">
                            <span class="focus-input"></span>
                            <span class="fa fa-eye-slash pwd-toggle"></span>
                          </div>
                        </div>
                        <!-- Auswahl des Gehöfts, wenn mehrere Gehöfte zugeordnet sind -->
                        <?php if ($gehoeft_auswaehlen) { ?>
                        <p>Bitte ein Gehöft auswählen</p>
                        <div class="form-group">
                        <select class="form-control" name="gehoeftauswaehlen" style="border: 1px solid #4E2B17;">
                          <?php
                            // SQL-Abfrage der Gehöfte, denen dieser Benutzer zugeordnet ist
                            $gehoeft_auswaehlen_query = 
                            "SELECT id_gehoeft, gehoeftname
                             FROM gehoeft 
                             WHERE id_gehoeft IN (SELECT id_gehoeft
                                                  FROM benutzer_verwaltet_gehoeft
                                                  WHERE id_benutzer = ?)";
                            $gehoeft_auswaehlen_sql = $mysqli->prepare($gehoeft_auswaehlen_query);
                            $gehoeft_auswaehlen_sql->bind_param("i", $id_benutzer);
                            $gehoeft_auswaehlen_sql->execute();
                            $gehoeft_auswaehlen_result = $gehoeft_auswaehlen_sql->get_result();
                            while ($gehoeft_auswaehlen_fetch = $gehoeft_auswaehlen_result->fetch_assoc()){
                              
                              $id_gehoeft = $gehoeft_auswaehlen_fetch['id_gehoeft'];

                              //SQL-Abfrage für den Ort aus der Adressen-Tabelle für das aktuell gefetchte Gehöft
                              $adresse_query = "SELECT ort FROM adresse, gehoeft WHERE adresse.id_adresse = gehoeft.id_adresse AND gehoeft.id_gehoeft = ?";
                              $adresse_sql = $mysqli->prepare($adresse_query);
                              $adresse_sql->bind_param("i", $id_gehoeft);
                              $adresse_sql->execute();
                              $adresse_result = $adresse_sql->get_result();
                              $adresse_fetch = $adresse_result->fetch_assoc();

                              echo '<option value = "'.  $id_gehoeft .'">'. $gehoeft_auswaehlen_fetch['gehoeftname'] .', '. $adresse_fetch['ort'] .'</option>';
                              
                         } ?>
                        </select>
                        </div>
                        <br />
                        <?php } ?>
                        <!-- Hidden-Input, für Admins die genau einem Gehöft angehören -->
                        <?php if ($ein_gehoeft) { ?>
                            <input type="hidden" name="ein_gehoeft">
                        <?php } ?>
                        <div class="form-group">
                          <button class="btn btn-lg btn-primary btn-block">Mit E-Mail einloggen</button>
                        </div>
                      </form>
                      <!-- Link zum Adminzugang -->
                        <?php if ($admin) { ?>
                          <div class="form-group">
                            <a href="admin.php" class="btn btn-lg btn-primary btn-block">Zum Adminbereich</a>
                          </div>
                        <?php } ?>
                    </div>
                  </div>
                </div> <!-- ./panel-login -->
                <!-- panel-signup start -->
                <div id="signup" class="authfy-panel panel-signup text-center tab-pane fade">
                  <div class="row">
                    <?php if ($error_register_password) { ?>
                    <p>Passwörter sind nicht identisch!</p>
                    <?php } ?>
                    <?php if ($error_register_email) { ?>
                    <p>Diese E-Mail wird bereits verwendet!</p>
                    <?php } ?>
                    <div class="col-xs-12 col-sm-12">
                      <form name="signupForm" class="signupForm" action="login.php" method="POST">
                        <div class="form-group wrap-input">
                          <input type="email" class="form-control email" name="register_email" value="<?php echo $register_email; ?>" placeholder="E-Mail Adresse" autofocus="autofocus" required>
                          <span class="focus-input"></span>
                        </div>
                        <div class="form-group wrap-input">
                          <div class="pwdMask">
                            <input type="password" minlength="8" class="form-control password" name="register_password" placeholder="Passwort" required>
                            <span class="focus-input"></span>
                            <span class="fa fa-eye-slash pwd-toggle"></span>
                          </div>
                        </div>
                        <div class="form-group wrap-input">
                          <div class="pwdMask">
                            <input type="password" minlength="8" class="form-control password" name="register_confirm_password" placeholder="Passwort wiederholen" required>
                            <span class="focus-input"></span>
                            <span class="fa fa-eye-slash pwd-toggle"></span>
                          </div>
                        </div>
                        <div class="form-group">
                          <p class="term-policy text-muted small">Ich stimme der <a href="impressum.html#datenschutz" target="_blank">Datenschutzerklärung</a> zu.</p>
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