<?php

function catalog()
{
    $dat = '';
    head($dat);

	$dat .= "<div class=\"ui segment content\">";
	nav($dat);
    form($formdat, 0);


    $dat .= "<div class=\"ui sticky passvalid\"> <a onClick=\"location.href=location.href\" ><button class=\"small ui icon button\"><i class=\"sync icon\"></i></button></a>".$formdat."</div><br />";
    $dat .= "<div class='cattable'>";
    $i = 0;
    $result = mysqli_call("select * from " . POSTTABLE . " order by root desc");

    while ($row = mysqli_fetch_row($result)) {
        list ($no, $now, $name, $email, $sub, $com, $host, $pwd, $ext, $w, $h, $tim, $time, $md5, $fname, $fsize, $root, $resto, $ip) = $row;
        if ((int) $resto == 0) {
			
		    $callreply = mysqli_call("SELECT COUNT(1) FROM " . POSTTABLE . " WHERE resto = " . $no . "");

            $nbreply = mysqli_fetch_row($callreply);

            list ($reply) = $nbreply;
			
			$callreply2 = mysqli_call("SELECT COUNT(1) FROM " . POSTTABLE . " WHERE resto = " . $no . " and ext != ''");

            $imgreply = mysqli_fetch_row($callreply2);

            list ($img) = $imgreply;
			
			mysqli_free_result($callreply);
			mysqli_free_result($callreply2);

			
            $dat .= "<a class='cata' href='index.php?res=$no'><div class='catthread'>";
			$dat .= "<span class='catresponse'>".$reply."r  |  ".$img."i</span><hr><div class=\"catconte\">";
            if ($ext && $ext == ".mp4" || $ext == ".webm") {
                $imgsrc = "<video class='catthumb' src=\"" . IMG_DIR . $tim . $ext . "\" alt=\"" . $fsize . " B\"/></video><br>";
                $dat .= "$imgsrc";
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
                $dat .= "$imgsrc";
            }
			$com = preg_replace("/&gt;/i", ">", $com);
			
			$sub = preg_replace("/&gt;/i", ">", $sub);
			
			if (strlen($sub) > 29) {
                $sub = mb_substr($sub, 0, 29) . "";
            }
			
            if (strlen($com) > 61) {
                $com = mb_substr($com, 0, 59) . "";
            }

            $sub = removenoelshack($sub);
            $com = removenoelshack($com);
			
			$com = strip_tags($com, '<br>');
            $dat .= "</div><hr><span class='cattitle titletxt'>$sub</span><br><span class='catcont'>$com</span></div></a>";

            $i ++;
        }
    }
    mysqli_free_result($result);
    foot($dat);
    echo ($dat);
}