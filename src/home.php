<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Echo Chamber</title>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
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
                    // echo $_SESSION["userID"];
                    $query = "SELECT communityID FROM Saved WHERE userID = ". $_SESSION["userID"];

                } else {
                    echo "<a href=\"signin.php\" class=\"nav-link active\">Account</a>";
                }
            ?>
          </li>
        </ul>
      </div>
    </nav>
    <div class="container pt-4">
      <div class="row row-cols-2">
        <div class="col-4">
          <div class="card">
            <div class="card-body">
              <h1 class="card-title">Your Communities</h1>
              <div class="card-text text-center">
                <?php
                    require_once("connection.php");
                    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
                        $query = "SELECT communityID FROM Saved WHERE userID = ". $_SESSION["userID"];
                        $rset = $conn->query($query);

                        if ($rset->num_rows > 0) {
                          while ($row = $rset->fetch_assoc()) {
                            $selectComm = "SELECT * FROM Community WHERE communityID = ". $row["communityID"];
                            $commRset = $conn->query($selectComm);
                            if ($commRow = mysqli_fetch_assoc($commRset)) {
                              echo "<div><a href=\"". $commRow["link"] ."\" class=\"btn btn-link\">". $commRow["name"] ."</a></div>";
                            }
                          }
                        } else {
                          echo "<p>You have no saved communities!</p>";
                        }
                    }
                ?>
              </div>
            </div>
          </div>
          <div class="card mt-4">
            <div class="card-body">
              <h1 class="card-title">Explore</h1>
              <div class="card-text">
                <?php
                  
                ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-8">
          <div class="card">
            <div class="card-body">
              <h1 class="card-title">All</h1>
              <div class="card-text">
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>
</html>