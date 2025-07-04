<?php
header('Content-Type: application/json');

// DB 연결
include("../config/db_connect.php");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB 연결 실패"]);
    exit;
}

// id 파라미터 확인
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(["success" => false, "error" => "잘못된 id"]);
    exit;
}

// 삭제 쿼리 실행
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "삭제 실패"]);
}
$stmt->close();
$conn->close();
?>