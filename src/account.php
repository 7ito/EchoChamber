<?php
    session_start();
    require_once("connection.php");

    // if (!isset($_SESSION["user"]) || !isset($_SESSION["userID"])) {
    //     header("location:signin.php");
    //     exit;
    // }

    $id = 0;
    $username = "";
    $isDisabled = 0; //0 is not disabled 
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
         
    } else {
        $username = $_SESSION["user"];
        $id = $_SESSION["userID"];
    }
    
    $result = $conn->query("SELECT * FROM User WHERE userID = ". $id);
    if ($row = $result->fetch_assoc()) {
        $username = $row["username"];
        if ($row["disabled"] == 1) {
            $isDisabled = 1;
        }
    } else {
        //user doesn't exist
        header("location:home.php");
    }

    $isAdmin = $signedIn = false;
    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
        $signedIn = true;
        $adminResult = $conn->query("SELECT * FROM Admin WHERE userID = ". $_SESSION["userID"]);
        if ($adminRow = $adminResult->fetch_assoc()) {
            $isAdmin = true;
        } 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/account.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
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
            <?php
                if($signedIn) {
                    if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
                        echo "<a href=\"account.php\" class=\"nav-link active\">". $_SESSION["user"] ."</a>";
                    } 
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
                        <h1 class="card-title">Account Overview</h1>
                        <div class="card-text">
                            <div class="accordion" id="contentAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#commentsContainer" aria-expanded="true" aria-controls="commentsContainer">Comments</button>
                                    </h2>
                                    <div id="commentsContainer" class="accordion-collapse collapse show" data-bs-parent="#contentAccordion">
                                        <?php
                                            $commentQuery = "SELECT * FROM Comment WHERE commenterId = ". $id . " ORDER BY whenPosted DESC";
                                            $commentResult = mysqli_query($conn, $commentQuery);

                                            if ($commentResult->num_rows > 0) {
                                                while ($row = $commentResult->fetch_assoc()) {
                                                    $postQuery = "SELECT * FROM Post WHERE postID = ". $row["postID"];
                                                    $postResult = mysqli_query($conn, $postQuery);
                                                    
                                                    $postTitle = $communityName = "";
                                                    if ($postRow = $postResult->fetch_assoc()) {
                                                        $postTitle = $postRow["title"];

                                                        $communityQuery = "SELECT * FROM Community WHERE communityID = ". $postRow["communityID"];
                                                        $communityResult = mysqli_query($conn, $communityQuery);
                                                        if ($communityRow = $communityResult->fetch_assoc()) {
                                                            $communityName = $communityRow["name"];
                                                        } else {
                                                            $communityName = "Error getting community info";
                                                        }
                                                    } else {
                                                        $postTitle = "Error getting post info";
                                                    }

                                                    echo "<div class=\"card\"><div class=\"card-body\">";
                                                    echo "<h5 class=\"card-title\"><a href=\"post.php?id=". $row["postID"] ."\">". $postTitle. "</a><small class=\"text-muted\"> in <a href=\"sub.php?id=". $communityName ."\">". $communityName ."</a></small></h5>";
                                                    echo "<p class=\"card-text\">". $row["text"]. "</p>";
                                                    echo "<p class=\"card-text\"><small class=\"text-muted\">Posted at ". $row["whenPosted"]. "</small></p>";
                                                    echo "</div></div>";
                                                }
                                            } else {
                                                echo "<p class=\"card-text m-3\">This user has not submitted any comments yet.</p>";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="accordian-item">
                                    <h2 class="accordian-header">
                                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#postsContainer" aria-expanded="true" aria-controls="postsContainer">Posts</button>
                                    </h2>
                                    <div id="postsContainer" class="accordian-collapse collapse" data-bs-parent="#contentAccordian">
                                        <?php
                                            $postRset = $conn->query("SELECT * FROM Post WHERE posterID = ". $id ." ORDER BY whenPosted DESC");
                                            
                                            if ($postRset->num_rows > 0) {
                                                while ($row = $postRset->fetch_assoc()) {
                                                    $communityRset = $conn->query("SELECT * FROM Community WHERE communityID = ". $row["communityID"]);

                                                    $communityName = "";
                                                    if ($commRow = mysqli_fetch_assoc($communityRset)) {
                                                        $communityName = $communityRow["name"];
                                                    } else {
                                                        $communityName = "Error getting community info";
                                                    }

                                                    echo '<div class="card"><div class="card-body">';
                                                    echo '<h5 class="card-title"><a href="post.php?id='. $row["postID"] .'">'. $row["title"] .'</a><small class="text-muted"> in <a href="sub.php?id='. $communityName .'">'. $communityName .'</a></small></h5>';
                                                    echo '<p class="card-text"><small class="text-muted">Posted at '. $row["whenPosted"]. '</small></p>';
                                                    echo '</div></div>';
                                                }
                                            } else {
                                                echo '<p class="card-text m-3">This user has not submitted any posts yet.</p>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title"><?php echo $username ?></h1>
                        <div class="pfp-frame mt-3">
                            <?php
                                $pfpExistenceQuery = "SELECT * FROM userImages WHERE userID = ". $id;
                                $result = $conn->query($pfpExistenceQuery);

                                if ($row = $result->fetch_assoc()) {
                                    //TODO: display pfp image
                                    $pfpStmt = $conn->prepare("SELECT fileType, image FROM userImages WHERE userID = ?");
                                    $pfpStmt->bind_param("i", $id);
                                    $rset = mysqli_stmt_execute($pfpStmt) or die(mysqli_stmt_error($pfpStmt));
                                    mysqli_stmt_bind_result($pfpStmt, $type, $image);
                                    mysqli_stmt_fetch($pfpStmt);
                                    mysqli_stmt_close($pfpStmt);

                                    echo '<div class="pfpcontainer"><img src="data:image/'. $type .';base64,'. base64_encode($image) .'"/></div>';
                                } else {
                                    if ($signedIn) {
                                        if ($_SESSION["userID"] == $id) {
                                            echo "<div class=\"text-center emptypfp\"><p>No profile picture set</p>";
                                            if ($isDisabled == 0) {
                                                echo "<button class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#upload-pfp\">Set one now</button>";
                                            }
                                            echo "</div>";
                                        } else {
                                            echo '<div class="text-center emptypfp"><p>No profile picture set</p></div>';
                                        }
                                    }
                                }
                            ?>
                        </div>
                        <div class="card-text text-center">
                            <?php
                                if ($signedIn) {
                                    if ($_SESSION["userID"] == $id) {
                                        echo "<div class=\"btn-group-vertical mt-3\"><a href=\"reset-password.php\" class=\"btn btn-warning\">Reset Password</a>";
                                        echo "<a href=\"logout.php\" class=\"btn btn-danger ml-3\">Log Out</a></div>";
                                    }
                                    if ($isAdmin) {
                                        if ($id != $_SESSION["userID"]) {
                                            if ($isDisabled == 0) {
                                                echo '<form action="toggleUserStatus.php" method="post" class="mt-3">';
                                                echo '<input type="text" name="userID" value="'. $id .'" hidden>';
                                                echo '<input type="text" name="userStatus" value="'. $isDisabled .'" hidden>';
                                                echo '<input type="submit" value="Disable User" class="btn btn-danger">';
                                                echo '</form>';
                                            } else {
                                                echo '<form action="toggleUserStatus.php" method="post" class="mt-3">';
                                                echo '<input type="text" name="userID" value="'. $id .'" hidden>';
                                                echo '<input type="text" name="userStatus" value="'. $isDisabled .'" hidden>';
                                                echo '<input type="submit" value="Enable User" class="btn btn-warning">';
                                                echo '</form>';
                                            }
                                        }
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="upload-pfp" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal"></button>
                    <div class="pfpform bg-white">
                        <h3>Upload Profile Picture</h3>
                        <form action="uploadPfp.php" method="post" enctype="multipart/form-data">
                            <label for="userImage"><small class="text-muted">File size must be less than 100kb and must be jpg, png, or gif.</small></label>
                            <div class="mt-3"><input class="form-control" type="file" name="userImage" id="userImage" required></div>
                            <div><button class="btn btn-primary mt-3">Submit</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>