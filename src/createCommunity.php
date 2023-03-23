<?php
    session_start();
    require_once("connection.php");

    if(isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["name"]) && isset($_POST["desc"]) && isset($_POST["rules"])) {
                $insertStmt = $conn->prepare("INSERT Community (name, description, rules, banner, creatorID) VALUES (?, ?, ?, ?, ?)");
                $insertStmt->bind_param("ssssi", $name, $desc, $rules, $banner, $creatorID);
                $name = $_POST["name"];
                $desc = $_POST["desc"];
                $rules = $_POST["rules"];
                $banner = "banner";
                $creatorID = $_SESSION["userID"];

                $insertStmt->execute();
                header("location:sub.php?id=". $_POST["name"]);
            } else {
                "Error fetching community data";
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