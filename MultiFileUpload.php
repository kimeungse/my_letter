<?php
class MultiFileUpload {
    private $uploadDir;
    private $maxFileSize;
    private $allowedFileTypes;
    private $errors = [];
    private $realFileName;
    private $originalFileName;

    public function __construct($uploadDir, $maxFileSize, $allowedFileTypes) {
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = $maxFileSize;
        $this->allowedFileTypes = $allowedFileTypes;
    }

    public function uploadFiles($files) {
  
        foreach ($files['name'] as $key => $name) {
            $fileTmpName = $files['tmp_name'][$key];
            $fileSize = $files['size'][$key];
            $fileError = $files['error'][$key];
            $fileType = pathinfo($name, PATHINFO_EXTENSION);
            if ($fileError === UPLOAD_ERR_OK) {
                if (in_array($fileType, $this->allowedFileTypes) && $fileSize <= $this->maxFileSize) {
                    $fileNameNew = uniqid('', true) . '.' . $fileType;
                    $fileDestination = $this->uploadDir . '/' . $fileNameNew;

                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        //echo "File uploaded successfully: $name\n";
                        $this->realFileName[] = $fileNameNew;
                        $this->originalFileName[] = $name;
                    } else {
                        $this->errors[] = "Failed to move uploaded file: $name";
                    }
                } else {
                    $this->errors[] = "File type not allowed or file too large: $name";
                }
            } else {
                $this->errors[] = "File error: $name (Error code: $fileError)";
            }
        }

        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
    public function getRealFileName() {
        return $this->realFileName;
    }
    public function getOriginalFileName() {
        return $this->originalFileName;
    }
}
?>