<?php
include("../config/db_connect.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SQL 인젝션 방지: prepare 사용
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    $realFileNames = [];
    $originalFileNames = [];
    $hasFile = isset($_FILES['files']['name'][0]) && $_FILES['files']['name'][0] !== "";

    if ($hasFile) {
        include('MultiFileUpload.php');
        $uploadDir = 'uploads';
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];

        $multiFileUpload = new MultiFileUpload($uploadDir, $maxFileSize, $allowedFileTypes);

        if ($multiFileUpload->uploadFiles($_FILES['files'])) {
            $realFileNames = $multiFileUpload->getRealFileName() ?? [];
            $originalFileNames = $multiFileUpload->getOriginalFileName() ?? [];
        } else {
            foreach ($multiFileUpload->getErrors() as $error) {
                echo $error . "<br>";
            }
            exit; // 파일 업로드 실패 시 DB 입력 중단
        }
    }

    // 파일명은 JSON으로 저장
    $realFileNamesJson = json_encode($realFileNames, JSON_UNESCAPED_UNICODE);
    $originalFileNamesJson = json_encode($originalFileNames, JSON_UNESCAPED_UNICODE);

    // prepare 사용 (XSS 방지는 출력 시 적용)
    $stmt = $conn->prepare("INSERT INTO posts (title, content, file_path, origin_file_path, user_id) VALUES (?, ?, ?, ?, ?)");
    $user_id = 'android';
    $stmt->bind_param('sssss', $title, $content, $realFileNamesJson, $originalFileNamesJson, $user_id);

    if ($stmt->execute()) {
        echo "<a href='upload_webview_write.php' style='height:25px;background-color: #6c757d;color: white;\tmargin-top: 10px;border-radius: 4px; cursor: pointer;'>뒤로가기</a>";
    } else {
        echo "오류: " . $stmt->error;
    }
    $stmt->close();
}
?>