<?php
    
    require_once (__DIR__.'/../phpmailer/class.phpmailer.php');
    
    require_once(__DIR__.'/vars.php');
    require_once(__DIR__.'/connect.php');
    require_once(__DIR__.'/funcs.php');
    
    //  Let's get the site setting values
    $GetSiteSettings = MysqlQuery("SELECT * FROM site_settings");
    $SiteSettings = array();
    if(mysqli_num_rows($GetSiteSettings))
    {
        for(; $ss = mysqli_fetch_assoc($GetSiteSettings);)
        {
            $SiteSettings[$ss['Code']] = $ss['Value'];
        }
    }

    $baseUrl = explode('?', $_SERVER['REQUEST_URI']);
    $params = $baseUrl[1];
    $baseUrl = $baseUrl[0];
    $FolderLevels = array_values(array_filter(explode('/', $baseUrl)));

    if(isset($_POST['RequestPage']) && isset($_POST['PerPage']))
    {
        $_SESSION['PerPage'][$_POST['RequestPage']] = $_POST['PerPage'];
    }
    
    //  Show appropriate message if the site is down
    $WhitelistedIPs = array_map('trim', explode(',', $SiteSettings['site_down_whitelist_ips']));
    if($SiteSettings['site_down'] == 'y' && !in_array(GetIP(), $WhitelistedIPs))
    {
        header('HTTP/1.1 503 Service Temporarily Unavailable');     //  To inform search engine bots
        header('Retry-After: '.($SiteSettings['site_down_eta'] * 60));
        die('Down for maintenance!');
    }
        
    if($FolderLevels[0] == 'admin')
    {
        define('_REQUEST_SOURCE', 'Web');

        require_once(_ROOT._AdminIncludesDir."admin-configs.php");
        require_once(_ROOT._AdminIncludesDir."admin-funcs.php");
        require_once(_ROOT._AdminIncludesDir."admin-login-check.php");
    }
    elseif($FolderLevels[0] == 'api')
    {
        /*if($FolderLevels[1] == 'user')
        {
            define('_API_VERSION', str_replace('v', '', $FolderLevels[2]));
            define('_API_VERSION_DISPLAY', 'v'._API_VERSION);
            define('_API_PATH', _API_ROOT.'user/'.$FolderLevels[2].'/');
            define('_API_FILES_PATH', _API_PATH.'api-files/');
            define('_API_DOC_PATH', _API_PATH.'doc/');
            
            require_once(_ROOT._API_PATH."config.php");
            require_once(_ROOT._API_PATH."api_funcs.php");
        }*/

        /**************************************************
         * IMP: Below "_REQUEST_SOURCE" setting must be 
         * here after the API config.php file is included
        **************************************************/
        if(@$_REQUEST['api_key'] == @_ACCESS_KEY_USER_AND)
        {
            define('_REQUEST_SOURCE', 'Android');
        }
        elseif(@$_REQUEST['api_key'] == @_ACCESS_KEY_USER_IOS)
        {
            define('_REQUEST_SOURCE', 'iOS');
        }
        else
        {
            define('_REQUEST_SOURCE', 'Web');
        }
    }
    else
    {
        define('_REQUEST_SOURCE', 'Web');
    }

    // Sanitize Cookies, Post & Get Values
    $_COOKIE    = array_map('SanitizePOST', $_COOKIE);
    $_GET       = array_map('SanitizePOST', $_GET);
    $_POST      = array_map('SanitizePOST', $_POST);
?>