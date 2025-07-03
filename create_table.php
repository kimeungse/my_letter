<?php
    include("../config/db_connect.php");
    $sql = "
    CREATE TABLE IF NOT EXISTS posts (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(50) NOT NULL,
        content VARCHAR(50) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        origin_file_path VARCHAR(255) NOT NULL,
        user_id VARCHAR(50) NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    if ($conn->query($sql) === TRUE) {
        echo "Table users created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
?>