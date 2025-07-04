<?php
/**
 * MultiFileUpload - 다중 파일 업로드 클래스
 *
 * @author
 */
class MultiFileUpload {
    private $uploadDir;
    private $maxFileSize;
    private $allowedFileTypes;
    private $errors = [];
    private $realFileName = [];
    private $originalFileName = [];

    /**
     * @param string $uploadDir 업로드 디렉토리
     * @param int $maxFileSize 최대 파일 크기 (byte)
     * @param array $allowedFileTypes 허용 확장자 목록
     */
    public function __construct($uploadDir, $maxFileSize, $allowedFileTypes) {
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = $maxFileSize;
        $this->allowedFileTypes = array_map('strtolower', $allowedFileTypes);
        // 업로드 폴더가 없으면 생성
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * 파일 업로드 실행
     * @param array $files $_FILES['files'] 배열
     * @return bool 전체 성공 여부
     */
    public function uploadFiles($files) {
        foreach ($files['name'] as $key => $name) {
            $fileTmpName = $files['tmp_name'][$key];
            $fileSize = $files['size'][$key];
            $fileError = $files['error'][$key];
            $fileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($fileError === UPLOAD_ERR_OK) {
                if (in_array($fileType, $this->allowedFileTypes) && $fileSize <= $this->maxFileSize) {
                    // 파일명에서 위험 문자 제거
                    $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', pathinfo($name, PATHINFO_FILENAME));
                    $fileNameNew = uniqid($safeName . '_', true) . '.' . $fileType;
                    $fileDestination = $this->uploadDir . '/' . $fileNameNew;

                    // 1MB 초과 이미지 리사이즈(압축)
                    if (in_array($fileType, ['jpg', 'jpeg', 'png']) && $fileSize > 1048576) {
                        $success = $this->resizeAndSaveImage($fileTmpName, $fileDestination, $fileType);
                        if ($success) {
                            $this->realFileName[] = $fileNameNew;
                            $this->originalFileName[] = $name;
                        } else {
                            $this->errors[] = "[{$name}] 이미지 리사이즈/압축 실패";
                        }
                    } else {
                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $this->realFileName[] = $fileNameNew;
                            $this->originalFileName[] = $name;
                        } else {
                            $this->errors[] = "[{$name}] 파일 이동 실패";
                        }
                    }
                } else {
                    $this->errors[] = "[{$name}] 허용 확장자: " . implode(', ', $this->allowedFileTypes) . ", 최대 크기: " . ($this->maxFileSize / 1024 / 1024) . "MB";
                }
            } else {
                $this->errors[] = "[{$name}] 업로드 에러 코드: $fileError";
            }
        }
        // 전체 성공 여부 반환
        return empty($this->errors);
    }

    /**
     * 이미지 파일을 1MB 이하로 압축/저장
     */
    private function resizeAndSaveImage($src, $dest, $ext) {
        $maxSize = 1048576; // 1MB
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $image = imagecreatefromjpeg($src);
            $quality = 85;
            ob_start();
            imagejpeg($image, null, $quality);
            $data = ob_get_clean();
            while (strlen($data) > $maxSize && $quality > 10) {
                $quality -= 5;
                ob_start();
                imagejpeg($image, null, $quality);
                $data = ob_get_clean();
            }
            $result = file_put_contents($dest, $data);
            imagedestroy($image);
            return $result !== false && filesize($dest) <= $maxSize;
        } elseif ($ext === 'png') {
            $image = imagecreatefrompng($src);
            $quality = 9; // 0(최고화질)-9(최고압축)
            ob_start();
            imagepng($image, null, $quality);
            $data = ob_get_clean();
            while (strlen($data) > $maxSize && $quality < 9) {
                $quality++;
                ob_start();
                imagepng($image, null, $quality);
                $data = ob_get_clean();
            }
            $result = file_put_contents($dest, $data);
            imagedestroy($image);
            return $result !== false && filesize($dest) <= $maxSize;
        }
        return false;
    }

    /**
     * 업로드 에러 목록 반환
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    /**
     * 실제 저장된 파일명 배열 반환
     * @return array
     */
    public function getRealFileName() {
        return $this->realFileName;
    }
    /**
     * 원본 파일명 배열 반환
     * @return array
     */
    public function getOriginalFileName() {
        return $this->originalFileName;
    }
}
?>