<?php

// 4feuilles build:200413
//
// For setup instructions and latest version, please visit:
// https://github.com/knarka/fikaba
//
// Based on GazouBBS, Futaba, Futallaby, and Fikaba
const S_NAMEVERSION = 'v1.3';

include 'config.php';
include 'boardslist.php';
include 'catalog.php';
include 'post.php';
include 'admin.php';
include 'strings/' . LANGUAGE . '.php'; // String resource file

if (LOCKDOWN) {
    // if not trying to do something other than managing, die
    if (! isset($_SESSION['capcode']) && ! ($_GET['mode'] == 'admin' || $_POST['mode'] == 'admin')) {
        die(S_LOCKDOWN);
    }
}

extract($_POST, EXTR_SKIP);
extract($_GET, EXTR_SKIP);
extract($_COOKIE, EXTR_SKIP);
if (isset($_FILES["upfile"])) {
    $upfile_name = $_FILES["upfile"]["name"];
    $upfile = $_FILES["upfile"]["tmp_name"];
}

session_start();

$path = realpath("./") . '/' . IMG_DIR;
ignore_user_abort(true);

if (! $con = mysqli_connect(SQLHOST, SQLUSER, SQLPASS)) {
    echo S_SQLCONF; // unable to connect to DB (wrong user/pass?)
    exit();
}

if (! file_exists(IMG_DIR) && ! is_dir(IMG_DIR)) {
    mkdir(IMG_DIR, 0777);
    echo (IMG_DIR . ': ' . S_FCREATE);
}
if (! file_exists(THUMB_DIR) && ! is_dir(THUMB_DIR)) {
    mkdir(THUMB_DIR, 0777);
    echo (THUMB_DIR . ': ' . S_FCREATE);
}

$db_id = mysqli_select_db($con, SQLDB);
if (! $db_id) {
    echo S_SQLDBSF;
}

if (! table_exist(POSTTABLE)) {
    echo (POSTTABLE . ': ' . S_TCREATE);
    $result = mysqli_call("create table " . POSTTABLE . " (primary key(no),
		no    int not null auto_increment,
		now   text,
		name  text,
		email text,
		sub   text,
		com   text,
		host  text,
		pwd   text,
		ext   text,
		w     int,
		h     int,
		tim   text,
		time  int,
		md5   text,
		fname text,
		fsize int,
		root  timestamp,
		resto int,
		ip    text,
		id    text)");
    if (! $result) {
        echo S_TCREATEF;
    }
    updatelog(); // in case of a database wipe or something
}

if (! table_exist(BANTABLE)) {
    echo (BANTABLE . ': ' . S_TCREATE);
    $result = mysqli_call("create table " . BANTABLE . " (ip text not null,
		start int,
		expires int,
		reason text)");
    if (! $result) {
        echo S_TCREATEF;
    }
}

if (! table_exist(MANATABLE)) {
    echo (MANATABLE . ': ' . S_TCREATE);
    $result = mysqli_call("create table " . MANATABLE . " (name text not null,
		password text not null,
		capcode text not null,
		candel int not null,
		canban int not null,
		cancap int not null,
		canacc int not null)");
    if (! $result) {
        echo S_TCREATEF;
    }
    $query = "insert into " . MANATABLE . " (name,password,capcode,candel,canban,cancap,canacc) values ('DUMMY', 'REPLACEME','',0,0,0,1)";
    if (! $result = mysqli_call($query)) {
        echo S_SQLFAIL;
    } // Post registration
    mysqli_free_result($result);
}

function humantime($time)
{
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
    return gmdate("d/m/y", $time + TIMEZONE * 60 * 60) . "(" . (string) $yd . ")" . gmdate("H:i", $time + TIMEZONE * 60 * 60);
}

function updatelog($resno = 0)
{
    global $path;

    $find = false;
    $resno = (int) $resno;
    if ($resno) {
        $result = mysqli_call("select * from " . POSTTABLE . " where root>0 and no=$resno");
        if ($result) {
            $find = mysqli_fetch_row($result);
            mysqli_free_result($result);
        }
        if (! $find) {
            error(S_REPORTERR);
        }
    }
    if ($resno) {
        if (! $treeline = mysqli_call("select * from " . POSTTABLE . " where root>0 and no=" . $resno . " order by root desc")) {
            echo S_SQLFAIL;
        }
    } else {
        if (! $treeline = mysqli_call("select * from " . POSTTABLE . " where root>0 and resto=0 order by root desc")) {
            echo S_SQLFAIL;
        }
    }

    // Finding the last entry number
    if (! $result = mysqli_call("select max(no) from " . POSTTABLE)) {
        echo S_SQLFAIL;
    }
    $row = mysqli_fetch_array($result);
    $lastno = (int) $row[0];
    mysqli_free_result($result);

    $counttree = mysqli_num_rows($treeline);
    if (! $counttree) {
        $logfilename = PHP_SELF2;
        $dat = '';
        head($dat);
        form($dat, $resno);
        $fp = fopen($logfilename, "w");
        set_file_buffer($fp, 0);
        rewind($fp);
        fputs($fp, $dat);
        fclose($fp);
        chmod($logfilename, 0666);
    }
    for ($page = 0; $page < $counttree; $page += PAGE_DEF) {
        $dat = '';
        head($dat);
        form($dat, $resno);
        if (! $resno)
            $st = $page;
        else
            $st = 0;
        $dat .= '<form action="' . PHP_SELF . '" method="post">';

        if ($resno == 0) {

            $dat .= '<div class="passvalid"> <a href="imgboard.php?mode=catalog">[' . S_CATALOGBUTTON . ']</a></div> <br>';
        } else {

            $dat .= '<div class="passvalid"><a href=' . PHP_SELF2 . '>[' . S_RETURNS . ']</a> <a href="imgboard.php?mode=catalog">[' . S_CATALOGBUTTON . ']</a></div> <br>';
        }

        $p = 0;
        for ($i = $st; $i < $st + PAGE_DEF; $i ++) {
            list ($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip, $id) = mysqli_fetch_row($treeline);
            if (! $no) {
                break;
            }
            if (! $fname) {
                $fname = S_ANOFILE;
            }

            // URL and link
            if ($email) {
                $name = "<a href=\"mailto:$email\">$name</a>";
            }
            $com = auto_link($com);
            $com = preg_replace("/&gt;/i", ">", $com);
            $com = preg_replace("/\>\>([0-9]+)/i", "<a href='" . PHP_SELF . "?res=$resto#r\\1'>&gt;&gt;\\1</a>", $com);
            $com = preg_replace("/(^|>)(\>[^<]*)/i", "\\1<span class=\"unkfunc\">\\2</span>", $com);
            // Picture file name
            $img = $path . $tim . $ext;
            $src = IMG_DIR . $tim . $ext;
            // img tag creation
            
            // Main creation
            $dat .= "<input type=\"checkbox\" name=\"$no\" value=\"delete\" /> <span class=\"filetitle\">$sub</span> ";
            $dat .= "<span class=\"postername\">$name</span> $now $userid <a class=\"reflink\" href=\"#r$no\">No.</a> <a class=\"reflink\" href=\"#\" onClick=\"addref('$no');\">$no</a> &nbsp;";
            if (! $resno) {
                $dat .= "<a href=\"" . PHP_SELF . "?res=$no\">[" . S_REPLY . "]</a>";
            }
            $imgsrc = "";
            if ($ext && $ext == ".mp4" || $ext == ".webm") {
                $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><video  controls width=\"350\" src=\"$src\" alt=\"" . $fsize . " B\" /></a>";
                $dat .= "<br><span class=\"filesize\">" . S_PICNAME . "<a href=\"$src\" target=\"_blank\">$tim$ext</a> ($fsize B, $fname)</span><br />$imgsrc";
            } elseif ($ext) {
                $size = $fsize; // file size displayed in alt text
                if ($w && $h) { // when there is size...
                    if (@is_file(THUMB_DIR . $tim . 's.jpg')) {
                        $imgsrc = "	<span class=\"thumbnailmsg\">" . S_THUMB . "</span><br /><a href=\"" . $src . "\" target=\"_blank\"><img src=\"" . THUMB_DIR . $tim . 's.jpg' . "\" width=\"$w\" height=\"$h\" alt=\"" . $size . " B\" /></a>";
                    } else {
                        $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"$src\" width=\"$w\" height=\"$h\" alt=\"" . $size . " B\" /></a>";
                    }
                } else {
                    $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"$src\" alt=\"" . $size . " B\" /></a>";
                }
                $dat .= "<br><span class=\"filesize\">" . S_PICNAME . "<a href=\"$src\" target=\"_blank\">$tim$ext</a> ($size B, $fname)</span><br />$imgsrc";
            }
            if (DISP_ID) {
                $userid = "ID:$id";
            } else {
                $userid = "";
            }
            // Main creation
 
            $dat .= "<blockquote>$com</blockquote>";

            if (! $resline = mysqli_call("select * from " . POSTTABLE . " where resto=" . $no . " order by no")) {
                echo S_SQLFAIL;
            }
            $countres = mysqli_num_rows($resline);

            if (! $resno) {
                $s = $countres - COLLAPSENUM;
                if ($s < 0) {
                    $s = 0;
                } elseif ($s > 0) {
                    $dat .= "<span class=\"omittedposts\"><a href=\"" . PHP_SELF . "?res=$no\">" . $s . S_ABBR . "</a></span><br />";
                }
            } else {
                $s = 0;
            }

            while ($resrow = mysqli_fetch_row($resline)) {
                if ($s > 0) {
                    $s --;
                    continue;
                }
                list ($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip, $id) = $resrow;

                if (! $no) {
                    break;
                }
                if (! $fname) {
                    $fname = S_ANOFILE;
                }

                if ($sub) {
                    $replytitle = "<span class=\"replytitle\">$sub</span>";
                } else {
                    $replytitle = "";
                }

                // URL and e-mail
                if ($email) {
                    $name = "<a href=\"mailto:$email\">$name</a>";
                }
                $com = auto_link($com);
                $com = preg_replace("/&gt;/i", ">", $com);
                $com = preg_replace("/\>\>([0-9]+)/i", "<a href='" . PHP_SELF . "?res=$resto#r\\1'>&gt;&gt;\\1</a>", $com);
                $com = preg_replace("/(^|>)(\>[^<]*)/i", "\\1<span class=\"unkfunc\">\\2</span>", $com);
                if (DISP_ID) {
                    $userid = "ID:$id";
                } else {
                    $userid = "";
                }
                // Main creation
                $dat .= "<table id='r$no'><tr><td class=\"doubledash\">&gt;&gt;</td><td class=\"reply\">";
                $dat .= "<span class='intro'><input type=\"checkbox\" name=\"$no\" value=\"delete\" /> $replytitle";
                $dat .= "<span class=\"commentpostername\">$name</span> $now $userid <a class=\"reflink\" href=\"#r$no\">No.</a><a class=\"reflink\" href=\"#\" onClick=\"addref('$no');\">$no</a> &nbsp;<br /></span>";
                $src = IMG_DIR . $tim . $ext;
                if ($ext && ($ext == ".webm" || $ext == ".mp4")) {
                    $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><video controls width=\"350\" src=\"$src\" alt=\"" . $fsize . " B\"  /></a>";
                    $dat .= "<span class=\"filesize commentfile\">" . S_PICNAME . "<a href=\"$src\" target=\"_blank\">$tim$ext</a> ($fsize B, $fname)</span><br />$imgsrc";
                }elseif ($ext) {
                    $size = $fsize; // file size displayed in alt text
                    if ($w && $h) { // when there is size...
                        if (@is_file(THUMB_DIR . $tim . 's.jpg')) {
                            $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"" . THUMB_DIR . $tim . 's.jpg' . "\" width=\"$w\" height=\"$h\" alt=\"" . $size . " B\" /></a>";
                        } else {
                            $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"" . $src . "\" width=\"$w\" height=\"$h\" alt=\"" . $size . " B\" /></a>";
                        }
                    } else {
                        $imgsrc = "<a href=\"" . $src . "\" target=\"_blank\"><img src=\"" . $src . "\" alt=\"" . $size . " B\" /></a>;br />";
                    }
                    if (@is_file(THUMB_DIR . $tim . 's.jpg')) {
                        $dat .= "<span class=\"filesize commentfile\">" . S_PICNAME . "<a href=\"$src\" target=\"_blank\">$tim$ext</a> ($size B, $fname)</span> <span class=\"thumbnailmsg\">" . S_THUMB . "</span><br />$imgsrc";
                    } else {
                        $dat .= "<span class=\"filesize commentfile\">" . S_PICNAME . "<a href=\"$src\" target=\"_blank\">$tim$ext</a> ($size B, $fname)</span><br />$imgsrc";
                    }
                }
                $dat .= "<blockquote>$com</blockquote>";
                $dat .= "</td></tr></table>";
            }
            $dat .= "<br class=\"leftclear\" /><hr />";
            clearstatcache(); // clear stat cache of a file
            mysqli_free_result($resline);
            $p ++;
            if ($resno) {
                break;
            } // only one tree line at time of res
        }
        $dat .= '<table class="righted"><tr><td class="nowrap righted">
		<input type="hidden" name="mode" value="usrdel" />' . S_REPDEL . '[<input type="checkbox" name="onlyimgdel" value="on" />' . S_DELPICONLY . ']<br />
		' . S_DELKEY . '<input type="password" name="pwd" size="8" maxlength="8" value="" />
		<input type="submit" value="' . S_DELETE . '" /></td></tr></table></form>
		<script><!--
	l();
	//--></script>';

        if (! $resno) { // if not in res display mode
            $prev = $st - PAGE_DEF;
            $next = $st + PAGE_DEF;
            // Page processing
            $dat .= "<table><tr>";
            if ($prev >= 0) {
                if ($prev == 0) {
                    $dat .= "<form action=\"" . PHP_SELF2 . "\" method=\"get\"><td>";
                } else {
                    $dat .= "<form action=\"" . $prev / PAGE_DEF . PHP_EXT . "\" method=\"get\"><td>";
                }
                $dat .= "<input type=\"submit\" value=\"" . S_PREV . "\" />";
                $dat .= "</td></form>";
            }

            $dat .= "<td>";
            for ($i = 0; $i < $counttree; $i += PAGE_DEF) {
                if ($i && ! ($i % (PAGE_DEF * 2))) {
                    $dat .= " ";
                }
                if ($st == $i) {
                    $dat .= "[" . ($i / PAGE_DEF) . "] ";
                } else {
                    if ($i == 0) {
                        $dat .= "[<a href=\"" . PHP_SELF2 . "\">0</a>] ";
                    } else {
                        $dat .= "<a href=\"" . ($i / PAGE_DEF) . PHP_EXT . "\">[" . ($i / PAGE_DEF) . "]</a> ";
                    }
                }
            }
            $dat .= "</td>";

            if ($p >= PAGE_DEF && $counttree > $next) {
                $dat .= "<form action=\"" . $next / PAGE_DEF . PHP_EXT . "\" method=\"get\"><td>";
                $dat .= "<input type=\"submit\" value=\"" . S_NEXT . "\" />";
                $dat .= "</form></td>";
                $dat .= "<a onClick=\"location.href=location.href\" >[" . S_REFRESH . "]</a>";
            }
            $dat .= "</tr></table><br class=\"allclear\" />";
        } else { // in res display mode

            $dat .= "<table></table><br class=\"allclear\" />";
            $dat .= "<a onClick=\"location.href=location.href\" >[" . S_REFRESH . "]</a>";
        }
        foot($dat);
        if (ECHOALL) {
            echo $dat;
            break;
        }
        if ($resno) {
            echo $dat;
            break;
        }
        if ($page == 0) {
            $logfilename = PHP_SELF2;
        } else {
            $logfilename = $page / PAGE_DEF . PHP_EXT;
        }
        $fp = fopen($logfilename, "w");
        set_file_buffer($fp, 0);
        rewind($fp);
        fputs($fp, $dat);
        fclose($fp);
        chmod($logfilename, 0666);
    }
    mysqli_free_result($treeline);
}

function mysqli_call($query)
{
    global $con;
    $ret = mysqli_query($con, $query) or die(mysqli_error($con));
    if (! $ret) {
        echo $query . "<br />";
    }
    return $ret;
}

function head(&$dat)
{
    $titlepart = '';
    if (SHOWTITLEIMG == 1) {
        $titlepart .= '<img src="' . TITLEIMG . '" alt="' . TITLE . '" />';
        if (SHOWTITLETXT == 1) {
            $titlepart .= '<br />';
        }
    } elseif (SHOWTITLEIMG == 2) {
        $titlepart .= '<img src="' . BANNERS[mt_rand(0, count(BANNERS) - 1)] . '" onclick="this.src=this.src;" alt="' . TITLE . '" />';
        if (SHOWTITLETXT == 1) {
            $titlepart .= '<br />';
        }
    }
    if (SHOWTITLETXT == 1) {
        $titlepart .= TITLE;
    }

    $dat .= '<!doctype html>
<html lang="' . LANGUAGE . '"> 
<link rel="stylesheet" type="text/css" href="https://4feuilles.org/css/4feuilles.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
<script src="https://dev.neonroot.net/js/scripts.js"></script><head>
<meta http-equiv="content-type"  content="text/html;charset=utf-8" />
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<script type="text/javascript">
  var _paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
  _paq.push(["setCookieDomain", "*.4feuilles.org"]);
  _paq.push(["setDomains", ["*.4feuilles.org"]]);
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);
  (function() {
    var u="//analytics.4feuilles.org/";
    _paq.push(["setTrackerUrl", u+"matomo.php"]);
    _paq.push(["setSiteId, "1"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];
    g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"matomo.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//analytics.4feuilles.org/matomo.php?idsite=1&amp;rec=1" style="border:0;" alt="" /></p></noscript>
';

    $dat .= '<title>' . TITLE . '</title>';

    $dat .= '</head>
	<body>
	<div class="menu-normal"><div class="styles">
	
	<a href="#" onclick="changeCSS(\'https://4feuilles.org/css/4feuilles.css\', 0);document.cookie=\'theme=https://4feuilles.org/css/4feuilles.css\';">[Style Nuit]</a>
    
	<a href="#" onclick="changeCSS(\'https://4feuilles.org/css/4feuilles_claire.css\', 0);document.cookie=\'theme=https://4feuilles.org/css/4feuilles_claire.css\';">[Style Océan]</a>
	
	';

    $pc = boardslist(3);

    $mobile = boardslist(2);

    $dat .= '</div>
	<div class="adminbar">
	<a href="https://4feuilles.org" target="_top">[Accueil]</a>
	' . $pc . '
	</div>
	</div>
	<div class="menu-mobile"onclick="mobile(this)">
	<a id="bouton-mobile">[Menu]</a>
	<ul id="mobile-cacher">
    ' . $mobile . '
	</ul>
	</div>
	
	
	
	<div class="logo"><br>' . $titlepart . ' <br></div> <hr class="logohr" /><br /><br /> <br>';
}

/* Contribution form */
function form(&$dat, $resno, $admin = "", $manapost = false)
{
    $maxbyte = MAX_KB * 1024;
    $no = $resno;
    if ($admin)
        $msg = "<em>" . S_NOTAGS . ".</em>";
    else
        $msg = '';

    $dat .= $msg . '<div class="centered"><div class="postarea">
		<form id="postform" action="' . PHP_SELF . '" method="post" enctype="multipart/form-data" style="display: inline-block;">
		<input type="hidden" name="mode" value="regist" />
		<input type="hidden" name="MAX_FILE_SIZE" value="' . $maxbyte . '" />';
    if ($no) {
        $dat .= '<input type="hidden" name="resto" value="' . $no . '" />';
    }
    $dat .= '<table>';
    if (! $admin) {
        if (! $resno) {
            $dat .= '<tr><td class="postblocktitle" colspan=2>' . S_NEWTHREAD . '</td></tr>';
        } else {
            $dat .= '<tr><td class="postblocktitle" colspan=2>' . S_POSTING . " <a href=\"" . PHP_SELF2 . "\">[" . S_RETURN . "]</a></td></tr>";
        }
    }
    if (! FORCED_ANON || $admin)
        // $dat.='<tr><td class="postblock">'.S_NAME.'</td><td><input type="text" name="name" value="" placeholder="Anonymous';
        if ($manapost)
            $dat .= $_SESSION['name'];
    // $dat .= '" size="35" /></td></tr>';
    if ($admin && $_SESSION['cancap']) {
        $dat .= '<tr><td class="postblock">' . S_CAPCODE . '</td><td><input type="checkbox" name="capcode" value="on" checked="checked" size="35" /> (' . $_SESSION['capcode'] . ')</td></tr>
		<tr><td class="postblock">' . S_REPLYTO . '</td><td><input type="text" name="resto" size="35" value="0" /></td></tr>';
    }
    $dat .= '<tr><td class="postblock">' . S_SUBJECT . '</td><td><input type="text" name="sub" size="35" />
	<input type="submit" value="' . S_SUBMIT . '" /></td></tr>
	<tr><td class="postblock">' . S_COMMENT . '</td><td><textarea id="com" name="com" cols="40" rows="4"></textarea></td></tr>';
    $dat .= '<tr id="filerow"><td class="postblock">' . S_UPLOADFILE . '</td>
<td><input type="file" name="upfile" size="35" />';

    $dat .= '</td></tr><tr><td class="postblock">' . S_DELPASS . '</td><td><input type="password" name="pwd" size="18" maxlength="8" value="" /> ' . S_DELEXPL . '</td></tr>
<tr><td colspan="2">
<div class="rules lefted">';
    if (SWF_ENABLED && WEBM_ENABLED)
        $dat .= S_RULES_BOTH;
    elseif (SWF_ENABLED)
        $dat .= S_RULES_SWF;
    elseif (WEBM_ENABLED)
        $dat .= S_RULES_WEBM;
    else
        $dat .= S_RULES;
    $dat .= '</div></td></tr></table></form></div></div><a onClick="location.href=location.href" >[' . S_REFRESH . ']</a> <br><hr />   ';
}

function fakefoot()
{
    $dat = '';
    foot($dat);
    return $dat;
}

/* Footer */
function foot(&$dat)
{
    $dat .= "<div class=\"footer\"> <a href ='https://github.com/MrBn100ful/fikaba-4feuilles.org' target ='_blank'> 4Feuilles.org</a> " . S_NAMEVERSION . "<br>" . S_FOOT . "</div></body></html>\n";
}

function error($mes, $dest = '')
{ /* Basically a fancy die() */
    global $upfile_name, $path;
    if (is_file($dest))
        unlink($dest);
    head($dat);
    echo $dat;
    echo "<br /><br /><hr size=1><br /><br />
		<p id='errormsg'>$mes<br /><br /><a href=" . PHP_SELF2 . ">" . S_RETURN . "</a></b></p>
		<br /><br /><hr size=1>";
    foot($dat);
    die("</body></html>\n");
}

function auto_link($proto)
{
    $proto = preg_replace("#(https?|ftp|news|irc|gopher|telnet|ssh)(://[[:alnum:]\+\$\;\?\.%,!\#~*/:@&=_-]+)#", "<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>", $proto);
    return $proto;
}

function proxy_connect($port)
{
    $fp = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $a, $b, 2);
    if (! $fp) {
        return false;
    } else {
        return true;
    }
}

// check version of gd
function get_gd_ver()
{
    if (function_exists("gd_info")) {
        $gdver = gd_info();
        $phpinfo = $gdver["GD Version"];
    }
    $end = strpos($phpinfo, ".");
    $phpinfo = substr($phpinfo, 0, $end);
    $length = strlen($phpinfo) - 1;
    $phpinfo = substr($phpinfo, $length);
    return $phpinfo;
}

/* text plastic surgery */
function CleanStr($str)
{
    $str = trim($str); // blankspace removal
    if (get_magic_quotes_gpc()) { // magic quotes is deleted (?)
        $str = stripslashes($str);
    }
    if (! (isset($_SESSION['cancap']) && ((int) $_SESSION['cancap']) != 0)) {
        $str = htmlspecialchars($str); // remove html special chars
        $str = str_replace("&amp;", "&", $str); // remove ampersands
    }
    return str_replace(",", "&#44;", $str); // remove commas
}

// check for table existance
function table_exist($table)
{
    $result = mysqli_call("show tables like '$table'");
    if (! $result) {
        return 0;
    }
    $a = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $a;
}

function stopsession()
{
    session_unset();
    session_destroy();
}

/* -----------Main------------- */
$ip = $_SERVER['REMOTE_ADDR'];
if (! isset($mode))
    $mode = '';
switch ($mode) {
    case 'regist':
        regist($ip, $name, $capcode, $email, $sub, $com, $oekaki, '', $pwd, $upfile, $upfile_name, $resto);
        break;
    case 'admin':
        if (! isset($pass)) {
            $pass = '';
        }
        valid($pass);
        if (! isset($admin)) {
            $admin = 'del';
        }
        adminhead();
        if ($admin == "del") {
            admindel();
        }
        if ($admin == "ban") {
            adminban();
        }
        if ($admin == "post") {
            if (! $_SESSION['cancap']) {
                die(S_NOPERMISSION);
            }
            form($post, $res, 1, true);
            echo $post;
            die(fakefoot());
        }
        if ($admin == "logout") {
            stopsession();
            echo ("<meta http-equiv=\"refresh\" content=\"0;URL=" . PHP_SELF2 . "\" />");
        }
        if ($admin == "rban") {
            removeban($ip);
        }
        if ($admin == "acc") {
            adminacc($accname, $accpassword, $acccapcode, $accdel, $accban, $acccap, $accacc);
        }
        break;
    case 'banned':
        checkban($ip);
        break;
    case 'catalog':
        catalog();
        break;
    case 'usrdel':
        usrdel($no, $pwd);
    default:
        if (isset($res)) {
            updatelog($res);
        } else {
            updatelog();
            if (! ECHOALL) {
                echo "<meta http-equiv=\"refresh\" content=\"0;URL=" . PHP_SELF2 . "\" />";
            }
        }
}