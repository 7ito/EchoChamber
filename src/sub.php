<?php
    session_start();
    require_once("connection.php");

    $communityID = $creatorID = -1;
    $name = $desc = $rules = $banner = "";
    $isDisabled = $isAdmin = $isCommunityOwner = false;

    if (isset($_GET["id"])) {
        $select = "SELECT * FROM Community WHERE name = \"". $_GET["id"] ."\"";
        $result = mysqli_query($conn, $select);

        if ($row = mysqli_fetch_assoc($result)) {
            $communityID = $row["communityID"];
            $name = $row["name"];
            $desc = $row["description"];
            $rules = $row["rules"];
            $banner = $row["banner"];
            $creatorID = $row["creatorID"];

            $creatorResult = $conn->query("SELECT * FROM User WHERE userID = ". $creatorID);
            $creatorName = "";
            if ($creatorRow = $creatorResult->fetch_assoc()) {
                $creatorName = $creatorRow["username"];
            } else {
                $creatorName = "Error fetching creator data";
            }
        }

        if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
            $adminResult = $conn->query("SELECT * FROM Admin WHERE userID = ". $_SESSION["userID"]);
            if ($adminRow = $adminResult->fetch_assoc()) {
                $isAdmin = true;
            } 
            if ($_SESSION["userID"] == $creatorID) {
                $isCommunityOwner = true;
            }

            $disabledResult = $conn->query("SELECT * FROM User WHERE userID = ". $_SESSION["userID"]);
            if($disabledRow = $disabledResult->fetch_assoc()) {
                if ($disabledRow["disabled"] == 1) {
                    $isDisabled = true;
                }
            } 
        }
    } else {
        header("location:home.php");
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
    <!-- <link rel="stylesheet" href="styles/reset.css"> -->
    <link rel="stylesheet" href="styles/postsubmit_styles.css">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title><?php echo $name ?></title>
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
        <div class="row cols-2">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">Posts in <?php echo $name; ?></h1>
                        <div class="card-text">
                            <?php
                                $postQuery = "SELECT * FROM Post WHERE communityID = ". $communityID .' ORDER BY whenPosted DESC';
                                $result = $conn->query($postQuery);
                                
                                if ($result->num_rows > 0) {
                                    echo "<div class=\"pt-3 pb-3\">";
                                    while ($row = $result->fetch_assoc()) {
                                        $getPosterQuery = "SELECT username FROM User WHERE userID = ". $row["posterID"];
                                        $userRset = mysqli_query($conn, $getPosterQuery);
                                        $userRow = mysqli_fetch_assoc($userRset);

                                        echo "<div class=\"card post\"><div class=\"card-body\">";
                                        echo "<a href=\"post.php?id=". $row["postID"] ."\" class=\"card-title\">". $row["title"] ."</a>";
                                        echo "<p class=\"card-text\"><small class=\"text-muted\">Posted by <a href=\"account.php?id=". $row["posterID"] ."\">". $userRow["username"] ."</a>, on ". $row["whenPosted"]. "</small></p>";

                                        if ($isAdmin || $isCommunityOwner) {
                                            echo '<form action="deletePost.php" method="post">';
                                            echo '<input type="text" name="postID" value="'. $row["postID"] .'" hidden>';
                                            echo '<input type="text" name="communityID" value="'. $name .'" hidden>';
                                            echo '<input type="submit" class="btn btn-danger" name="submitDelete" value="Delete">';
                                            echo '</form>';
                                        } elseif (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
                                            if ($row["posterID"] == $_SESSION["userID"] && !$isDisabled) {
                                                echo '<form action="deletePost.php" method="post">';
                                                echo '<input type="text" name="normalUser" value="true" hidden>';
                                                echo '<input type="text" name="postID" value="'. $row["postID"] .'" hidden>';
                                                echo '<input type="text" name="communityID" value="'. $name .'" hidden>';
                                                echo '<input type="submit" class="btn btn-danger" name="submitDelete" value="Delete">';
                                                echo '</form>';
                                            }
                                        }

                                        echo "</div></div>";
                                    }
                                    echo "</div>";
                                } else {
                                    echo "<p>There doesn't seem to be any posts here... You could be the first!</p>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">Sidebar</h1>
                        <div class="card-text">
                            <?php
                                if (isset($_SESSION["user"]) && isset($_SESSION["userID"]) && !$isDisabled) {
                                    echo "<div class=\"text-center pt-3\"><button class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#submit\">Submit a Post</button></div>";
                                }
                                echo "<h3 class=\"card-text pt-3\">Description</h3>";
                                echo "<p class=\"card-text\">". $desc ."</p>";
                                echo "<h3 class=\"card-text\">Rules</h3>";
                                echo "<p class=\"card-text\">". $rules ."</p>";
                                echo '<p class="card-text">Community owner: <a href="account.php?id='. $creatorID .'">'. $creatorName .'</a></p>';
                            ?>
                        </div>
                        <?php
                            if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
                                //check if user has already saved this community
                                $saveCheck = "SELECT * FROM Saved WHERE userID = ". $_SESSION["userID"] ." and communityID = ". $communityID;
                                $result = $conn->query($saveCheck);

                                if ($result->num_rows > 0) {
                                    //if the user has saved this commmunity
                                    echo "<div class=\"text-center pt-4\"><form action=\"handleSaveCommunity.php\" method=\"post\">";
                                    echo "<input type=\"text\" name=\"communityID\" value=\"". $communityID ."\" hidden>";
                                    echo "<input type=\"text\" name=\"type\" value=\"0\" hidden>";
                                    echo "<input type=\"text\" name=\"communityName\" value=\"". $name ."\" hidden>";
                                    echo "<input type=\"submit\" class=\"btn btn-outline-info\" name=\"save\" value=\"Unsave this community\">";
                                    echo "</form></div>";   
                                } else {
                                    echo "<div class=\"text-center pt-4\"><form action=\"handleSaveCommunity.php\" method=\"post\">";
                                    echo "<input type=\"text\" name=\"communityID\" value=\"". $communityID ."\" hidden>";
                                    echo "<input type=\"text\" name=\"type\" value=\"1\" hidden>";
                                    echo "<input type=\"text\" name=\"communityName\" value=\"". $name ."\" hidden>";
                                    echo "<input type=\"submit\" class=\"btn btn-info\" name=\"save\" value=\"Save this community to your home sidebar\">";
                                    echo "</form></div>";   
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="submit" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" 
                    class="btn btn-close"
                    data-bs-dismiss="modal"></button>
                    <div class="submitform bg-white">
                        <h3>Submit a Post</h3>
                        <form action="submitPost.php" method="post">
                            <div>
                                <label for="title">Post Title</label>
                                <input type="text" class="form-control" name="title">
                            </div>
                            <div>
                                <label for="body">Post Body</label>
                                <textarea class="form-control" name="body" id="body" rows="7"></textarea>
                                <input type="text" name="communityID" value="<?php echo $communityID ?>" hidden>
                            </div>
                            <div>
                                <button class="btn btn-primary mt-3">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>