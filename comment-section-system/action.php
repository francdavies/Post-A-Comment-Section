<?php
  require 'config.php';

  $msg="";
  $u_id = "";
  $u_name = "";
  $u_comment = "";
  $update = false;

  if(isset($_POST['submit'])){
    $name= $conn->real_escape_string($_POST['name']);
    $comment= $conn->real_escape_string($_POST['comment']);
    $date=date("Y-m-d");

    $sql="INSERT INTO comment_table(name, comment, cur_date)VALUES('$name', '$comment', '$date')";

    if($conn->query($sql)){
        $msg = "Comment Posted!";
    }
    else{
        $msg ="Comment not posted!" . $conn->error;
    }
  }

  if(isset($_GET['del'])){
    $id = (int)$_GET['del'];
    $sql = "DELETE FROM comment_table WHERE id = '$id'";

    if($conn->query($sql)){
        header('location:index.php');
    }
  }

  if(isset($_GET['edit'])){
    $id = (int)$_GET['edit'];

    $sql = "SELECT * FROM comment_table WHERE id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $u_id = $row['id'];
    $u_name = $row['name'];
    $u_comment = $row['comment'];

    $update = true;
  }

  if(isset($_POST['update'])){
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $comment = $conn->real_escape_string($_POST['comment']);

    $sql = "UPDATE comment_table SET name='$name', comment='$comment' WHERE id = '$id'";

    if($conn->query($sql)){
        $msg = "Comment Edited!";
    }
  }


  // Reply comment 
if(isset($_POST['submit_reply'])){
    $parent_id = (int)$_POST['parent_id'];
    $name = $conn->real_escape_string($_POST['reply_name']);
    $comment = $conn->real_escape_string($_POST['reply_comment']);
    $date = date("Y-m-d H:i:s");
    
    $sql = "INSERT INTO comment_table(parent_id, name, comment, cur_date) 
            VALUES($parent_id, '$name', '$comment', '$date')";
    
    if($conn->query($sql)){
        header("Location: index.php?reply_to=$parent_id&msg=Reply+posted");
    } else {
        header("Location: index.php?error=Reply+failed");
    }
    exit();
}

// Share link
if(isset($_GET['generate_share'])){
    $id = (int)$_GET['generate_share'];
    $token = md5(uniqid($id, true));
    $conn->query("UPDATE comment_table SET share_token='$token' WHERE id=$id");
    header("Location: index.php?comment=$id");
    exit();
}
?>

