<?php

	/*****************************************************************************
	IMP : NO OTHER FILE SHOULD BE INCLUDED IN THIS FILE
	******************************************************************************/

    function SetReturnURL($admin = true)
    {
        if($admin)
        {
            $_SESSION['UserReturnURL'] = $_SERVER['REQUEST_URI'];
        }
        else
        {
            $_SESSION['ReturnURL'] = $_SERVER['REQUEST_URI'];
        }
    }

    function GoToLastPage($admin = true)
    {
        if($admin)
        {
            return $_SESSION['UserReturnURL'];
        }
        else
        {
            return $_SESSION['ReturnURL'];
        }
    }

    function TotalRows($query = '', $column = '', $ReturnRecords = false)
    {
        if($query != '' && $column != '')
        {
            //  FIRST LETS REMOVE STRING FROM 'ORDER BY' ONWARDS IF IT EXISTS
            if(stripos($query, 'ORDER BY', 0) !== false)
            {
                $query = substr($query, 0, stripos($query, ' ', stripos($query, 'ORDER BY', 0) - 1));
            }
            $RemoveSelectedColumns = substr($query, 7, stripos($query, ' FROM', 0) - 7);
            $query = str_replace($RemoveSelectedColumns, $column, $query);
            if($ReturnRecords)
            {
                $result = MysqlQuery($query);
                if(mysqli_num_rows($result) >= 0)
                {
                    return $result;
                }
                else
                {
                    return mysqli_error();
                }
            }
            else
            {
                $rows = @mysqli_num_rows(MysqlQuery($query));
                if($rows >= 0)
                {
                    return $rows;
                }
                else
                {
                    return mysqli_error();
                }
            }
        }
        elseif($column == '')
        {
            return 'DEFAULT COLUMN NOT SET';
        }
        else
        {
            return false;
        }
    }

    function redirect($page, $PopupMessage = '')
    {
        if($PopupMessage != '')
        {
            $_SESSION['AlertMessage'] = $PopupMessage;
        }
        header('Location: '.$page);
        exit;
    }

    function LogError($priority, $error, $LineNo = 0, $MysqlQuery = '')
    {
        $log = array();
        $log['Priority'] = $priority;
        $log['Error'] = $error;
        $log['LineNo'] = $LineNo;
        $log['ScriptPath'] = $_SERVER['PHP_SELF'];
        $log['MysqlQuery'] = $MysqlQuery;
        $log['Added'] = time();
        //$log = json_encode($log).PHP_EOL;

        $fh = fopen(_ROOT.'/error-logs/'.date('Y-M').'.csv', 'a');
        fputcsv($fh, $log);
        fclose($fh);

        //  Let's also send an email to the website administrator/develop an email if it's a critical failure
        if($priority <= 1)
        {
            $MailContent = '<table cellpadding="5" cellspacing="1" style="min-width:400px;background:#ddd;">';
            $MailContent .=     '<tr style="background:#fff;">';
            $MailContent .=         '<td>Priority</td>';
            $MailContent .=         '<td>'.$priority.' (Critical)</td>';
            $MailContent .=     '</tr>';
            $MailContent .=     '<tr style="background:#fff;">';
            $MailContent .=         '<td>Error</td>';
            $MailContent .=         '<td>'.$error.'</td>';
            $MailContent .=     '</tr>';
            $MailContent .=     '<tr style="background:#fff;">';
            $MailContent .=         '<td>Line No.</td>';
            $MailContent .=         '<td>'.$LineNo.'</td>';
            $MailContent .=     '</tr>';
            $MailContent .=     '<tr style="background:#fff;">';
            $MailContent .=         '<td>Script</td>';
            $MailContent .=         '<td>'.$_SERVER['PHP_SELF'].'</td>';
            $MailContent .=     '</tr>';
            $MailContent .=     '<tr style="background:#fff;">';
            $MailContent .=         '<td>Mysql Query</td>';
            $MailContent .=         '<td>'.$MysqlQuery.'</td>';
            $MailContent .=     '</tr>';
            $MailContent .= '</table>';
            SendMailHTML(_AdminEmail, 'Critical Error : '._HOST, $MailContent);
        }
    }

    function ValidateGSTIN($gstin)
    {
        $regex = "/^([0][1-9]|[1-2][0-9]|[3][0-5])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/";
        return preg_match($regex, $gstin);
    }

    function ValidateEmail($Email)
    {
        if(!preg_match( "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $Email))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function ValidateMobile($Mobile)
    {
        if(!preg_match( "/^[1-9][0-9]{9,15}$/", $Mobile))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function PrettyPrint($str)
    {
        if(is_array($str))
        {
            // Do nothing
        }
        else
        {
            $str = json_decode($str);
        }
        printf("<pre>%s</pre>", json_encode($str, JSON_PRETTY_PRINT));
    }

    function SanitizePOST($var)
    {
        if(is_array($var))
        {
            return array_map('SanitizePOST', $var);
        }
        else
        {
            while (strpos($var, '  ') !== false)
            {
                $var = str_replace('  ', ' ', $var);
            }
            return addslashes(trim($var));
        }
    }

    function cURLRequest($request_url, $methodPOST = false)
    {
        if($methodPOST)
        {
            $url_string = explode('?', $request_url);
            $request_url = $url_string[0];
            $params = $url_string[1];

            $ch = curl_init($request_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	// to stop verifying the SSL Certificate
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Content-Length: '.strlen($params)));
            $response = curl_exec($ch);
            curl_close($ch);
        }
        else
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	// to stop verifying the SSL Certificate
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }
        return $response;
    }

    function BulkInsert($TargetTable, $fields, $ValuesToInsert, $MaxInserts = 5000)
    {
        //  Return no. of successful inserts or the error
        $FieldCount = count(explode(',', $fields));
        if($FieldCount == 0)
        {
            return 'No fields found!';
        }
        elseif($FieldCount != count(array_filter(explode(',', $fields))))
        {
            return 'Invalid field found!';
        }
        //  Let's start builing the query
        $InsertQuery = "INSERT INTO ".$TargetTable." (".$fields.") VALUES ";

        foreach($ValuesToInsert as $values)
        {
            $InsertValues[] = "(".$values.")";
        }

        $InsertResult = false;
        while(count($InsertValues) > 0)
        {
            $ValuesToInsert = array_splice($InsertValues, 0, $MaxInserts);
            $QueryResult = MysqlQuery($InsertQuery.implode(',', $ValuesToInsert));
            $NoOfInserts = MysqlAffectedRows();
            if($NoOfInserts > 0)
            {
                $InsertResult = true;
                $TotalInserts = $TotalInserts + $NoOfInserts;
            }
            else
            {
                $InsertResult = false;
                break;  //  Let's stop further inserts on error
            }
        }
        return $InsertResult ? $TotalInserts : $QueryResult;    //  Return the no of rows affected or the mysql error in case of failure
    }

    function CleanText($input)
    {
        return preg_replace('!\s+!', ' ', trim($input));
    }

    function DeSanitizeVar($var)
    {
        if(is_array($var))
        {
            return array_map('DeSanitizeVar', $var);
        }
        else
        {
            return stripslashes(htmlentities($var));
        }
    }

    function AlphaNumericCode($l, $alpha = false)
    {
        $AlphaNumbers = $alpha ? array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z") : array("1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z","1","2","3","4","5","6","7","8","9");
        for($code = '', $c = 1; $c <= $l; $c++)
        {
            $code .= $AlphaNumbers[mt_rand(0, count($AlphaNumbers) - 1)];
        }
        return $code;
    }

    function GetBrowser()
    {
        return substr($_SERVER['HTTP_USER_AGENT'], 0, 200);
    }

    //	FUNCTION TO DISPLAY DATE AND TIME
    function FormatDateTime($format, $t = '')
    {
        $t = $t == '' ? time() : $t;
        
        if(is_numeric($t) && $t > 0)
        {
            if($format == "dm")     //  for displaying only day and month
                return date("jS M", $t);
            elseif($format == "d")
                return date("jS M Y", $t);
            elseif($format == "dayd")
                return date("l, jS M Y", $t);
            elseif($format == "dt")
                return date("jS M Y, g:ia", $t);
            elseif($format == "dts")
                return date("jS M Y, g:i:sa", $t);
            elseif($format == "dtz")
                return date("jS M Y, g:ia T", $t);
            elseif($format == "t")
                return date("g:ia", $t);
            elseif($format == "api")
                return date("Y-m-d H:i:s", $t);
        }
        else
        {
            return 'NA';
        }
    }

    function GetIP()
    {
        return substr($_SERVER['REMOTE_ADDR'], 0, 40);  //  40 characters should be sufficient enough for ipv6
    }

    function ValidatePassword($str)
    {
        if(strpos($str, ' ', 0) !== false || CleanText($str) == '')
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function GenerateHash($rounds = 7)
    {
        $salt = "";
        $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
        for($i=0; $i < 22; $i++) {
          $salt .= $salt_chars[array_rand($salt_chars)];
        }
        return crypt(microtime(), sprintf('2a$%02d', $rounds) . $salt);
    }

    function SendMailHTML($ToEmail, $subject, $body, $AttachmentPath = '', $ReplyToEmail = '', $ReplyToName = '', $CCEmail = '')
    {
        if($ReplyToEmail == '')
        {
            global $SiteSettings;
            $ReplyToEmail = $SiteSettings['email_support'];
            $ReplyToName = _WebsiteName;
        }

        $AttachmentPath = array_filter(explode(',', $AttachmentPath));
        $ToEmail = array_filter(explode(',', $ToEmail));
        $ToEmail = array_map('trim', $ToEmail);
        $CCEmail = array_filter(explode(',', $CCEmail));
        $CCEmail = array_map('trim', $CCEmail);

        //Create a new PHPMailer instance
        $mail = new PHPMailer();
        //Tell PHPMailer to use SMTP
        $mail->IsSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug  = 0;
        //Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $mail->Host       = _SENDER_MAIL_HOST;
        //Set the SMTP port number - likely to be 25, 465 or 587
        $mail->Port       = _SENDER_MAIL_SMTP_PORT;
        //Set the protocol for sending request
        $mail->SMTPSecure = _SENDER_MAIL_SMTP_PROTOCOL;
        //Whether to use SMTP authentication
        $mail->SMTPAuth   = true;
        //Username to use for SMTP authentication
        $mail->Username   = _SENDER_MAIL_USER;
        //Password to use for SMTP authentication
        $mail->Password   = _SENDER_MAIL_PWD;
        //Set who the message is to be sent from
        $mail->SetFrom(_SENDER_MAIL_ID, _WebsiteName);
        //Set an alternative reply-to address
        $mail->AddReplyTo($ReplyToEmail, $ReplyToName);

        //Set who the message is to be sent to
        for($MailCounter = 0; $MailCounter < count($ToEmail); $MailCounter++)
        {
            $mail->AddAddress($ToEmail[$MailCounter], '');
        }

        //Set who the message is to be sent to
        for($MailCounter = 0; $MailCounter < count($CCEmail); $MailCounter++)
        {
            $mail->AddCC($CCEmail[$MailCounter], '');
        }

        //  LETS ADD ATTACHMENTS
        for($AttachmentCounter = 0; $AttachmentCounter < count($AttachmentPath); $AttachmentCounter++)
        {
            $mail->AddAttachment($AttachmentPath[$AttachmentCounter]);
        }

        //Set the subject line
        $mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
        $mail->MsgHTML($body);
        //Replace the plain text body with one created manually
        $mail->AltBody = '';

        //Send the message, check for errors
        if(!$mail->Send())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function GetParams($drop = '', $add = '', $glue = false, $url = '')
    {
        //  ASSIGN THE "REQUEST_URI" AS URL IF NO URL IS PASSED
        $url = $url != '' ? $url : $_SERVER['REQUEST_URI'];

        $drop = array_filter(explode(',', $drop));

        //  Let's also add the parameters to be "added" as older values must be dropped before adding new ones
        $ToBeAddedToDrop = explode('&', $add);
        foreach($ToBeAddedToDrop as $val)
        {
            $val = explode('=', $val);
            $drop[] = $val[0];
        }
        
        $baseUrl = explode('?', $url);
        $params = $baseUrl[1];
        $baseUrl = $baseUrl[0];

        $params = explode('&', $params);
        $ParamArray = array();
        for($c = 0; $c < count($params); $c++)
        {
            $value = explode('=', $params[$c]);
            if($value[1] != '' && @!in_array($value[0], $drop))
            {
                $ParamArray[] = $value[0].'='.$value[1];
            }
        }
        $params = implode('&', $ParamArray);
        $params = $params != '' ? $params : '';
        $params = $params != '' && $add != '' ? $params.'&'.$add : ($add != '' ? $add : $params);

        if($params != '')
        {
            return $glue ? '?'.$params : $params;
        }
    }

    function CreateThumbnail($ImagePath, $ThumbDir, $MaxAllowedWidth = 200, $WatermarkPath = '')
    {
        $file = strrchr($ImagePath, '/');

        list($currwidth, $currheight, $type, $attr) = getimagesize($ImagePath);
        if($currwidth > $MaxAllowedWidth)
        {
            $ImageRatio = $currheight/$currwidth;
            $NewHeight = RoundOff($MaxAllowedWidth*$ImageRatio,0);
            $MaxAllowedWidth = $MaxAllowedWidth;
            $NewHeight = $NewHeight;
        }
        else
        {
            $NewHeight = $currheight;
            $MaxAllowedWidth = $currwidth;
        }

        if($WatermarkPath != '')
        {
            $watermark = imagecreatefrompng($WatermarkPath);
        }

        // Set the margins for the stamp and get the height/width of the watermark image
        $marge_right = 10;
        $marge_bottom = 10;
        $sx = @imagesx($watermark);
        $sy = @imagesy($watermark);

        if(strrchr($file,".") == ".jpg" || strrchr($file,".") == ".JPG" || strrchr($file,".") == ".jpeg" || strrchr($file,".") == ".JPEG")
        {
            $simg = @imagecreatefromjpeg($ImagePath);   // Make A New Temporary Image To Create The Thumbanil From
            if($WatermarkPath != '')
            {
                imagecopy($simg, $watermark, imagesx($simg) - $sx - $marge_right, imagesy($simg) - $sy - $marge_bottom, 0, 0, imagesx($watermark), imagesy($watermark));
            }
            $dimg = imagecreatetruecolor($MaxAllowedWidth, $NewHeight);   // Make New Image For Thumbnail
            @imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $MaxAllowedWidth, $NewHeight, $currwidth, $currheight);

            if(imagejpeg($dimg, $ThumbDir.$file, 90))   // Saving The Image
                $counter++;
            @imagedestroy($simg);   // Destroying The Temporary Image
            @imagedestroy($dimg);   // Destroying The Other Temporary Image
        }
        elseif(strrchr($file,".") == ".png" || strrchr($file,".") == ".PNG")
        {
            $simg = imagecreatefrompng($ImagePath);   // Make A New Temporary Image To Create The Thumbanil From
            if($WatermarkPath != '')
            {
                imagecopy($simg, $watermark, imagesx($simg) - $sx - $marge_right, imagesy($simg) - $sy - $marge_bottom, 0, 0, imagesx($watermark), imagesy($watermark));
            }
            $dimg = imagecreatetruecolor($MaxAllowedWidth, $NewHeight);   // Make New Image For Thumbnail

            imagealphablending($dimg, false);
            $colorTransparent = imagecolorallocatealpha($dimg, 0, 0, 0, 127);
            imagefill($dimg, 0, 0, $colorTransparent);
            imagesavealpha($dimg, true);

            imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $MaxAllowedWidth, $NewHeight, $currwidth, $currheight);

            if(imagepng($dimg, $ThumbDir.$file, 5))   // Saving The Image
                $counter++;
            imagedestroy($simg);   // Destroying The Temporary Image
            imagedestroy($dimg);   // Destroying The Other Temporary Image
        }
        elseif(strrchr($file,".") == ".gif" || strrchr($file,".") == ".GIF")
        {
            $simg = imagecreatefromgif($ImagePath);   // Make A New Temporary Image To Create The Thumbanil From
            if($WatermarkPath != '')
            {
                imagecopy($simg, $watermark, imagesx($simg) - $sx - $marge_right, imagesy($simg) - $sy - $marge_bottom, 0, 0, imagesx($watermark), imagesy($watermark));
            }
            $dimg = imagecreatetruecolor($MaxAllowedWidth, $NewHeight);   // Make New Image For Thumbnail
            imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $MaxAllowedWidth, $NewHeight, $currwidth, $currheight);

            if(imagegif($dimg, $ThumbDir.$file))   // Saving The Image
                $counter++;
            imagedestroy($simg);   // Destroying The Temporary Image
            imagedestroy($dimg);   // Destroying The Other Temporary Image
        }

        if(file_exists($ThumbDir.$file))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function CreateNavs($NoOfRecords, $PerPage, $NavName, $NoOfNavs = 5, $message = '', $ShowTotalPages = true, $link = '', $hash = '')
    {
        /************ FIRST FIND THE FORMAT THE URL TO BE LINKED FOR NAVS ***********/
        if($link == '')
        {
            $url = $_SERVER['REQUEST_URI'];

            $baseUrl = explode('?', $url);
            $params = $baseUrl[1];
            $baseUrl = $baseUrl[0];

            $params = explode('&', $params);
            $ParamArray = array();
            for($c = 0; $c < count($params); $c++)
            {
                $value = explode('=', $params[$c]);
                if($value[1] != '' && strpos($value[0], $NavName, 0) === false)
                {
                    $ParamArray[] = $value[0].'='.$value[1];
                }
            }
            $params = implode('&', $ParamArray);
            if($params != '')
            {
                $link = $baseUrl.'?'.$params;
            }
            else
            {
                $link = $baseUrl;
            }
        }
        $link = strpos($link, '?', 0)?$link.'&':$link.'?';

        /************ START CREATING NAVS ***********/
        $CurrentPage = !is_numeric($_GET[$NavName])?1:$_GET[$NavName];	//	SET DEFAULT PAGE VALUE AS 1 IF PASSES VALUE IS NON-NUMERIC
        $TotalPages = ceil($NoOfRecords/$PerPage);
        $CurrentPage = $CurrentPage;
        $prev = $CurrentPage - 1;
        $next = $CurrentPage + 1;
        $navs = '';

        if($NoOfRecords > $PerPage)
        {
            $StartNav = $CurrentPage - ceil($NoOfNavs/2) <= 1 ? 2 : $CurrentPage - ceil($NoOfNavs/2);
            if($TotalPages - $StartNav < $NoOfNavs && $TotalPages > $NoOfNavs + 1)
                $StartNav = $StartNav - 1;
            if($CurrentPage == $TotalPages && $TotalPages > $NoOfNavs)
                $StartNav = $CurrentPage - $NoOfNavs == 1 ? 2 : $CurrentPage - $NoOfNavs;

                        $navs .= '<div class="navs-holder">';

            $navs .= '<A class="fixedNav" href="'.$link.$NavName.'=1'.($hash != '' ? '#'.$hash : '').'"><SPAN class="font21" style="color:#1776b5">&laquo;</SPAN><SPAN class="font15" style="color:#1776b5"> First</SPAN></A>';

            if($CurrentPage == 1)
            {
                $navs .= '<SPAN class="navsOn">1</SPAN>';
            }
            else
            {
                $navs .= '<A class="navs" href="'.$link.$NavName.'=1'.($hash != '' ? '#'.$hash : '').'">1</A>';
            }

            for($NavCounter = 1; $NavCounter <= $NoOfNavs && $StartNav < $TotalPages; $NavCounter++, $StartNav++)
            {
                if($StartNav == $CurrentPage)
                {
                    $navs .= '<SPAN class="navsOn">'.$CurrentPage.'</SPAN>';
                }
                else
                {
                    $navs .= '<A class="navs" href="'.$link.'page='.$StartNav.($hash != '' ? '#'.$hash : '').'">'.$StartNav.'</A>';
                }
                //$navs .= '&nbsp;&nbsp;';
            }

            if($CurrentPage == $TotalPages)
            {
                $navs .= '<SPAN class="navsOn">'.$TotalPages.'</SPAN>';
            }
            else
            {
                $navs .= '<A class="navs" href="'.$link.$NavName.'='.$TotalPages.($hash != '' ? '#'.$hash : '').'">'.$TotalPages.'</A>';
            }

            $navs .= '<A class="fixedNavLast" href="'.$link.$NavName.'='.$TotalPages.($hash != '' ? '#'.$hash : '').'"><SPAN class="font15" style="color:#1776b5">Last </SPAN><SPAN class="font21" style="color:#1776b5">&raquo;</SPAN></A>';
                        $navs .= '</div">';
        }
        $navs = $navs != '' ? $navs.'&nbsp;&nbsp;&nbsp;': $navs;
        $message = $message != '' ? $message : 'Total Records ';
        $navs = $NoOfRecords > 0 ? ($ShowTotalPages ? $navs.$message.$NoOfRecords : $navs) : '';
        return $navs;
    }

    function FormatAmount($amount, $decimal = 2, $prefix = true)
    {
        if(is_numeric($amount))
        {
            if($prefix === 'Rs.')
            {
                return 'Rs.'.number_format($amount, $decimal);
            }
            elseif($prefix === true)
            {
                return '&#x20B9;'.number_format($amount, $decimal);
            }
            else
            {
                return number_format($amount, $decimal);
            }
        }
        else
        {
            return $amount;
        }
    }

    function PerPageOption($PerPage = 0)
    {
        $RowsPerPage = array(10, 20, 50, 100, 500);
        $PageId = md5($_SERVER['PHP_SELF']);
        
        if(!isset($_SESSION['PerPage'][$PageId]))
        {
            $_SESSION['PerPage'][$PageId] = $PerPage > 0 ? $PerPage : $RowsPerPage[0];
        }

        $html = '';
        $html .= '<div class="records-per-page">';
        $html .= '  <input type="hidden" name="RequestPage" value="'.$PageId.'">';
        $html .= '  <select class="browser-default" name="PerPage" onchange="SetPerPageRows(this)">';
        foreach($RowsPerPage as $records)
        {
            $html .= '<option'.($_SESSION['PerPage'][$PageId] == $records ? ' selected' : '').' value="'.$records.'">Show '.$records.' Rows</option>';
        }
        $html .= '  </select>';
        $html .= '</div>';

        return $html;
    }

    function GeneratePwd($input, $rounds = 7)
    {
		$salt = "";
    	$salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
    	for($i=0; $i < 22; $i++)
    	{
        	$salt .= $salt_chars[array_rand($salt_chars)];
    	}
    	return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
    }

    function VerifyPwd($input, $password_hash)
    {
    	if(crypt($input, $password_hash) == $password_hash)
            return true;
    	else
            return false;
    }

    function FormatElapsedTime($timestamp, $ReturnArray = false)
    {
        //	DEPENDENCY FormatDateTime();
        $timestamp = is_numeric($timestamp) ? $timestamp : time();
        $now = time();
        $DiffSeconds = $now - $timestamp;
        if($DiffSeconds < 0)
        {
            return '';
        }
        else
        {
            $years = floor($DiffSeconds / (365*60*60*24));
            $months = floor($DiffSeconds / (30*3600*24));
            $days = floor($DiffSeconds / (3600*24));
            $hours = floor($DiffSeconds / 3600);
            $minutes = floor($DiffSeconds / 60);
            $seconds = $DiffSeconds;

            if($months > 0)
            {
                $months = $months.($months > 1 ? ' days' : ' day');
                if($ReturnArray)
                {
                    return array('label' => $months, 'unit' => 'months');
                }
                else
                {
                    return $months;
                }
            }
            elseif($days > 0)
            {
                $days = $days.($days > 1 ? ' days' : ' day');
                if($ReturnArray)
                {
                    return array('label' => $days, 'unit' => 'days');
                }
                else
                {
                    return $days;
                }
            }
            elseif($hours > 0)
            {
                $hours = $hours.($hours > 1 ? ' hrs' : ' hr');
                if($ReturnArray)
                {
                    return array('label' => $hours, 'unit' => 'hrs');
                }
                else
                {
                    return $hours;
                }
            }
            elseif($minutes > 0)
            {
                $minutes = $minutes.($minutes > 1 ? ' mins' : ' min');
                if($ReturnArray)
                {
                    return array('label' => $minutes, 'unit' => 'mins');
                }
                else
                {
                    return $minutes;
                }
            }
            else
            {
                $seconds = $seconds.($seconds > 1 ? ' seconds' : ' second');
                if($ReturnArray)
                {
                    return array('label' => $seconds, 'unit' => 'seconds');
                }
                else
                {
                    return $seconds;
                }
            }
        }
    }

    function GenerateCaptcha($grids, $CaptchaName = 'Captcha', $text = 'Click the [color] box')
    {
        $CaptchaColors = array('Blue' => '#5D9CEC', 'Red' => '#e40000', 'Green' => '#1b9c5a', 'Yellow' => '#FFEB3B', 'Black' => '#0c0702', 'Pink' => '#EC87C0', 'Orange' => '#ff7f00', 'Brown' => '#795548');
        $min = 1 * mt_rand(1, 100000);
        $max = $min + $grids - 1;
        $number = mt_rand($min, $max);
        $_SESSION[$CaptchaName] = $number;
        $randColorKey = array_rand($CaptchaColors);
        $randColor = $CaptchaColors[$randColorKey];
        unset($CaptchaColors[$randColorKey]);

        //  Now let's convert the text to image
        $text = str_replace('[color]', $randColorKey, $text);
        $CanvasWidth = strlen($text) * 7.5;
        $im = imagecreatetruecolor($CanvasWidth, 22);

        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 80, 80, 80);
        $black = imagecolorallocate($im, 40, 40, 40);
        $saffron = imagecolorallocate($im, 33, 150, 243);
        imagefilledrectangle($im, 0, 0, $CanvasWidth, 22, $grey);

        // Replace path by your own font path
        $font = realpath(_ROOT).'/captcha/arial.ttf';

        // Add the text
        imagettftext($im, 10.5, 0, 5, 15, $white, $font, $text);

        // Using imagepng() results in clearer text compared with imagejpeg()
        $captchaImagePath = _ROOT.'/captcha/captcha-image.png';
        imagepng($im, $captchaImagePath);
        imagedestroy($im);

        $type = pathinfo($captchaImagePath, PATHINFO_EXTENSION);
        $data = file_get_contents($captchaImagePath);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        @unlink($captchaImagePath);

        $captcha = '<div class="myCaptcha">';
                $captcha .=  '<div class="text"><IMG src="'.$base64.'"></div>';
                $captcha .=  '<div class="captcha-container">';

        for($r = 0, $n = $min; $r < $grids; $r++)
        {
            $style = ' style="background-color:'.($n == $number ? $randColor : $CaptchaColors[array_rand($CaptchaColors)]).'"';
            $captcha .= '<div class="captcha-div">
                            <button class="captcha-btn" data-value="'.$n.'"'.$style.' type="button"></button>
                         </div>';
            $n++;
        }
                $captcha .= '</div>';
        $captcha .= '</div>';

        return $captcha;
    }

    function FormatEmail($content)
    {
        global $SiteSettings;

        $EmailMessage = '<html><head><link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"><style>table td {border-collapse:collapse;} body{ font-family: "Open Sans", Helvetica, Arial, sans-serif; color: #333333; }</style></head><body><table align="center" width="600" border="0" cellspacing="0" cellpadding="0" style="font-family: "Open Sans", Helvetica, Arial, sans-serif;"><tbody><tr><td height="25px">&nbsp;</td></tr><tr><td style="border:1px solid #cccccc;" valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff"><tbody><tr><td valign="top" style="padding:20px 0 15px 20px;"><a href="'._HOST.'?utm_source=email_logo" target="_blank"><img src="'._HOST._LOGO.'" alt="'._WebsiteName.'" width="160px" /></a></td></tr></tbody></table></td></tr><tr><td style="border-bottom:1px solid #cccccc;border-left:1px solid #cccccc;border-right:1px solid #cccccc"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td style="padding:20px" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:14px; line-height: 1.5">';

    	$EmailMessage .= $content;

        $EmailMessage .= '<tr><td style="padding-top:45px;">Warm Regards,<br><a href="'._HOST.'?utm_source=email_logo" target="_blank" style="color:#137fc3;text-decoration:none">Team '._WebsiteName.'</a></td></tr></table></td></tr></table></td></tr><tr><td valign="bottom" style="border:1px solid #cccccc;border-top:none"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td valign="top" style="color:#777777; font-size:11px; padding:10px 0 10px 20px">This is a system generated email. Kindly do not reply to this email. You can email us at <a href="mailto:@'.$SiteSettings['email_support'].'" target="_top">'.$SiteSettings['email_support'].'</td></tr></tbody></table></td></tr></tbody></table></body></html>';

        return $EmailMessage;
    }

    function ProcessingTime()
    {
        return number_format(microtime(true) - _StartTime, 2);
    }

    function SortArray($MasterArray, $SortKeys, $direction = 'a')
    {
        $SortKeys = explode(',', $SortKeys);
        /******************************************************************************
        SORTS A MULTI-DEMENSIONAL ARRAY ON BASIS OF 'KEY' PASSES. USE THIRD PARAM
        AS a or d TO DENOTE 'ASC' OR 'DESC'
        ******************************************************************************/
        //	LETS COLLECT ALL VALUES OF 'SORT KEY'
        $SortKeyValues = array();
        foreach($MasterArray as $key => $value)
        {
            $ValueForSort = array();
            foreach($SortKeys as $SortKey)
            {
                $ValueForSort[] = $value[$SortKey];
            }
            $SortKeyValues[$key] = implode('', $ValueForSort);
        }

        if($direction == 'a')
        {
            natcasesort($SortKeyValues);
        }
        else
        {
            natcasesort($SortKeyValues);
            $SortKeyValues = array_reverse($SortKeyValues, true);
        }

        $OutputArray = array();
        foreach($SortKeyValues as $key => $value)
        {
            $OutputArray[$key] = $MasterArray[$key];
        }
        return $OutputArray;
    }

    function PasswordStrength($password, $ReturnNumeric = true)
    {
        $strength = 0;

        $length = strlen($password);
        if($length >= 7)
        {
            $strength++;
        }
        //  If password contains both lower and uppercase characters, increase strength value.
        if (preg_match("/([a-z].*[A-Z])|([A-Z].*[a-z])/", $password))
        {
            $strength++;
        }

        //  If it has numbers and characters, increase strength value.
        if (preg_match("/([a-zA-Z])/", $password) && preg_match("/([0-9])/", $password))
        {
            $strength++;
        }

        //  If it has one special character, increase strength value.
        if (preg_match("/([!,%,&,@,#,$,^,*,?,_,~])/", $password))
        {
            $strength++;
        }

        //  If it has one special character, increase strength value.
        if (preg_match("/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/", $password))
        {
            $strength++;
        }

        if ($strength < 2 )
        {
            return $ReturnNumeric ? $strength : 'weak';
        }
        elseif ($strength == 2 )
        {
            return $ReturnNumeric ? $strength : 'good';
        }
        elseif ($strength == 3 )
        {
            return $ReturnNumeric ? $strength : 'strong';
        }
        else
        {
            return $ReturnNumeric ? $strength : 'very_strong';
        }
    }

    function Encrypt($string)
    {
        // Store the cipher method 
        $ciphering = "AES-256-CBC"; 
          
        // Non-NULL Initialization Vector for encryption 
        $encryption_iv = '<~X4PzVQ5qQx2JDk';         

        // Store the encryption key 
        $encryption_key = "*C7rm9\ku+c3zs'U";
        $encoded_str = openssl_encrypt($string, $ciphering, $encryption_key, 0, $encryption_iv);
        return base64_encode($encoded_str);
    }

    function Decrypt($string)
    {
        $string = base64_decode($string);

        // Store the cipher method 
        $ciphering = "AES-256-CBC"; 
          
        // Non-NULL Initialization Vector for encryption 
        $encryption_iv = '<~X4PzVQ5qQx2JDk'; 
          
        // Store the encryption key 
        $encryption_key = "*C7rm9\ku+c3zs'U";
          
        return openssl_decrypt($string, $ciphering, $encryption_key, 0, $encryption_iv);  
    }

    function CheckUserBrute($UserID, $Username = '')
    {
        $return = array();
        $return['account_locked'] = 'n';
        $return['attempts_left'] = _UserBruteLimit;
        $return['attempt_id'] = 0;

        $CheckUser = MysqlQuery("SELECT UserID FROM users WHERE UserID = '".$UserID."' LIMIT 1");
        if(mysqli_num_rows($CheckUser) == 1)
        {
            $KeyArrayValues = array();
            $KeyArrayValues['UserID'] = $UserID;
            $KeyArrayValues['Username'] = $Username;
            $KeyArrayValues['IPAddress'] = GetIP();
            $KeyArrayValues['AttemptDatetime'] = time();
            $KeyArrayValues['IsSuccess'] = 'n';
            $KeyArrayValues['Source'] = _REQUEST_SOURCE;
            $TheQuery = PrepareInsertQuery('user_login_attempts', $KeyArrayValues);
            $res = MysqlQuery($TheQuery);
            if(MysqlAffectedRows() == 1)
            {
                $return['attempt_id'] = MysqlInsertID();

                $CheckAttemptFrom = time() - _UserBruteDuration;
                $CheckAttempts = MysqlQuery("SELECT ID FROM user_login_attempts
                                        WHERE UserID = '".$UserID."'
                                        AND AttemptDatetime > '".$CheckAttemptFrom."'
                                        AND IsSuccess = 'n'");
                if(_UserBruteLimit > 0 && ($NoOfFailedAttempts = mysqli_num_rows($CheckAttempts)) >= _UserBruteLimit)
                {
                    $TheQuery = "UPDATE users SET UserStatus = 'l'
                                WHERE UserID = '".$UserID."'
                                LIMIT 1";
                    $res = MysqlQuery($TheQuery);
                    if(MysqlAffectedRows() >= 0)
                    {
                        $return['account_locked'] = 'y';
                        $return['attempts_left'] = 0;
                    }
                    else
                    {
                        LogError(1, 'User account lock update failed | '.$res, 1141, $TheQuery);
                    }
                }
                elseif(_UserBruteLimit > 0)
                {
                    $AttemptsLeft = _UserBruteLimit - $NoOfFailedAttempts;
                    $AttemptsLeft = $AttemptsLeft > 0 ? $AttemptsLeft : 0;
                    $return['attempts_left'] = $AttemptsLeft;
                }
            }
            else
            {
                LogError(1, 'User login attempt insert failed | '.$res, 1144, $TheQuery);
            }
        }
        return $return;
    }

    function SignInTechnician($TechnicianID, $FcmID = '', $DeviceID = '', $AttemptID = 0)
    {
        //  Let's logout previous session if multi-session login not allowed
        if(!_MultiSessionTechnicianLogin)
        {
            MysqlQuery("UPDATE user_login_sessions SET
                                LogoutDateTime = '".time()."',
                                ForcedLogout = 'y'
                            WHERE UserID = '".$TechnicianID."'
                            AND LogoutDateTime = '0'");
        }

        $response = array();

        $SID = md5(AlphaNumericCode(10).$TechnicianID.microtime(true));
        $FCMDeviceID = 0;
        if($FcmID != '')
        {
            $GetFCMDevice = MysqlQuery("SELECT FCMDeviceID FROM user_fcm_ids
                                        WHERE FCMID = '".$FcmID."'
                                        LIMIT 1");
            if(mysqli_num_rows($GetFCMDevice) == 1)
            {
                $GetFCMDevice = mysqli_fetch_assoc($GetFCMDevice);
                $FCMDeviceID = $GetFCMDevice['FCMDeviceID'];
            }
        }

        $KeyValuesArray = array(
                        'SessionID' => $SID,
                        'UserID' => $TechnicianID,
                        'FCMDeviceID' => $FCMDeviceID,
                        'DeviceID' => $DeviceID,
                        'AddedDate' => strtotime('12am'),
                        'AddedDateTime' => time(),
                        'LastActivity' => time(),
                        'LogoutDateTime' => 0,
                        'Source' => _REQUEST_SOURCE,
                        'IPAddress' => GetIP(),
                        'Browser' => GetBrowser()
                    );

        $TheQuery = PrepareInsertQuery('user_login_sessions', $KeyValuesArray);
        $res   = MysqlQuery($TheQuery);
        
        if(MysqlAffectedRows() == 1)
        {
            $response['status']   = 'success';
            $response['session_id'] = $SID;

            RecordUserActivity($TechnicianID, 'LOGGED_IN', 'users', $TechnicianID, 'Logged In');

            //  Let's also mark the last login in the "users" table
            $TheQuery = "UPDATE users SET LastLoginDateTime = CurrentLoginDateTime,
                            LastLoginIP = CurrentLoginIP,
                            CurrentLoginDateTime = '".time()."',
                            CurrentLoginIP = '".GetIP()."'
                            WHERE UserID = '".$TechnicianID."'
                            LIMIT 1";
            $res = MysqlQuery($TheQuery);
            if(MysqlAffectedRows() < 0)
            {
                LogError(1, 'User sign in last login time and ip update failed | '.$res, 1208, $TheQuery);
            }

            //  Let's also mark the "login attempt" as successful
            if($AttemptID > 0)
            {
                $TheQuery = "UPDATE user_login_attempts SET IsSuccess = 'y'
                                WHERE ID = '".$AttemptID."'
                                LIMIT 1";
                $res = MysqlQuery($TheQuery);
                if(MysqlAffectedRows() < 0)
                {
                    LogError(1, 'User login attempt status marking failed | '.$res, 1227, $TheQuery);
                }
            }

            //  Let's also update the status against the fcm id
            if($FCMDeviceID > 0)
            {
                $TheQuery = "UPDATE user_fcm_ids SET UserID = '".$TechnicianID."', IsLoggedIn = 'y'
                            WHERE FCMDeviceID = '".$FCMDeviceID."'
                            LIMIT 1";
                $res = MysqlQuery($TheQuery);
                if(MysqlAffectedRows() < 0)
                {
                    LogError(1, 'User login marking against fcm device failed | '.$res, 1233, $TheQuery);
                }
            }
        }
        else
        {
            $response['status']   = 'error';
            $response['message'] = 'Something went wrong! Try after some time. LN1560';
            LogError(1, 'User Signin Failed | '.$res, 1228, $TheQuery);
        }

        return $response;
    }

    function RecordUserActivity($UserID, $ActivityCode, $TableName = '', $FieldID = 0, $Description = '', $Critical = 'n')
    {
        $Description = addslashes($Description);
        $ActivityCode = strtoupper(addslashes($ActivityCode));

        $KeyValuesArray = array(
                        'UserID' => $UserID,
                        'TableName' => $TableName,
                        'FieldID' => $FieldID,
                        'Activity' => $ActivityCode,
                        'Description' => $Description,
                        'Critical' => $Critical,
                        'AddedDate' => strtotime('12am'),
                        'AddedDateTime' => time(),
                        'Browser' => GetBrowser(),
                        'IPAddress' => GetIP(),
                        'Source' => _REQUEST_SOURCE,
                    );

        $TheQuery = PrepareInsertQuery('user_activities', $KeyValuesArray);
        $res   = MysqlQuery($TheQuery);
        if(MysqlAffectedRows() == 1)
        {
            return true;
        }
        else
        {
            LogError(1, 'User activity insert failed | '.$res, 1261, $TheQuery);
            return false;
        }
    }
    
    function CalculatePercent($amount, $percent)
    {
        return RoundOff(($amount * $percent) / 100, 2);
    }

    function CalculatePercentage($value, $total, $suffix = false, $decimal = true)
    {
        if(is_numeric($value) && is_numeric($total))
        {
            $percent = $total ? RoundOff( ($value * 100) / $total, $decimal ? 2 : 0) : 0;
            return $percent.($suffix ? '%' : '');
        }
        else
        {
            return false;
        }
    }

    function AssignPOSTValues($row, $overwrite = false)
    {
        //  Dependencies: DeSanitizeVar()
        $temp = $row;
        foreach($temp as $key => $values)
        {
            if(!$overwrite && isset($_POST[$key]))
            {
                $temp[$key] = $_POST[$key];
            }
        }
        $_POST = DeSanitizeVar($temp);
    }

    function FormatDescription($desc, $len = 80)
    {
        if(strlen($desc) > $len)
        {
            return substr($desc, 0, strrpos(substr($desc, 0, $len), ' ')).'...';
        }
        else
        {
            return $desc;
        }
    }

    function MakeFriendlyURL($str, $KeepSlash = false)
    {
        $AlphaNumbers = array("1","2","3","4","5","6","7","8","9","0","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"," ","-",".");
        if($KeepSlash)
        {
            array_unshift($AlphaNumbers, "/");
        }
        $str = str_split(strtolower($str));
        for($c = 0; $c < count($str); $c++)
        {
            if(!in_array($str[$c], $AlphaNumbers))
            {
                $str[$c] = '';
            }
        }
        $str = implode('', $str);
        for(; strpos($str,"  ",0) !== false;)
        {
            $str = str_replace("  "," ",$str);
        }
        $str = trim($str);
        return str_replace(' ', '-', $str);
    }

    function ReplaceNewLineChar($str, $splitter = '')
    {
        $find = array('%0D%0A', '%0A');
        $str = urlencode($str);
        $str = str_replace($find, $splitter, $str);
        return urldecode($str);
    }

    function IfFound($source, $find, $RunOnArray = false, $CaseSensitive = false)
    {
        if(is_array($source) && !$RunOnArray)
        {
            $source = implode(' ', $source);
        }

        if(!is_array($find))
        {
            $find = array_unique(array_filter(explode(' ', $find)));
        }
        
        if(!$RunOnArray)
        {
            $source = array_unique(array_filter(explode(' ', $source)));
        }
        
        if(!$CaseSensitive)
        {
            $source = array_map('strtolower', $source);
            $find = array_map('strtolower', $find);
        }
        
        $MatchQuotient = 0;
        //  First of check the whole match, if so assign highest value
        if(!$RunOnArray && implode(' ', $find) == implode(' ', $source))
        {
            $MatchQuotient = 999;
        }
        elseif(!$RunOnArray && $NoOfSearchKeywords == 1)
        {
            $FullSource = implode(' ', $source);
            //  Whole word match in middle
            if(($MatchPosition = strpos($FullSource, ' '.$find[0])) !== false)
            {
                $MatchQuotient = 100;
            }
            //  Whole word match in middle with "dot", "bracket" or "comma"
            elseif(
                ($MatchPosition = strpos($FullSource, '('.$find[0])) !== false
                ||
                ($MatchPosition = strpos($FullSource, '.'.$find[0])) !== false
                ||
                ($MatchPosition = strpos($FullSource, ','.$find[0])) !== false
            )
            {
                $MatchQuotient = 100;
            }
            //  Whole word match at start
            elseif(($MatchPosition = strpos($FullSource, $find[0])) === 0)
            {
                $MatchQuotient = 100;
            }
            elseif(($MatchPosition = strpos($FullSource, $find[0])))
            {
                $MatchQuotient = 50;
            }
        }
        else
        {
            //print_r($source);exit;
            $NoOfSourceKeywords = count($source);
            $NoOfSearchKeywords = count($find);
            $matches = 0;
            foreach($find as $id => $keyword)
            {
                //  If it's last word of all keywords, match it appx.
                if($id + 1 == $NoOfSearchKeywords)
                {
                    if(strpos(implode(' ', $source), $keyword) !== false)
                    {
                        $matches++;
                    }
                }
                else
                {
                    if(in_array($keyword, $source))
                    {
                        $matches++;
                    }
                }
            }
            $MatchQuotient = CalculatePercentage($matches, $NoOfSearchKeywords, false);
        }
        
        return $MatchQuotient;
    }

    function TitleCase($str)
    {
        return ucwords(strtolower($str));
    }

    function SanitizeOutput($buffer)
    {
        //  Useful while generating PDFs
        $search = array(
            '/\>[^\S]+/s',     // strip whitespaces after tags, except space
            '/[^\S]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

    function GetTableRow($TableName, $FieldName, $FieldValue, $Fields = '*')
    {
        $data = MysqlQuery("SELECT ".$Fields." FROM ".$TableName." WHERE ".$FieldName." = '".$FieldValue."' LIMIT 1");
        if(mysqli_num_rows($data) == 1)
        {
            return mysqli_fetch_assoc($data);
        }
        else
        {
            return false;
        }
    }

    function RoundOff($value, $decimals = 0)
    {
        //  multiplied by 1 to make sure it returns an "int" instead of "string"
        return bcdiv(round($value, $decimals), 1, $decimals) * 1;
    }

    function PeakMemoryUsage()
    {
        //  Returns memeroy used by php in MBs
        $usage = memory_get_peak_usage();
        $UsageInKB = ceil($usage / 1024);
        return RoundOff($UsageInKB/1024, 2);
    }

    function CacheFilteredData($FormID)
    {
        $_POST['PerPage'] = $_POST['PerPage'] > 0 ? $_POST['PerPage'] : _DefaultPerPage;
        $_POST['page'] = is_numeric($_POST['page']) ? $_POST['page'] : (is_numeric($_SESSION['Filters'][$FormID]['page']) ? $_SESSION['Filters'][$FormID]['page'] : 1);
        $_SESSION['Filters'][$FormID] = $_POST;
    }

    function SetCacheData($FormID, $GETParams = '')
    {
        /*****************************************************************
        $GETParams let's you pass the specific "GET" parameters which
        should be affect the form content. This is very useful and important
        for the pages where same form is used to load content of different "tabs"
        based on certain filter passed through $_GET parameters
        *****************************************************************/
        $GETParams = $GETParams != '' ? explode(',', $GETParams) : array();
        if(count($GETParams))
        {
            foreach($GETParams as $gParam)
            {
                $FormID .= $gParam.'-'.$_GET[$gParam];
            }
        }

        $FormID = md5($FormID);
        return $FormID;
    }

    function LoadContentResponse($html, $TotalRecords, $ActiveFilters)
    {
        $response['status']         = 'success';
        $response['message']        = $html;
        $response['total_pages']    = $TotalRecords / $_POST['PerPage'] ? ceil($TotalRecords / $_POST['PerPage']) : 0;
        $response['total_records']  = $TotalRecords;
        $response['page']           = !is_numeric($_POST['page']) ? 1 : $_POST['page'];
        $response['PerPage']        = $_POST['PerPage'];
        $response['filters']        = $ActiveFilters;

        return $response;
    }

    function ExtractVIN($string)
    {
        //  Tries to find the VIN within text string
        $vin = '';
        $string = trim(str_replace(' ', '', $string));
        if(strlen($string) == 17)
        {
            $vin = $string;
        }
        else
        {
            preg_match("/[0-9]{6}/", $string, $NumbersArray);
            if(count($NumbersArray))
            {
                foreach($NumbersArray as $val)
                {
                    $vinStart = strpos($string, $val) - 11;
                    $vin = substr($string, $vinStart, 17);
                    if(strlen($vin) == 17)
                    {
                        break;
                    }
                }
            }
        }
        
        return $vin;
    }

    function InsertTicketHistory($UserID, $TicketID = 0, $activity = '', $remark = '')
    {
        $GetTicket = MysqlQuery("SELECT TicketStatusID, EscalatedToUserID, VehicleConfiguration
                                FROM tickets
                                WHERE TicketID = '".$TicketID."'
                                LIMIT 1");
        if(mysqli_num_rows($GetTicket))
        {
            $GetTicket = mysqli_fetch_assoc($GetTicket);

            $KeyArrayValues = array();
            $KeyArrayValues['TicketID'] = $TicketID;
            $KeyArrayValues['TicketStatusID'] = $GetTicket['TicketStatusID'];
            $KeyArrayValues['EscalatedToUserID'] = $GetTicket['EscalatedToUserID'];
            $KeyArrayValues['VehicleConfiguration'] = $GetTicket['VehicleConfiguration'];
            $KeyArrayValues['ActivityDescription'] = $activity;
            $KeyArrayValues['Remark'] = $remark;
            $KeyArrayValues['AddedDate'] = strtotime('12:00:00am');
            $KeyArrayValues['AddedDateTime'] = time();
            $KeyArrayValues['AddedByUserID'] = $UserID;

            $TheQuery = PrepareInsertQuery('ticket_history', $KeyArrayValues);
            $res = MysqlQuery($TheQuery);
            if(MysqlAffectedRows() == 1)
            {
                return true;
            }
            else
            {
                LogError(1, 'Ticket history insert failed | '.$res, 1617, $TheQuery);
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function InsertTicketDiagStep($UserID, $TicketID = 0, $StepData = array())
    {
        $GetTicket = MysqlQuery("SELECT TicketStatusID, EscalatedToUserID, VehicleConfiguration
                                FROM tickets
                                WHERE TicketID = '".$TicketID."'
                                LIMIT 1");
        if(mysqli_num_rows($GetTicket))
        {
            $KeyArrayValues = array();
            $KeyArrayValues['TicketID'] = $TicketID;
            $KeyArrayValues['StepDescription'] = $StepData['step_description'];
            $KeyArrayValues['EventType'] = $StepData['event_type'];
            $KeyArrayValues['LoadedModules'] = $StepData['loaded_module_ids'];
            $KeyArrayValues['ProcessName'] = $StepData['process_name'];
            $KeyArrayValues['StepNumber'] = is_numeric($StepData['step_number']) ? $StepData['step_number'] : 0;
            $KeyArrayValues['PartID'] = $StepData['part_id'];
            $KeyArrayValues['DiagnosticTreeID'] = is_numeric($StepData['diagnostic_tree_id']) ? $StepData['diagnostic_tree_id'] : 0;
            $KeyArrayValues['ActivityUniqueNumber'] = $StepData['activity_unique_number'];
            $KeyArrayValues['AddonData'] = $StepData['addon_data'];
            $KeyArrayValues['FileIDs'] = $StepData['file_ids'];
            $KeyArrayValues['AddedDate'] = strtotime('12:00:00am');
            $KeyArrayValues['AddedDateTime'] = time();
            $KeyArrayValues['AddedByUserID'] = $UserID;

            $TheQuery = PrepareInsertQuery('ticket_diag_steps', $KeyArrayValues);
            $res = MysqlQuery($TheQuery);
            if(MysqlAffectedRows() == 1)
            {
                return true;
            }
            else
            {
                LogError(1, 'Ticket diagnostic step insert failed | '.$res, 1659, $TheQuery);
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function InsertTicketEscalation($TicketID, $EscalatedFromUserID, $EscalatedToUserID, $EscalationMode = 'auto', $EscalationRemark = '')
    {
        global $WUID;
        $WUID = is_numeric($WUID) ? $WUID : 0;
        $return = false;

        $EscalationDuration = 60;  //  Default no. of mins to respond to an escalated ticket
        $GetEscalationDuration = MysqlQuery("SELECT at.EscalationDuration
                                            FROM users u
                                            LEFT JOIN master_account_types at ON at.AccountTypeID = u.AccountTypeID
                                            WHERE u.UserID = '".$EscalatedToUserID."'
                                            LIMIT 1");
        if(mysqli_num_rows($GetEscalationDuration))
        {
            $GetEscalationDuration = mysqli_fetch_assoc($GetEscalationDuration);
            $EscalationDuration = $GetEscalationDuration['EscalationDuration'];
        }

        $EscalationDuration = $EscalationDuration * 60; //  Convert it to seconds
        $KeyArrayValues = array();
        $KeyArrayValues['TicketID'] = $TicketID;
        $KeyArrayValues['EscalatedFromUserID'] = $EscalatedFromUserID;
        $KeyArrayValues['EscalatedToUserID'] = $EscalatedToUserID;
        $KeyArrayValues['EscalationMode'] = $EscalationMode;
        $KeyArrayValues['EscalationRemark'] = $EscalationRemark;
        $KeyArrayValues['EscalationStatusID'] = '1';    //  default status is "assigned"
        $KeyArrayValues['ResponseDueTime'] = time() + $EscalationDuration; //  Due time as per current settings to respond
        $KeyArrayValues['AddedDate'] = strtotime('12:00:00am');
        $KeyArrayValues['AddedDateTime'] = time();
        $KeyArrayValues['AddedByUserID'] = $WUID;

        $TheQuery = PrepareInsertQuery('ticket_escalations', $KeyArrayValues);
        $res = MysqlQuery($TheQuery);
        if(MysqlAffectedRows() == 1)
        {
            $EscalationID = MysqlInsertID();

            $UpdateKeyValues = array();
            $UpdateKeyValues['TicketStatusID'] = '3';
            $UpdateKeyValues['EscalatedToUserID'] = $EscalatedToUserID;
            $UpdateKeyValues['EscalationDateTime'] = time();
            $UpdateKeyValues['TicketEscalationID'] = $EscalationID;
            $UpdateKeyValues['EscalationRemark'] = $EscalationRemark;

            $TheQuery = PrepareUpdateQuery('tickets', $UpdateKeyValues, 'TicketID='.$TicketID);
            $res = MysqlQuery($TheQuery);
            if(MysqlAffectedRows() >= 0)
            {
                //  Now let's create a inapp notification
                $GetDealership = MysqlQuery("SELECT
                                                di.DealershipName, di.DealershipCode, v.VIN
                                            FROM tickets t
                                            LEFT JOIN dealership_info di ON di.UserID = t.DealershipUserID
                                            LEFT JOIN vehicles v ON v.VehicleID = t.VehicleID
                                            WHERE t.TicketID = '".$TicketID."'
                                            LIMIT 1");
                $DealershipDetails = mysqli_fetch_assoc($GetDealership);

                $NotificationTitle = 'Ticket from '.$DealershipDetails['DealershipName'].' ('.$DealershipDetails['DealershipCode'].') with VIN # '.$DealershipDetails['VIN'].' escalated';
                InsertInappNotification($EscalatedToUserID, $TicketID, 'ticket_escalation', $NotificationTitle);
                
                $return = true;
            }
            else
            {
                LogError(1, 'Ticket escalation update in tickets table failed | '.$res, 1730, $TheQuery);
            }
        }
        else
        {
            LogError(1, 'Ticket escalation insert failed | '.$res, 1718, $TheQuery);
        }
        return $return;
    }

    function InsertInappNotification($TargetUserID, $EntityID, $NotificationType, $NotificationTitle)
    {
        $KeyArrayValues = array();
        $KeyArrayValues['TargetUserID'] = $TargetUserID;
        $KeyArrayValues['EntityID'] = $EntityID;
        $KeyArrayValues['NotificationType'] = $NotificationType;
        $KeyArrayValues['NotificationTitle'] = $NotificationTitle;
        $KeyArrayValues['AddedDate'] = strtotime('12:00:00am');
        $KeyArrayValues['AddedDateTime'] = time();
        $KeyArrayValues['IsRead'] = 'n';

        $TheQuery = PrepareInsertQuery('notifications_inapp', $KeyArrayValues);
        $res = MysqlQuery($TheQuery);
        if(MysqlAffectedRows() == 1)
        {
            return true;
        }
        else
        {
            LogError(1, 'Inapp notification insert failed | '.$res, 1761, $TheQuery);
            return false;
        }
    }

    function FormatFileSize($size, $unit = 'KB')
    {
        $unit = strtoupper($unit);
        if($unit == 'KB')
        {
            $bytes = $size * 1024;
        }
        elseif($unit == 'MB')
        {
            $bytes = $size * 1024 * 1024;
        }
        elseif($unit == 'GB')
        {
            $bytes = $size * 1024 * 1024 * 1024;
        }
        elseif($unit == 'MB')
        {
            $bytes = $size;
        }
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 1) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 1) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 1) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    function GetEscalationUsers($TicketID, $NextAccountType)
    {
        $EscalatedToUserIDs = array();

        $GetTicket = MysqlQuery("SELECT DealershipUserID FROM tickets
                                WHERE TicketID = '".$TicketID."'
                                LIMIT 1");
        if(mysqli_num_rows($GetTicket))
        {
            $GetTicket = mysqli_fetch_assoc($GetTicket);
            $TicketDealershipUserID = $GetTicket['DealershipUserID'];

            //  If the ticket to be escalated to TFF
            if($NextAccountType == '3')
            {
                $GetTFFQuery = "SELECT
                                        u.UserID,
                                        (SELECT COUNT(TicketID) FROM tickets
                                            WHERE EscalatedToUserID = u.UserID
                                            AND TicketStatusID <> '2'
                                            AND IsDeleted = 'n'
                                        ) AS UnresolvedTickets
                                    FROM users u
                                    LEFT JOIN tff_dealerships td ON td.TFFUserID = u.UserID
                                    WHERE u.AccountTypeID = '3'
                                    AND u.UserStatus = 'a'
                                    AND td.DealershipUserID = '".$TicketDealershipUserID."'
                                    AND td.IsDeleted = 'n'
                                    ORDER BY UnresolvedTickets ASC, u.UserID ASC
                                    LIMIT 1";
                $GetTFF = MysqlQuery($GetTFFQuery);
                if(mysqli_num_rows($GetTFF))
                {
                    $SelectedTFF = mysqli_fetch_assoc($GetTFF);
                    $EscalatedToUserIDs[$SelectedTFF['UserID']] = $SelectedTFF['UserID'];
                }
                else
                {
                    LogError(1, 'Failed to fetch any TFF user for Escalation of Ticket # '.$TicketID, 1901, $GetTFFQuery);
                }
            }
            //  If the ticket to be escalated to Zonal Head
            elseif($NextAccountType == '4')
            {
                $GetZonalHeadQuery = "SELECT mz.ZonalHeadUserID FROM dealership_info di
                                        LEFT JOIN users u ON u.UserID = di.UserID
                                        LEFT JOIN master_states ms ON ms.StateID = di.StateID
                                        LEFT JOIN master_zones mz ON mz.ZoneID = ms.ZoneID
                                        WHERE di.UserID = '".$TicketDealershipUserID."'
                                        AND u.UserStatus = 'a'
                                        AND mz.ZonalHeadUserID > 0
                                        LIMIT 1";
                $GetZonalHead = MysqlQuery($GetZonalHeadQuery);
                if(mysqli_num_rows($GetZonalHead))
                {
                    $SelectedZonalHead = mysqli_fetch_assoc($GetZonalHead);
                    $EscalatedToUserIDs[$SelectedZonalHead['ZonalHeadUserID']] = $SelectedZonalHead['ZonalHeadUserID'];
                }
                else
                {
                    LogError(1, 'Failed to fetch any Zonal Head user for Escalation of Ticket # '.$TicketID, 1923, $GetZonalHeadQuery);
                }
            }
            //  If the ticket to be escalated to Product Support Team
            elseif($NextAccountType == '5')
            {
                //  Let's get the ticket issue clusters
                $GetTicketIssueClusters = MysqlQuery("SELECT ClusterID FROM ticket_issue_clusters ic
                                            WHERE ic.TicketID = '".$TicketID."'
                                            AND ic.IsDeleted = 'n'");
                if(mysqli_num_rows($GetTicketIssueClusters))
                {
                    $IssueClusters = array();
                    for(; $icr = mysqli_fetch_assoc($GetTicketIssueClusters);)
                    {
                        $IssueClusters[$icr['ClusterID']] = $icr['ClusterID'];
                    }

                    foreach($IssueClusters as $ClusterID)
                    {
                        $GetPSTQuery = "SELECT
                                                u.UserID,
                                                (SELECT COUNT(TicketID) FROM tickets
                                                    WHERE EscalatedToUserID = u.UserID
                                                    AND TicketStatusID <> '2'
                                                    AND IsDeleted = 'n'
                                                ) AS UnresolvedTickets
                                            FROM pst_clusters pc
                                            LEFT JOIN users u ON u.UserID = pc.PSTUserID
                                            WHERE u.AccountTypeID = '5'
                                            AND u.UserStatus = 'a'
                                            AND pc.ClusterID = '".$ClusterID."'
                                            ORDER BY UnresolvedTickets ASC, u.UserID ASC
                                            LIMIT 1";
                        $GetPST = MysqlQuery($GetPSTQuery);
                        if(mysqli_num_rows($GetPST))
                        {
                            $SelectedPST = mysqli_fetch_assoc($GetPST);
                            $EscalatedToUserIDs[$SelectedPST['UserID']] = $SelectedPST['UserID'];
                        }
                        else
                        {
                            LogError(1, 'Failed to fetch any PST user for Escalation of Ticket # '.$TicketID.' for the Cluster ID '.$ClusterID, 1965, $GetPSTQuery);
                        }
                    }
                }
                else
                {
                    //LogError(1, 'Failed to find Ticket Issue Clusters for Escalation of Ticket # '.$TicketID.' to PST member', 1971);
                }
            }
            //  If the ticket to be escalated to Super Admin
            elseif($NextAccountType == '6')
            {
                $GetSuperAdminQuery = "SELECT
                                        u.UserID,
                                        (SELECT COUNT(TicketID) FROM tickets
                                            WHERE EscalatedToUserID = u.UserID
                                            AND TicketStatusID <> '2'
                                            AND IsDeleted = 'n'
                                        ) AS UnresolvedTickets
                                    FROM users u
                                    WHERE u.AccountTypeID = '6'
                                    AND u.UserStatus = 'a'
                                    ORDER BY UnresolvedTickets ASC, u.UserID ASC
                                    LIMIT 1";
                $GetSuperAdmin = MysqlQuery($GetSuperAdminQuery);
                if(mysqli_num_rows($GetSuperAdmin))
                {
                    $SelectedTFF = mysqli_fetch_assoc($GetSuperAdmin);
                    $EscalatedToUserIDs[$SelectedTFF['UserID']] = $SelectedTFF['UserID'];
                }
                else
                {
                    LogError(1, 'Failed to fetch any Super Admin user for Escalation of Ticket # '.$TicketID, 1997, $GetSuperAdminQuery);
                }
            }
        }
        return $EscalatedToUserIDs;
    }

    function UserProfilePicture($image, $ReturnThumb = true)
    {
        if($ReturnThumb)
        {
            $image = $image != '' ? _RESOURCE_HOST._UserThumbs.$image : _HOST._UserDefaultImage;
        }
        else
        {
            $image = $image != '' ? _RESOURCE_HOST._UserImages.$image : _HOST._UserDefaultImage;
        }
        return $image;
    }

    function UploadFile($phpTempFilePath, $fileName, $tagetFolder, $restrictImageWidth = 0, $thumbnailFolder = '', $thumbnailWidth = 200)
    {
        global $AllowedImageTypes, $s3Client;
        if(is_uploaded_file($phpTempFilePath))
        {
            if(move_uploaded_file($phpTempFilePath, _ROOT._WorkingArea.$fileName))
            {
                $TempFilePath = _ROOT._WorkingArea.$fileName;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $TempFilePath = $phpTempFilePath;
        }
            
        $ext = substr(strtolower(strrchr($fileName, '.')), 1);
        $IsImage = isset($AllowedImageTypes[$ext]) ? true : false;

        //  Let's resize the image if it's to be restricted in resolution
        if($IsImage && $restrictImageWidth > 0)
        {
            if(!CreateThumbnail($TempFilePath, str_replace($fileName, '', $TempFilePath), $restrictImageWidth))
            {
                return false;
            }
        }

        if(_USE_S3)
        {
            //  Let's remove the leading slash from the target folder, if any
            $tagetFolder = substr($tagetFolder, 0, 1) == '/' ? substr($tagetFolder, 1) : $tagetFolder;
            try{
                $result = $s3Client->putObject([
                    'Bucket' => _S3_BUCKET_NAME,
                    'Key'    => $tagetFolder.$fileName,
                    'Body'   => fopen($TempFilePath, 'r'),
                    'ACL'    => 'public-read', // make file 'public'
                ]);

                //  If thumbnail folder is passed, let's also generate a thumbail
                if($IsImage && $thumbnailFolder != '')
                {
                    if(CreateThumbnail($TempFilePath, _ROOT._WorkingArea, $thumbnailWidth))
                    {
                        $thumbnailTempPath = _ROOT._WorkingArea.$fileName;

                        //  Let's remove the leading slash from the target folder, if any
                        $thumbnailFolder = substr($thumbnailFolder, 0, 1) == '/' ? substr($thumbnailFolder, 1) : $thumbnailFolder;

                        $result = $s3Client->putObject([
                            'Bucket' => _S3_BUCKET_NAME,
                            'Key'    => $thumbnailFolder.$fileName,
                            'Body'   => fopen($thumbnailTempPath, 'r'),
                            'ACL'    => 'public-read', // make file 'public'
                        ]);
                    }
                    else
                    {
                        LogError(1, 'An error occurred while generating thumbnail for S3 Bucket named '._S3_BUCKET_NAME.' in '._S3_REGION.' Region', 1950);
                    }
                }

                //  Delete the original file from the "working area"
                @unlink($TempFilePath);

                return $result->get('ObjectURL');
            }
            catch (Aws\S3\Exception\S3Exception $e)
            {
                LogError(1, 'An error occurred while uploading to S3 Bucket named '._S3_BUCKET_NAME.' in '._S3_REGION.' Region | '.$e->getMessage(), 1961);
                return false;
            }
        }
        else
        {
            if(rename($TempFilePath, _ROOT.$tagetFolder.$fileName))
            {
                //  If thumbnail folder is passed, let's also generate a thumbail
                if($IsImage && $thumbnailFolder != '')
                {
                    if(!CreateThumbnail(_ROOT.$tagetFolder.$fileName, _ROOT.$thumbnailFolder, $thumbnailWidth))
                    {
                        LogError(1, 'An error occurred while generating thumbnail in the native directory', 1950);
                    }
                }
                //  There's no need to delete the file from "working area" since it's moved to the target folder
            }
            else
            {
                LogError(1, 'An error occurred while moving the file from "working area" to '.$tagetFolder, 1976);
                return false;
            }
        }
    }

    function DeleteFile($fileName, $tagetFolder, $thumbnailFolder = '')
    {
        global $AllowedImageTypes, $s3Client;
        
        $ext = substr(strtolower(strrchr($fileName, '.')), 1);
        $IsImage = isset($AllowedImageTypes[$ext]) ? true : false;

        if(_USE_S3)
        {
            //  Let's remove the leading slash from the target folder, if any
            $tagetFolder = substr($tagetFolder, 0, 1) == '/' ? substr($tagetFolder, 1) : $tagetFolder;
            try{
                $result = $s3Client->deleteObject([
                    'Bucket' => _S3_BUCKET_NAME,
                    'Key' => $tagetFolder.$fileName,
                ]);

                //  If thumbnail folder is passed, let's also delete the thumbail
                if($IsImage && $thumbnailFolder != '')
                {
                    //  Let's remove the leading slash from the target folder, if any
                    $thumbnailFolder = substr($thumbnailFolder, 0, 1) == '/' ? substr($thumbnailFolder, 1) : $thumbnailFolder;

                    $result = $s3Client->deleteObject([
                        'Bucket' => _S3_BUCKET_NAME,
                        'Key' => $thumbnailFolder.$fileName,
                    ]);
                }
            }
            catch (Aws\S3\Exception\S3Exception $e)
            {
                LogError(1, 'An error occurred while deleting from S3 Bucket named '._S3_BUCKET_NAME.' in '._S3_REGION.' Region | '.$e->getMessage(), 2029);
                return false;
            }
        }
        else
        {
            @unlink(_ROOT.$tagetFolder.$fileName);

            if($IsImage && $thumbnailFolder != '')
            {
                @unlink(_ROOT.$thumbnailFolder.$fileName);
            }
        }
    }
?>