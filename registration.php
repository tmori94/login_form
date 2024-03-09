<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Registration Form</title>
</head>
<body>
    <div class="container">
        <?php 
        // global variable that catches all the data the user inputs in the form. We can access the data input by the user with this variable ex.: print_r($_POST)
        // $_POST
        // print_r($_POST);
        
        if (isset($_POST["submit"])) {
            $fullName = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $repeatPassword = $_POST["repeat_password"];
        
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
            $errors = array(); // if any error, we will add it to this array
            if (empty($fullName) OR empty($email) OR empty($password) OR empty($repeatPassword)) {
                array_push($errors,"All fields are required");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email is not valid");
            }
            if (strlen($password) < 8) {
                array_push($errors, "Your password must be at least 8 characters long");
            }
            if ($password !== $repeatPassword) {
                array_push($errors, "Your password does not match");
            }
        
            require_once "database.php"; // include the contents of the file database.php into the current script
            $sql = "SELECT * FROM users WHERE email = '$email'"; // check for existing email in the db table users
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount > 0) {
                array_push($errors, "This email address already exists");
            }
        
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)"; // ? is a placeholder used to avoid SQL injection
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql); // returns TRUE or FALSE (False if anything goes wrong)
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You have successfully registered!</div>";
                } else {
                    die("Something went wrong");
                }
            }
        }
        ?>
        <form action="registration.php" method="post">
        <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email Address">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
            <div><p>Already have an account? <a href="login.php">Login Here</a></p></div>
        </form>
    </div>
</body>
</html>