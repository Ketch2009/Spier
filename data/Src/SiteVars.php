<?php
//------------------------ contact and SSL
define('SiteAdminEmail','emailhere@address.com');
define('SendEmailAlerts','Yes'); // 'Yes' or 'No'
define('SiteName','Red Cross Care');
define('UseSSL','No'); // 'Yes' or 'No'
//------------------------ locations
ini_set('register_globals',0); // always have this off, test and dev
ini_set('allow_url_fopen',0); // always have this off, test and dev
ini_set('allow_url_include',0); // always have this off, test and dev
if (@file_exists('/var/www/RedCrossCare/web/index.php'))
  {
  define('host','testprod');
  define('SSLhost','https://testprod'); // must NOT end on / (use with "RewriteBase /")
  define('DocRoot','/var/www');
  define('RunDir','/RedCrossCare/web');
  define('SupportSSL','https://testprod/RedCrossCare/web/'); // TestAuditor Support
  define('GEOIP','/var/www/RedCrossCare/data/geoip/');
  //------------------------ db
  define('DBMainDBType','MySQL'); //MySQL or Oracle or MSSql
  define('DBMainDBServ','127.0.0.1'); // ip or hostname
  define('DBMainDBName','rccmain');
  define('DBMainDBUser','root');
  define('DBMainDBPWord','ta64'); // The DB password is not encrypted. This is possible and easy if required at a later date
  define('DBMainDBPort','22000');
  define('DBCharset','utf8');
  //------------------------
  error_reporting(E_ALL); /* 0 on site, E_ALL on dev */
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('report_memleaks', 1);
  ini_set('xmlrpc_errors', 1);
  ini_set('xmlrpc_error_number', 1);
  ini_set('track_errors', 1);
  ini_set('html_errors', 1);
  @set_time_limit(5000); // default time limit. This is reset for imports.
  //--
  define('SMTPServer','smtp.ntlworld.com');
  define('SMTPPort','25');
  define('SMTPAuth','0');
  define('SMTPUser','');
  define('SMTPPass','');
  }
//------------------------ session and cookies
if (defined('DocRoot'))
  {
  define('LibDir',str_replace('//','/',constant('DocRoot').constant('RunDir').'/../data/Libs/'));
  define('PicDir',str_replace('//','/',constant('RunDir').'/assets/images/'));
  define('JSDir',str_replace('//','/',constant('RunDir').'/assets/js/'));
  define('CSSDir',str_replace('//','/',constant('RunDir').'/assets/css/'));
  //------------------------ locale
  define('charset','UTF-8');
  define('tz','Europe/London');
  date_default_timezone_set(constant('tz'));
  }
if ((defined('DocRoot')) && (isset($_SERVER['REMOTE_ADDR'])))
  {
  //--
  @error_log($error_log, 3, $_SERVER['DOCUMENT_ROOT'] . "/php_error.log");
  //define('SessName','RCCSupp'); // If this is the same as the Session name then the login will fail
  //session_name(constant('SessName'));
  //session_start();
  }
//------------------------
header("Content-Type: text/html");
?>
