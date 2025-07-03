<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>일상에 대하여.</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .toggle {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: white;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar a {
                display: none;
                width: 100%;
                text-align: left;
            }

            .dropdown-content {
                position: static;
                box-shadow: none;
                width: 100%;
            }

            .navbar.active a {
                display: block;
            }

            .toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="toggle" onclick="toggleMenu()">☰</span>
        <a href="/my_letter/?menu=home">Home</a>

        <div class="dropdown">
            <a href="#">관리자메뉴</a>
            <div class="dropdown-content">
                <a href="/my_letter/?menu=list">글목록</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="#">Services</a>
            <div class="dropdown-content">
                <a href="#">Web Design</a>
                <a href="#">SEO</a>
                <a href="#">Marketing</a>
            </div>
        </div>

        <a href="#">Contact</a>

        <?php echo(isset($_SESSION['user_id']) ? "<a href='?menu=logout'>Logout</a>":"<a href='?menu=login'>Login</a>");?>
    </div>