<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>

<body>

    <?php
        $dsn='データベース名';
        $user='ユーザー名';
        $password='パスワード';
        $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "CREATE TABLE IF NOT EXISTS mission5"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "pass char(32)"
        .");";
        $stmt = $pdo->query($sql);

        if(empty($_POST["delete"])){
            if(!empty($_POST["name"])&&!empty($_POST["comment"])){
                if(empty($_POST["hidnum"])&&!empty($_POST["pass1"])){//新規投稿はパスワード入力必須
                    $sql='INSERT INTO mission5 (name,comment,date,pass) VALUES(:name, :comment, :date, :pass)';
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(':name',$name,PDO::PARAM_STR);
                    $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
                    $stmt->bindParam(':pass',$pass1,PDO::PARAM_STR);
                    $stmt->bindParam(':date',$date,PDO::PARAM_STR);
                    $name=$_POST["name"];
                    $comment=$_POST["comment"];
                    $date=date("Y-m-d H:i:s");
                    $pass1=$_POST["pass1"];
                    $stmt->execute();
                }elseif(!empty($_POST["hidnum"])){//編集書き込みではパスワードの再度入力は不用、パスワードの更新不可
                    $id=$_POST["hidnum"];
                    $name=$_POST["name"];
                    $comment=$_POST["comment"];
                    $sql='UPDATE mission5 SET name=:name,comment=:comment WHERE id=:id';
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(':name',$name,PDO::PARAM_STR);
                    $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
                    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                    $stmt->execute();
                }
                
            }elseif(!empty($_POST["edit"])&&!empty($_POST["pass3"])) {
                $editnum=$_POST["edit"];
                $check3=$_POST["pass3"];
                $id=$editnum;
                $sql='SELECT*FROM mission5 WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                $stmt->execute();
                $results=$stmt->fetchAll();
                    foreach($results as $row){
                        if($check3==$row['pass']){
                            $getname=$row['name'];
                            $getcomment=$row['comment'];
                        }
                    }
            }   
        }elseif(!empty($_POST["delete"])&&!empty($_POST["pass2"])){
            $postnum=$_POST["delete"];
            $check2=$_POST["pass2"];
            $id=$postnum;
            $sql='SELECT*FROM mission5 WHERE id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
            $results=$stmt->fetchAll();
                foreach($results as $row){
                    $pass1=$row['pass'];
                }
            if($check2==$pass1){
                $sql='DELETE from mission5 WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    ?>

    <form method="post" action="">
        <h1>簡易掲示板（データベース版）</h1>
        <ul>
            <li>はじめに名前を記入してください。</li>
            <li>自由にコメントを記入してください。</li>
            <li>投稿する際にパスワードを設定してください。パスワードは編集で変更することができません。</li>
            <li>投稿の削除／編集にはパスワードが必要です。
        </ul>    
        名前　　　：<input type="text" name="name" value="<?php if(isset($getname)){echo $getname;} ?>"><br>
        コメント　：<input type="text" name="comment" value="<?php if(isset($getcomment)){echo $getcomment;} ?>"><br>
        パスワード：<input type="password" name="pass1">
        <input type="hidden" name="hidnum" value="<?php if(isset($editnum)){echo $editnum;} ?>">
        <input type="submit" name="submit1" value="投稿"><br>
        <br>
        削除番号　：<input type="text" name="delete"><br>
        パスワード：<input type="password" name="pass2">
        <input type="submit" name="submit2" value="削除"><br>
        <br>
        編集番号　：<input type="text" name="edit"><br>
        パスワード：<input type="password" name="pass3">
        <input type="submit" name="submit3" value="編集"><br>
        <br>
        【投稿一覧】
    </form>
    
    <?php
        
        $sql='SELECT * FROM mission5';
        $stmt=$pdo->query($sql);
        $results=$stmt->fetchAll();
            foreach ($results as $row){
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            }
    
    ?>

</body>
</html>
