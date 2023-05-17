<?php
    session_start();

    function tabStudenti($studenti)
    {
        echo "<table><th>Cognome</th><th>Nome</th><th>Classe</th><th>Username</th>";
        while ($row = $studenti->fetch_assoc())
        {
            echo "<tr><td>{$row["Cognome"]}</td><td>{$row["Nome"]}</td><td>{$row["AnnoClasse"]}{$row["SezClasse"]}</td><td>{$row["Username"]}</td>";
            echo "<td class='center'><form action='studenti_insegnanti.php' onsubmit='return conferma()' method='GET'>" . "<button class='delete' name='usernameDel' value='s{$row["Username"]}' type='submit'>Elimina</button>" . "</form></td></tr>";
        }
        echo "</table>";
    }

    function classiStrDocente($username, $conn)
    {
        $aClassi = array();
        $sql = $conn->prepare("SELECT AnnoClasse, SezClasse FROM Assegnamento WHERE UserDocente = ?");
        $sql->bind_param("s", $username);
        $sql->execute();
        $res = $sql->get_result();
        while ($row = $res->fetch_assoc())
            array_push($aClassi, $row["AnnoClasse"] . $row["SezClasse"]);

        return implode(", ", $aClassi);
    }

    function materieStrDocente($username, $conn)
    {
        $aMaterie = array();
        $sql = $conn->prepare("SELECT Materia FROM Abilitazione WHERE UserDocente = ?");
        $sql->bind_param("s", $username);
        $sql->execute();
        $res = $sql->get_result();
        while ($row = $res->fetch_assoc())
            array_push($aMaterie, $row["Materia"]);
            
        return implode(", ", $aMaterie);
    }

    function tabDocenti($docenti, $conn)
    {
        echo "<table><th>Cognome</th><th>Nome</th><th>Classi</th><th>Materie</th><th>Username</th><th>Amministratore</th>";
        while ($row = $docenti->fetch_assoc())
        {
            $classiStr = classiStrDocente($row["Username"], $conn);
            $materieStr = materieStrDocente($row["Username"], $conn);
            echo "<tr><td>{$row["Cognome"]}</td><td>{$row["Nome"]}</td><td>{$classiStr}</td><td>{$materieStr}</td><td>{$row["Username"]}</td><td>" . ($row["Amministratore"] ? "Sì" : "No") . "</td>";

            if ($row["Username"] != $_SESSION["user"]["Username"]) // un amministratore non può autocancellarsi
                echo "<td class='center'><form action='studenti_insegnanti.php' onsubmit='return conferma()' method='GET'>" . "<button class='delete' name='usernameDel' value='d{$row["Username"]}' type='submit'>Elimina</button>" . "</form></td>";
            if (!$row["Amministratore"])
                echo "<td class='center'><form action='studenti_insegnanti.php' onsubmit='return conferma()' method='GET'>" . "<button class='up' name='usernameUp' value='{$row["Username"]}' type='submit'>Rendi Admin</button>" . "</form></td></tr>";
            else
                echo "</tr>";
        }
        echo "</table>";
    }

    if (!isset($_SESSION["user"]))
        header("Location: index.php");
    if (!isset($_SESSION["user"]["Amministratore"]))
        header("Location: home.php");
    if (!$_SESSION["user"]["Amministratore"])
        header("Location: home.php");

    // connessione
    $user = "root"; $password = ""; $host = "localhost"; $database = "scuola";
    $conn = @mysqli_connect($host, $user, $password, $database) or die("Impossibile connettersi al database");

    // query di dimensione
    $sql = $conn->prepare("SELECT COUNT(*) AS NumMaterie FROM Materia");
    $sql->execute();
    $numMaterie = $sql->get_result()->fetch_assoc()["NumMaterie"];

    $sql = $conn->prepare("SELECT COUNT(*) AS NumClassi FROM Classe");
    $sql->execute();
    $numClassi = $sql->get_result()->fetch_assoc()["NumClassi"];

    // eventuale richiesta cancellazione
    if (isset($_GET["usernameDel"]))
    {
        $discriminante = $_GET["usernameDel"][0];
        $username = substr($_GET["usernameDel"], 1);
        if ($discriminante == 'd')
        {
            $sql = $conn->prepare("DELETE FROM Assegnamento WHERE UserDocente = ?");
            $sql->bind_param("s", $username);
            $sql->execute();
            $sql = $conn->prepare("DELETE FROM Abilitazione WHERE UserDocente = ?");
            $sql->bind_param("s", $username);
            $sql->execute();
        }
        $sql = $conn->prepare("DELETE FROM " . ($discriminante == 's' ? "Studente" : "Docente") . " WHERE Username = ?");
        $sql->bind_param("s", $username);
        $sql->execute();
    }

    // eventuale richiesta di rendere admin un account
    if (isset($_GET["usernameUp"]))
    {
        $sql = $conn->prepare("UPDATE Docente SET Amministratore = 1 WHERE Username = ?");
        $sql->bind_param("s", $_GET["usernameUp"]);
        $sql->execute();
    }

    function vuotaToNull($str)
    {
        return $str == "" ? null : $str;
    }

    // richiesta registrazione nuovo account
    $erroreInserimento = false;

    if (isset($_POST["tipo"]))
    {
        // controllo se esiste già un account con quello username
        $sql = $conn->prepare("SELECT Username FROM Studente WHERE Username = ? UNION SELECT Username FROM Docente WHERE Username = ?");
        $sql->bind_param("ss", $_POST["username"], $_POST["username"]);
        $sql->execute();

        if (!mysqli_num_rows($sql->get_result()))
        {
            $isDocente = ($_POST["tipo"] == "Docente");
            $query = "INSERT INTO {$_POST["tipo"]} VALUES (?,?,?,?,?,?,?,?,?,?" . ($isDocente ? ",?" : ",?,?") . ")";
            $sql = $conn->prepare($query);
            $pswHash = hash('sha3-512', $_POST["password"]);
            $admin = isset($_POST["amministratore"]);
            $civico = ($_POST["civico"] == "" ? null : $_POST["civico"]);
            $_POST["codiceFiscale"] = vuotaToNull($_POST["codiceFiscale"]);
            $_POST["localita"] = vuotaToNull($_POST["localita"]);
            $_POST["CAP"] = vuotaToNull($_POST["CAP"]);
            if ($isDocente) 
                $sql->bind_param("ssissssssis", $_POST["username"], $pswHash, $admin, $_POST["codiceFiscale"], $_POST["nome"], $_POST["cognome"], $_POST["dataNascita"], $_POST["email"], $_POST["localita"], $civico, $_POST["CAP"]);
            else
            {
                $annoClasse = intval($_POST["classe"][0]);
                $sezClasse = $_POST["classe"][1];
                $sql->bind_param("ssssssssisis", $_POST["username"], $pswHash, $_POST["codiceFiscale"], $_POST["nome"], $_POST["cognome"], $_POST["dataNascita"], $_POST["email"], $_POST["localita"], $civico, $_POST["CAP"], $annoClasse, $sezClasse);
            }
            $sql->execute();

            if ($isDocente) // assegnazione del docente alle sue classi e alle sue materie
            {
                // materie
                $sql = $conn->prepare("INSERT INTO Abilitazione VALUES (?,?)");
                $username = $_POST["username"];
                for ($i = 0; $i < $numMaterie; $i++)
                {
                    if (isset($_POST["materia{$i}"]))
                    {
                        $sql->bind_param("ss", $username, $_POST["materia{$i}"]);
                        $sql->execute();
                    }
                }

                // classi
                $sql = $conn->prepare("INSERT INTO Assegnamento VALUES (?,?,?)");
                for ($i = 0; $i < $numClassi; $i++) // classi
                {
                    if (isset($_POST["classe{$i}"]))
                    {
                        $annoClasse = intval($_POST["classe{$i}"][0]);
                        $sezClasse = $_POST["classe{$i}"][1];
                        $sql->bind_param("sis", $username, $annoClasse, $sezClasse);
                        $sql->execute();
                    }
                }
            } 
        }
        else // esiste già un utente con tale username
        {
            $erroreInserimento = true;
        }
    }

    // query
    $sql = $conn->prepare("SELECT Username, Nome, Cognome, AnnoClasse, SezClasse FROM Studente ORDER BY Cognome");
    $sql->execute();
    $studenti = $sql->get_result();

    $sql = $conn->prepare("SELECT Username, Nome, Cognome, Amministratore FROM Docente ORDER BY Cognome");
    $sql->execute();
    $docenti = $sql->get_result();

    $sql = $conn->prepare("SELECT * FROM CLASSE");
    $sql->execute();
    $sqlClassi = $sql->get_result();
    $classi = array();
    while ($row = $sqlClassi->fetch_assoc())
    {
        $classe = "{$row["Anno"]}{$row["Sezione"]}";
        array_push($classi, $classe);
    }

    $sql = $conn->prepare("SELECT * FROM Materia");
    $sql->execute();
    $sqlMaterie = $sql->get_result();
    $materie = array();
    while ($row = $sqlMaterie->fetch_assoc())
        array_push($materie, $row["Nome"]);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="behavior.js"></script>
    <title>Studenti e Docenti</title>
</head>
<body onload="onloadHandler()">
    <a href="home.php">Torna indietro</a>
    <h1>Studenti</h1>
    <?php
        tabStudenti($studenti);
    ?>
    <br>
    <h1>Docenti</h1>
    <?php
        tabDocenti($docenti, $conn);
        mysqli_close($conn);
    ?>
    <br>
    <h1>Inserimento account</h1>
    <div class='sfondoSx'>
        <?php
            if ($erroreInserimento)
                echo "<span><b>Errore: esiste già un utente con tale username.</b></span><br><br>";
        ?>
        <form action="studenti_insegnanti.php" method="POST" onsubmit="return checkUsername()">
            Tipo<br>
            <input type="radio" id="studente" name="tipo" value="Studente" onclick="studenti()" checked>
            <label for="studente">Studente</label><br>
            <input type="radio" id="docente" name="tipo" value="Docente" onclick="docenti()">
            <label for="docente">Docente</label><br><br>
            Username<br>
            <input type="text" id="username" name="username" required><br><br>
            Password<br>
            <input type="password" name="password" required><br><br>
            <div name="docenti">
                Amministratore<br>
                <input type="checkbox" name="amministratore" value="Amministratore"><br><br>
            </div>
            Codice fiscale<br>
            <input type="text" name="codiceFiscale"><br><br>
            Nome<br>
            <input type="text" name="nome" required><br><br>
            Cognome<br>
            <input type="text" name="cognome" required><br><br>
            Data di nascita<br>
            <input type="date" name="dataNascita" required><br><br>
            Email<br>
            <input type="email" name="email" required><br><br>
            Località<br>
            <input type="text" name="localita"><br><br>
            Civico<br>
            <input type="number" name="civico" min="1"><br><br>
            CAP<br>
            <input type="text" name="CAP"><br><br>
            <div name="studenti">
                Classe<br>
                <?php
                    foreach ($classi as $classe)
                        echo "<input type='radio' name='classe' value='{$classe}'><label>{$classe}</label><br>";
                ?>
            </div>
            <div name="docenti">
                Classi<br>
                <?php
                    $numClassi = count($classi);
                    for ($i = 0; $i < $numClassi; $i++)
                        echo "<input type='checkbox' id='classe{$i}' name='classe{$i}' value='{$classi[$i]}'><label for='classe{$i}'>{$classi[$i]}</label><br>";
                ?>
                <br>
            </div>
            <div name="docenti">
                Materie<br>
                <?php
                    for ($i = 0; $i < $numMaterie; $i++)
                        echo "<input type='checkbox' id='materia{$i}' name='materia{$i}' value='{$materie[$i]}'><label for='materia{$i}'>{$materie[$i]}</label><br>";
                ?>
            </div>
            <?php
                echo "<br><button type='submit'>Registra</button>";
            ?>
        </form>
    </div>
</body>
</html>