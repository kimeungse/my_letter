
<style>
           h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: white;
        }
        button[type="button"] {
            background-color: #6c757d;
            color: white;
            margin-top: 10px;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>

<div class="container">
    <h2>글 작성하기</h2>
    <form action="/my_letter/upload_webview_save.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">제목</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="form-group">
            <label for="content">내용</label>
            <textarea name="content" id="content" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="files">파일 선택</label>
            <input type="file" name="files[]" id="files" multiple>
        </div>
        <div class="form-group">
            <button type="submit">등록하기</button>
            <button type="button" onclick="location.href='?menu=list'">목록으로</button>
        </div>
    </form>
</div>

