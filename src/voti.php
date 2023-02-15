<?php
    session_start();

    if (!isset($_SESSION["user"]))
        header("Location: index.php");
    $isDocente = isset($_SESSION["user"]["Amministratore"]);
    
    // connessione
    $user = "root"; $password = ""; $host = "localhost"; $database = "scuola";
    $conn = @mysqli_connect($host, $user, $password, $database) or die("Impossibile connettersi al database");
    $username = $_SESSION["user"]["Username"];

    // eventuale richiesta di cancellazione voto
    if (isset($_GET["votoDel"]) && $isDocente)
    {
        $sql = $conn->prepare("DELETE FROM Valutazione WHERE ID = ?");
        $sql->bind_param("i", $_GET["votoDel"]);
        $sql->execute();
    }

    // eventuale richiesta di registrazione voto
    if (isset($_GET["votoAdd"]) && $isDocente)
    {
        $sql = $conn->prepare("INSERT INTO Valutazione(UserDocente, Materia, UserStudente, Data, Voto, Nota) VALUES (?,?,?,?,?,?)");
        $sql->bind_param("ssssis", $username, $_GET["materia"], $_GET["userStudente"], $_GET["data"], $_GET["voto"], $_GET["nota"]);
        $sql->execute();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="behavior.js"></script>
    <title>Voti</title>
</head>
<body onload="coloraVoti()">
    <h1>Voti</h1>
    <a href="home.php">Torna indietro</a>
    <?php
        function rowsToArray($sqlResult)
        {
            $aSql = array();
            while ($row = $sqlResult->fetch_assoc())
                array_push($aSql, $row);

            return $aSql;
        }

        if ($isDocente)
        {
            // materie insegnate
            $sql = $conn->prepare("SELECT Materia FROM Abilitazione WHERE UserDocente = ? ORDER BY Materia");
            $sql->bind_param("s", $username);
            $sql->execute();
            $materieInsegnate = rowsToArray($sql->get_result());

            // classi assegnate
            $sql = $conn->prepare("SELECT AnnoClasse, SezClasse FROM Assegnamento WHERE UserDocente = ? ORDER BY AnnoClasse, SezClasse");
            $sql->bind_param("s", $username);
            $sql->execute();
            $classiAssegnate = rowsToArray($sql->get_result());

            foreach ($classiAssegnate as $classe)
            {
                echo "<h2>{$classe["AnnoClasse"]}{$classe["SezClasse"]}</h2>";
                $sql = $conn->prepare("SELECT Username, Cognome, Nome FROM Studente WHERE (AnnoClasse, SezClasse) = (?, ?) ORDER BY Cognome, Nome");
                $sql->bind_param("ss", $classe["AnnoClasse"], $classe["SezClasse"]);
                $sql->execute();
                $studenti = rowsToArray($sql->get_result());
                foreach ($materieInsegnate as $materia)
                {
                    echo "<h3>{$materia["Materia"]}</h3>";
                    echo "<table><th>Cognome</th><th>Nome</th><th>Nuovo voto</th>";
                        foreach ($studenti as $studente)
                        {
                            echo "<tr><td>{$studente["Cognome"]}</td><td>{$studente["Nome"]}</td>";
                            echo "<td><form action='voti.php' method='GET' onsubmit='return conferma()'>";
                            echo "<select name='voto' required>";
                            for ($v = 0; $v <= 10; $v++)
                                echo "<option value='{$v}'>{$v}</option>";
                            echo "</select>";
                            $oggi = date('Y-m-d');
                            echo "<input type='date' name='data' value='{$oggi}' required>";
                            echo "<input type='text' name='nota' placeholder='Nota'>";
                            echo "<input type='text' name='userStudente' value='{$studente["Username"]}' class='undisplay'>";
                            echo "<input type='text' name='materia' value='{$materia["Materia"]}' class='undisplay'>";
                            echo "<button type='submit' name='votoAdd'>Salva</button>";
                            echo "</form></td>";
                            $sql = $conn->prepare("SELECT ID, UserStudente, Data, Voto, Nota FROM Valutazione WHERE (UserStudente, Materia) = (?, ?) ORDER BY Data");
                            $sql->bind_param("ss", $studente["Username"], $materia["Materia"]);
                            $sql->execute();
                            $sqlValutazioni = $sql->get_result();
                            while ($valutazione = $sqlValutazioni->fetch_assoc())
                            {
                                echo "<td>" . "<form action='voti.php' method='GET' onsubmit='return conferma()'>";
                                echo "<button type='submit' name='votoDel' class='delete' value='{$valutazione["ID"]}'>&times</button>";
                                echo "</form><br>";
                                echo "<div class='tooltip'><span class='tooltiptext'><b>Nota</b>: {$valutazione["Nota"]}</span>" . "<p class='datina'>{$valutazione["Data"]}</p><div class='votoCol' name='votoCol'>{$valutazione["Voto"]}</div></div>" . "</td>";
                            }
                            echo "</tr>";
                        }
                    echo "</table>";
                }
            }

        }
        else
        {
            // materie con almeno un voto
            $sql = $conn->prepare("SELECT DISTINCT Materia FROM Valutazione WHERE UserStudente = ? ORDER BY Materia");
            $sql->bind_param("s", $username);
            $sql->execute();
            $materieSql = $sql->get_result();

            echo "<table>";
            while ($materia = $materieSql->fetch_assoc())
            {
                echo "<tr>" . "<td><b>{$materia["Materia"]}</b></td>";
                $sql->prepare("SELECT D.Cognome, V.Data, V.Voto, V.Nota FROM Valutazione V, Docente D WHERE V.UserDocente = D.Username AND V.UserStudente = ? AND V.Materia = ?");
                $sql->bind_param("ss", $username, $materia["Materia"]);
                $sql->execute();
                $valutazioniSql = $sql->get_result();
                while ($valutazione = $valutazioniSql->fetch_assoc())
                {
                    echo "<td>" . "<div class='tooltip'><span class='tooltiptext'><b>{$valutazione["Cognome"]}</b>: {$valutazione["Nota"]}</span>" . "<p class='datina'>{$valutazione["Data"]}</p><div class='votoCol' name='votoCol'>{$valutazione["Voto"]}</div></div></td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        mysqli_close($conn);
    ?>
</body>
</html>