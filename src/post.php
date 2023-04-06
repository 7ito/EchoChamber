<?php
    session_start();
    require_once("connection.php");

    $title = $body = $whenPosted = $poster = "";
    $points = $posterID = $communityID = $communityOwnerID = -1;
    $communityName = $desc = $rules = "";
    $isDisabled = $isAdmin = $isCommunityOwner = $isPoster = false;
    $userType = 0; //0 = user, 1 = community owner, 2 = admin

    if (isset($_GET["id"])) {
        $select = "SELECT * FROM Post WHERE postId = ". $_GET["id"];
        $result = mysqli_query($conn, $select);

        if ($row = mysqli_fetch_assoc($result)) {
            $title = $row["title"];
            $body = $row["body"];
            $whenPosted = $row["whenPosted"];
            $points = $row["points"];
            $posterID = $row["posterID"];
            $communityID = $row["communityID"];

            $getUser = "SELECT username FROM User WHERE userID = ". $posterID;
            $userResult = mysqli_query($conn, $getUser);
            if ($userRow = mysqli_fetch_assoc($userResult)) {
                $poster = $userRow["username"];
            }

            $getCommunity = "SELECT * FROM Community WHERE communityID = ". $communityID;
            $commResult = mysqli_query($conn, $getCommunity);
            if ($commRow = mysqli_fetch_assoc($commResult)) {
                $communityName = $commRow["name"];
                $desc = $commRow["description"];
                $rules = $commRow["rules"];
                $communityOwnerID = $commRow["creatorID"];
            }
        }

        if (isset($_SESSION["user"]) && isset($_SESSION["userID"])) {
            $adminResult = $conn->query("SELECT * FROM Admin WHERE userID = ". $_SESSION["userID"]);
            if ($adminRow = $adminResult->fetch_assoc()) {
                $isAdmin = true;
            } 
            if ($_SESSION["userID"] == $communityOwnerID) {
                $isCommunityOwner = true;
            }
            if ($_SESSION["userID"] == $posterID) {
                $isPoster = true;
            }
            $disabledResult = $conn->query("SELECT * FROM User WHERE userID = ". $_SESSION["userID"]);
            if($disabledRow = $disabledResult->fetch_assoc()) {
                if ($disabledRow["disabled"] == 1) {
                    $isDisabled = true;
                }
            } 
        } 

        if ($isCommunityOwner) {
            $userType = 1;
        } 
        if ($isAdmin) {
            $userType = 2;
        } 

    } else {
        header("location:home.php");
        exit;
    }

    function checkReplies($commentID, $conn, $depth, $userType, $isDisabled) {
        $getReplies = "SELECT * FROM Comment WHERE replyTo = ". $commentID;
        $result = $conn->query($getReplies);

        // get comment data
        $thisComment = "SELECT * FROM Comment WHERE commentID = ". $commentID;
        $commentResult = $conn->query($thisComment);
        $commentRow = mysqli_fetch_assoc($commentResult);

        // get commenter data
        $commenterQuery = "SELECT * FROM User WHERE userID = ". $commentRow["commenterID"];
        $userResult = $conn->query($commenterQuery);
        $userRow = mysqli_fetch_assoc($userResult);

        echo "<div style=\"padding-left: ". $depth*20 ."px\"><div class=\"card\"><div class=\"card-body\">";
        echo "<p class=\"card-text\">". $commentRow["text"];
        echo "<p class\"card-text\"><small class=\"text-muted\">Posted by <a href=\"account.php?id=". $userRow["userID"] ."\">". $userRow["username"]. "</a>, on ". $commentRow["whenPosted"]. "</small></p>";

        if (isset($_SESSION["user"]) && isset($_SESSION["userID"]) && !$isDisabled) {
            echo "<button class=\"btn btn-outline-primary\" data-bs-toggle=\"collapse\" data-bs-target=\"#replyForm". $commentID ."\" aria-expanded=\"false\" aria-controls=\"replyForm". $commentID ."\">Reply</button>";
            
            if ($_SESSION["userID"] == $commentRow["commenterID"]) {
                echo '<button class="btn btn-outline-primary ms-2" data-bs-toggle="collapse" data-bs-target="#editCommentForm'. $commentID .'" aria-expanded="false" aria-controls"editCommentForm'. $commentID . '">Edit</button>';
                
                echo '<form action="deleteComment.php" method="post" class="deleteButton">';
                echo '<input type="text" name="normalUser" value="true" hidden>';
                echo '<input type="text" name="commentID" value="'. $commentRow["commentID"] .'" hidden>'; 
                echo '<input type="text" name="postID" value="'. $_GET["id"] .'" hidden>';
                echo '<input type="submit" class="btn btn-danger" name="submitDelete" value="Delete">';
                echo '</form>';
                
                echo '<div id="editCommentForm'. $commentID .'" class="collapse">';
                echo '<form action="editComment.php" method="post">';
                echo '<div><textarea class="form-control" name="text" rows="4" required>'. $commentRow["text"] .'</textarea></div>';
                echo "<input type=\"text\" name=\"postID\" value=\"". $_GET["id"]. "\" hidden>";
                echo '<input type="text" name="commentID" value="'. $commentRow["commentID"]. '" hidden>';
                echo '<input type="submit" class="btn btn-primary" name="submitEditComment" value="Submit Edit">';
                echo '</form></div>';
            } else {
                if ($userType == 1) {
                    echo '<form action="deleteComment.php" method="post" class="deleteButton">';
                    echo '<input type="text" name="communityOwner" value="true" hidden>';
                    echo '<input type="text" name="commentID" value="'. $commentRow["commentID"] .'" hidden>'; 
                    echo '<input type="text" name="postID" value="'. $_GET["id"] .'" hidden>';
                    echo '<input type="submit" class="btn btn-danger" name="submitDelete" value="Delete">';
                    echo '</form>';
                }
                if ($userType == 2) {
                    echo '<form action="deleteComment.php" method="post" class="deleteButton">';
                    echo '<input type="text" name="admin" value="true" hidden>';
                    echo '<input type="text" name="commentID" value="'. $commentRow["commentID"] .'" hidden>'; 
                    echo '<input type="text" name="postID" value="'. $_GET["id"] .'" hidden>';
                    echo '<input type="submit" class="btn btn-danger" name="submitDelete" value="Delete">';
                    echo '</form>';
                }
            }
            echo "<div id=\"replyForm". $commentID ."\" class=\"collapse\">";
            echo "<form action=\"submitComment.php\" method=\"post\">";
            echo "<div><textarea class=\"form-control\" name=\"body\" id=\"body\" rows=\"4\" required></textarea></div>";
            echo "<input type=\"text\" name=\"replyTo\" value=\"". $commentID ."\" hidden>";
            echo "<input type=\"text\" name=\"postID\" value=\"". $_GET["id"]. "\" hidden>";
            echo "<input type=\"submit\" class=\"btn btn-primary\" name=\"postReply\" value=\"Submit\">";
            echo "</form></div>";
        }
        
        echo "</div></div></div>";
        
        if ($result->num_rows > 0) {
            $depth++;
            while ($row = $result->fetch_assoc()) {
                checkReplies($row["commentID"], $conn, $depth, $userType, $isDisabled);
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
    <link rel="stylesheet" href="styles/postsubmit_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title><?php echo $title ?></title>
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
                <div class="post">
                    <div class="card">
                        <div class="card-header"><h1 class="card-title"><?php echo $title ?></h1></div>
                        <div class="card-body">
                            <p class="card-text"><?php echo $body ?></p>
                        </div>
                        <div class="card-footer">
                            <?php echo "Posted by <a href=\"account.php?id=". $posterID ."\">". $poster. "</a>, on ". $whenPosted; ?>
                            <?php
                                if(isset($_SESSION["user"]) && isset($_SESSION["userID"]) && !$isDisabled) {
                                    echo "<button class=\"btn btn-outline-primary ms-2\" data-bs-toggle=\"collapse\" data-bs-target=\"#commentForm\" aria-expanded=\"false\" aria-controls=\"commentForm\">Leave a comment</button>"; 
                                    if ($posterID == $_SESSION["userID"]) {
                                        echo "<button class=\"btn btn-outline-primary ms-2\" data-bs-toggle=\"modal\" data-bs-target=\"#editPost\">Edit</button>"; 
                                        echo '<form action="deletePost.php" method="post" class="deleteButton">';
                                        echo '<input type="text" name="normalUser" value="true" hidden>';
                                        echo '<input type="text" name="postID" value="'. $_GET["id"] .'" hidden>';
                                        echo '<input type="text" name="communityID" value="'. $communityName .'" hidden>';
                                        echo '<input type="submit" class="btn btn-danger" name="submitDelete" value="Delete">';
                                        echo '</form>';
                                    }
                                    if ($isAdmin || $isCommunityOwner) {
                                        echo '<form action="deletePost.php" method="post" class="deleteButton">';
                                        echo '<input type="text" name="postID" value="'. $_GET["id"] .'" hidden>';
                                        echo '<input type="text" name="communityID" value="'. $communityName .'" hidden>';
                                        echo '<input type="submit" class="btn btn-danger" name="submitDelete" value="Delete (as admin)">';
                                        echo '</form>';
                                    }
                                }
                            ?>
                        </div>
                        <div id="commentForm" class="collapse">
                            <form action="submitComment.php" method="post">
                                <div>
                                    <textarea name="body" id="body" rows="4" class="form-control" required></textarea>
                                    <input type="text" name="replyTo" value="-1" hidden>
                                    <input type="text" name="postID" value="<?php echo $_GET["id"] ?>" hidden>
                                </div>
                                <input type="submit" class="btn btn-primary" name="postComment" value="Submit">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="comments pt-3">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Comments</h3></div>
                        <div class="card-body">
                            <?php 
                                $commentQuery = "SELECT * FROM Comment WHERE postID = ". $_GET["id"]. " and replyTo = -1";
                                $commentResult = mysqli_query($conn, $commentQuery);

                                if ($commentResult->num_rows > 0) {
                                    while ($row = $commentResult->fetch_assoc()) {
                                        checkReplies($row["commentID"], $conn, 0, $userType, $isDisabled);
                                    }
                                } else {    
                                    echo "<p class=\"card-text\">There are no comments yet.</p>";
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
                        <?php
                            if (isset($_SESSION["user"]) && isset($_SESSION["userID"]) && !$isDisabled) {
                                echo "<div class=\"text-center pt-3\"><button class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#submit\">Submit a Post</button></div>";
                            }
                            echo "<h3 class=\"card-text pt-3\">Description</h3>";
                            echo "<p class=\"card-text\">". $desc ."</p>";
                            echo "<h3 class=\"card-text\">Rules</h3>";
                            echo "<p class=\"card-text\">". $rules ."</p>";
                        ?>
                        <div class="text-center">
                            <a href="sub.php?id=<?php echo $communityName; ?>" class="btn btn-primary">Back to <?php echo $communityName; ?></a>
                        </div>
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
    <div class="modal fade" id="editPost">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal"></button>
                    <h3>Edit Post</h3>
                    <form action="editPost.php" method="post">
                        <div>
                            <label for="body">Body</label>
                            <textarea class="form-control" name="body" rows="7"><?php echo $body ?></textarea>
                            <input type="text" name="postID" value="<?php echo $_GET["id"] ?>" hidden>
                        </div>
                        <div>
                            <button class="btn btn-primary mt-3">Submit</button>
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </div>
</body>
</html>