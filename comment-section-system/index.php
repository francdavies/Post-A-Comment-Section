<?php
  require 'action.php';

// Display shared comment
if(isset($_GET['share'])){
    $token = $conn->real_escape_string($_GET['share']);
    $shared = $conn->query("SELECT * FROM comment_table WHERE share_token='$token'");
    if($shared->num_rows > 0){
        $shared_comment = $shared->fetch_assoc();
        echo '<div class="alert alert-success">Shared comment from '.htmlspecialchars($shared_comment['name']).'</div>';
    }
}


//Time stamp & Current date display
function time_ago($timestamp) {
    $time_diff = time() - strtotime($timestamp);
    if ($time_diff < 60) return "Just now";
    elseif ($time_diff < 3600) return floor($time_diff/60) . " mins ago";
    elseif ($time_diff < 86400) return floor($time_diff/3600) . " hours ago";
    else return floor($time_diff/86400) . " days ago";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Section</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!---Bootstrap Icons---->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-secondary">

    <div class="container">

        <div class="row justify-content-center mb-2">

            <div class="col-lg-5 bg-light rounded mt-2">

                <h4 class="text-center p-2">Post a comment</h4>

                <form action="index.php" method="POST" class="p-2">

                    <input type="hidden" name="id" value="<?= $u_id; ?>">

                    <div class="form-group">
                        <input type="text" name="name" class="form-control rounded-0" placeholder="Your name" required value="<?= $u_name; ?>">
                    </div>
                    
                    <div class="form-group pt-3">
                        <textarea name="comment" class="form-control rounded-0" placeholder="Write a comment" required><?= $u_comment; ?></textarea>
                    </div>

                    <div class="d-grid pt-3">

                        <?php if($update==true){ ?>
                            <input type="submit" name="update" class="btn btn-success" value="Edit Comment">
                        <?php } else{ ?>

                        <input type="submit" name="submit" class="btn btn-primary btn-block" value="Post">
                        <?php } ?>

                        <h5 class="float-right text-success p-3"><?= $msg; ?></h5>

                    </div>

                </form>


            </div>

        </div>

        <div class="row justify-content-center">
            <div class="col-lg-5 rounded bg-light p-3">

            <?php
              $sql = "SELECT * FROM comment_table ORDER BY id DESC";
              $result = $conn->query($sql);
              while($row = $result->fetch_assoc()){

            ?>

            <div class="card mb-2 border-secondary">

                <div class="card-header bg-secondary py-1 text-light">
                    <span class="font-italic"> <?= $row['name'] ?></span>
                </div>

                <div class="card-body py-2">
                    <p class="card-text"><?= $row['comment'] ?></p>
                    <span class="float-right font-italic"> <?= time_ago($row['cur_date']) ?></span>
                </div>

                <div class="card-footer py-2">
                    <div class="float-right">
                        <a href="index.php?edit=<?= $row['id'] ?>" class="text-success btn" title="edit"> Edit <i class="bi bi-pencil-square"></i></a>
                    
                        <a href="index.php?reply_to=<?= $row['id'] ?>" class="text-primary mr-2 btn"> Reply <i class="bi bi-reply"></i></a>

                        <a href="index.php?generate_share=<?= $row['id'] ?>" class="text-info mr-2 btn"> Share <i class="bi bi-share"></i></a>

                        <a href="action.php?del=<?= $row['id'] ?>" class="text-danger mr-2 btn" onclick="return confirm('This comment will be deleted.');" title="Delete">Delete <i class="bi bi-trash3"></i></a>
                    </div>
                </div>
            </div>
            <?php } ?>
            </div>

            <div class="container">
                <div class="row justify-content-center mb-2">
                    <div class="col-lg-5 bg-light rounded mt-2">

                <!-- SHARE LINK DISPLAY -->
                <?php if(isset($_GET['comment'])): 
                $comment_id = (int)$_GET['comment'];
                $result = $conn->query("SELECT share_token FROM comment_table WHERE id=$comment_id");
                if($result->num_rows > 0):
                    $token = $result->fetch_assoc()['share_token'];
                ?>
                <div class="alert alert-info mt-3">
                    Share link: <input type="text" class="form-control" value="<?= "https://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?share=$token" ?>" readonly>
                </div>
                <?php endif; endif; ?>

            <!-- REPLY FORM -->
            <?php if(isset($_GET['reply_to'])): 
            $parent_id = (int)$_GET['reply_to'];
            ?>
            <div class=" border-top bg-light p-2">
                <h5>Replying to comment #<?= $parent_id ?></h5>
                <form action="action.php" method="POST" class="p-2">
                    <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
                    <div class="form-group">
                        <input type="text" name="reply_name" class="form-control" placeholder="Your name" required>
                    </div>
                    <div class="form-group mt-2">
                        <textarea name="reply_comment" class="form-control" placeholder="Your reply..." required></textarea>
                    </div>
                    <button type="submit" name="submit_reply" class="btn btn-primary mt-2">Post Reply</button>
                    <a href="index.php" class="btn btn-secondary mt-2">Cancel</a>
                </form>
            </div>
            <?php endif; ?>

                   </div>
                </div>
           </div>
        </div>

    </div>
    
</body>
</html>