<?php
    session_start();

    function printFastLink($path, $valore)
    {
        echo "<a href='{$path}'>$valore</a>";
    }

    if (!isset($_SESSION["user"]))
        header("Location: index.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home</title>
</head>
<body>
    <h1>Home</h1>
    <?php
        $user = $_SESSION["user"];
        echo "<p>Benvenuto <b> {$user["Nome"]} {$user["Cognome"]}</b>.</p>";
        echo "<div class='sfondo'>";
        if (isset($user["Amministratore"])) // è un docente
        {
            printFastLink("voti.php", "Voti");
            printFastLink("orario.php", "Orario lavorativo");

            if ($user["Amministratore"]) // il docente è anche un amministratore
            {
                printFastLink("studenti_insegnanti.php", "Studenti e insegnanti");
            }
        }
        else // è uno studente
        {
            printFastLink("voti.php", "I tuoi voti");
            printFastLink("orario.php", "Orario delle lezioni");
        }
        echo "</div>";
    ?>
    <a href="index.php?logout=1">Esci</a>
</body>
</html>