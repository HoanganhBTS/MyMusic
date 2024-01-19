<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Music</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php
    header("refresh: 5;");

    include_once("C:\MAMP\htdocs\MyMusic\\functions_globales.php");
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["session_name"])) {
        $_SESSION["session_name"] = $_POST["session_name"];
    }
    

    if (sessionExist() == true) {
        addUserSession();
        leaveSession();
        show_Desc();
        showFormSong();
        addSong();
        removeSongSession();
        vote();

        ?>

    </body>

    </html>
<?php } ?>