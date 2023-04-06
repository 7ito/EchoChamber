<?php
session_start();
require_once("connection.php");

$userExistsError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"])) {
        $result = $conn->query('SELECT * FROM User WHERE username = "'. $_POST["username"] .'"');
        
        if ($row = $result->fetch_assoc()) {
            header("location:account.php?id=". $row["userID"]);
        } else {
            $userExistsError = "User does not exist on the database";
        }
    }
}

$isAdmin = false;
if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
    $adminResult = $conn->query("SELECT * FROM Admin WHERE userID = ". $_SESSION["userID"]);
    if ($adminRow = $adminResult->fetch_assoc()) {
        $isAdmin = true;
    } 

    if (!$isAdmin) {
        header("location:home.php");
        exit;
    }

} else {
    header("location:signin.php");
    exit;
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
    <title>Find User</title>
</head>
<body>
    <nav class="navbar navbar-expand navbar-dark bg-dark">
      <div class="container">
        <a href="home.php" class="navbar-brand">Echo Chamber</a>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="home.php" class="nav-link active">Home</a>
          </li>
          <li class="nav-item">
            <?php
                if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
                    echo "<a href=\"account.php\" class=\"nav-link active\">". $_SESSION["user"] ."</a>";

                } else {
                    echo "<a href=\"signin.php\" class=\"nav-link active\">Account</a>";
                }
            ?>
          </li>
          <?php
            if ($isAdmin) {
              echo '<li class="nav-item"><a href="findUser.php" class="nav-link active">Find User</a></li>';
            }
          ?>
        </ul>
      </div>
    </nav>
    <div class="container pt-4">
        <div class="row justify-content-md-center">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">Find User</h1>
                        <form action="findUser.php" method="post">
                            <div class="input-group mb-3 mt-3">
                                <span class="input-group-text" id="addon">@</span>
                                <input type="text" name="username" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="addon">
                                <?php
                                    if (!empty($userExistsError)) {
                                        echo '<span class="input-group-text">'. $userExistsError .'</span>';
                                    }
                                ?>
                            </div>
                            <div class="input-group mb-3 mt-3">
                                <input type="submit" class="btn btn-primary" value="Search">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>