<?php
    session_start();
    require_once("connection.php");
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
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h1 class="card-title">Your Communities</h1>
              <div class="card-text">
                <?php
                    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
                        $query = "SELECT communityID FROM Saved WHERE userID = ". $_SESSION["userID"];
                        $rset = $conn->query($query);

                        if ($rset->num_rows > 0) {
                          while ($row = $rset->fetch_assoc()) {
                            $selectComm = "SELECT * FROM Community WHERE communityID = ". $row["communityID"];
                            $commRset = $conn->query($selectComm);
                            if ($commRow = mysqli_fetch_assoc($commRset)) {
                              echo "<div><a href=\"sub.php?id=". $commRow["name"] ."\" class=\"btn btn-link\">". $commRow["name"] ."</a></div>";
                            }
                          }
                        } else {
                          echo "<p>You have no saved communities!</p>";
                        }
                        echo "<div class=\"text-center\"><button class=\"btn btn-outline-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#createCommunity\">Create a Community</button></div>"; 
                    } else {
                      echo "<p><a href=\"signin.php\">Sign in or create an account</a> to save communities</p>";
                    }
                ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <div class="card">
              <div class="card-body">
                <h1 class="card-title">Catalog</h1>
                <div class="card-text">
                  <?php
                    $select = "SELECT * FROM Community";
                    $result = $conn->query($select);
    
                    if ($result->num_rows > 0) {
                      while ($exploreRow = $result->fetch_assoc()) {
                        echo "<div><a href=\"sub.php?id=". $exploreRow["name"] ."\" class=\"btn btn-link\">". $exploreRow["name"] ."</a></div>";
                      }
                    } else {
                      echo "<p>There are no communities yet. Be the first to create one!</p>";
                    }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="createCommunity" class="modal fade">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body">
            <button type="button" data-bs-dismiss="modal" class="btn btn-close"></button>
            <div class="createCommForm">
              <h1 class="text-center">Create a Community</h1>
              <form action="createCommunity.php" method="post">
                <div class="mb-3 mt-3">
                  <label for="name">Community Name</label>
                  <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3 mt-3">
                  <label for="desc">Description</label>
                  <p><small class="text-muted">This will be the text in your sidebar.</small></p>
                  <textarea name="desc" rows="8" class="form-control"></textarea>
                </div>
                <div class="mb-3 mt-3">
                  <label for="rules">Rules</label>
                  <p><small class="text-muted">Set your submission rules here, this will be listed in your sidebar as well.</small></p>
                  <textarea name="rules" rows="8" class="form-control"></textarea>
                </div>
                <input type="submit" class="btn btn-primary mt-3" name="create" value="Create">
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>
</html>