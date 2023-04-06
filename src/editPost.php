<?php
session_start();
require_once("connection.php");

if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
         if (isset($_POST["body"]) && isset($_POST["postID"])) {
            $updateStmt = $conn->prepare("UPDATE Post SET body = ? WHERE postID = ?");
            $updateStmt->bind_param("si", $_POST["body"], $_POST["postID"]);
            $updateStmt->execute();
            header("location:post.php?id=". $_POST["postID"]);
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