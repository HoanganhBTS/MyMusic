
<?php
include_once("C:\MAMP\htdocs\MyMusic\\functions_globales.php");
include_once("C:\MAMP\htdocs\MyMusic\User.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    register();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register MyMusic</title>
</head>
<body>
    <Section id="register">
        <form action="register.php" method="post">
            <label for="register_user">Nom d'utilisateur</label>
            <input type="text" name="register_user">
            <label for="register_password">Password</label>
            <input type="text" name="register_password">
            <button type=submit >Register</button>
        </form>
    </Section>

    
</body>
</html>