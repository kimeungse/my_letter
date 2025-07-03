<?php
include("../config/db_connect.php");

$itemsPerPage = 10;
// 현재 페이지 번호 (기본값: 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// offset 계산 (SQL LIMIT 절에 사용)
$offset = ($page - 1) * $itemsPerPage;
// 전체 아이템 수 쿼리
$sqlTotal = "SELECT COUNT(*) AS total FROM posts";
$resultTotal = $conn->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();
$totalItems = $rowTotal['total'];


// 페이지네이션된 데이터 쿼리
$sql = "SELECT id, title, content, file_path, reg_date FROM posts ORDER BY id DESC LIMIT $itemsPerPage OFFSET $offset"; // id는 예시, 실제 정렬 기준에 맞게 변경
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if(trim($row['file_path']) != "") {
			$str = rtrim($row['file_path'], '|');
			$images = explode("|",$str);
			
			$data[] = [
			'id' => $row['id'],
			'title' => $row['title'],
			'content' => $row['content'],
			'file_path' => $images,
			'reg_date' => $row['reg_date']
			];
		}else{
			$data[] = [
			'id' => $row['id'],
			'title' => $row['title'],
			'content' => $row['content'],
			'reg_date' => $row['reg_date']
			];
		}
	}
}

// 결과 배열에 추가 정보 포함 (옵션)
$response = array(
	"page" => $page,
	"itemsPerPage" => $itemsPerPage,
	"totalItems" => $totalItems,
	"data" => $data
);

// JSON 형식으로 응답
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();?>