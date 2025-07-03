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
            </tr>
        </thead>
        <tbody>
            <?php
            // 게시판 데이터 가져오기
            $sql = "SELECT * FROM posts order by reg_date desc";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // 데이터 출력
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["title"] . "</td>";
                    echo "<td>" . $row["reg_date"] . "</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td colspan='3'>" . nl2br($row["content"]) . "</td>";
                    echo "</tr>";
                    if(trim($row["file_path"]) != ""){
                        $filePaths = explode("|", $row["file_path"]);
                        $originFilePaths = explode("|", $row["origin_file_path"]);
                        echo "<tr>";
                        echo "<td colspan='3'>";
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
                echo "<tr><td colspan='3' style='text-align:center;'>게시물이 없습니다.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>