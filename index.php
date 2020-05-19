<?php

// 4feuilles build:200519
//
// For setup instructions and latest version, please visit:
// https://github.com/knarka/fikaba
//
// Based on GazouBBS, Futaba, Futallaby, and Fikaba
const S_NAMEVERSION = 'v2.1';

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
        if (! $treeline = mysqli_call("select * from " . POSTTABLE . " where root>0 and no=" . $resno . " order by root desc")) {
            echo S_SQLFAIL;
        }


    // Finding the last entry number
    if (! $result = mysqli_call("select max(no) from " . POSTTABLE)) {
        echo S_SQLFAIL;
    }
    mysqli_free_result($result);

    $counttree = mysqli_num_rows($treeline);

    for ($page = 0; $page < $counttree; $page += PAGE_DEF) {
        $dat = '';
        head($dat);
		$dat .= "<div class=\"content\">";
		nav($dat);
        form($dat, $resno);
        $st = 0;
        $dat .= '<div class="passvalid"> <a onClick="location.href=location.href" ><button>' . S_REFRESH . '</button></a>  <a href="index.php"><button class="button-full">' . S_RETURNS . '</button></a></div> <br>';

        $p = 0;
        for ($i = $st; $i < $st + PAGE_DEF; $i ++) {
            list ($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip, $id) = mysqli_fetch_row($treeline);
            if (! $no) {
                break;
            }

            if (! $resline = mysqli_call("select * from " . POSTTABLE . " where resto=" . $no . " or no=". $no ." order by no")) {
                echo S_SQLFAIL;
            }
            $countres = mysqli_num_rows($resline);
            $s = 0;
                
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
                
                $fname = substr($fname,0,22);
                $com = auto_link($com);
                $com = preg_replace("/&gt;/i", ">", $com);
                $com = preg_replace("/\>\>([0-9]+)/i", "<a href='index.php?res=$resto#r\\1'>&gt;&gt;\\1</a>", $com);
                $com = preg_replace("/(^|>)(\>[^<]*)/i", "\\1<span class=\"unkfunc\">\\2</span>", $com);
                if (DISP_ID) {
                    $userid = "ID:$id";
                } else {
                    $userid = "";
                }
                // Main creation
                $dat .= "<table id='r$no' class=\"centermsg\"><tr><td class=\"reply\">";
                $dat .= "<span class='intro'> ";
                $dat .= "<span class=\"filetitle\">$name : $sub</span><div class=\"righted\"><a class=\"reflink\" href=\"#\" onClick=\"addref('>>$no');\">Nb:$no</a> &nbsp;</span></div><hr>";

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
                        $dat .= " <span class=\"thumbnailmsg\">" . S_THUMB . "</span>$imgsrc<br>";
                    } else {
                        $dat .= "$imgsrc<br>";
                    }
                }
                $dat .= "<blockquote> $com</blockquote>";
                $dat .= "";
                $dat .= "<hr><div class=\"righted\"><span class=\"commentpostername\">$now</span></div></td></tr></table>";
            }
            clearstatcache(); // clear stat cache of a file
            mysqli_free_result($resline);
            $p ++;
            if ($resno) {
                break;
            } // only one tree line at time of res
        }
        // in res display mode

        $dat .= "<table></table>";
        $dat .= "</div></div>";
        
        foot($dat);
        if ($resno) {
            echo $dat;
            break;
        }
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
    $dat .= '<!doctype html>
<html lang="' . LANGUAGE . '"> 

<link rel="stylesheet" type="text/css" href="https://4feuilles.org/css/4feuilles_claire.css">
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
<script src="/js/4feuilles.js"></script><head>
<script src="/js/risibank.js"></script>
<script src="/js/webring.js"></script>
<script src="https://www.hCaptcha.com/1/api.js" async defer></script>

<meta http-equiv="content-type"  content="text/html;charset=utf-8" />

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

';

    $dat .= '<title>' . TITLE . '</title></head>';

}

function nav(&$dat)
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
	
	
	$pc = boardslist(3);
    
    $mobile = boardslist(2);
	
	$dat .= '<body>
	
    <div class="navbar">
    <div class="menu-normal">
	
    <div class="boardslist">
    <a href="https://4feuilles.org" target="_top"><button>Accueil</button></a>
	'. $pc .'
	</div>
    <div class="webring"></div>
    
    <div class="style">
	Thème: 
	<a href="#" onclick="changeCSS(\'/css/4feuilles_claire.css\', 0);document.cookie=\'theme=/css/4feuilles_claire.css\';"><button>Clair</button></a> 
	
    <a href="#" onclick="changeCSS(\'/css/4feuilles_sombre.css\', 0);document.cookie=\'theme=/css/4feuilles_sombre.css\';"><button>Sombre</button></a>
	</div>

    </div>
	</div>
	
    <div class="menu-mobile"onclick="mobile(this)">
    <div class="navbar">
	<a id="bouton-mobile"><button class="button-full">Menu</button></a>
	<ul id="mobile-cacher">
    <li><a href="https://4feuilles.org" target="_top"><button class="button-mobile">Accueil</button></a></li>
    ' . $mobile . '
    <div class="webring"></div>
	</ul>
	</div>
    </div>
	
	<div class="logo"><br>' . $titlepart . ' <br></div>';


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

    $dat .= $msg . '<div class="centered"><div class="postarea" > ';
   
    if (! $admin) {
        if (! $resno) {
            $dat .= '<a id="bouton-form" onclick="hideform(this)"><button class="button-full">' . S_NEWTHREAD . '</button></a>';
        } else {
            $dat .= '<a id="bouton-form" onclick="hideform(this)"><button class="button-full">' . S_POSTING . '</button></a>';
        }
    } 
    
    $dat .= $msg . '<div id="postarea-hidden">
		<form id="postform" action="index.php" method="post" enctype="multipart/form-data" style="display: inline-block;">
		<input type="hidden" name="mode" value="regist" />
		<input type="hidden" name="MAX_FILE_SIZE" value="' . $maxbyte . '" />';
    if ($no) {
        $dat .= '<input type="hidden" name="resto" value="' . $no . '" />';
    }
    $dat .= '<table>';

    if (! FORCED_ANON || $admin)
        // $dat.='<tr><td class="postblock">'.S_NAME.'</td><td><input type="text" name="name" value="" placeholder="Anonymous';
        if ($manapost)
            $dat .= $_SESSION['name'];
    // $dat .= '" size="35" /></td></tr>';
    if ($admin && $_SESSION['cancap']) {
        $dat .= '<tr><td class="postblock">' . S_CAPCODE . '</td><td>(' . $_SESSION['capcode'] . ')</td></tr>
		<tr><td class="postblock">' . S_REPLYTO . '</td><td><input type="text" name="resto" size="35" value="0" /></td></tr>';
    }
    $dat .= '<tr><td class="postblock">' . S_SUBJECT . '</td><td><input type="text" name="sub"  />
	<input type="submit" value="' . S_SUBMIT . '" /></td></tr>
	<tr><td class="postblock">' . S_COMMENT . '</td><td><textarea id="com" name="com" ></textarea> 
<br>
  <a href="javascript:void(0)" onclick="RisiBank.activate(\'com\')">
    <img class="risibank" style="width: 120px" src="https://risibank.fr/src/picts/banner-light.png">
  </a> 
</td></tr>';
    $dat .= '<tr id="filerow"><td class="postblock">' . S_UPLOADFILE . '</td>
<td> <div id="file-selector"><label for="file" class="label-file"><input type="file" id="file" class="input-file" name="upfile" accept=".jpg, .jpeg, .png, .gif, .mp4, .webm" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path fill="none" d="M0 0h24v24H0z"/><path d="M4 19h16v-7h2v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-8h2v7zM14 9v6h-4V9H5l7-7 7 7h-5z" fill="rgba(255,255,255,1)"/></svg> <span class="file-name"> Aucune image sélectionnée</span>   </label></div>  ';
    
    $dat .='<tr><td class="postblock">' . S_VERIF . '</td><td><div class="h-captcha" data-sitekey="' . HCAPTSITEKEY . '"></div></td></tr>';
    
   
    // $dat .= '</td></tr><tr><td class="postblock">' . S_DELPASS . '</td><td><input type="password" name="pwd" maxlength="8" value="" /> ' . S_DELEXPL . '</td></tr>
    // <tr><td colspan="2">
     $dat .= ' </td></tr><tr><tr><td colspan="2"><div class="rules lefted">';
    if (SWF_ENABLED && WEBM_ENABLED)
        $dat .= S_RULES_BOTH;
    elseif (SWF_ENABLED)
        $dat .= S_RULES_SWF;
    elseif (WEBM_ENABLED)
        $dat .= S_RULES_WEBM;
    else
        $dat .= S_RULES;
    $dat .= '</div></td></tr></table></form></div></div></div><br>   ';
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
    $dat .= "<div class=\"footer\"> <a href ='https://github.com/MrBn100ful/4feuilles' target ='_blank'> 4Feuilles.org</a> " . S_NAMEVERSION . "<br>" . S_FOOT . "</div></body></html>\n";
}

function error($mes, $dest = '')
{ /* Basically a fancy die() */
    global $upfile_name, $path;
    if (is_file($dest))
        unlink($dest);
    head($dat);
    echo $dat;
    echo "<br /><br /><br /><br />
		<p id='errormsg'>$mes<br /><br /><a href=" . PHP_SELF2 . ">" . S_RETURN . "</a></b></p>
		<br /><br />";
    foot($dat);
    die("</body></html>\n");
}

function auto_link($proto)
{
    $proto = preg_replace("~https?://(?![^' <>]*(?:jpg|png|gif|jpeg))[^' <>]+~", "<a href=\"$0\" target=\"_blank\">$0</a>", $proto);
    
    $proto = preg_replace("/(http:\/\/|https:\/\/)[^\s]+(.png|.jpg|.gif|jpeg)/", " <a href=\"$0\"><img class=\"sticker\" src=\"$0\" ></a>", $proto);
    
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
        regist($ip, $name, $capcode, $email, $sub, $com, $oekaki, '', $pwd, $upfile, $upfile_name, $resto,$_POST['h-captcha-response']);
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
    case 'usrdel':
        usrdel($no, $pwd);
    default:
        if (isset($res)) {
            updatelog($res);
        } else {
            catalog();
            break;
        }
}