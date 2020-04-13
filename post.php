<?php

function regist($ip, $name, $capcode, $email, $sub, $com, $oekaki, $url, $pwd, $upfile, $upfile_name, $resto)
{
    global $con, $path, $pwdc, $textonly, $admin;

    if (isbanned($ip))
        error(S_BANRENZOKU);

    // time
    $time = time();
    $tim = $time . substr(microtime(), 2, 3);

    // upload processing
    if ($upfile && file_exists($upfile)) {
        $dest = $path . $tim . '.tmp';
        move_uploaded_file($upfile, $dest);
        // if an error in up, it changes to down (what?)
        // copy($upfile, $dest);
        $upfile_name = CleanStr($upfile_name);
        if (! is_file($dest))
            error(S_UPFAIL, $dest);
        if (exec("file -b " . escapeshellarg($dest)) == "WebM") {
            $ext = ".webm";
            // pass
        }else if(exec("file --mime-type -b " . escapeshellarg($dest)) == "video/mp4"){
            $ext = ".mp4";
        }else {
            $size = getimagesize($dest);
            if (! is_array($size))
                error(S_NOREC, $dest);
            $W = $size[0];
            $H = $size[1];
            switch ($size[2]) {
                case 1:
                    $ext = ".gif";
                    break;
                case 2:
                    $ext = ".jpg";
                    break;
                case 3:
                    $ext = ".png";
                    break;
                case 4:
                    $ext = ".swf";
                    break; // flash files are definitely images, thanks php
                case 13:
                    $ext = ".swf";
                    break;
                // case 5 : $ext=".psd";break;
                // case 6 : $ext=".bmp";break;
                default:
                    error(S_NODETECT);
                    break;
            }
        }
        $md5 = md5_file($dest);
        foreach (BADFILE as $value) {
            if (preg_match("/^$value/", $md5)) {
                error(S_SAMEPIC, $dest); // Refuse this image
            }
        }
        chmod($dest, 0666);
        $fsize = filesize($dest);
        if ($fsize > MAX_KB * 1024)
            error(S_TOOBIG, $dest);
        if ($ext == ".swf" && ! SWF_ENABLED)
            error(S_SWF_DISABLED);
        if ($ext == ".webm" && ! WEBM_ENABLED)
            error(S_WEBM_DISABLED);

        // Picture reduction
        if ($ext != ".webm" && ($W > MAX_W || $H > MAX_H)) {
            $W2 = MAX_W / $W;
            $H2 = MAX_H / $H;
            ($W2 < $H2) ? $key = $W2 : $key = $H2;
            $W = ceil($W * $key);
            $H = ceil($H * $key);
        }
        $mes = ' ' . $upfile_name . S_UPGOOD;
    }

    if ($_FILES["upfile"]["error"] == 2) {
        error(S_TOOBIG, $dest);
    }
    if ($ext != ".webm" && ! isset($oekaki) && $upfile_name && $_FILES["upfile"]["size"] == 0) {
        if ($_FILES["upfile"]["error"] == 1) {
            error(S_TOOBIG . "<br />$upfile_name", $dest);
        }
        error(S_TOOBIGORNONE . "<br />$upfile_name", $dest);
    }

    // The last result number
    if (! $result = mysqli_call("select max(no) from " . POSTTABLE)) {
        echo S_SQLFAIL;
    }
    $row = mysqli_fetch_array($result);
    $lastno = (int) $row[0];
    mysqli_free_result($result);

    // Number of log lines
    $result = mysqli_call("select * from " . POSTTABLE . " where resto=0");
    if (! $resto) {
        $threadcount = 1;
    } else {
        $threadcount = 0;
    }
    while ($resrow = mysqli_fetch_row($result)) {
        $threadcount ++;
    }
    mysqli_free_result($result);

    /* Purge old threads */
    $result = mysqli_call("select no,ext,tim from " . POSTTABLE . " where resto=0 order by root asc");
    while ($threadcount > THREADLIMIT) {
        list ($dno, $dext, $dtim) = mysqli_fetch_row($result);
        if (! mysqli_call("delete from " . POSTTABLE . " where no=" . $dno)) {
            echo S_SQLFAIL;
        }
        if ($dext) {
            if (is_file($path . $dtim . $dext))
                unlink($path . $dtim . $dext);
            if (is_file(THUMB_DIR . $dtim . 's.jpg'))
                unlink(THUMB_DIR . $dtim . 's.jpg');
        }
        $threadcount --;
    }
    mysqli_free_result($result);

    $find = false;
    $resto = (int) $resto;
    if ($resto) {
        if (! $result = mysqli_call("select * from " . POSTTABLE . " where root>0 and no=$resto")) {
            echo S_SQLFAIL;
        } else {
            $find = mysqli_fetch_row($result);
            mysqli_free_result($result);
        }
        if (! $find)
            error(S_NOTHREADERR, $dest);
    }

    foreach (BADSTRING as $value) {
        if (preg_match('/' . $value . '/', $com) || preg_match('/' . $value . '/', $sub) || preg_match('/' . $value . '/', $name) || preg_match('/' . $value . '/', $email)) {
            error(S_STRREF, $dest);
        }
        ;
    }
    if ($_SERVER["REQUEST_METHOD"] != "POST")
        error(S_UNJUST, $dest);
    // Form content check
    if (! $name || preg_match("/^[ |@|]*$/", $name))
        $name = '';
    if (! $com || preg_match("/^[ |@|\t]*$/", $com))
        $com = '';
    if (! $sub || preg_match("/^[ |@|]*$/", $sub))
        $sub = '';

    if (! isset($oekaki) && ! $resto && ! is_file($dest)) {
        if (FORCEIMAGE)
            error(S_NOPIC, $dest);
        // else $textonly = "on";
    }
    if (! $com && ! is_file($dest))
        error(S_NOTEXT, $dest);

    if (strlen($com) > 10000)
        error(S_TOOLONG, $dest);
    if (strlen($name) > 100)
        error(S_TOOLONG, $dest);
    if (strlen($email) > 100)
        error(S_TOOLONG, $dest);
    if (strlen($sub) > 100)
        error(S_TOOLONG, $dest);
    if (strlen($resto) > 10)
        error(S_UNUSUAL, $dest);
    if (strlen($url) > 10)
        error(S_UNUSUAL, $dest);

    // host check
    $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

    if (preg_match("/^mail/", $host) || preg_match("/^ns/", $host) || preg_match("/^dns/", $host) || preg_match("/^ftp/", $host) || preg_match("/^prox/", $host) || preg_match("/^pc/", $host) || preg_match("/^[^\.]\.[^\.]$/", $host)) {
        $pxck = true;
    }

    if ($pxck && PROXY_CHECK && ! isset($_SESSION['name'])) {
        if (proxy_connect('80') == true) {
            error(S_PROXY80, $dest);
        } elseif (proxy_connect('8080') == true) {
            error(S_PROXY8080, $dest);
        }
    }

    // No, path, time, and url format
    srand((double) microtime() * 1000000);
    if ($pwd == "") {
        if ($pwdc == "") {
            $pwd = rand();
            $pwd = substr($pwd, 0, 8);
        } else {
            $pwd = $pwdc;
        }
    }

    $c_pass = $pwd;
    $pass = ($pwd) ? substr(md5($pwd), 2, 8) : "*";
    $youbi = array(
        S_SUN,
        S_MON,
        S_TUE,
        S_WED,
        S_THU,
        S_FRI,
        S_SAT
    );
    $yd = $youbi[gmdate("w", $time + TIMEZONE * 60 * 60)];
    $now = gmdate(DATEFORMAT, $time + TIMEZONE * 60 * 60) . "(" . (string) $yd . ")" . gmdate("H:i", $time + TIMEZONE * 60 * 60);
    $posterid = substr(crypt(md5($_SERVER["REMOTE_ADDR"] . 'id' . gmdate("Ymd", $time + TIMEZONE * 60 * 60)), 'id'), - 8);
    // Text plastic surgery (rorororor)
    $email = CleanStr($email);
    $email = preg_replace("/[\r\n]/", "", $email);
    $sub = CleanStr($sub);
    $sub = preg_replace("/[\r\n]/", "", $sub);
    $url = CleanStr($url);
    $url = preg_replace("/[\r\n]/", "", $url);
    $resto = CleanStr($resto);
    $resto = preg_replace("/[\r\n]/", "", $resto);
    $com = CleanStr($com);
    // Standardize new character lines
    $com = str_replace("\r\n", "\n", $com);
    $com = str_replace("\r", "\n", $com);
    // Continuous lines
    $com = preg_replace("/\n((!@| )*\n) {3,}/", "\n", $com);
    $com = nl2br($com); // newlines get substituted by br tags
    $com = str_replace("\n", "", $com); // \n is erased (is this necessary?)

    foreach (FILTERS as $filterin => $filterout) {
        $com = str_replace($filterin, $filterout, $com);
    }

    $name = preg_replace("/[\r\n]/", "", $name);
    $names = $name;
    $name = trim($name); // blankspace removal
    if (get_magic_quotes_gpc()) { // magic quotes is deleted (?)
        $name = stripslashes($name);
    }
    $name = htmlspecialchars($name); // remove html special chars
    $name = str_replace("&amp;", "&", $name); // remove ampersands
    $name = str_replace(",", "&#44;", $name); // remove commas

    if (preg_match("/(#|!)(.*)/", $names, $regs)) {
        $cap = $regs[2];
        $cap = strtr($cap, "&amp;", "&");
        $cap = strtr($cap, "&#44;", ",");
        $name = preg_replace("/(#|!)(.*)/", "", $name);
        $salt = substr($cap . "H.", 1, 2);
        $salt = preg_replace("/[^\.-z]/", ".", $salt);
        $salt = strtr($salt, ":;<=>?@[\\]^_`", "ABCDEFGabcdef");
        $name .= TRIPKEY . substr(crypt($cap, $salt), - 10) . "";
    }

    if (! $name || (FORCED_ANON && ! $_SESSION['name']))
        $name = S_ANONAME; // manas can post with name when forced anon is on
                           // TODO: add a setting for this
                           // if (!$com) $com = S_ANOTEXT;
                           // if (!$sub) $sub = S_ANOTITLE;
    if (! $com)
        $com = '';
    if (! $sub)
        $sub = '';

    // Add capcode
    if ($capcode && isset($_SESSION['capcode']) && $_SESSION['cancap'])
        $name .= ' ' . $_SESSION['capcode'];

    // Read the log
    $query = "select time from " . POSTTABLE . " where com='" . mysqli_escape_string($con, $com) . "' " . "and host='" . mysqli_escape_string($con, $host) . "' " . "and no>" . ($lastno - 20); // The same
    if (! $result = mysqli_call($query)) {
        echo S_SQLFAIL;
    }
    $row = mysqli_fetch_array($result);
    mysqli_free_result($result);
    if ($row && ! $upfile_name)
        error(S_RENZOKU3, $dest);

    $query = "select time from " . POSTTABLE . " where time>" . ($time - RENZOKU) . " " . "and host='" . mysqli_escape_string($con, $host) . "' "; // From precontribution
    if (! $result = mysqli_call($query)) {
        echo S_SQLFAIL;
    }
    $row = mysqli_fetch_array($result);
    mysqli_free_result($result);
    if ($row && ! $upfile_name)
        error(S_RENZOKU3, $dest);

    // Upload processing
    if ($dest && file_exists($dest)) {

        $query = "select time from " . POSTTABLE . " where time>" . ($time - RENZOKU2) . " " . "and host='" . mysqli_escape_string($con, $host) . "' "; // From precontribution
        if (! $result = mysqli_call($query)) {
            echo S_SQLFAIL;
        }
        $row = mysqli_fetch_array($result);
        mysqli_free_result($result);
        if ($row && $upfile_name)
            error(S_RENZOKU2, $dest);
    }

    $restoqu = (int) $resto;
    $rootqu = "now()";
    if ($resto) { // res,root processing
        if (! $resline = mysqli_call("select * from " . POSTTABLE . " where resto=" . $resto)) {
            echo S_SQLFAIL;
        }
        $countres = mysqli_num_rows($resline);
        mysqli_free_result($resline);
        if (! stristr($email, 'sage') && $countres < BUMPLIMIT) {
            $query = "update " . POSTTABLE . " set root=now() where no=$resto"; // age
            if (! $result = mysqli_call($query)) {
                echo S_SQLFAIL;
            }
        }
    }

    $query = "insert into " . POSTTABLE . " (now,name,email,sub,com,host,pwd,ext,w,h,tim,time,md5,fname,fsize,root,resto,ip,id) values (" . "'" . $now . "'," . "'" . mysqli_escape_string($con, $name) . "'," . "'" . mysqli_escape_string($con, $email) . "'," . "'" . mysqli_escape_string($con, $sub) . "'," . "'" . mysqli_escape_string($con, $com) . "'," . "'" . mysqli_escape_string($con, $host) . "'," . "'" . mysqli_escape_string($con, $pass) . "'," . "'" . $ext . "'," . (int) $W . "," . (int) $H . "," . "'" . $tim . "'," . (int) $time . "," . "'" . $md5 . "'," . "'" . $upfile_name . "'," . (int) $fsize . "," . $rootqu . "," . (int) $resto . ",
        \"" . $_SERVER['REMOTE_ADDR'] . "\",
        '$posterid')";
    if (! $result = mysqli_call($query)) {
        echo S_SQLFAIL;
    } // post registration

    // Cookies
    setcookie("pwdc", $c_pass, time() + 7 * 24 * 3600); /* 1 week cookie expiration */
    if (function_exists("mb_internal_encoding") && function_exists("mb_convert_encoding") && function_exists("mb_substr")) {
        if (preg_match("/MSIE|Opera/", $_SERVER["HTTP_USER_AGENT"])) {
            $i = 0;
            $c_name = '';
            mb_internal_encoding("SJIS");
            while ($j = mb_substr($names, $i, 1)) {
                $j = mb_convert_encoding($j, "UTF-16", "SJIS");
                $c_name .= "%u" . bin2hex($j);
                $i ++;
            }
            header("Set-Cookie: namec=$c_name; expires=" . gmdate("D, d-M-Y H:i:s", time() + 7 * 24 * 3600) . " GMT", false);
        } else {
            $c_name = $names;
            setcookie("namec", $c_name, time() + 7 * 24 * 3600); /* 1 week cookie expiration */
        }
    }

    if ($dest && file_exists($dest)) {
        rename($dest, $path . $tim . $ext);
        if (USE_THUMB) {
            thumb($path, $tim, $ext);
        }
    }
    updatelog();

    if (stristr($email, 'nonoko') || ! $resto) {
        echo "<html><head><meta http-equiv=\"refresh\" content=\"1;URL=" . PHP_SELF2 . "\" /></head>";
    } else {
        echo "<html><head><meta http-equiv=\"refresh\" content=\"1;URL=" . PHP_SELF . "?res=$resto\" /></head>";
    }
}

// thumbnails
function thumb($path, $tim, $ext)
{
    if (! function_exists("ImageCreate") || ! function_exists("ImageCreateFromJPEG")) {
        return;
    }
    $fname = $path . $tim . $ext;
    $thumb_dir = THUMB_DIR; // Thumbnail directory
    $width = MAX_W; // Output width
    $height = MAX_H; // Output height
                     // width, height, and type are acquired
    $size = GetImageSize($fname);
    switch ($size[2]) {
        case 1:
            if (function_exists("ImageCreateFromGIF")) {
                $im_in = @ImageCreateFromGif($fname);
                if ($im_in) {
                    break;
                }
            }
            if (! file_exists($path . $tim . '.png')) {
                return;
            }
            $im_in = @ImageCreateFromPNG($path . $tim . '.png');
            unlink($path . $tim . '.png');
            if (! $im_in) {
                return;
            }
            break;
        case 2:
            $im_in = @ImageCreateFromJPEG($fname);
            if (! $im_in) {
                return;
            }
            break;
        case 3:
            if (! function_exists("ImageCreateFromPNG")) {
                return;
            }
            $im_in = @ImageCreateFromPNG($fname);
            if (! $im_in) {
                return;
            }
            break;
        default:
            return;
    }
    // Resizing
    if ($size[0] > $width || $size[1] > $height) {
        $key_w = $width / $size[0];
        $key_h = $height / $size[1];
        ($key_w < $key_h) ? $keys = $key_w : $keys = $key_h;
        $out_w = ceil($size[0] * $keys) + 1;
        $out_h = ceil($size[1] * $keys) + 1;
    } else {
        $out_w = $size[0];
        $out_h = $size[1];
    }
    // the thumbnail is created
    if (function_exists("ImageCreateTrueColor") && get_gd_ver() == "2") {
        $im_out = ImageCreateTrueColor($out_w, $out_h);
    } else {
        $im_out = ImageCreate($out_w, $out_h);
    }
    // change background color
    $backing = imagecolorallocate($im_out, ...THUMBBACK);
    imagefill($im_out, 0, 0, $backing);
    // copy resized original
    imagecopyresampled($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
    // thumbnail saved
    ImageJPEG($im_out, $thumb_dir . $tim . 's.jpg', 80);
    chmod($thumb_dir . $tim . 's.jpg', 0666);
    // created image is destroyed
    ImageDestroy($im_in);
    ImageDestroy($im_out);
}


