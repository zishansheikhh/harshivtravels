<?php

    include_once("../include-files/autoload-server-files.php");

    $FolderLevelsCount = count($FolderLevels);
    $RewritePageFound = '';
    $RewritePageRedirect = '';
    $HomeURL = '/';

    /********NOW LET'S CHECK IF THE URL ENDS WITH '/', IF NOT REDIRECT TO PROPER URL*******************/
    $RewriteFileExt = substr(strrchr(strtolower($FolderLevels[$FolderLevelsCount - 1]), '.'), 1);
    $AllowedExts = array('php', 'html', 'htm', 'css', 'js', 'jpg', 'jpeg', 'png', 'gif');
    if(($RewriteFileExt === false || !in_array($RewriteFileExt, $AllowedExts)) && substr($baseUrl, -1, 1) != '/')
    {
        header('HTTP/1.0 301 Permanently Moved');
        redirect($baseUrl.'/'.($params != '' ? '?'.$params : ''));
    }
    
    $FolderURL = '/'.implode('/', $FolderLevels).($RewriteFileExt == '' ? '/' : '');
    /****************************************************************************************************/

    $HomeURL = $FolderLevels[0] == 'admin' ? '/admin/' : '/';
    if($FolderLevels[0] == 'api')
    {
        //  API calls should be handled if authenticated with the "api_key"
        if(_REQUEST_SOURCE == 'Android' || _REQUEST_SOURCE == 'iOS')
        {
            if($FolderLevels[3] == 'tester')
            {
                $RewritePageFound = _ROOT._API_PATH.'api-tester.php';
            }
            else
            {
                define('_API_NAME', $FolderLevels[3]);
                $RewritePageFound = _ROOT._API_PATH.'index.php';
            }
        }
        elseif($FolderLevels[3] == 'doc')
        {
            $RewritePageFound = _ROOT._API_DOC_PATH.'index.php';
        }
    }
    elseif($FolderLevels[0] == 'admin')
    {
        // If in Edit mode
        if(isset($_GET['id']))
        {
            $ID = Decrypt($_GET['id']);
        }

        if($FolderURL == '/admin/login/')
        {
            $RewritePageFound = _ROOT._AdminRoot.'include-files/ajax-login.php';
        }
        elseif($FolderURL == '/admin/dashboard/')
        {
            $RewritePageFound = _ROOT._AdminRoot.'dashboard.php';
        }
        elseif($FolderLevels[1] == 'ajax')
        {
            if(isset($FolderLevels[3]))
            {
                $RewritePageFound = _ROOT._AdminRoot.$FolderLevels[2].'/'.$FolderLevels[3].'.php';
            }
            else
            {
                $RewritePageFound = _ROOT._AdminRoot.'include-files/'.$FolderLevels[2].'.php';
            }
        }
        elseif(count($FolderLevels) == 2)
        {
            
        }
        elseif(count($FolderLevels) == 3)
        {
            $RewritePageFound = _ROOT._AdminRoot.$FolderLevels[1].'/'.$FolderLevels[2].'.php';
        }

        if($RewritePageFound == '')
        {
            if($RewriteFileExt == '')
            {
                $RewritePageFound = _ROOT.str_replace('/admin/', _AdminRoot, $FolderURL).'index.php';
            }
            else
            {
                $RewritePageFound = _ROOT.str_replace('/admin/', _AdminRoot, $FolderURL);
            }
        }
        //echo $RewritePageFound;exit;
    }
    elseif($FolderLevels[0] == 'ajax')
    {
        if(isset($FolderLevels[2]))
        {
            $RewritePageFound = _ROOT.$FolderLevels[1].'/'.$FolderLevels[2].'.php';
        }
        else
        {
            $RewritePageFound = _ROOT._IncludesDir.$FolderLevels[1].'.php';
        }
    }
    elseif(file_exists(_ROOT.'/pages/'.$FolderLevels[0].'.php'))
    {
        $RewritePageFound = _ROOT.'/pages/'.$FolderLevels[0].'.php';
    }

    //  Let's check if the url has any 301 redirection set
    $CheckRedirectUrl = MysqlQuery("SELECT NewUrl FROM redirect_urls
                            WHERE OldURL = '".$FolderURL."' OR OldURL = '/".$FolderLevels[0]."'
                            LIMIT 1");
    if(mysqli_num_rows($CheckRedirectUrl))
    {
        $CheckRedirectUrl = mysqli_fetch_assoc($CheckRedirectUrl);
        $RewritePageRedirect =  $CheckRedirectUrl['NewUrl'];
    }

	if($RewritePageFound != '')
	{
		$_SERVER['PHP_SELF'] = $RewritePageFound;
		header('HTTP/1.0 200 OK');
		include($RewritePageFound);
		exit;
	}
	elseif($RewritePageRedirect != '')
	{
		header('HTTP/1.0 301 Permanently Moved');
		header('Location: '.$RewritePageRedirect);
		exit;
	}
	header('HTTP/1.0 404 Page Not Found');
?>
<!DOCTYPE>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="noindex,follow" />
    <title>Error 404 | Sorry the page you are looking for is renamed, moved or deleted!</title>
    <?php include_once(_ROOT._IncludesDir."common-css.php"); ?>
    <style>
        .col-centered{ float: none; margin: 0 auto; padding: 50px 15px 100px 15px; text-align: center; }
    </style>
</head>

<body class="home">
    <div class="container">
        	<div class="row col-centered" style="background: rgba(255, 255, 255, 0.5) !important; margin-top: 10%">
            	<H1 class="lighter">
                	404 Page Not Found
                </H1>

                <H3 class="lighter">Oops! The page you are looking is either moved or not available</H3>

                <div style="margin-top:60px">
                   <a href="<?=$HomeURL?>" class="btn btn-large blue"><i class="material-icons left">home</i> &nbsp;Return Home</a>
                </div>
			</div>
		</div>
</body>
</html>