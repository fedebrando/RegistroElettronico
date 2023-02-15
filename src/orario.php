<?php
    session_start();

    if (!isset($_SESSION["user"]))
        header("Location: index.php");
    $isDocente = isset($_SESSION["user"]["Amministratore"]);

    // connessione
    $user = "root"; $password = ""; $host = "localhost"; $database = "scuola";
    $conn = @mysqli_connect($host, $user, $password, $database) or die("Impossibile connettersi al database");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Orario</title>
</head>
<body>
    <?php
        echo "<h1>Orario " . ($isDocente ? "lavorativo" : "delle lezioni") . "</h1>";
    ?>
    <a href="home.php">Torna indietro</a>
    <table>
        <th>lunedì</th><th>martedì</th><th>mercoledì</th><th>giovedì</th><th>venerdì</th><th>sabato</th>
        <?php
            function lezione($lezioni, $giorno, $ora)
            {
                foreach($lezioni as $lez)
                    if ($lez["Giorno"] == $giorno && $lez["Ora"] == $ora)
                        return $lez;
                return null;
            }

            $lezioni = array();
            $giorni = array("lunedì", "martedì", "mercoledì", "giovedì", "venerdì", "sabato");
            if ($isDocente)
                $sql = $conn->prepare("SELECT Giorno, Ora, Materia, AnnoClasse, SezClasse, Aula FROM Docente, Lezione WHERE UserDocente = ? ORDER BY Ora");
            else
                $sql = $conn->prepare("SELECT Lezione.Giorno, Lezione.Ora, Lezione.Materia, Docente.Cognome, Lezione.Aula FROM Docente, Lezione WHERE Docente.Username = Lezione.UserDocente AND (Lezione.AnnoClasse, Lezione.SezClasse) = ANY (SELECT AnnoClasse, SezClasse FROM Studente WHERE Username = ?) ORDER BY Lezione.Ora");
            $sql->bind_param("s", $_SESSION["user"]["Username"]);
            $sql->execute();
            $sqlLezioni = $sql->get_result();
            while ($row = $sqlLezioni->fetch_assoc())
                array_push($lezioni, $row);

            if (count($lezioni) > 0)
            {
                $oraMax = $lezioni[count($lezioni) - 1]["Ora"];
                for ($ora = 1; $ora <= $oraMax; $ora++)
                {
                    echo "<tr>";
                    for ($i = 0; $i < count($giorni); $i++)
                    {
                        $lez = lezione($lezioni, $giorni[$i], $ora);
                        echo "<td class='center'>";
                        if ($isDocente)
                            echo $lez ? "<b>{$lez["Materia"]}</b><br>{$lez["AnnoClasse"]}{$lez["SezClasse"]}<br><i>{$lez["Aula"]}</i>" : "";
                        else
                            echo $lez ? "<b>{$lez["Materia"]}</b><br>{$lez["Cognome"]}<br><i>{$lez["Aula"]}</i>" : "";
                        echo "</td>";
                    }
                    echo "</tr>";
                }
            }
        ?>
    </table>
</body>
</html>