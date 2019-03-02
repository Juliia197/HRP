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
            <?php if ($error_gehoeft) { ?>
            <p>Kein Gehöft zugeordnet!</p>
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
