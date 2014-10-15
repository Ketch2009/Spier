<?php
if (!defined('SiteName')) {header('HTTP/1.0 404 Not Found');die;}
//---------------------------------------------------------
function PrepareMail($link,$Template,$SubstituteArray)
{
$OutArray=array('ToAddress' => '','ToName' => '','Subject'=>'','TmplHTMLBody'=>'');
$OutArray['ToAddress']  = $SubstituteArray['ToAddress'];
$OutArray['ToName']     = $SubstituteArray['ToName'];
//--
if (is_numeric($Template))
  $query  = "SELECT TemplName,TmplSubject,TmplHTMLBody from Templates where idTemplate = ?";
else
  $query  = "SELECT TemplName,TmplSubject,TmplHTMLBody from Templates where TemplName like ?";
$Stmt = GetResult(constant("DBMainDBType"),$link,$query,array($Template));
list($TemplateName,$TmplSubject,$TmplHTMLBody)=GetRowArray($link,$Stmt);
CleanUpResources($Stmt);
$TmplHTMLBody = base64_decode($TmplHTMLBody);
//--
$OutArray['Subject']     = $TmplSubject;
//--
if (substr_count($TmplHTMLBody,'##FirstName##')>0) $TmplHTMLBody=str_replace('##FirstName##','##ContactFirstName##',$TmplHTMLBody);
if (substr_count($TmplHTMLBody,'##ContactFirstName##')>0)
  {
  $NameArray = array();
  preg_match('#^(\w+\.)?\s*([\'\’\w]+)\s+([\'\’\w]+)\s*(\w+\.?)?$#', $SubstituteArray['ToName'], $NameArray);
  if (isset($NameArray[2])) $TmplHTMLBody = str_replace('##ContactFirstName##',htmlentities($NameArray[2], ENT_QUOTES, "UTF-8"),$TmplHTMLBody); else $TmplHTMLBody = str_replace('##ContactFirstName##',htmlentities($SubstituteArray['ToName'], ENT_QUOTES, "UTF-8"),$TmplHTMLBody);
  unset($NameArray);
  }
//--
$OutMail=$TmplHTMLBody;$MaxLen=strlen($TmplHTMLBody);$lp=0;
while ($lp<$MaxLen)
  {
  $st=strpos($TmplHTMLBody,'##',$lp);
  if ($st===false) break; // no more found
  $nd=strpos($TmplHTMLBody,'##',($st+2));
  $HashVar=substr($TmplHTMLBody,$st,$nd-$st+2);//ELog('found hash var :['.$HashVar.']'); // testing
  $lp=($nd+1);
  //--
  if (substr_count($HashVar,' ')>0) continue; // as was just a hash, not a hashvar
  $HashVarNoHash=str_replace('##','',$HashVar);
  if ($HashVarNoHash=='') {$lp=$st+1;continue;} // eg found ##idbugs# - must translate to #123
  //--
  //ELog('lp at ['.$lp.'], found at ['.$st.'] till ['.$nd.'] hashvar ['.$HashVar.'] makes var ['.$HashVarNoHash.']');
  if (!array_key_exists($HashVarNoHash,$SubstituteArray))
    {
    SendAlert('Critical email error - email replacement variable ['.$HashVarNoHash.'] was not found for template ['.$TemplateName.']');
    ELog('Critical email error - email replacement variable ['.$HashVarNoHash.'] was not found for template ['.$TemplateName.']');
    ELog(print_r($SubstituteArray,true));
    return(false);
    }
  $OutMail = str_replace($HashVar,$SubstituteArray[$HashVarNoHash],$OutMail);
  }
$TmplHTMLBody=$OutMail;
//--
$OutArray['TmplHTMLBody']     = $TmplHTMLBody;
return($OutArray);
}
//---------------------------------------------------------
function ToDec($In)
{
$In=trim($In);
$len=strlen($In);
$Out='';
for ($lp=0;$lp<$len;$lp++) $Out.="&#".ord($In[$lp]).";";
return($Out);
}
//---------------------------------------------------------
function ToUTF8($In)
{
$In=trim($In);
$Out="=?utf-8?B?".base64_encode($In)."?=";
return($Out);
}
//---------------------------------------------------------
function SendEmail($link,$MailVarsArray)
{
include_once (constant('LibDir').'class.phpmailer.php');
$ToAddress    = $MailVarsArray['ToAddress'];
$ToName       = $MailVarsArray['ToName'];
$Subject      = $MailVarsArray['Subject'];
$TmplHTMLBody = $MailVarsArray['TmplHTMLBody'];
//--
@ob_end_flush();
@flush();
ob_start();
$mail = new PHPMailer();
$mail->IsSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug   = 0;
$mail->Debugoutput = 'html';
$mail->Host        = constant('SMTPServer');
$mail->Port        = constant('SMTPPort');
if (constant('SMTPAuth')=='1') $mail->SMTPAuth=true; else $mail->SMTPAuth=false;
$mail->Username    = constant('SMTPUser');
$mail->Password    = constant('SMTPPass');
$mail->SetFrom('noreply@TestAuditor.com', 'TestAuditor Team');
$mail->AddReplyTo('noreply@TestAuditor.com','TestAuditor Team');
$mail->AddAddress($ToAddress, $ToName);
//$mail->addCustomHeader("X-Mailer: mailer");
$mail->CharSet = 'UTF-8';
//ELog('a:template was :'.$TmplHTMLBody);
//--
$CurrPos=0;$ImgNo=1; // cid:IMG1:LogoPic.gif --> cid:IMG1
while (strpos($TmplHTMLBody,'<img src=',$CurrPos)>0)
  {
  $CurrPos=strpos($TmplHTMLBody,'<img src=',$CurrPos)+1;                 //ELog('CurrPos :'.$CurrPos);
  $SttPos1=strpos($TmplHTMLBody,"'",$CurrPos);                           //ELog('SttPos1 :'.$SttPos1);
  $SttPos2=strpos($TmplHTMLBody,'"',$CurrPos);                           //ELog('SttPos2 :'.$SttPos2);
  if ($SttPos1=='') $SttPos1=$SttPos2+1;
  if ($SttPos2=='') $SttPos2=$SttPos1+1;
  if ($SttPos1<$SttPos2) $SttPos=$SttPos1; else $SttPos=$SttPos2;        //ELog('SttPos :'.$SttPos);
  //--
  $EndPos1=strpos($TmplHTMLBody,"'",($SttPos+1));                        //ELog('EndPos1 :'.$EndPos1);
  $EndPos2=strpos($TmplHTMLBody,'"',($SttPos+1));                        //ELog('EndPos2 :'.$EndPos2);
  if ($EndPos1=='') $EndPos1=$EndPos2+1;
  if ($EndPos2=='') $EndPos2=$EndPos1+1;
  if ($EndPos1<$EndPos2) $EndPos=$EndPos1; else $EndPos=$EndPos2;        //ELog('EndPos :'.$EndPos);
  //--
  $ImgName=substr($TmplHTMLBody,($SttPos+1),($EndPos-$SttPos-1));        //ELog('ImgName :'.$ImgName);
  if (substr($ImgName,0,4)=='cid:') {/*ELog('already a CID');*/continue;}
  $TmplHTMLBody = str_replace($ImgName,'cid:IMG'.$ImgNo,$TmplHTMLBody);
  $File=str_replace('//','/',constant('DocRoot').constant('RunDir').'/'.$ImgName); //ELog('preparing to embedd file :'.$File);
  $mail->AddEmbeddedImage($File, 'IMG'.$ImgNo, $ImgName);
  $ImgNo++;                                                              //ELog('ImgNo :'.$ImgNo);
  }
//ELog('c:template is now :'.$TmplHTMLBody);
//--
$mail->Subject = $Subject;
$mail->MsgHTML(utf8_encode($TmplHTMLBody));
$Alt=$TmplHTMLBody;if (strpos(strtolower($Alt),'<body>')!==false) $Alt=substr($Alt,strpos(strtolower($Alt),'<body>')+7);$Alt=strip_tags($Alt);
//$Alt=iconv("UTF-8","ISO-8859-1",$Alt);
$Alt=html_entity_decode($Alt, ENT_QUOTES, 'ISO-8859-1');
//$Alt=iconv("ISO-8859-1","UTF-8",$Alt);
$mail->AltBody = $Alt;
$Res=$mail->Send(); // Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
if(!$Res)
  {
  $Ob=@ob_get_contents();
  @ob_end_clean();
  ELog('Error found trying to send email : ['.$Res.']');
  ELog($Ob);
  }
return($Res);
}
//---------------------------------------------------------
?>
