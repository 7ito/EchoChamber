<?php 
session_start();
require_once("connection.php");

if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["postID"]) && isset($_POST["communityID"])) {
            if (isset($_POST["normalUser"])) {
                $updateStmt = $conn->prepare('UPDATE Post SET body = "[removed by user]" WHERE postID = ?');
                $updateStmt->bind_param("i", $_POST["postID"]);
                $updateStmt->execute();
                header("location:sub.php?id=". $_POST["communityID"]);
            } else {
                $deleteStmt = $conn->prepare('DELETE FROM Post WHERE postID = ?');
                $deleteStmt->bind_param("i", $_POST["postID"]);
                $deleteStmt->execute();
                header("location:sub.php?id=". $_POST["communityID"]);
            }
        } else {
            echo "Error fetching post data";
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