<?php
require_once("func/util.php");
?>

<?php 
print writeMenu();
//print writeMenu2();

function writeMenu() {
    global $page_title;
    $s = "";

    $current = ( ($page_title == "Home - Welcome") ? " class='current'" : "" );
    $s .= "<li$current><a href='index.php'>Home</a>";

    $current = ( ($page_title == "About Us") ? " class='current'" : "" );
    $s .= "<li$current><a href='about.php'>About Us</a>";

    if (isset($_SESSION['role']) && $_SESSION['role'] == "admin") {
        if ($page_title == "Site Admin") {
            $s .= "<li class='current'><a href='#'>Site Admin</a>";
        } else {
            $current = "";
            if ( startsWith($page_title, "Site Admin") ) $current = "class='current'";
            $s .= "<li $current><a href='adminhome.php'>Site Admin</a>";
        }
        $s .= "<ul>";
        $s .= "<li><a href='admin_users.php'>Manage Users</a></li>";
        //$s .= "<li><a href='admin_images.php'>Manage Images</a></li>";
        $s .= "<li><a href='#' onclick='javascript: open_file(\"admin_images.php\");'>Manage Images</a></li>";
        $s .= "<li><a href='admin_create_schema.php'>Create Schema For Tables</a></li>";
        $s .= "<li><a href='admin_dump_table.php'>Dump Contents Of Tables</a></li>";
        $s .= "<li><a href='admin_backup_db.php'>Backup Database</a></li>";
        $s .= "</ul>";
        $s .="</li>";
    }

    if (isset($_SESSION['username'])) {
        if ($page_title == "Member Home") {
            $s .= "<li class='current'><a href='#'>Member Home</a></li>";
        } else {
            $s .= "<li><a href='home.php'>Member Home</a></li>";
        }

        if ($page_title == "My Profile") {
            $s .= "<li class='current'><a href='#'>My Profile</a></li>";
        } else {
            $s .= "<li><a href='profile.php'>My Profile</a></li>";
        }

        $t = "<a href='logout.php'>Log out</a>";
        $s .= "<li>$t</li>";
    }

    $s = "<ul id='nav'>$s</ul>";
    return $s;
}

function writeMenu2() {
    global $page_title;
    $s = "&nbsp;";

    if (isset($_SESSION['role']) && $_SESSION['role'] == "admin") { 
        if ($page_title == "Site Admin") {
            $s .= "Site Admin | ";
        } else {
            $s .= "<a href='adminhome.php'>Site Admin</a> | ";
        }
    }

    if ($page_title == "Member Home") {
        $s .= "Member Home | ";
    } else {
        $s .= "<a href='home.php'>Member Home</a> | ";
    }

    if ($page_title == "My Profile") {
        $s .= "My Profile | ";
    } else {
        $s .= "<a href='profile.php'>My Profile</a> | ";
    }

    $s .= "<a href='logout.php'>Log out</a>";

    $s = "<div width='100%' style='background-color: #ccccff;'>$s</div>";

    return $s;
}
?>
  

