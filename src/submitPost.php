<?php
    session_start();
    require_once("connection.php");

    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["title"]) && isset($_POST["body"]) && isset($_POST["communityID"])) {
                $insertStmt = $conn->prepare("INSERT Post (title, body, whenPosted, points, posterID, communityID) VALUES (?, ?, ?, 1, ?, ?)");
                $insertStmt->bind_param("sssii", $title, $body, $whenPosted, $posterID, $communityID);
                $title = $_POST["title"];
                $body = $_POST["body"];
                $whenPosted = date("Y-m-d H:i:s");
                $posterID = $_SESSION["userID"];
                $communityID = $_POST["communityID"];

                $insertStmt->execute();
                header("location:post.php?id=". mysqli_insert_id($conn));
            }
            
        } else {
            header("location:home.php");
            exit;
        }
    } else {
        header ("location:signin.php");
        exit;
    }
?>