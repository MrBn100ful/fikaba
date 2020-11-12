<?php
function preview($nbpost){
	
	$nbpost = (int) $nbpost;
	
	if (! $result = mysqli_call("select * from " . POSTTABLE . " where no=". $nbpost ."")) {
		echo S_SQLFAIL;
	}
	$row = mysqli_fetch_array($result);


list($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip, $id) = $row;

if (!$fname)
{
    $fname = S_ANOFILE;
}
$dat = '';
$fname = substr($fname, 0, 22);
$com = auto_link($com);
$com = preg_replace("/&gt;/i", ">", $com);
$com = preg_replace("/\>\>([0-9]+)/i", "<a href='index.php?res=$resto#r\\1'>&gt;&gt;\\1</a>", $com);
$com = preg_replace("/(^|>)(\>[^<]*)/i", "\\1<span class=\"unkfunc\">\\2</span>", $com);
if (DISP_ID)
{
    $userid = "ID:$id";
}
else
{
    $userid = "";
}
// Main creation
    $dat .= "<div id='r$no' class=\"centermsg reply \" >";
    $dat .= "<div class=\"filetitle\">$name : $sub</div><div class=\"righted\"> $now <a class=\"reflink\"  onClick=\"addref('>>$no');openform('.bouton-form');\">Nb:$no</a> &nbsp; </span></div><hr>";

    $src = IMG_DIR . $tim . $ext;
    if ($ext && ($ext == ".webm" || $ext == ".mp4")) {
        $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><video controls width=\"350\" class=\"videonew\" src=\"$src\" alt=\"" . $fsize . " B\"  /></a>";
        $dat .= "$imgsrc";
    }elseif ($ext) {
        $size = $fsize; // file size displayed in alt text
        if ($w && $h) { // when there is size...
            if (@is_file(THUMB_DIR . $tim . 's.jpg')) {
                $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"" . THUMB_DIR . $tim . 's.jpg' . "\" width=\"$w\" height=\"$h\" alt=\"" . $size . " B\" /></a>";
            } else {
                $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"" . $src . "\" width=\"$w\" height=\"$h\" alt=\"" . $size . " B\" /></a>";
            }
        } else {
            $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"" . $src . "\" alt=\"" . $size . " B\" /></a>;";
        }
        if (@is_file(THUMB_DIR . $tim . 's.jpg')) {
            $dat .= " <div class=\"thumbnailmsg\">" . S_THUMB . "</div>$imgsrc<br>";
        } else {
            $dat .= "$imgsrc<br>";
        }
    }
    $dat .= "<div class='com'>$com</div></div>";
mysqli_free_result($result);
print $dat;
}