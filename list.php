<style>

        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f9;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .add-post {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 1rem;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .add-post:hover {
            background-color: #45a049;
        }
        .image-container {
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        img {
            max-width: 300px;
            height: auto;
            display: block;
        }

    </style>
<header>
        <h1>게시판 리스트</h1>
</header>
<div class="container">
    <a href="?menu=write" class="add-post">새 글 작성</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>제목</th>
                <th>날짜</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 페이지네이션 설정
            $itemsPerPage = 5;
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $offset = ($page - 1) * $itemsPerPage;

            // 전체 게시글 수 구하기
            $sqlTotal = "SELECT COUNT(*) AS total FROM posts";
            $resultTotal = $conn->query($sqlTotal);
            $rowTotal = $resultTotal->fetch_assoc();
            $totalItems = $rowTotal['total'];
            $totalPages = ceil($totalItems / $itemsPerPage);

            // 게시판 데이터 가져오기 (페이지네이션 적용)
            $sql = "SELECT * FROM posts ORDER BY reg_date DESC LIMIT $itemsPerPage OFFSET $offset";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // 데이터 출력
                while ($row = $result->fetch_assoc()) {
                    $postId = $row["id"];
                    echo "<tr class='post-group-{$postId}'>";
                    echo "<td>" . $postId . "</td>";
                    echo "<td>" . $row["title"] . "</td>";
                    echo "<td>" . $row["reg_date"] . "</td>";
                    echo "<td><button class='delete-btn' data-id='{$postId}'>삭제</button></td>";
                    echo "</tr>";

                    echo "<tr class='post-group-{$postId}'>";
                    echo "<td colspan='4'>" . nl2br($row["content"]) . "</td>";
                    echo "</tr>";
                    
                    if(trim($row["file_path"]) != ""){
                        $filePaths = json_decode($row["file_path"], true);
                        if (!is_array($filePaths)) {
                            // 구버전 데이터 호환: |로 구분된 문자열 처리
                            $filePaths = explode("|", $row["file_path"]);
                        }
                        $originFilePaths = json_decode($row["origin_file_path"], true);
                        if (!is_array($originFilePaths)) {
                            $originFilePaths = explode("|", $row["origin_file_path"]);
                        }

                        echo "<tr class='post-group-{$postId}'>";
                        echo "<td colspan='4'>";
                        foreach ($filePaths as $file_path): 
                            if (trim($file_path) != ''):  ?>
                                <div class="image-container">
                                    <img src="uploads/<?php echo htmlspecialchars($file_path); ?>" alt="이미지">
                                </div>
                            <?php endif;
                        endforeach;
                        echo "</td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>게시물이 없습니다.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <!-- 페이지네비게이션 UI -->
    <?php if ($totalPages > 1): ?>
        <div style="text-align: center; margin: 1rem 0;">
            <?php
            $pageBlock = 5; // 한 번에 보여줄 페이지 번호 개수
            $currentBlock = ceil($page / $pageBlock);
            $startPage = ($currentBlock - 1) * $pageBlock + 1;
            $endPage = min($totalPages, $currentBlock * $pageBlock);

            // 처음으로
            if ($page > 1) {
                echo "[<a href='?menu=list&page=1' style='margin:0 5px;'>처음</a>] ";
            }
            // 이전 5페이지
            if ($startPage > 1) {
                $prevBlock = $startPage - 1;
                echo "[<a href='?menu=list&page=$prevBlock' style='margin:0 5px;'>이전</a>] ";
            }
            // 페이지 번호
            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($i == $page) {
                    echo "<strong>[$i]</strong> ";
                } else {
                    echo "<a href='?menu=list&page=$i' style='margin:0 5px;'>[$i]</a> ";
                }
            }
            // 다음 5페이지
            if ($endPage < $totalPages) {
                $nextBlock = $endPage + 1;
                echo "[<a href='?menu=list&page=$nextBlock' style='margin:0 5px;'>다음</a>] ";
            }
            // 마지막으로  v
            if ($page < $totalPages) {
                echo "[<a href='?menu=list&page=$totalPages' style='margin:0 5px;'>마지막</a>] ";
            }
            ?>
        </div>
    <?php endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('table');
    if (table) {
        table.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-btn')) {
                const button = event.target;
                const postId = button.dataset.id;

                if (confirm('정말 삭제하시겠습니까?')) {
                    const formData = new FormData();
                    formData.append('id', postId);

                    fetch('webview_delete.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('게시물이 삭제되었습니다.');
                            const rowsToDelete = document.querySelectorAll('.post-group-' + postId);
                            rowsToDelete.forEach(row => row.remove());
                        } else {
                            alert('삭제 실패: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('삭제 중 오류가 발생했습니다.');
                    });
                }
            }
        });
    }
});
</script>
