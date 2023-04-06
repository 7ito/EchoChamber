<?php
session_start();
require_once("connection.php");

if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory. basename($_FILES["userImage"]["name"]);
        $uploadOk = true;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        //check if file is an image
        $check = getimagesize($_FILES["userImage"]["tmp_name"]);
        if ($check !== false) {
            //file is an image
            $uploadOk = true;
        } else {
            $uploadOk = false;
        }

        //limit file size
        if ($_FILES["userImage"]["size"] > 100000) {
            $uploadOk = false;
        }

        //limit file type
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif") {
            //file not of type jpg, png, or gif
            $uploadOk = false;
        }
        
        if ($uploadOk) {
            $imageData = file_get_contents($_FILES["userImage"]["tmp_name"]);
            $updateStmt = $conn->prepare("UPDATE userImages SET fileType = ?, image = ? WHERE userID = ?");
            $null = null;
            $updateStmt->bind_param("sbi", $imageFileType, $null, $_SESSION["userID"]);

            mysqli_stmt_send_long_data($updateStmt, 1, $imageData);
            $result = mysqli_stmt_execute($updateStmt) or die(mysqli_stmt_error($updateStmt));
            mysqli_stmt_close($updateStmt);
            header("location:account.php");
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