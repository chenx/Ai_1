<?php
require_once("auth.php");
require_once("auth_admin.php");
require_once("conf/conf.php");
require_once("func/util_fs.php");
require_once("func/util.php");

$page_title = "Site Admin - Manage Image";
$max_file_size = 1024 * 1024 * 2;

$dir_gallery = "gallery";
$dir_default = "images"; //"";
$dir = (isset($_REQUEST['listImgDirs']) ? $_REQUEST['listImgDirs'] : $dir_default);
$reserved_images = array("bg.jpg", "loading.gif", "add.png", "close.png", "refresh.png", "edit.png",
"download.png", "warning.gif", "lens.gif", "check.gif"); // Images in "images" that cannot be deleted.

$win_h = (isset($_REQUEST['win_h']) ? $_REQUEST['win_h'] : 600) - 250;

$doPopup = (isset($_REQUEST['doPopup']) && $_REQUEST['doPopup'] != '') ? 1 : 0;
$doOverwrite = (isset($_REQUEST['doOverwrite']) && $_REQUEST['doOverwrite'] != '') ? 1 : 0;
$doLoopInt = U_REQUEST('listLoopInt', 5);

$msg = "";
if (isset($_REQUEST['btnUpload']) && $_REQUEST['btnUpload'] != "") {
    $msg = file_upload($dir, $doOverwrite);
}
else if (isset($_REQUEST['btnDelete']) && $_REQUEST['btnDelete'] != "") {
    $msg = file_delete($dir . "/" . $_REQUEST['btnDelete']);
}
else if (U_REQUEST('btnNewDir') != "" && U_REQUEST('newDir') != "") {
    $msg = dir_create( U_REQUEST('newDir') );
}
else if (U_REQUEST('btnRmDir') != "" && U_REQUEST('listImgDirs') != "") {
    $msg = dir_rm( U_REQUEST('listImgDirs') );
    $dir = "";
}

?>

<?php include("header.php"); ?>

<h3>Manage Image</h3>

<?php
if( "" != $msg ) {
    //$msg = strip_tags($msg);
    echo "<script type='text/javascript'>alert(\"$msg\");</script>";
}
?>

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

<!--
Max Upload file size is <?php echo ($max_file_size) / 1024 ?> KB. <br/>
-->

<input type='hidden' id="win_h" name="win_h" value="<?php echo (isset($_REQUEST['win_h']) ? $_REQUEST['win_h'] : $win_h)?>"/>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size?>"> <!--max size: in Byte-->

<span title='Current gallery'>Choose gallery: 
<?php echo getDirsAsSelect($dir_default);?>
</span>
&nbsp;&nbsp;&nbsp;

<?php
$newDir = ((isset($_REQUEST['newDir'])) ? trim($_REQUEST['newDir']) : "" );
?>
<span title='Create new gallery'>
<input type='submit' id='btnNewDir' name='btnNewDir' value="Create Gallery" />
<input type='text' id='newDir' name='newDir' value="<?php echo $newDir?>" size='10' />
</span>
&nbsp;&nbsp;&nbsp;


<?php if ($dir != "") { ?> <!-- start of "if (dir != '') {" -->

<span title='Upload image'>
Upload: 
<input type='hidden' id='btnUpload' name='btnUpload' value='' />
<input name="files[]" type="file" multiple size="20" onchange="javascript: uploadFile(this);">
<input type='checkbox' name='doOverwrite' value='Y' <?php echo  ($doOverwrite ? 'checked' : '') ?> style='vertical-align: middle;' />Overwrite</span>
</span>
&nbsp;&nbsp;&nbsp;

<input type='submit' name='btnLoop' value='Slide Show' title='Slide show all images' /> 
<?php echo  getLoopIntervalList($doLoopInt); ?>sec 
<span title='Show popup window'>
<input type='checkbox' name='doPopup' value='Y' <?php echo  ($doPopup ? 'checked' : '') ?> style='vertical-align: middle;' />Popup</span>
<span id='divLoading'></span>
&nbsp;&nbsp;&nbsp;

<input type='submit' name='btnRefresh' value='Refresh Page' title='Refresh page to clear any effect'/>
&nbsp;&nbsp;&nbsp;

<br/><br/>

<input type='hidden' id='btnDelete' name='btnDelete' value='' />
<input type='hidden' id='btnView' name='btnView' value='' />
<input type='hidden' id='btnViewW' name='btnViewW' value='' />
<input type='hidden' id='btnViewH' name='btnViewH' value='' />

<table width='100%' border='0' cellpadding='0'>
<tr>
<td valign='top' width='400'>
<div style="width:100%;height:<?php echo $win_h?>px;overflow:auto; padding: 5px; border-color: #cccccc; border-width: 1px; ">

<?php
if ($dir != "") {
    $dir_handle = getSubFilesAsArray($dir);
    if (! is_array($dir_handle)) { echo $dir_handle; }
    else { show_backup_list($dir_handle); }
}
?>

</div>
</td>
<td valign='top'>
<div style='margin-left: 10px; margin-top: 1px;'>

<div id='divImg'></div>

</div>
</td>
</tr>
</table>

<?php } ?> <!-- end of "if (dir != '') {" -->

</form>

<script type="text/javascript">
function uploadFile(v) {
    document.getElementById('btnUpload').value = 'Upload';
    document.forms[0].submit();
    return false;
}

function deleteFile(file) {
    var r = confirm("Are you sure to delete file " + file + "?");
    if (r) {
        document.getElementById('btnDelete').value = file;
        document.forms[0].submit();
    }
}

function viewFile(file, w, h) {
    var s = "<?php echo $dir?>/" + file;
    if (h > <?php echo $win_h?>) h = " height='<?php echo $win_h?>'";
    var v = "<a href='#' onclick='javascript: var w=window.open(\"" + s + "\", \"_img\", \"height=800,width=800,left=100,top=100,location=0,toolbar=0,menubar=0,status=0,scrollbars=1,resizable=1,titlebar=0\"); w.focus();'><img src='" + s + "' title='" + file + "' border='0' " + h + "></a>"
    document.getElementById('divImg').innerHTML = v;
}

//
// Update win_h if window is resized.
// Update is done only when window resize is done for 0.5 second.
// http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed
//
$(window).resize(function() {
    if(this.resizeTO) clearTimeout(this.resizeTO);
    this.resizeTO = setTimeout(function() {
        $(this).trigger('resizeEnd');
    }, 500);
});
$(window).bind('resizeEnd', function() {
    var win_h_new = $(window).height();
    if (win_h_new != <?php echo $win_h?>) {
        //document.getElementById('win_h').value = win_h_new;
        $('#win_h').val(win_h_new);
    }
});
</script>


<?php include("footer.php"); ?>

<?php

function getLoopIntervalList($default_interval) {
    $opts = array('1', '1', '2', '2', '5', '5', '10', '10', '20', '20', '30', '30', '60', '60');
    return writeSelect('listLoopInt', 'listLoopInt', $opts, 'Slide show interval', $default_interval, '0');
}

function getDirsAsSelect($default_dir) {
    global $dir_gallery;
    // pairs of folders: (actual value, display value).
    $dirs = array('', '-- Select Gallery --', 'images', 'Images');

    $dir_handle = getSubDirsAsArray($dir_gallery);
    if ( is_array($dir_handle) ) {
        for ($i = 0, $ct = count($dir_handle); $i < $ct; ++ $i) {
            if ($dir_handle[$i][0] != '.') { // do not display folder name starting with ".".
                $d = "$dir_gallery/$dir_handle[$i]";
                array_push($dirs, $d);
                array_push($dirs, $d);
            }
        }
    }

    $s = writeSelect('listImgDirs', 'listImgDirs', $dirs, '', $default_dir, '0');

    global $dir;
    //if ( dir_is_empty(U_REQUEST('listImgDirs')) ) {
    //echo $dir;
    if ( dir_is_empty($dir) ) {
        $s .= "<input type='submit' id='btnRmDir' name='btnRmDir' value='Remove' title='Remove empty gallery' />";
    }

    return $s;
}


function show_backup_list($dir_handle) {
    $useIcon = 0;
    global $reserved_images;
    global $dir;
    $ct = count($dir_handle);

    $loop = isset($_REQUEST['btnLoop']) ? 1 : 0;
    $t1 = "";
    $t2 = "";

    $s = "";
    $j = 0; // count of valid image files, ignore non-image files.
    for ($i = 0; $i < $ct; ++ $i) {
        $f = $dir_handle[$i];
        if (! is_image_ext($f)) { continue; }

        $size = getimagesize("$dir/$f");
        //print_r($size);
        $w = $size[0];
        $h = $size[1];

        $view = "View"; $download = "Download"; $delete = "Delete";
        if ($useIcon) {
            $view = "<img src='images/lens.gif' border='0' height='18' style='vertical-align: middle;'>";
            $download = "<img src='images/download.png' border='0' height='18' style='vertical-align: middle;'>";
            $delete = "<img src='images/close.png' border='0' height='18' style='vertical-align: middle;'>";
        }

        $s .= "<tr id='tr_$j'>";
        $s .= "<td>&nbsp;" . ($j + 1) . "&nbsp;</td>";
        $s .= "<td><a href='#' onclick='javascript: viewFile(\"$f\", $w, $h);'><img src='$dir/$f' height='20' width='30' title='File name: $f'></a></td><td>";
        $s .= "&nbsp;&nbsp;</td><td>";
        $s .= "<a href='#' onclick='javascript: viewFile(\"$f\", $w, $h);' title='View'>$view</a>&nbsp;";
        $s .= "<a href='#' onclick=\"javascript: window.location.href='download_img.php?f=$f';\" title='Download'>$download</a>&nbsp;";
        if ( in_array( $f, $reserved_images) ) {
            $s .= "<span title='Reserved image cannot be deleted.'><font color='#cccccc'>$delete</font></span>&nbsp;";
        } else {
            $s .= "<a href='#' onclick='javascript: deleteFile(\"$f\");' title='Delete'>$delete</a>&nbsp;";
        }
        $s .= "</td><td><input type='text' value='" . $f . "' READONLY size='20' title='File name: $f'/></td>";
        $s .= "</tr>";

        if ($loop) { 
            $t1 .= ($t1 == "") ? "'$f'" : ", '$f'"; 
            $t2 .= ($t2 == "") ? "$h" : ", $h";
        }
        ++ $j;
    }
    $s = "<table border='0' cellspacing='0' cellpadding='0'>$s</table>";

    echo $s;

    if ($loop) { autoLoop($t1, $t2, $j, 0); }
}

//
// Note: the for loop is done all at once, all the ct setTimeout functions are created.
// i = ct. But j is still 0, and is incremented after each setTimeout call.
//
function autoLoop($url, $h, $ct, $close_popup_at_end = 1) {
    global $doPopup;
    global $doLoopInt;
    global $dir;
    global $win_h;

    $s = <<<EOF
document.getElementById('divLoading').innerHTML = "<img src='images/loading.gif' border='0' height='20' style='vertical-align: middle;'>";
var j = 0;
var w;
for (var i = 0; i <= $ct; ++ i) {
    setTimeout(function() {
        if (j == $ct) {
            $("#divLoading").html("");
            $("#divImg").html("");
            if (j > 0) $("#tr_"+(j-1)).css("background-color", "");
            if ($close_popup_at_end) { w.close(); }
            return;
        }
        var a = new Array($url); 
        var b = new Array($h);
        var h = (b[j] < $win_h) ? b[j] : $win_h;
        var m = "<a href='#' onclick='javascript: var w=window.open(\"$dir/" + a[j] + "\", \"_img\", \"height=800,width=800,left=100,top=100,location=0,toolbar=0,menubar=0,status=0,scrollbars=1,resizable=1,titlebar=0\"); w.focus();'><img src=\"$dir/" + a[j] + "\" height=" + h + " border='0' title='No " + (1+j) + ". " + a[j] + "'></a>";
        $("#divImg").html(m); //alert(m);

        if (j > 0) $("#tr_"+(j-1)).css("background-color", "");
        $("#tr_"+j).css("background-color", "#99ccff");

        if ($doPopup) {
            w = window.open("$dir/" + a[j], "_img", 
                  "height=800,width=800,left=100,top=100,location=0,toolbar=0,menubar=0,status=0,scrollbars=1,resizable=1,titlebar=0");
            w.focus();
        }

        ++ j;
    }, $doLoopInt * 1000 * i);
}
EOF;
    $s = "<script type='text/javascript'>$s</script>";
    echo $s;
}

//
// remove path, return file name only.
//
/*
function getFilename($filename) {
    //echo $filename;
    $path_parts = pathinfo($filename);
    $filename  = $path_parts['basename'];
    return $filename;
}
*/

function file_delete($file) {
    global $reserved_images;
    $filename = basename($file);
    if ( in_array($filename, $reserved_images) ) {
        echo "<p><font color='red'>Cannot delete a reserved image.</font></p>";
        return;
    }
    try {
        if (! file_exists($file)) {
            $s = "File $filename does not exist.";
            $color = "green";
        }
        unlink($file);
        //$s = "File $filename has been successfully deleted.";
        $color = 'green';
    } catch (Exception $e) {
        $s = $e->getMessage();
        $color = 'red';
    }

    return $s;
}

function file_upload($dir, $doOverwrite) {
    global $reserved_images;

    $s = "";
    //if (is_uploaded_file($_FILES['files']['tmp_name'])) {
    if ( is_array( $_FILES['files']['tmp_name'] ) ) {
        $ct = count( $_FILES['files']['tmp_name'] );
        for ($i = 0; $i < $ct; ++ $i) {
            $tmpname = $_FILES['files']['tmp_name'][$i];
            $filename = $_FILES['files']['name'][$i];
            //echo $filename . "<br/>";

            if ( in_array($filename, $reserved_images) ) {
                $s .= "Cannot overwrite reserved file: $filename.\\n";
            } else if ( ! $doOverwrite && file_exists("$dir/$filename") ) {
                $s .= "Cannot overwrite existing file: $filename\\n";
            } else if (! is_image($filename, $tmpname)) {
                $s .= "Cannot upload file that is not an image: $filename\\n";
            } else {  
                move_uploaded_file($tmpname, "$dir/$filename");
                $s .= "Successfully uploaded file: $filename\\n";
            }
        }
    }

    return $s;
}

//
// http://www.bitrepository.com/how-to-validate-an-image-upload.html
//
function is_image($filename, $tmpname) {
    if (! is_image_ext($filename) ) return 0;

    $mimes = array('image/gif','image/jpeg','image/pjpeg','image/png');

    $mime = getimagesize($tmpname);
    //print $filename; print_r($mime);
    $mime = $mime['mime'];

    //echo "[$ext] mine: $mime<br/>";
    return in_array($mime, $mimes);
}

//
// extension is image.
//
function is_image_ext($filename) {
    $extensions = array('gif', 'jpeg', 'jpg', 'png', 'bmp', 'tiff');
    $ext = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
    return in_array($ext, $extensions);
}

function dir_create($dir) {
    if ($dir == "") return "";

    $dir = $_SESSION['username'] . "_" . $dir; // use user accunt as prefix.

    global $dir_gallery;
    $path = "$dir_gallery/$dir";

    if ( file_exists( $path ) ) {
        $s = "Gallery already exist: $dir";
    }
    else {
        mkdir($path, 0755);
        $s = "Successfully created new gallery: $dir";
    }

    return $s;
}

function dir_rm($dir) {
    if ($dir == "") return "";

    global $dir_gallery;
    $path = $dir; //"$dir_gallery/$dir";

    if (! file_exists($path)) {
        $s = "Cannot delete non-existent folder: $dir";
    } else if (! dir_is_empty($path)) {
        $s = "Cannot delete non-empty folder: $dir";
    } else {
        rmdir($dir);
        $s = "Successfully deleted folder: $dir";
    }

    return $s;
}
?>
