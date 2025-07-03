<?php
include("../config/db_connect.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);

    if($_FILES['files']['name'][0] == "") {
        $realFileNames = "";
        $originalFileNames = "";    
    }else{
        // 사용 예제
        include ('MultiFileUpload.php');
        $uploadDir = 'uploads';
        $maxFileSize = 5 * 1024 * 1024; // 2MB
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];

        $multiFileUpload = new MultiFileUpload($uploadDir, $maxFileSize, $allowedFileTypes);
    
        if ($multiFileUpload->uploadFiles($_FILES['files'])) {
            $realFileNames = "";
            foreach ($multiFileUpload->getRealFileName() as $fileName) {
                $realFileNames .= $fileName."|";
            }

            $originalFileNames = "";
            foreach ($multiFileUpload->getOriginalFileName() as $fileName) {
                $originalFileNames .= $fileName."|";
            }
        } else {
            foreach ($multiFileUpload->getErrors() as $error) {
                echo $error . "<br>";
            }
        }
    }
 
    $sql = "INSERT INTO posts (title, content, file_path, origin_file_path, user_id) VALUES ('$title', '$content', '$realFileNames','$originalFileNames','android')";
 
    if ($conn->query($sql) === TRUE) {
        echo "<a href='upload_webview_write.php' style='height:25px;background-color: #6c757d;color: white;	margin-top: 10px;border-radius: 4px; cursor: pointer;'>뒤로가기</a>";
    } else {
        echo "오류: " . $sql . "<br>" . $conn->error;
    }
}
?>