<?php

    session_start();
    if (isset($_SESSION["user"]) || isset($_SESSION["userID"])) {
        header("location:signin.php");
        exit;
    }

    require_once("connection.php");

    $username = $password = $confirmpw = "";
    $usernameerror = $passworderror = $confirmpwerror = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // validate username
        if (empty(trim($_POST["username"]))) {
            $usernameerror = "Please enter a username";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
            $usernameerror = "Username can only contain letters, numbers, and underscores.";
        } else {
            $select = "SELECT userID FROM User WHERE username = \"". $_POST["username"]. "\"";
            $rset = $conn->query($select);

            if ($rset->num_rows > 0) {
                $usernameerror = "This username is already taken";
            } else {
                $username = trim($_POST["username"]);
            }
        } 

        //validate password
        if (empty(trim($_POST["password"]))) {
            $passworderror = "Please enter a password";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $passworderror = "Password must have at least 6 characters";
        } else {
            $password = trim($_POST["password"]);
        }

        //validate confirm password
        if (empty(trim($_POST["password-confirm"]))) {
            $confirmpwerror = "Please re-enter your password to confirm it";
        } else {
            $confirmpw = trim($_POST["password-confirm"]); 
            if (empty($passworderror) && ($password != $confirmpw)) {
                $confirmpwerror = "Passwords did not match";
            }
        }

        $_SESSION["usernameError"] = $usernameerror;
        $_SESSION["passwordError"] = $passworderror;
        $_SESSION["confirmpwError"] = $confirmpwerror;

        if (empty($usernameerror) && empty($passworderror) && empty($confirmpwerror)) {
            $insert = "INSERT User (username, password) VALUES (\"". $_POST["username"] ."\", \"". $_POST["password"] ."\")";          
            if ($conn->query($insert) == TRUE) {
                header("location:signin.php");
            } else {
                echo "Error creating account";
            }
        } else {
            header("location:signin.php");
        }
    } 

?>