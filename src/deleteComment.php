<?php
session_start();
require_once("connection.php");

if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["postID"]) && isset($_POST["commentID"])) {
            if (isset($_POST["normalUser"])) {
                $updateStmt = $conn->prepare('UPDATE Comment SET text = "[removed by user]" WHERE commentID = ?');
                $updateStmt->bind_param("i", $_POST["commentID"]);
                $updateStmt->execute();
                header("location:post.php?id=". $_POST["postID"]);
            } elseif (isset($_POST["communityOwner"])) {
                $updateStmt = $conn->prepare('UPDATE Comment SET text = "[removed by community moderator]" WHERE commentID = ?');
                $updateStmt->bind_param("i", $_POST["commentID"]);
                $updateStmt->execute();
                header("location:post.php?id=". $_POST["postID"]);
            } elseif (isset($_POST["admin"])) {
                $updateStmt = $conn->prepare('UPDATE Comment SET text = "[removed by administrator]" WHERE commentID = ?');
                $updateStmt->bind_param("i", $_POST["commentID"]);
                $updateStmt->execute();
                header("location:post.php?id=". $_POST["postID"]);
            }
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