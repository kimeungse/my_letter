<?php
header("Content-Type: application/json");


$response = array();

$uploadDir = "uploads/"; // 업로드된 파일을 저장할 디렉터리
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // 디렉터리가 없으면 생성
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 입력 값 가져오기
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    // 입력 값 검증
    if (empty($title) || empty($content)) {
        $response["success"] = false;
        $response["message"] = "Title and content are required.";
        echo json_encode($response);
        exit;
    }

    // 파일 업로드 처리
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES["file"]["tmp_name"];
        $fileName = basename($_FILES["file"]["name"]);
        $fileSize = $_FILES["file"]["size"];
        $fileType = $_FILES["file"]["type"];

        // 허용할 파일 확장자 목록
        $allowedExtensions = array("jpg", "jpeg", "png", "pdf", "docx");
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $response["success"] = false;
            $response["message"] = "Invalid file type. Allowed types: jpg, png, pdf, docx.";
            echo json_encode($response);
            exit;
        }

        // 파일 크기 제한 (5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            $response["success"] = false;
            $response["message"] = "File size exceeds the 5MB limit.";
            echo json_encode($response);
            exit;
        }

        // 파일 저장
        $newFileName = uniqid("upload_", true) . "." . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $response["success"] = true;
            $response["message"] = "File uploaded successfully!";
            $response["file_url"] = $destPath; // 저장된 파일 경로 반환
        } else {
            $response["success"] = false;
            $response["message"] = "File upload failed.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response["success"] = false;
        $response["message"] = "No file uploaded or upload error.";
        echo json_encode($response);
        exit;
    }

    // 데이터베이스 저장 (옵션)
    /*
    $db = new mysqli("localhost", "username", "password", "database_name");
    if ($db->connect_error) {
        die("Database connection failed: " . $db->connect_error);
    }

    $stmt = $db->prepare("INSERT INTO uploads (title, content, file_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $destPath);
    if ($stmt->execute()) {
        $response["message"] .= " Data saved to database.";
    } else {
        $response["message"] .= " Failed to save data.";
    }
    $stmt->close();
    $db->close();
    */

    echo json_encode($response);
} else {
    $response["success"] = false;
    $response["message"] = "Invalid request method.";
    echo json_encode($response);
}
?>
