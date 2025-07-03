<?PHP 
session_start();
include("header.php");
//include("myconfig.php");
include("../config/db_connect.php");

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'home';
}

switch (trim($menu)) {
    case 'home':
        include("home.php");
        break;
    case 'about':
        include("about.php");
        break;
    case 'list':
        include("list.php");
        break;           
    case 'write':
        include("write.php");
        break; 
    case 'save':
        include("save.php");
        break;             
    default:
        include("home.php");
        break;
}

include("footer.php"); 
?>