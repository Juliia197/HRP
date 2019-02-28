<?php
session_start();

if (isset($_SESSION['logged']) && $_SESSION['logged']) {
    header('location:dashboard.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hrppr_db1";
$error = false;
$mail = '';
$name = '';
$surname = '';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

$error = false;

if (isset($_POST['email'], $_POST['password'], $_POST['confirm_password'], $_POST['name'], $_POST['surname'])) {
    $mail = trim($_POST['email']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $error = 'Passwort ist nicht gleich.';
    } else {
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

        if (isset($user['passwort'])) {
            $error = 'Benutzer mit Emailadresse ' . $mail . ' schon existiert.';
        } else {
            $prepareCon = $conn->prepare('
                                INSERT INTO 
                                    adresse 
                                  ( 
                                    strasse,
                                    hausnr,
                                    plz,
                                    ort,
                                    land
                                  ) VALUES (?,?,?,?,?)'
            );
            $bindCon = ['','','','',''];
            $prepareCon->execute($bindCon);
            $addressId = $conn->lastInsertId();

            $prepareCon = $conn->prepare('
                                INSERT INTO 
                                    person 
                                  ( 
                                    nachname,
                                    vorname,
                                    email,
                                    telefonnr,
                                    geburtsdatum,
                                    id_adresse
                                  ) VALUES (?,?,?,?,?,?)'
            );
            $bindCon = [$surname, $name, $mail,'','', $addressId];
            $prepareCon->execute($bindCon);

            $personId = $conn->lastInsertId();

            $date = date('Y-m-d H:i:s');

            $prepareCon = $conn->prepare('
                                INSERT INTO 
                                    benutzer 
                                  ( 
                                    passwort,
                                    regestrierungsdatum,
                                    id_person
                                  ) VALUES (?, ?, ?)'
            );
            $bindCon = [md5($password), $date, $personId];
            $prepareCon->execute($bindCon);
        }
    }

    if ($error === false) {
        $_SESSION['logged'] = true;
        header('location:dashboard.php?registered=true');
        exit();
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
        <div class="card-header">Register</div>
        <div class="card-body">
            <?php if ($error) { ?>
                <p><?php echo $error; ?></p>
            <?php } ?>
            <form action="register.php" method="post">
                <div class="form-group">
                    <div class="form-label-group">
                        <input type="email" value="<?php echo $mail; ?>" name="email" id="inputEmail" class="form-control" placeholder="Email address" required="required" autofocus="autofocus">
                        <label for="inputEmail">Email address</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="required">
                        <label for="inputPassword">Password</label>
                    </div>
                </div><div class="form-group">
                    <div class="form-label-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm password" required="required">
                        <label for="inputPassword">Confirm password</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <input type="text" value="<?php echo $name; ?>" name="name" id="name" class="form-control" placeholder="Vorname" required="required" autofocus="autofocus">
                        <label for="inputEmail">Vorname</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <input type="text" value="<?php echo $surname; ?>" name="surname" id="inputEmail" class="form-control" placeholder="Nachname" required="required" autofocus="autofocus">
                        <label for="inputEmail">Nachname</label>
                    </div>
                </div>
                <button class="btn btn-primary btn-block">Register</button>
            </form>
            <div class="text-center">
                <a class="d-block small mt-3" href="login.php">Login</a>
                <a class="d-block small" href="forgot-password.html">Passwort vergessen?</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<script type="text/javascript">
    let password = document.getElementById("password")
        , confirm_password = document.getElementById("confirm_password");

    function validatePassword(){
        if(password.value !== confirm_password.value) {
            confirm_password.setCustomValidity("Passwords Don't Match");
        } else {
            confirm_password.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    confirm_password.onkeyup = validatePassword;
</script>

</body>

</html>
