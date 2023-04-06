<?php 
session_start();
require_once("connection.php");

if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["text"]) && isset($_POST["commentID"]) && isset($_POST["postID"])) {
            $updateStmt = $conn->prepare("UPDATE Comment SET text = ? WHERE commentID = ?");
            $updateStmt->bind_param("si", $_POST["text"], $_POST["commentID"]);
            $updateStmt->execute();
            header("location:post.php?id=". $_POST["postID"]);
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