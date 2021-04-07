<?php

// 4Feuilles build:210406
//
// For setup instructions and latest version, please visit:
// https://github.com/MrBn100ful/4feuilles
//
// Based on GazouBBS, Futaba, Futallaby, Fikaba
const S_NAMEVERSION = 'v3.2.4';

include 'config.php';
include 'boardslist.php';
include 'catalog.php';
// include 'forum.php';
include 'post.php';
include 'admin.php';
include 'preview.php';
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
		$dat .= "<div class=\"ui segment content\">";
		nav($dat);
        form($formdat, $resno);
        $st = 0;
        $dat .= '<div class="ui sticky passvalid"><a href="index.php"><button class=" small ui button">' . S_RETURNS . '</button></a> <a onClick="location.href=location.href" ><button class="small ui icon button"><i class="sync icon"></i></button></a>'.$formdat.'</div> <br>';

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
                $com = preg_replace("/\>\>([0-9]+)/i", "<a id='tag\\1' href='index.php?res=$resto#r\\1'>&gt;&gt;\\1</a>", $com);
                $com = preg_replace("/(^|>)(\>[^<]*)/i", "\\1<span class=\"unkfunc\">\\2</span>", $com);
                if (DISP_ID) {
                    $userid = "ID:$id";
                } else {
                    $userid = "";
                }
                // Main creation
                $dat .= "<div id='r$no' class=\"centermsg reply \">";
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

<link rel="stylesheet" type="text/css" href="https://' . WEBSITEURL . '/css/4feuilles.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://' . WEBSITEURL . '/js/jquery.ui.touch-punch.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.6/dist/semantic.min.css">
<script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.6/dist/semantic.min.js"></script>
<script src="https://' . WEBSITEURL . '/js/4feuilles.js"></script><head>
<script src="https://' . WEBSITEURL . '/js/risibank.js"></script>
<script src="https://www.hCaptcha.com/1/api.js" async defer></script>

<meta http-equiv="content-type"  content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">

';

    $dat .= '<title>' . TITLE . '</title></head>';

}

function nav(&$dat)
{
    $titlepart = '';
    if (SHOWTITLEIMG == 1) {
        $titlepart .= '<img src="' . TITLEIMG . '" alt="' . TITLE . '" /><br>';
        if (SHOWTITLETXT == 1) {
            $titlepart .= '<br />';
        }
    } elseif (SHOWTITLEIMG == 2) {
        $titlepart .= '';
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
	
    <div class="navbar" id="navpc">
    <div class="menu-normal">
	
    <div class="boardslist">
    <div class="ui secondary menu stackable inverted">
    <a class="item active" href="https://' . WEBSITEURL . '" target="_top">Accueil</a>
	'. $pc .'
	<div class="right menu">
	    <a class="item">
                <i class="moon outline icon"></i>
                <div class="ui theme toggle checkbox inverted" id="theme-switcher">
                    <input type="checkbox">
                </div>
                <i class="moon icon"></i>
        </a>
	</div>

	</div>

    </div>
	</div>

    </div>
    
    <div class="menu-mobile" id="navmobile">
    <div class="navbar" >
	<a id="bouton-mobile"><button class="ui small  button primary" onclick="mobile(\'menu-mobile\')">Menu</button></a>
	<div id="mobile-cacher">
      <div class="ui secondary center stackable menu inverted">
    <a class="item active" href="https://' . WEBSITEURL . '" target="_top">Accueil</a>
	'. $pc .'
	<div class="right menu">
	    <a class="item">
                <i class="moon outline icon"></i>
                <div class="ui theme toggle checkbox inverted" id="theme-switcher">
                    <input type="checkbox">
                </div>
                <i class="moon icon"></i>
        </a>
	</div>
	</div>
	</div>
	</div>
    </div>
    <div class="logo">'.$titlepart.' <br></div><br>
    ';


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

    //$dat .= $msg . '<div class="centered"><div class="postarea" >';
   
    if (! $admin) {
        if (! $resno) {
            $dat .= '<a id="bouton-form" onclick="hideform(this)"><button class="ui small labeled icon button primary"><i class="comments outline icon"></i>' . S_NEWTHREAD . '</button></a>';
        } else {
            $dat .= '<a id="bouton-form" onclick="hideform(this)"><button class="ui small labeled icon button primary"><i class="comment outline icon"></i>' . S_POSTING . '</button></a>';
        }
    }
	if ($admin && $_SESSION['cancap']) {
		$dat .= '<a id="bouton-form" onclick="hideform(this)"><button class="ui small labeled icon button primary"><i class="comment outline icon"></i>' . S_POSTING . '</button></a>';
	}
    
    $dat .= $msg . '<div id="postarea-hidden">
		<div id="draggable" class="ui-widget-content" style="position: fixed; left:calc(50vw - 168px);"><div class="titlebar">Nouveau message<a id="bouton-form" class="righted" onclick="hideform(\'.bouton-form\')"><button class="mini ui compact icon primary button"><i class="times icon"></i></button></a></div>
		<form id="form-board" class="ui form" action="index.php" method="post" enctype="multipart/form-data" style="display: inline-block;">
		<input type="hidden" name="mode" value="regist" />
		<input type="hidden" name="MAX_FILE_SIZE" value="' . $maxbyte . '" />';
    if ($no) {
        $dat .= '<input type="hidden" name="resto" value="' . $no . '" />';
    }

    if ($admin && $_SESSION['cancap']) {
        $dat .= '<tr><td class="postblock">' . S_CAPCODE . '</td><td>(' . $_SESSION['capcode'] . ')</td></tr>
		<tr><td class="postblock">' . S_REPLYTO . '</td><td><input type="text" name="resto" value="0" /></td></tr>';
    }
    $dat .= '<input type="text" name="sub" placeholder="Titre"/>      
	        <div class="ui icon menu bottommargin">
                <a class="item" onclick="styletext(\'!b!\')">
                     <i class="bold icon"></i>
                </a>
                <a class="item" onclick="styletext(\'!i!\')">
                     <i class="italic icon"></i>
                </a>
                <a class="item" onclick="styletext(\'!u!\')">
                     <i class="underline icon"></i>
                </a>
                <a class="item" onclick="styletext(\'!s!\')">
                     <i class="eye slash icon"></i>
                </a>
                <a class="item" href="javascript:void(0)" onclick="RisiBank.activate(\'com\')">
                    <img class="risibank" style="width: 55px; " src="https://risibank.fr/src/picts/banner-light.png">
                </a>
                 <a class="item">
                    <i class="smile icon"></i>
                </a>
            </div>
	        <textarea id="com" name="com" placeholder="Message"></textarea> <br>
<div class="ui labeled button topmargin" tabindex="0">
  <div class="ui icon button"><i class="photo video icon"></i></div>
    <a class="ui basic label"><div id="file-selector"><label for="file" class="label-file"><input type="file" id="file" class="input-file" name="upfile" accept=".jpg, .jpeg, .png, .gif, .mp4, .webm" /> <span class="file-name"> Aucune image sélectionnée</span></label></div> </a>
</div>
<button class="ui small button primary" type="submit" id="submitbtn" >' . S_SUBMIT . '</button>
 ';
    
    $dat .='<tr><td><div class="h-captcha" data-sitekey="'+ HCAPTCHASECRET +'" data-callback="onSubmit" data-size="invisible" ></div></td></tr>';

     $dat .= ' </td></tr><tr><tr><td colspan="2"><div class="rules lefted">';

     //$dat .= S_RULES_WEBM;

    $dat .= '</form></div></div></div>';
}

/* Footer */
function foot(&$dat)
{
    $dat .= "</div></div><div class=\"footer\"> <a href ='https://github.com/MrBn100ful/4feuilles' target ='_blank'> 4Feuilles.org</a> " . S_NAMEVERSION . "<br>" . S_FOOT . "</div></body></html>\n";
}

function error($mes, $dest = '')
{ /* Basically a fancy die() */
	$dat = '';
    global $upfile_name, $path;
    if (is_file($dest))
        unlink($dest);
    head($dat);
	$dat .= "<div class=\"content\">";
	nav($dat);
    $dat .= "<div class=\"centered\"> <br /><br />
		<div class=\"ui negative message\" style=\"max-width: 500px;margin: auto;\">
  			<div class=\"header\">
    			$mes
  			</div>
		</div>
		<br /><a href=\"index.php\"><button class=\"small ui icon button\">" . S_RETURN . "</button></a></b>";
	$dat .= "</div></div></div>";
    foot($dat);
	echo $dat;
    die("</body></html>\n");
}

function auto_link($proto)
{
    $proto = preg_replace("~https?://(?![^' <>]*(?:jpg|png|gif|jpeg))[^' <>]+~", "<a href=\"$0\" target=\"_blank\">$0</a>", $proto);
    
    $proto = preg_replace("/(http:\/\/|https:\/\/)[^\s]+(.png|.jpg|.gif|jpeg)/", " <a href=\"$0\"><img class=\"sticker\" src=\"$0\" ></a>", $proto);

    $proto = preg_replace('/!b!(.*?)!b!/', '<b>$1</b>', $proto);

    $proto = preg_replace('/!i!(.*?)!i!/', '<i>$1</i>', $proto);

    $proto = preg_replace('/!u!(.*?)!u!/', '<u>$1</u>', $proto);

    $proto = preg_replace('/!s!(.*?)!s!/', '<div class="spoiler">$1</div>', $proto);

    $urlimg = "https://4feuilles.org/img/";

    $txt = array(":)", ":hap:", ":rire:", ":ok:", ":(", ":malade:", ":bave:");

    $img   = array("<img class=\"smiley\" src=\"{$urlimg}smile.png\" >", "<img class=\"smiley\" src=\"{$urlimg}hap.png\" >", "<img class=\"smiley\" src=\"{$urlimg}rire.png\" >", "<img class=\"smiley\" src=\"{$urlimg}ok.png\" >", "<img class=\"smiley\" src=\"{$urlimg}sceptique.png\" >", "<img class=\"smiley\" src=\"{$urlimg}malade.png\" >", "<img class=\"smiley\" src=\"{$urlimg}bave.png\" >");

    $proto = str_replace($txt, $img, $proto);

    return $proto;
}
function removenoelshack($proto)
{
    $proto = preg_replace("~https?://image.noelshack.com(?![^' <>]*(?:jpg|png|gif|jpeg))[^' <>]+~", "", $proto);
    $proto = preg_replace("~http?://image.noelshack.com(?![^' <>]*(?:jpg|png|gif|jpeg))[^' <>]+~", "", $proto);

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

/* text plastic surgery */
function CleanStr($str)
{
    $str = trim($str); // blankspace removal
        $str = stripslashes($str);
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
	case 'preview':
        preview($nbpost);
		break;
    default:
        if (isset($res)) {
            updatelog($res);
        } else {
            catalog();
            break;
        }
}