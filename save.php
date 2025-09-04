<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. 입력 값 검증 및 준비
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (empty($title) || empty($content) || empty($userId)) {
        // 필수 정보가 누락된 경우 에러 처리
        // 실제 운영 환경에서는 사용자에게 더 친절한 메시지를 보여주고 로깅하는 것이 좋습니다.
        die("오류: 제목, 내용, 사용자 정보는 필수입니다.");
    }

    $realFileNamesJson = '[]';
    $originalFileNamesJson = '[]';

    // 2. 파일 업로드 처리
    if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
        include('MultiFileUpload.php');
        $uploadDir = 'uploads';
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];

        $multiFileUpload = new MultiFileUpload($uploadDir, $maxFileSize, $allowedFileTypes);

        if ($multiFileUpload->uploadFiles($_FILES['files'])) {
            // 성공 시 파일 이름을 JSON으로 인코딩
            $realFileNamesJson = json_encode($multiFileUpload->getRealFileName());
            $originalFileNamesJson = json_encode($multiFileUpload->getOriginalFileName());
        } else {
            // 실패 시 에러 처리
            // 실제 운영 환경에서는 에러를 로깅하고 사용자에게 안내 메시지를 보여주는 것이 좋습니다.
            $errors = $multiFileUpload->getErrors();
            // 여기서는 간단히 첫 번째 에러만 출력합니다.
            die("파일 업로드 실패: " . htmlspecialchars($errors[0]));
        }
    }

    // 3. 데이터베이스에 저장 (Prepared Statements 사용)
    // SQL 인젝션 방지를 위해 Prepared Statements를 사용합니다.
    $sql = "INSERT INTO posts (title, content, file_path, origin_file_path, user_id) VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // 파라미터 바인딩: 'ssssi'는 각 파라미터의 타입을 나타냅니다. (string, string, string, string, integer)
    $stmt->bind_param("ssssi", $title, $content, $realFileNamesJson, $originalFileNamesJson, $userId);

    if ($stmt->execute()) {
        // 4. 성공 시 리디렉션
        // 성공적으로 데이터베이스에 삽입된 후, 목록 페이지로 사용자를 리디렉션합니다.
        header("Location: ?menu=list");
        exit(); // 리디렉션 후에는 스크립트 실행을 중단하는 것이 중요합니다.
    } else {
        // 데이터베이스 삽입 실패 시 에러 처리
        // 실제 운영 환경에서는 에러를 로깅하고 사용자에게 일반적인 실패 메시지를 보여주는 것이 좋습니다.
        echo "오류: 글을 등록하지 못했습니다. <br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // POST 요청이 아닐 경우
    echo "잘못된 접근입니다.";
}

?>