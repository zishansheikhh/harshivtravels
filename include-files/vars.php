<?php
    
    define('_PHP_BASE_VERSION', 5.4);   //  base php version below which the site must deny to run

    //  domain host e.g. www.xyz.com | do NOT put trailing "/"
    define('_HOST_DOMAIN_PROD', 'harshivtravels.gitdebug.xyz');
    define('_HOST_DOMAIN_STAGING', 'harshivtravels.gitdebug.xyz');
    define('_HOST_DOMAIN_DEV', 'zishansheikhh.github.io/harshivtravels');
    define('_HOST_DOMAIN_LOCAL', 'harshiv');

    /********************PRODUCTION ENVIRONMENT*****************************/

    if($_SERVER['HTTP_HOST'] == _HOST_DOMAIN_PROD)
    {
        define('_ROOT', '/home/public_html/harshivtravels.com'); //  Root directory path

        define('_HOST_DOMAIN', _HOST_DOMAIN_PROD); //  domain host e.g. www.xyz.com | do NOT put trailing "/"
        define('_SHORTHOST', _HOST_DOMAIN_PROD); //  short hostname e.g. xyz.com | used for sending shortened links via mail/sms
        define('_SSL_STATUS', true); //  if SSL is active on server | true or false

        define('_DB_HOST', 'localhost');   //  database host server
        define('_DB_USER', 'sandordiagnostic_live');   // mysql username for database
        define('_DB_PASSWORD', 'p^y)2q&tv[7M');   // mysql password for database
        define('_DB_NAME', 'sandordiagnostic_live');   //  mysql database name

        define('_SENDER_MAIL_HOST', 'mail.harshiv.co.in');    //  email host
        define('_SENDER_MAIL_SMTP_PORT', 465);    //  email smtp port
        define('_SENDER_MAIL_SMTP_PROTOCOL', 'ssl');    //  email smtp protocol
        define('_SENDER_MAIL_ID', 'noreply@harshiv.co.in');    //  Display email id for "From" e.g. noreply@blahblah.com
        define('_SENDER_MAIL_USER', 'noreply@harshiv.co.in');  //  Email/username for SMTP authentication e.g. noreply@blahblah.com
        define('_SENDER_MAIL_PWD', 'u6{6$Mx(lX0o'); //  Password for SMTP authentication

        define('_API_HOST_DOMAIN', 'example.com'); //  api host domain url

        define('_USE_S3', false);     //  If S3 bucket to be used for file uploads
        define('_S3_REGION', 'xxx');
        define('_S3_BUCKET_NAME', 'xxxx');
        define('_AWS_IAM_USER_KEY', 'xxxxxx');
        define('_AWS_IAM_USER_SECRET', 'xxxxxx');
    }
    /********************STAGING ENVIRONMENT*****************************/
    elseif($_SERVER['HTTP_HOST'] == _HOST_DOMAIN_STAGING)
    {
        define('_ROOT', '/public_html/xxxx'); //  Root directory path

        define('_HOST_DOMAIN', _HOST_DOMAIN_STAGING); //  domain host e.g. www.xyz.com | do NOT put trailing "/"
        define('_SHORTHOST', _HOST_DOMAIN_STAGING); //  short hostname e.g. xyz.com | used for sending shortened links via mail/sms
        define('_SSL_STATUS', false); //  if SSL is active on server | true or false

        define('_DB_HOST', 'localhost');   //  database host server
        define('_DB_USER', 'xxxx');   // mysql username for database
        define('_DB_PASSWORD', 'xxxxxxxx');   // mysql password for database
        define('_DB_NAME', 'xxxxxxxxdb');   //  mysql database name

        define('_SENDER_MAIL_HOST', 'smtp.gmail.com');    //  email host
        define('_SENDER_MAIL_SMTP_PORT', 587);    //  email smtp port
        define('_SENDER_MAIL_SMTP_PROTOCOL', 'tls');    //  email smtp protocol
        define('_SENDER_MAIL_ID', 'no-reply@sandor.co.in');    //  Display email id for "From" e.g. noreply@blahblah.com
        define('_SENDER_MAIL_USER', 'no-reply@sandor.co.in');  //  Email/username for SMTP authentication e.g. noreply@blahblah.com
        define('_SENDER_MAIL_PWD', 'u6{6$Mx(lX0o'); //  Password for SMTP authentication

        define('_API_HOST_DOMAIN', 'example.com'); //  api host domain url

        define('_USE_S3', false);     //  If S3 bucket to be used for file uploads
        define('_S3_REGION', 'ap-south-1');
        define('_S3_BUCKET_NAME', 'xxxxxx');
        define('_AWS_IAM_USER_KEY', 'xxxxxx');
        define('_AWS_IAM_USER_SECRET', 'xxxxxx');
    }
    /********************DEVELOPMENT ENVIRONMENT*****************************/
    elseif($_SERVER['HTTP_HOST'] == _HOST_DOMAIN_DEV)
    {
        define('_ROOT', '/home/u391245239/domains/gitdebug.xyz/public_html/harshivtravels'); //  Root directory path
        
        define('_HOST_DOMAIN', _HOST_DOMAIN_DEV); //  domain host e.g. www.xyz.com | do NOT put trailing "/"
        define('_SHORTHOST', _HOST_DOMAIN_DEV); //  short hostname e.g. xyz.com | used for sending shortened links via mail/sms
        define('_SSL_STATUS', false); //  if SSL is active on server | true or false

        define('_DB_HOST', 'localhost');   //  database host server
        define('_DB_USER', 'gitdebug_mysql');   // mysql username for database
        define('_DB_PASSWORD', 'gitdebug1379!');   // mysql password for database
        define('_DB_NAME', 'gitdebug_harshiv');   //  mysql database name

        define('_SENDER_MAIL_HOST', 'smtp.gmail.com');    //  email host
        define('_SENDER_MAIL_SMTP_PORT', 587);    //  email smtp port
        define('_SENDER_MAIL_SMTP_PROTOCOL', 'tls');    //  email smtp protocol
        define('_SENDER_MAIL_ID', 'no-reply@harshiv.co.in');    //  Display email id for "From" e.g. noreply@blahblah.com
        define('_SENDER_MAIL_USER', 'no-reply@harshiv.co.in');  //  Email/username for SMTP authentication e.g. noreply@blahblah.com
        define('_SENDER_MAIL_PWD', 'u6{6$Mx(lX0o'); //  Password for SMTP authentication

        define('_API_HOST_DOMAIN', 'harshiv.gitdebug.com'); //  api host domain url

        define('_USE_S3', false);     //  If S3 bucket to be used for file uploads
        define('_S3_REGION', 'xxx');
        define('_S3_BUCKET_NAME', 'xxxx');
        define('_AWS_IAM_USER_KEY', 'xxxxxx');
        define('_AWS_IAM_USER_SECRET', 'xxxxxx');
    }
    /********************LOCAL DEVELOPER MACHINE ENVIRONMENT*****************************/
    elseif($_SERVER['HTTP_HOST'] == _HOST_DOMAIN_LOCAL)
    {
        define('_ROOT', '/server/harshiv'); //  Root directory path

        define('_HOST_DOMAIN', _HOST_DOMAIN_LOCAL); //  domain host e.g. www.xyz.com | do NOT put trailing "/"
        define('_SHORTHOST', _HOST_DOMAIN_LOCAL); //  short hostname e.g. xyz.com | used for sending shortened links via mail/sms
        define('_SSL_STATUS', false); //  if SSL is active on server | true or false

        define('_DB_HOST', '127.0.0.1');   //  database host server
        define('_DB_USER', 'root');   // mysql username for database
        define('_DB_PASSWORD', 'Xperia!2#4');   // mysql password for database
        define('_DB_NAME', 'sandor_db');   //  mysql database name

        define('_SENDER_MAIL_HOST', 'smtp.gmail.com');    //  email host
        define('_SENDER_MAIL_SMTP_PORT', 587);    //  email smtp port
        define('_SENDER_MAIL_SMTP_PROTOCOL', 'tls');    //  email smtp protocol
        define('_SENDER_MAIL_ID', 'no-reply@sandor.co.in');    //  Display email id for "From" e.g. noreply@blahblah.com
        define('_SENDER_MAIL_USER', 'no-reply@sandor.co.in');  //  Email/username for SMTP authentication e.g. noreply@blahblah.com
        define('_SENDER_MAIL_PWD', 'u6{6$Mx(lX0o'); //  Password for SMTP authentication

        define('_API_HOST_DOMAIN', 'sandor'); //  api host domain url

        define('_USE_S3', false);     //  If S3 bucket to be used for file uploads
        define('_S3_REGION', 'ap-south-1');
        define('_S3_BUCKET_NAME', 'xxxxxx');
        define('_AWS_IAM_USER_KEY', 'xxxxxx');
        define('_AWS_IAM_USER_SECRET', 'xxxxxx');
    }
    else
    {
        die('Oops! Some configuration seems wrong.');
    }
    
    //CHECK PHP VERSION INSTALLED ON THE SERVER
    if(($phpver = phpversion()) < _PHP_BASE_VERSION)
    {
        die('<DIV align="center" style="color:#CC0000;font-family:Arial;padding:10px">Currently PHP version '.$phpver.' is installed on your system and it is not compatible. Please upgrade it to PHP version '._PHP_BASE_VERSION.' or higher</DIV>');
    }

    //  Now let's check if the S3 Bucket exists, readable and writeable
    /*require_once _ROOT.'/aws-sdk/aws-autoloader.php';

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;
    if(_USE_S3)
    {
        // Instantiate an Amazon S3 client.
        $s3Client = new S3Client([
        'version' => 'latest',
        'region'  => _S3_REGION,
        'credentials' => [
                'key'    => _AWS_IAM_USER_KEY,
                'secret' => _AWS_IAM_USER_SECRET
            ]
        ]);

        $BucketsList = $s3Client->listBuckets();
        $buckets = array();
        foreach($BucketsList['Buckets'] as $bucket)
        {
            $buckets[] = $bucket['Name'];
        }

        if(!in_array(_S3_BUCKET_NAME, $buckets))
        {
            die('Oops! S3 Bucket does not exist for <b>'.$_SERVER['HTTP_HOST'].'</b>');
        }
    }*/
    //  Finally before proceeding, let's check if the root directory exists
    if(!file_exists(_ROOT))
    {
        die('Oops! Root directory not found. Please check root directory path for <b>'.$_SERVER['HTTP_HOST'].'</b>');
    } 
    else
    {
        define('_HOST', (_SSL_STATUS ? 'http://' : 'http://')._HOST_DOMAIN);
        define('_API_HOST', (_SSL_STATUS ? 'http://' : 'http://')._API_HOST_DOMAIN);
        // define('_API_HOST_DEV', (_SSL_STATUS_DEV ? 'https://' : 'http://')._API_HOST_DOMAIN_DEV);
        // define('_API_HOST_STAGING', (_SSL_STATUS_STAGING ? 'https://' : 'http://')._API_HOST_DOMAIN_STAGING);
        // define('_API_HOST_PROD', (_SSL_STATUS_PROD ? 'https://' : 'http://')._API_HOST_DOMAIN_PROD);

        if(_USE_S3)
        {
            define('_RESOURCE_HOST', 'http://'._S3_BUCKET_NAME.'.s3.amazonaws.com');
        }
        else
        {
            define('_RESOURCE_HOST', _HOST);
        }
    }

    if(isset($_GET['error_on']))
    {
        error_reporting(-1);
        ini_set('display_errors', 1);
    }
    else
    {
        error_reporting(0);
        ini_set('display_errors', 0);
    }

    /*************************************************************************
     * If everything goes well till here, let's load rest of the variables & constants
    *************************************************************************/
    
    @session_start();

    define('_StartTime', microtime(true));
    define('_IS_LOCAL', $_SERVER['HTTP_HOST'] == _HOST_DOMAIN_LOCAL ? true : false); //  If it's local developer machine

    define('_WebsiteName', 'Harshiv- Your Travel Buddy');
    define('_LOGO', '/images/harshiv-travels-logo.svg');
    define('_LOGO_WHITE', '/images/harshiv-travels-logo.svg');
    
    define('_AdminEmail', 'zishan.sheikh@gitdebug.com');    //  web developer/administrator email for error reporting and admin notifications
    
    define('_AdminRoot', '/AP92SQL493/');
    define('_IncludesDir', '/include-files/');
    define('_AdminIncludesDir', _AdminRoot.'include-files/');

    define('_Version', '?v='.date('dmYgi'));

    define('_WorkingArea', '/files/working-area/');   //  Folder containing temporary files, typically for preparing downloads
    define('_FileSizeLimit', 50000);    //  Max size in KB for uploaded files (single file)
    define('_DefaultPerPage', 20);  //  Default no. of records per page

    /*****************************************************************************
     * Third-Party Credentials
    *****************************************************************************/
    define('_AWS_IAM_KEY', 'xxxxxx');  //  AWS IAM User Key
    define('_AWS_IAM_SECRET', 'xxxxxx');  //  AWS IAM User Secret
    /*****************************************************************************/

    $AllowedImageTypes = array(
                            'jpg' => 'jpg',
                            'jpeg' => 'jpeg',
                            'png' => 'png'
                        );
    $AllowedFileTypes = array(
                            'pdf' => 'pdf',
                            'doc' => 'doc',
                            'docx' => 'docx',
                            'xls' => 'xls',
                            'xlsx' => 'xlsx',
                            'txt' => 'txt',
                        );
    $AllowedFileTypes = array_merge($AllowedFileTypes, $AllowedImageTypes);

    /*******Start of API and Mobile app related variables and constants****************/
    define('_API_ROOT', '/API89182PSK/');  //  root directory to store all api resources
    
    define('_APP_VERSION_USER_AND', '1.1');  //  Live android app version
    define('_APP_VERSION_USER_IOS', '1.1');  //  Live ios app version
    define('_FORCE_UPGRADE_USER_AND', true);
    define('_FORCE_UPGRADE_USER_IOS', true);
    /*******End of API and Mobile app related variables and constants****************/
?>