<?php
    session_start();

    if (!isset($_SESSION["user"]) || !isset($_SESSION["userID"])) {
        header("location:signin.php");
        exit;
    }

    require_once("connection.php");

    $oldpw = $newpw = $confirmnewpw = "";
    $oldpwerror = $newpwerror = $confirmnewpwerror = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // check oldpw
        if (empty(trim($_POST["oldpw"]))) {
            $oldpwerror = "Please enter your old password";
        } else {
            $query = "SELECT password FROM User WHERE userID = \"". $_SESSION["userID"] ."\"";
            $rset = $conn->query($query);

            if ($row = mysqli_fetch_assoc($rset)) {
                if (md5($_POST["oldpw"]) == md5($row["password"])) {
                    $oldpwerror = "Incorrect password";
                }
            } else {
                echo "Error: User not found";
            }
        }

        // validate new password
        if (empty(trim($_POST["newpw"]))) {
            $newpwerror = "Please enter a new password";
        } elseif (strlen(trim($_POST["newpw"])) < 6) {
            $newpwerror = "Password must have at least 6 characters";
        } else {
            $newpw = trim($_POST["newpw"]);
        }

        // validate password check
        if (empty(trim($_POST["confirmnewpw"]))) {
            $confirmnewpwerror = "Please re-enter your new password to confirm it";
        } else {
            $confirmnewpw = trim($_POST["confirmnewpw"]);
            if (empty($newpwerror) && ($newpw != $confirmnewpw)) {
                $confirmnewpwerror = "Passwords did not match";
            }
        }

        if (empty($oldpwerror) && empty($newpwerror) && empty($confirmnewpwerror)) {
            $update = "UPDATE User SET password = \"". md5($newpw) ."\" WHERE userID = ". $_SESSION["userID"];
            if ($conn->query($update) == TRUE) {
                session_destroy();
                header("location: signin.php");
                exit();
            } else {
                echo "Error updating password";
            }
        }
    }
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title>Reset Password</title>
</head>
<body>
    <div class="container pt-2">
        <h1>Reset Password</h1>
        <form action="reset-password.php" method="post">
            <div class="form-group mb-3 mt-3">
                <label for="oldpw">Old Password</label>
                <input type="password" name="oldpw" id="oldpw" class="form-control <?php echo (!empty($oldpwerror)) ? 'is-invalid' : ''; ?>" value="<?php echo $oldpw; ?>">
                <span class="invalid-feedback"><?php echo $oldpwerror; ?></span>
            </div>
            <div class="form-group mb-3 mt-3">
                <label for="newpw">New Password</label>
                <input type="password" name="newpw" id="newpw" class="form-control <?php echo (!empty($newpwerror)) ? 'is-invalid' : ''; ?>" value="<?php echo $newpw; ?>">
                <span class="invalid-feedback"><?php echo $newpwerror; ?></span>
            </div>
            <div class="form-group mb-3 mt-3">
                <label for="confirmnewpw">Confirm New Password</label>
                <input type="password" name="confirmnewpw" id="confirmnewpw" class="form-control <?php echo (!empty($confirmnewpwerror)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmnewpw; ?>">
                <span class="invalid-feedback"><?php echo $confirmnewpwerror; ?></span>
            </div>
            <div class="form-group mb-3 mt-3">
                <div class="btn-group">
                    <input type="submit" class="btn btn-danger" value="Reset Password">
                    <a href="account.php" class="btn btn-primary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>