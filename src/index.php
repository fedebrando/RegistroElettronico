<?php
    session_start();

    if (isset($_GET["logout"]))
    {
        session_unset();
        session_destroy();
    }
    else if (isset($_POST["username"]))
    {
        $error = false; // errore credenziali errate

        // connessione
        $user = "root"; $password = ""; $host = "localhost"; $database = "scuola";
        $conn = @mysqli_connect($host, $user, $password, $database) or die("Impossibile connettersi al database");

        // preparazione query (impedisce l'attacco sql injection)
        $sql = $conn->prepare("SELECT Username, Nome, Cognome FROM Studente WHERE (Username, PswHash) = (?,?)");
        $username = $_POST["username"];
        $passwordHash = hash('sha3-512', $_POST["password"]);
        $sql->bind_param("ss", $username, $passwordHash);

        // esecuzione query
        $sql->execute();
        $result = $sql->get_result();

        if (mysqli_num_rows($result) == 1)
            $_SESSION["user"] = $result->fetch_assoc();
        else
        {
            // preparazione query
            $sql = $conn->prepare("SELECT Username, Nome, Cognome, Amministratore FROM Docente WHERE (Username, PswHash) = (?,?)");
            $sql->bind_param("ss", $username, $passwordHash);
            
            // esecuzione query
            $sql->execute();
            $result = $sql->get_result();

            if (mysqli_num_rows($result) == 1)
                $_SESSION["user"] = $result->fetch_assoc();
            else
                $error = true;
        }
        mysqli_close($conn);

        if (!$error)
            header('Location: home.php');      
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <h1>Registro elettronico - Login</h1>
    <div class="sfondo">
        <form action="index.php" method="POST">
            Username<br>
            <input type="text" id="txtUsername" name="username" required><br><br>
            Password<br>
            <input type="password" id="pswPassword" name="password" required><br><br>
            <button type="submit">Entra</button>
        </form>
        <?php
            if (isset($error)) // se siamo qui è perchè c'è stato un errore di inserimento delle credenziali
                echo "<span><b>Errore: nome utente e/o password errati.</b></span>"
        ?>
    </div>
</body>
</html>