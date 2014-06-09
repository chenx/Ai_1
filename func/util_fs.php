<?php
//
// File system utility.
//

//
// Get list of sub-files (non-directory) of dir.
// http://php.net/manual/en/function.readdir.php
//
function getSubFilesAsArray($dir) {
    $a = array();
    if ($handle = opendir("$dir")) {
        // This is the correct way to loop over the directory. 
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && ! is_dir("$dir/$entry")) {
                //echo "$entry<br/>";
                array_push($a, $entry);
            }
        }

        // This is the WRONG way to loop over the directory. 
        //while ($entry = readdir($handle)) {
        //    echo "$entry\n";
        //}

        closedir($handle);
    }
    else {
        //return "<p><font color='red'>Error: Can not open directory: $dir</font></p>";
    }
    return $a;
}

//
// Get list of sub-directories of dir.
//
function getSubDirsAsArray($dir) {
    $a = array();
    if ($dir == "") return $a;

    if ($handle = opendir("$dir")) {
        // This is the correct way to loop over the directory.
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && is_dir("$dir/$entry")) {
                //echo "$entry<br/>";
                array_push($a, $entry);
            }
        }

        closedir($handle);
    }
    else {
        //return "<p><font color='red'>Error: Can not open directory: $dir</font></p>";
    }
    return $a;
}

function dir_is_empty($dir) {
    if ($dir == "") return 0;

    $handle = opendir($dir);
    $c = 0;
    while (($file = readdir($handle)) && $c < 3) {
        //echo $file . "<br/>";
        $c++;
    }
    closedir($handle);

    return $c <= 2;
}

?>

