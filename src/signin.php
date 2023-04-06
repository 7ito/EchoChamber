<?php 
    session_start();

    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
      header("location:home.php");
      exit;
    }
    $errorCheck = false;
    require_once("connection.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $query = "SELECT * FROM User WHERE username = '". $_POST["username"] ."' and password='". md5($_POST["password"]) ."'";
      $result = mysqli_query($conn, $query);
      
      if ($row = mysqli_fetch_assoc($result)) {
          $_SESSION["user"] = $_POST["username"];
          $_SESSION["userID"] = $row["userID"];
          header("location:home.php");
      } else {
          $errorCheck = true;
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
    <link rel="stylesheet" href="styles/account_styles.css">
    <script type="text/javascript" src="script/account.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title>Account</title>
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
              <a href="signin.php" class="nav-link active">Account</a>
            </li>
          </ul>
        </div>
    </nav>
    <div class="container pt-4">
      <div class="row justify-content-md-center">
        <div class="col-4">
          <div class="card">
            <div class="card-body text-center">
              <h1 class="card-title">Account</h1>
              <div class="card-body">
                <button class="btn btn-primary" data-bs-toggle="modal" 
                data-bs-target="#login">Log In</button>
                <p>or</p>
                <button class="btn btn-secondary" data-bs-toggle="modal"
                data-bs-target="#register">Create an Account</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="login" class="modal fade">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body">
            <button type="button" 
            class="btn btn-close btn-close-white"
            data-bs-dismiss="modal"></button>
            <div class="loginform bg-dark">
              <h1 class="text-center">Log In</h1>
              <form action="signin.php" method="post">
                <div class="mb-3 mt-3">
                  <label for="username">Username</label>
                  <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3 mt-3">
                  <label for="password">Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <?php
                    if ($errorCheck == true) {
                ?>
                <div class="alert-light text-danger text-center py-3"><?php echo "Invalid username or password" ?></div>
                <?php
                }
                ?>
                <input type="submit" class="btn btn-light mt-3" name="login" value="Log In">
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="register" class="modal fade">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body">
            <button type="button"
            class="btn btn-close btn-close-white"
            data-bs-dismiss="modal"></button>
            <div class="registerform bg-dark">
              <h1 class="text-center">Create an Account</h1>
              <form action="signup.php" method="post">
                <div class="mb-3 mt-3">
                  <label for="username">Username</label>
                  <input type="text" name="username" class="form-control <?php if (isset($_SESSION["usernameError"])) {
                    echo (!empty($_SESSION["usernameError"])) ? 'is-invalid' : '';
                  } ?>">
                  <span class="invalid-feedback"><?php echo $_SESSION["usernameError"] ?></span>
                </div>
                <div class="mb-3 mt-3">
                  <label for="password">Password</label>
                  <input type="password" name="password" class="form-control <?php if (isset($_SESSION["passwordError"])) {
                    echo (!empty($_SESSION["passwordError"])) ? 'is-invalid' : '';
                  } ?>">
                  <span class="invalid-feedback"><?php echo $_SESSION["passwordError"] ?></span>
                </div>
                <div class="mb-3 mt-3">
                  <label for="password-confirm">Re-enter Password</label>
                  <input type="password" name="password-confirm" class="form-control <?php if(isset($_SESSION["confirmpwError"])) {
                    echo (!empty($_SESSION["confirmpwError"])) ? 'is-invalid' : '';
                  } ?>">
                  <span class="invalid-feedback"><?php echo $_SESSION["confirmpwError"] ?></span>
                </div>
                <input type="submit" class="btn btn-light mt-3" name="register" value="Create an Account">
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>
</html>