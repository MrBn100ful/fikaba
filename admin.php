<?php

function adminhead()
{
    $list = boardslist(4);
    global $admin;
    head($dat);
    echo $dat;
    echo ("<div class='passvalid'>" . S_MANAMODE . " <a href=\"" . PHP_SELF2 . "\">[" . S_RETURNS . "]</a></div>");
    echo ("<div class='manabuttons'>[<a href='" . PHP_SELF . "'>" . S_LOGUPD . "</a>] ");
    echo ("[<a class='admd$admin' href='" . PHP_SELF . "?mode=admin&admin=del'>" . S_MANAREPDEL . "</a>] ");
    echo ("[<a class='admb$admin' href='" . PHP_SELF . "?mode=admin&admin=ban'>" . S_MANABAN . "</a>] ");
    echo ("[<a class='admp$admin' href='" . PHP_SELF . "?mode=admin&admin=post'>" . S_MANAPOST . "</a>] ");
    echo ("[<a class='adma$admin' href='" . PHP_SELF . "?mode=admin&admin=acc'>" . S_MANAACCS . "</a>] ");
    echo ("[<a href='" . PHP_SELF . "?mode=admin&admin=logout'>" . S_LOGOUT . "</a>]</div>");
    echo ("<div class='manabuttons'>" . $list . "</div>");
}

/* password validation */
function valid($pass)
{
    if (isset($_SESSION['capcode']))
        return;
    head($dat);
    echo $dat;
    echo "<div class='passvalid'>" . S_MANAMODE . " <a href='" . PHP_SELF2 . "'>[" . S_RETURNS . "]</a> </div>";
    if ($pass) {
        $result = mysqli_call("select name,password,capcode,candel,canban,cancap,canacc from " . MANATABLE);
        while ($row = mysqli_fetch_row($result)) {
            list ($adminname, $password, $capcode, $candel, $canban, $cancap, $canacc) = $row;
            if ($pass == $password) {
                $_SESSION["name"] = $adminname;
                $_SESSION["capcode"] = $capcode;
                $_SESSION["candel"] = $candel;
                $_SESSION["canban"] = $canban;
                $_SESSION["cancap"] = $cancap;
                $_SESSION["canacc"] = $canacc;
                echo ("<div class='passvalid'>" . S_MANALOGGEDIN . "</div>");
                echo ("<meta http-equiv=\"refresh\" content=\"2;URL=" . PHP_SELF . "?mode=admin\" />");
                die(fakefoot());
            }
        }
        die(S_WRONGPASS);
        mysqli_free_result($result);
    }

    // Mana login form
    if (! $pass) {
        echo "<br /><div class='centered'><form action=\"" . PHP_SELF . "\" method=\"post\">";
        echo "<input type=hidden name=mode value=admin />";
        echo "<input type=password name=pass size=8>";
        echo "<input type=code name=code size=8>";
        echo "<input type=submit value=\"" . S_MANASUB . "\"></form></div>";
        die(fakefoot());
    }
}

function adminacc($accname, $accpassword, $acccapcode, $accdel, $accban, $acccap, $accacc)
{
    if (! $_SESSION['canacc'])
        die(S_NOPERMISSION);
    if (! $accname) {
        echo ('<div class="centered">');
        echo "<p><form style='display: inline-block;' action=\"" . PHP_SELF . "\" method=\"post\">";
        echo "<input type=hidden name=mode value=admin />";
        echo "<input type=hidden name=admin value=acc />";
        echo "<table><tbody>";
        echo ('<tr><td class="postblock">' . S_NAME . '</td><td><input type="text" size="28" name="accname" />');
        echo (" <input type=submit value=\"" . S_MANASUB . "\" /></td></tr>");
        echo ('<tr><td class="postblock">' . S_DELPASS . '</td><td><input type="password" size="28" name="accpassword" /></td></tr>');
        echo ('<tr><td class="postblock">' . S_CAPCODE . '</td><td><input type="text" size="28" name="acccapcode" value="## Moderator" /></td></tr>');
        echo ('<tr><td class="postblock">' . S_ACCDEL . '</td><td><input type="checkbox" name="accdel" value=1 /></td></tr>');
        echo ('<tr><td class="postblock">' . S_ACCBAN . '</td><td><input type="checkbox" name="accban" value=1 /></td></tr>');
        echo ('<tr><td class="postblock">' . S_ACCCAP . '</td><td><input type="checkbox" name="acccap" value=1 /></td></tr>');
        echo ('<tr><td class="postblock">' . S_ACCACC . '</td><td><input type="checkbox" name="accacc" value=1 /></td></tr>');
        echo ("</tbody></table></form></div>");
        die(fakefoot());
    }
    if (! $accdel)
        $accdel = 0;
    if (! $accban)
        $accban = 0;
    if (! $acccap)
        $acccap = 0;
    if (! $accacc)
        $accacc = 0;
    $query = "insert into " . MANATABLE . " (name,password,capcode,candel,canban,cancap,canacc) values (
        '$accname',
        '$accpassword',
        '$acccapcode',
        $accdel,
        $accban,
        $acccap,
        $accacc)";
    if (! $result = mysqli_call($query)) {
        echo S_SQLFAIL;
    }
    mysqli_free_result($result);
    $query = "delete from " . MANATABLE . " where name='DUMMY' and password='REPLACEME'";
    if (! $result = mysqli_call($query)) {
        echo S_SQLFAIL;
    }
    mysqli_free_result($result);
    die("<p>" . S_ACCCREATED . "</p></body></html>");
}

function adminban()
{
    global $banip, $banexp, $banpubmsg, $banprivmsg, $rmp, $rmallp, $unban;
    if (! $_SESSION['canban'])
        die(S_NOPERMISSION);
    if ($banip != '') {
        if ($banexp == '')
            error(S_BANEXPERROR);
        if (strpos($banip, '.')) {
            $banmode = 1;
        } else {
            $banexp = (int) $banexp;
            $banmode = 0;
        }
        insertban($banip, $banexp, $banpubmsg, $banprivmsg, $banmode, $rmp, $rmallp, $unban); // 0 is IP mode, 1 is post no. mode
        if ($unban) {
            die(S_UNBANSUCCESS);
        } else {
            die(S_BANSUCCESS);
        }
    }
    echo ('<div class="centered">' . "");
    echo "<p><form style='display: inline-block;' action=\"" . PHP_SELF . "\" method=\"post\">";
    echo "<input type=hidden name=mode value=admin />";
    echo "<input type=hidden name=admin value=ban />";
    echo "<table><tbody>";
    echo ('<tr><td class="postblock">' . S_MANABANIP . '</td><td><input type="text" size="28" name="banip" />');
    echo (" <input type=submit value=\"" . S_MANASUB . "\" /></td></tr>");
    echo ('<tr><td class="postblock">' . S_MANABANEXP . '</td><td><input value="7" type="number" size="5" name="banexp" /></td></tr>');
    echo ('<tr><td class="postblock">' . S_MANABANPUBMSG . '</td><td><textarea rows="3" cols="33" name="banpubmsg">' . S_BANNEDMSG . '</textarea></td></tr>');
    echo ('<tr><td class="postblock">' . S_MANABANPRIVMSG . '</td><td><textarea rows="3" cols="33" name="banprivmsg"></textarea></td></tr>');
    echo ('<tr><td class="postblock">' . S_MANARMP . '</td><td><input value="7" type="checkbox" name="rmp" value="on" /></td></tr>');
    echo ('<tr><td class="postblock">' . S_MANARMALLP . '</td><td><input value="7" type="checkbox" name="rmallp" value="on" /></td></tr>');
    echo ('<tr><td class="postblock">' . S_MANAUNBAN . '</td><td><input value="7" type="checkbox" name="unban" value="on" /></td></tr>');
    echo ("</tbody></table></form></div>");
    die(fakefoot());
}

/* Admin deletion */
function admindel()
{
    global $path, $onlyimgdel;
    if (! $_SESSION['candel'])
        die(S_NOPERMISSION);
    $delno = array();
    $delflag = false;
    reset($_POST);
    while ($item = each($_POST)) {
        if ($item[1] == 'delete') {
            array_push($delno, $item[0]);
            $delflag = true;
        }
    }
    if ($delflag) {
        if (! $result = mysqli_call("select * from " . POSTTABLE . "")) {
            echo S_SQLFAIL;
        }
        $find = false;
        while ($row = mysqli_fetch_row($result)) {
            list ($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip) = $row;
            if ($onlyimgdel == 'on') {
                if (array_search($no, $delno) !== false) { // only a picture is deleted
                    $delfile = $path . $tim . $ext; // only a picture is deleted
                    if (is_file($delfile))
                        unlink($delfile); // delete
                    if (is_file(THUMB_DIR . $tim . 's.jpg'))
                        unlink(THUMB_DIR . $tim . 's.jpg'); // delete
                }
            } else {
                if (array_search($no, $delno) !== false) { // It is empty when deleting
                    $find = true;
                    if (! mysqli_call("delete from " . POSTTABLE . " where no=" . $no)) {
                        echo S_SQLFAIL;
                    }
                    $delfile = $path . $tim . $ext; // Delete file
                    if (is_file($delfile))
                        unlink($delfile); // Delete
                    if (is_file(THUMB_DIR . $tim . 's.jpg'))
                        unlink(THUMB_DIR . $tim . 's.jpg'); // Delete
                }
            }
        }
        mysqli_free_result($result);
    }
    // Deletion screen display
    echo "<p><form action=\"" . PHP_SELF . "\" method=\"post\">";
    echo "<input type=hidden name=mode value=admin>";
    echo "<input type=hidden name=admin value=del>";
    echo "<div class=\"delbuttons\"><input type=submit value=\"" . S_ITDELETES . "\">";
    echo "<input type=reset value=\"" . S_RESET . "\"> ";
    echo "[<input type=checkbox name=onlyimgdel value=on><!--checked-->" . S_MDONLYPIC . " ]</div>";
    echo "<table class=\"postlists\">";
    echo "<tr class=\"managehead\">" . S_MDTABLE1;
    echo S_MDTABLE2;
    echo "</tr>";

    if (! $result = mysqli_call("select * from " . POSTTABLE . " order by no desc")) {
        echo S_SQLFAIL;
    }
    $j = 0;
    $all = 0;
    while ($row = mysqli_fetch_row($result)) {
        $j ++;
        $img_flag = false;
        list ($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip, $id) = $row;
        // Format
        $now = preg_replace('#.{2}/(.*)$#', '\1', $now);
        $now = preg_replace('/\(.*\)/', ' ', $now);
        if (strlen($name) > 10)
            $name = substr($name, 0, 9) . "...";
        $name = htmlspecialchars($name);
        if (strlen($sub) > 10)
            $sub = substr($sub, 0, 9) . "...";
        if ($email)
            $name = "<a href=\"mailto:$email\">$name</a>";
        $com = str_replace("<br />", " ", $com);
        $com = htmlspecialchars($com);
        if (strlen($com) > 20)
            $com = substr($com, 0, 18) . "...";
        // Link to the picture
        if ($ext && is_file($path . $tim . $ext)) {
            $img_flag = true;
            $clip = "<a href=\"" . IMG_DIR . $tim . $ext . "\" target=\"_blank\">" . $tim . $ext . "</a><br />";
            $size = $fsize;
            $all += $size;
            $md5 = substr($md5, 0, 10);
        } else {
            $clip = "";
            $size = 0;
            $md5 = "";
        }
        $class = ($j % 2) ? "row1" : "row2"; // BG color

        echo "<tr class=$class><td><input type=checkbox name=\"$no\" value=delete></td>";
        echo "<td>$no</td><td>$now</td><td>$sub</td>";
        echo "<td>$name</td><td>$ip</td><td>$com</td>";
        echo "<td>$host</td><td>$clip($size)</td><td>$md5</td><td>$resto</td><td>$tim</td><td>$time</td>";
        echo "</tr>";
    }
    mysqli_free_result($result);

    echo "</table><input type=submit value=\"" . S_ITDELETES . "\">";
    echo "<input type=reset value=\"" . S_RESET . "\"></form>";

    $all = (int) ($all / 1024);
    echo "[ " . S_IMGSPACEUSAGE . "<b>$all</b> KB ]";
    die(fakefoot());
}

function insertban($target, $days, $pubmsg, $privmsg, $bantype, $rmp, $rmallp, $unban)
{
    $time = time();
    $daylength = 60 * 60 * 24;
    $expires = $time + ($daylength * $days);
    if ($bantype == 0) {
        $result = mysqli_call("select no, ip from " . POSTTABLE);
        while ($row = mysqli_fetch_row($result)) {
            list ($no, $ip) = $row;
            if ($target == (int) $no) {
                $banip = $ip;
                break;
            }
        }
        if (! isset($banip)) {
            die(S_NOSUCHPOST);
        }
    } else {
        $banip = $target;
    }
    mysqli_free_result($result);

    if ($pubmsg && ! $unban) {
        $pubmsg = strtoupper($pubmsg);
        $pubmsg = "<br /><br /><span style=\"color: red; font-weight: bold;\">($pubmsg)</span>";
        $query = "update " . POSTTABLE . "
            set com=concat(com,'$pubmsg')
            where no='$no'";
        if (! $result = mysqli_call($query)) {
            echo S_SQLFAIL;
        }
        mysqli_free_result($result);
    }

    if (! $unban) {
        $query = "insert into " . BANTABLE . " (ip,start,expires,reason) values (
            '$banip',
            '$time',
            '$expires',
            '$privmsg')";
        if (! $result = mysqli_call($query)) {
            echo S_SQLFAIL;
        }
        mysqli_free_result($result);
    } else {
        $query = "delete from " . BANTABLE . " where `ip`='$banip'";
        if (! $result = mysqli_call($query)) {
            echo S_SQLFAIL;
        }
        mysqli_free_result($result);
    }

    if ($rmp && $bantype == 0) {
        $query = "delete from " . POSTTABLE . " where `no`='$target'";
        if (! $result = mysqli_call($query)) {
            echo S_SQLFAIL;
        }
        mysqli_free_result($result);
    }
    if ($rmallp) {
        $query = "delete from " . POSTTABLE . " where `ip`='$banip'";
        if (! $result = mysqli_call($query)) {
            echo S_SQLFAIL;
        }
        mysqli_free_result($result);
    }
}

function isbanned($ip)
{ // check ban, returning true or false
    $result = mysqli_call("select ip, expires from " . BANTABLE);
    $banned = false;
    while ($row = mysqli_fetch_row($result)) {
        list ($bip, $expires) = $row;
        if ($ip == $bip) {
            if ((int) $expires < time()) {
                removeban($ip);
            } else {
                return true;
            }
        }
    }
    mysqli_free_result($result);
    return false;
}

function checkban($ip)
{
    $result = mysqli_call("select * from " . BANTABLE);
    $banned = false;
    while ($row = mysqli_fetch_row($result)) {
        list ($bip, $time, $expires, $reason) = $row;
        if ($ip == $bip) {
            if ((int) $expires < time()) {
                removeban($ip);
                error(S_BANEXPIRED);
            } else {
                error(S_BANNEDMESSAGE . "<br />" . S_BANTIME . humantime($time) . "<br />" . S_BANEXPIRE . humantime($expires));
            }
        }
    }
    if (! $banned) {
        error(S_NOTBANNED . $ip);
    }
    mysqli_free_result($result);
}

function removeban($ip)
{
    $result = mysqli_call("select ip from " . BANTABLE);
    while ($row = mysqli_fetch_row($result)) {
        list ($bip) = $row;
        if ($ip == $bip) {
            $result = mysqli_call("delete from " . BANTABLE . " where `ip` = '" . $ip . "'");
            break;
        }
    }
    mysqli_free_result($result);
}

/* user image deletion */
function usrdel($no, $pwd)
{
    global $path, $pwdc, $onlyimgdel;
    $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    $delno = array();
    $delflag = false;
    reset($_POST);
    while ($item = each($_POST)) {
        if ($item[1] == 'delete') {
            array_push($delno, $item[0]);
            $delflag = true;
        }
    }
    if ($pwd == "" && $pwdc != "")
        $pwd = $pwdc;
    $countdel = count($delno);

    $flag = false;
    for ($i = 0; $i < $countdel; $i ++) {
        if (! $result = mysqli_call("select no,ext,tim,pwd,host from " . POSTTABLE . " where no=" . $delno[$i])) {
            echo S_SQLFAIL;
        } else {
            while ($resrow = mysqli_fetch_row($result)) {
                list ($dno, $dext, $dtim, $dpass, $dhost) = $resrow;
                if (substr(md5($pwd), 2, 8) == $dpass || substr(md5($pwdc), 2, 8) == $dpass || $dhost == $host) {
                    $flag = true;
                    $delfile = $path . $dtim . $dext; // path to delete
                    if (! $onlyimgdel) {
                        if (! mysqli_call("delete from " . POSTTABLE . " where no=" . $dno)) {
                            echo S_SQLFAIL;
                        } // sql is broke
                        if (! mysqli_call("delete from " . POSTTABLE . " where resto=" . $dno)) {
                            echo S_SQLFAIL;
                        } // sql is broke
                    }
                    if (is_file($delfile))
                        unlink($delfile); // Deletion
                    if (is_file(THUMB_DIR . $dtim . 's.jpg'))
                        unlink(THUMB_DIR . $dtim . 's.jpg'); // Deletion
                }
            }
            mysqli_free_result($result);
        }
    }
    if (! $flag)
        error(S_BADDELPASS);
}
