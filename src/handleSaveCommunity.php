<?php
    session_start();
    require_once("connection.php");

    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["communityID"]) && isset($_POST["type"]) && isset($_POST["communityName"])) {
                if ($_POST["type"] == 0) {
                    //unsave
                    $deleteStmt = $conn->prepare("DELETE FROM Saved WHERE userID = ? and communityID = ?");
                    $deleteStmt->bind_param("ii", $userID, $communityID);
                    $userID = $_SESSION["userID"];
                    $communityID = $_POST["communityID"];

                    $deleteStmt->execute();
                } else {
                    //save
                    $insertStmt = $conn->prepare("INSERT Saved VALUES(?, ?)");
                    $insertStmt->bind_param("ii", $userID, $communityID);
                    $userID = $_SESSION["userID"];
                    $communityID = $_POST["communityID"];

                    $insertStmt->execute();
                }
                header("location:sub.php?id=". $_POST["communityName"]);
            } else {
                echo "Error fetching data";
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