<?php

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
 
    $sql = "INSERT INTO posts (title, content, file_path, origin_file_path, user_id) VALUES ('$title', '$content', '$realFileNames','$originalFileNames','".$_SESSION['user_id']."')";
 
    if ($conn->query($sql) === TRUE) {
        echo "새 글과 파일이 성공적으로 등록되었습니다.";
    } else {
        echo "오류: " . $sql . "<br>" . $conn->error;
    }
}


?>