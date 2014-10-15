<?php
if (!defined('SiteName')) {header('HTTP/1.0 404 Not Found');die;}
//--------------------------------------------------------------------------------------------
function AddHiddenFormField($Fname,$Fval="0")
{
echo "<input id=\"inp".$Fname."\" autocomplete=\"off\" type=\"hidden\" value=\"".$Fval."\" />\n";
}
//--------------------------------------------------------------------------------------------
function MyFileExists($Chk)
{
//ELog("looking for file [".$Chk."]");
$res=file_exists($Chk);
if (DIRECTORY_SEPARATOR!='/')
  if ($res)
    {
    $fn=str_replace('\\','/',realpath($Chk));
    $fn=substr($fn,strrpos($fn,'/')+1);
    //--
    $Chk=str_replace('\\','/',$Chk);
    $Chk=substr($Chk,strrpos($Chk,'/')+1);
    $res=($fn==$Chk);
    }
return($res);
}
//--------------------------------------------------------------------------------------------
function TimeAgo($UnixTimeStamp,$IncludeSeconds = false)
{
if (trim($UnixTimeStamp)=='') return('never');
$cur_tm = time(); $dif = $cur_tm-$UnixTimeStamp;
$pds = array('second','minute','hour','day','week','month','year','decade');
$lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

$no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s",$no,$pds[$v]);
if(($IncludeSeconds == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= ' '.TimeAgo($_tm);
return $x." ago";
}
//--------------------------------------------------------------------------------------------
function Sec2Time($Seconds)
{
if(is_numeric($Seconds))
  {
  $value = array('years' => 0, 'days' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0);
  if($Seconds >= 86400)   {$value['days']    = floor($Seconds/86400);    $Seconds = ($Seconds%86400);    }
  if($Seconds >= 3600)    {$value['hours']   = floor($Seconds/3600);     $Seconds = ($Seconds%3600);     }
  if($Seconds >= 60)      {$value['minutes'] = floor($Seconds/60);       $Seconds = ($Seconds%60);       }
  $value['seconds'] = floor($Seconds);
  if ($value['hours']<10)   $value['hours']='0'.$value['hours'];
  if ($value['minutes']<10) $value['minutes']='0'.$value['minutes'];
  if ($value['seconds']<10) $value['seconds']='0'.$value['seconds'];
  //-- set granularity
  if ($value['days']!=0)  {$value['minutes']=0;$value['seconds']=0;}
  if ($value['years']!=0) {$value['hours']=0;}
  if ($value['hours']!=0) {$value['seconds']=0;}
  //-- prepare return
  $Ret='';
  if ($value['years']!=0)    $Ret.= $value['years'].' years,';
  if ($value['days']!=0)     $Ret.= $value['days'].' days,';
  if ($value['hours']!=0)    $Ret.= $value['hours'].'h ';
  if ($value['minutes']!=0)  $Ret.= $value['minutes'].'m ';
  if ($value['seconds']!=0)  $Ret.= $value['seconds'].'seconds';
  return($Ret);

  //return($value['days'].' days,'.$value['hours'].'h:'.$value['minutes'].':'.$value['seconds']);
  }
else
  {
  return (bool) false;
  }
}
//--------------------------------------------------------------------------------------------
function BrowserLang()
{
$UsrLang='en';
if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $UsrLang=$_SERVER['HTTP_ACCEPT_LANGUAGE'];
if (substr_count($UsrLang,',')>0) $UsrLang=substr($UsrLang, 0, strpos($UsrLang,','));
if (substr_count($UsrLang,';')>0) $UsrLang=substr($UsrLang, 0, strpos($UsrLang,';'));
if (strlen($UsrLang)>5) $UsrLang=substr($UsrLang, 0, 5);
$UsrLang=strtolower($UsrLang);
return($UsrLang);
}
//--------------------------------------------------------------------------------------------
function GetSelfName($OrigScriptName,$ToAddToScriptName) // This returns the full path and file name
{
$Dir=dirname($OrigScriptName); // on windows you may need to do a getcwd()
$Dir=str_replace('\\','/',$Dir);
if (substr($Dir,-1)!="/") $Dir.='/';
$pth=parse_url($OrigScriptName);
$RetScriptName=basename($pth['path']);
return($Dir.$ToAddToScriptName.$RetScriptName);
}
//--------------------------------------------------------------------------------------------
function MyEncrypt($StrToWorkWith,$DoBase64=false)
{
$OutString="";
// choose a method of encryption.
// 1) = AA
$SL=strlen($StrToWorkWith);
//ELog('SL:'.$SL);
for ($a=0;$a<$SL;$a++) $OutString.=chr(ord($StrToWorkWith[$a]) ^ 255);
//$OutString=$StrToWorkWith;
//ELog('Encrypted is :['.$OutString.']');
if ($DoBase64) $OutString=base64_encode($OutString);
$OutString=rawurlencode($OutString);
$OutString="AA:".$OutString; // notification that AA was used.
ELog('base64encoded :['.$OutString.']');
// 2) = BB
// ?
return($OutString);
}
//--------------------------------------------------------------------------------------------
function MyDecrypt($StrToWorkWith,$DoBase64=false)
{
$OutString="";
$StrToWorkWith=html_entity_decode($StrToWorkWith);
$Meth=substr($StrToWorkWith,0,3);
$StrToWorkWith=substr($StrToWorkWith,3);

switch($Meth)
  {
  case 'AA:' ://ELog('base64encoded :['.$StrToWorkWith.']');
              $StrToWorkWith = rawurldecode($StrToWorkWith);
              if ($DoBase64) $StrToWorkWith=base64_decode($StrToWorkWith);
              $SL=strlen($StrToWorkWith);
              //ELog('SL:'.$SL);
              for ($a=0;$a<$SL;$a++) {
                  $OutString.=chr(ord($StrToWorkWith[$a]) ^ 255);
              }
              //ELog('Decrypted is :['.$OutString.']');
              //$OutString=$StrToWorkWith;
              break;
  default    :if ($DoBase64) $OutString=base64_decode($StrToWorkWith); else $OutString=$StrToWorkWith;
  }
// you should string the 'AA:' prefix here
return($OutString);
}
//--------------------------------------------------------------------------------------------
function data_uri($file, $mime)
{
$file=constant("DocRoot").$file;
$file=str_replace ('\\', '/', $file);
$contents = file_get_contents($file);
$base64   = base64_encode($contents);
return ('data:' . $mime . ';base64,' . $base64);
}
//--------------------------------------------------------------------------------------------
function AddImg($Img,$StyleMore,$alt,$ttl,$idn) // Should actually add CLASS
{
// At some stage implement an all image caching system, like this:
// change all CSS URLs to blah.php?imagename.jpg
// change all links set here to src="blah.php?imagename.jpg"
// then create blah.php which returns headers and the image only from the picdir directory :)
$Res="";
if (basename($_SERVER['PHP_SELF'])==basename(__FILE__)) {SendAlert(addslashes($Img).' hacker attempt:'.__function__);return;}
$Img=constant("PicDir").$Img;
if (!MyFileExists(constant("DocRoot").$Img)) {SendAlert('image:'.constant("DocRoot").$Img.' not found:'.__function__);return;}
if( !is_readable(constant("DocRoot").$Img))  {SendAlert('image:'.constant("DocRoot").$Img.' not readable:'.__function__);return;}
$FSize=filesize(constant("DocRoot").$Img);
$Res.="<img ";
if (trim($idn)!="") $Res.="id='".$idn."' ";
if (trim($alt)!="") $Res.="alt='".$alt."' ";
if (trim($ttl)!="") $Res.="title='".$ttl."' ";
if (($FSize<1000) && (substr_count($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0")==0) && (substr_count($_SERVER['HTTP_USER_AGENT'],"MSIE 7")==0))
  $Res.="src='".data_uri($Img,'image/png')."' ";
else
  $Res.="src='".$Img."' ";
list($ImgWidth,$ImgHeight)=getimagesize(constant("DocRoot").$Img);
$Res.="style='width:".$ImgWidth."px;height:".$ImgHeight."px;".$StyleMore."' />";
return($Res);
}
//--------------------------------------------------------------------------------------------
function SendAlert($Msg)
{
ELog('SendAlert - starting');
$Go=strtoupper(constant("SendEmailAlerts"));
if (($Go!="YES") && ($Go!="Y")) {ELog('aborting mail send');return;}
if (isset($_SERVER["SERVER_NAME"])) $Msg.='<br />Sent from server :'.$_SERVER["SERVER_NAME"].'<br />';
//------
$headers='';$message=''; /* This is a build up of email : cannot use library as they have not been loaded */
//------
$SenderName="TestAuditor Team";
$SenderAddr="<".constant("SiteAdminEmail").">";
$headers .= "From: \"".$SenderName."\" ".$SenderAddr."\r\n";
$headers .= "Reply-To: \"".$SenderName."\" ".$SenderAddr."\r\n";
$headers .= "Return-Path: \"".$SenderName."\" ".$SenderAddr."\r\n";
$headers .= "Content-Language: en-gb\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "X-Priority: 3\r\n";
$headers .= "Importance: Normal\r\n";
$headers .= "Content-Type: text/html;\r\n  charset=\"US-ASCII\"\r\nContent-Transfer-Encoding: 7bit\r\n";
//------
$message .="<html><head>";
$message .="<style type='text/css'>\np {font: normal normal normal 12px Verdana;}\n</style>\n";
$message .="</head>\n";
ELog("[".$Msg."]".nl2br($Msg));
$message .="<body>Time : ".date("Y-m-d H:i:s")."<br />".nl2br($Msg)."</body>\n";
$message .="</html>\n";
$To="support@".str_replace(' ','',constant("SiteName")).".com";if (defined("SiteAdminEmail")) $To=constant("SiteAdminEmail");
ELog("To:".$To);
ELog("SenderName:".$SenderName);
ELog("message:".$message);
ELog("headers:".$headers);
$Res=@mail($To,'Site Alert',$message,$headers);
return($Res);
}
//--------------------------------------------------------------------------------------------
function es($string,$whattodo) // eg $aa=es($_POST['aa'],'trim,tags,HTE');
{
$WTDarray=explode(',',$whattodo);
$stringToEscape = $string;
foreach($WTDarray as $Action)
  {
  switch ($Action)
    {
    case 'trim' :$stringToEscape = trim($stringToEscape);break;
    case 'hde'  :$stringToEscape = html_entity_decode($stringToEscape);break;
    case 'tags' :$stringToEscape = strip_tags($stringToEscape);break;
    case 'b64'  :$stringToEscape = base64_decode($stringToEscape);break;
    case 'HTE'  :$stringToEscape = htmlentities($stringToEscape, ENT_QUOTES, "UTF-8");break;
    case 'adds' :$stringToEscape = addslashes($stringToEscape);break;
    default     :die('Escape action ['.$Action.'] not found');
    }
  }
return $stringToEscape;
}
//--------------------------------------------------------------------------------------------
function AddJScript($UseDir,$lkfor, $_encoding = 62, $_fastDecode = true, $_specialChars = false)
{
//--
//$Extra="";
//if (isset($_SERVER['PHP_SELF'])) {$Extra.="|".parse_url($_SERVER['PHP_SELF'],PHP_URL_PATH)."|".parse_url($_SERVER['PHP_SELF'],PHP_URL_HOST);}
//$fp = fopen("AddJScript.txt", "a");
//fwrite($fp,$lkfor.$Extra."\n");
//fclose($fp);
include_once (constant("LibDir")."class.JavaScriptPacker.inc.php");
//--
if ($UseDir=="") $script=constant('DocRoot').constant("JSDir").$lkfor; else $script=$UseDir.$lkfor;
$script=str_replace('\\','/',$script);
$script=str_replace('//','/',$script);
if (trim($script) == "")    { echo "<!-- *".$lkfor." no script name* -->\n";return; }
if (!MyFileExists($script)) { echo "<!-- *".$script." not found* -->\n";return; }
if (!is_readable($script))  { echo "<!-- *".$lkfor." not readable* -->\n";return; }
//header("content-type: application/x-javascript");
echo "<script type=\"text/JavaScript\" language=\"JavaScript\">\n";
ob_start(); // start buffer
include ($script);
$filecontent = ob_get_contents(); // assign buffer contents to variable
//ob_end_flush();
ob_end_clean(); // end buffer and remove buffer contents
$SomthingBad=false;
if (substr_count($filecontent,'<b>Notice</b>')>0)  $SomthingBad=true;
if (substr_count($filecontent,'<b>Error</b>')>0)   $SomthingBad=true;
if (substr_count($filecontent,'<b>Warning</b>')>0) $SomthingBad=true;
if ($SomthingBad==true)
  {
  echo "alert('Ooops there is a problem with the script ".basename($script)."');\n";
  //echo "<!---- ".$lkfor." ------>\n";
  echo $filecontent;
  }
else
  {
  if (substr_count($filecontent,'#Debug#')>0)
    echo $filecontent;
  else
    {
    $packer = new JavaScriptPacker($filecontent,$_encoding,$_fastDecode,$_specialChars);
    echo $packer->pack();
    }
  }
echo "</script>\n";
}
//--------------------------------------------------------------------------------------------
function EchoMeta($TitleExtra)
{
echo '<title>TestAuditor - '.$TitleExtra.'</title>'."\n";
echo '    <meta name="description" content="TestAuditor offers test management software which can assist project teams, departments &amp; entire organizations in making tough decisions. TestAuditor aims to be a replacement of Quality Center starting with importing their projects." />'."\n";
echo '    <meta name="keywords"    content="test management, test management software, software test management, software testing management, testing management software, quality center,testopia, bugzilla" />'."\n";
echo '    <meta name="author"      content="Dev Team" />'."\n";
echo '    <meta name="copyright"   content="Copyright 2013. All rights reserved." />'."\n";
echo '    <meta name="robot"       content="index,follow">'."\n";
echo '    <meta name="rating"      content="General" />'."\n";
echo '    <meta name="GOOGLEBOT"   content="index follow" />'."\n";
echo '    <meta name="revisit"     content="28 days" />'."\n";
$IconWebPath=$_SERVER["PHP_SELF"];
$IconWebPath=substr($IconWebPath,0,strrpos($IconWebPath,"/")+1);
$IconDirPath=$_SERVER["SCRIPT_FILENAME"];
$IconDirPath=str_replace ("\\", "/", $IconDirPath);
$IconDirPath=substr($IconDirPath,0,strrpos($IconDirPath,'/')+1);
if (MyFileExists($IconDirPath.'favicon.ico'))
  {
  echo "    <link rel=\"shortcut icon\" href=\"".constant('RunDir')."/favicon.ico\" type=\"image/ico\" />\n";
  echo "    <link rel=\"icon\" href=\"".constant('RunDir')."/favicon.ico\" type=\"image/vnd.microsoft.icon\" />\n";
  }
//---
}
//--------------------------------------------------------------------------------------------
?>
