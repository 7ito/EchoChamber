<?php
    session_start();
    require_once("connection.php");

    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["body"]) && isset($_POST["replyTo"])) {
                $insertStmt = $conn->prepare("INSERT Comment (text, whenPosted, points, replyTo, commenterID, postID) VALUES (?, ?, 1, ?, ?, ?)");
                $insertStmt->bind_param("ssiii", $text, $whenPosted, $replyTo, $commenterID, $postID);
                $text = $_POST["body"];
                $whenPosted = date("Y-m-d H:i:s");
                $replyTo = $_POST["replyTo"];
                $commenterID = $_SESSION["userID"];
                $postID = $_POST["postID"];

                $insertStmt->execute();
                header("location:post.php?id=". $_POST["postID"]);
            } else {
                echo "Error fetching comment data";
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