<?php
session_start();
require_once("connection.php");

if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["userID"]) && isset($_POST["userStatus"])) {
            if ($_POST["userStatus"] == 0) {
                $updateStmt = $conn->prepare("UPDATE User SET disabled = 1 WHERE userID = ?");
                $updateStmt->bind_param("i", $_POST["userID"]);
                $updateStmt->execute();
                header("location:account.php?id=". $_POST["userID"]);
            } else {
                $updateStmt = $conn->prepare("UPDATE User SET disabled = 0 WHERE userID = ?");
                $updateStmt->bind_param("i", $_POST["userID"]);
                $updateStmt->execute();
                header("location:account.php?id=". $_POST["userID"]);
            }
        } else {
            echo "Error fetching user data";
        }
    } else {
        header("location:home.php");
        exit;
    }
} else {
    header("location:signin.php");
    exit;
}

?>