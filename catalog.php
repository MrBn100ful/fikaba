<?php

function catalog()
{
    $dat = '';
    head($dat);

    form($dat, 0);

    $dat .= "<div class=\"passvalid\"> <a href=\"" . PHP_SELF2 . "\">[" . S_RETURNS . "]</a> </div><br />";
    $dat .= "<div class='cattable'>";
    $i = 0;
    $result = mysqli_call("select * from " . POSTTABLE . " order by root desc");

    while ($row = mysqli_fetch_row($result)) {
        list ($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip) = $row;
        if ((int) $resto == 0) {
            $dat .= "<div class='catthread'>";
            if ($ext && $ext == ".mp4" || $ext == ".webm") {
                $imgsrc = "<video class='catthumb' src=\"" . IMG_DIR . $tim . $ext . "\" alt=\"" . $fsize . " B\" />";
                $dat .= "<a href='" . PHP_SELF . "?res=$no'>$imgsrc</a>";
            } elseif ($ext) {
                $size = $fsize; // file size displayed in alt text
                if ($w && $h) { // when there is size...
                    if (@is_file(THUMB_DIR . $tim . 's.jpg')) {
                        $imgsrc = "<img class='catthumb' src=\"" . THUMB_DIR . $tim . 's.jpg' . "\"  alt=\"" . $size . " B\" />";
                    } else {
                        $imgsrc = "<img class='catthumb' src=\"" . IMG_DIR . $tim . $ext . "\"  alt=\"" . $size . " B\" />";
                    }
                } else {
                    $imgsrc = "<img class='catthumb' src=\"$src\" alt=\"" . $size . " B\" />";
                }
                $dat .= "<a href='" . PHP_SELF . "?res=$no'>$imgsrc</a>";
            }
            if (strlen($com) > 55) {
                $com = substr($com, 0, 54) . "...";
            }

            $callreply = mysqli_call("SELECT COUNT(1) FROM " . POSTTABLE . " WHERE resto = " . $no . "");

            $nbreply = mysqli_fetch_row($callreply);

            list ($reply) = $nbreply;

            $dat .= "<a class='cata' href='" . PHP_SELF . "?res=$no'><span class='cattitle filetitle'>$sub</span> <br> <span class='catresponse'>R: $reply</span> <br /><span class='catcont'>$com</span> </a></div>";

            mysqli_free_result($callreply);

            $i ++;
        }
    }
    mysqli_free_result($result);

    $dat .= "</div>";
    $dat .= "<hr> <a onClick=\"location.href=location.href\" >[" . S_REFRESH . "]</a>";
    foot($dat);
    echo ($dat);
}