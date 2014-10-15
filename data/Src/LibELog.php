<?php
if (!defined('SiteName')) {header('HTTP/1.0 404 Not Found');die;}
//---------------------------------------------------------
function PurgeELog()
{
if (constant("DoDebug")===false) return;
//-
if (isset($_SERVER['REMOTE_ADDR']))
  $logfile=constant("DocRoot").dirname($_SERVER['PHP_SELF']).'/log.txt';
else
  $logfile=constant("DocRoot").constant("RunDir").'/log.txt';
@chmod($logfile, 0755);
//--
$fp = @fopen($logfile, "r");
$readArray=array();$lines=100;$LogLines='';
if ($fp)
  {
  $offset=-1;$CurLine='';
  while( $lines && fseek($fp, $offset, SEEK_END) >= 0 )
    {
    $c = fgetc($fp);
    if (ord($c)==0x0a)
      {
      $lines--;
      $readArray[]=strrev($CurLine);$CurLine='';
      }
    else
      $CurLine.=$c;
    $offset--;
    }
  fclose ($fp);
  $readArray=array_reverse($readArray);
  $LogLines=implode("\n",$readArray);
  }
//--
$fp=@fopen($logfile,'w');
if (is_resource($fp))
  {
  fwrite($fp,$LogLines);
  fwrite($fp,date("Y-m-d H:i:s")."\n");
  fwrite($fp,date("Y-m-d H:i:s")."\n");
  fwrite($fp,date("Y-m-d H:i:s")."\n");
  fwrite($fp,date("Y-m-d H:i:s")."\n");
  fwrite($fp,date("Y-m-d H:i:s")."\n");
  fwrite($fp,date("Y-m-d H:i:s")."\n");
  $TraceBackArray=debug_backtrace();
  fwrite($fp,date("Y-m-d H:i:s")." --- Log purge here ---( ".basename($TraceBackArray[0]['file'])." started )\n");
  fclose($fp);
  }
return($logfile);
}
//---------------------------------------------------------
function ELog($Msg)
{
if (constant("DoDebug")===false) return;
//if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) return; // only necessary if echoing to screen
if (!defined('DebugArea')) define('DebugArea','All');
$TraceBackArray=debug_backtrace();$Initiator=count($TraceBackArray)-1;$Trace="";
if ((substr_count(constant("DebugArea"),$TraceBackArray[$Initiator]['function'])>0) || (constant("DebugArea")=="All"))
  {
  for ($lp=$Initiator;$lp>-1;$lp--) $Trace.=GetFuncTrace($TraceBackArray[$lp]);
  //--
  if (isset($_SERVER['REMOTE_ADDR']))
    $logfile=constant("DocRoot").dirname($_SERVER['PHP_SELF']).'/log.txt';
  else
    $logfile=constant("DocRoot").constant("RunDir").'/log.txt';
  $fp=@fopen($logfile,'a');
  if (is_resource($fp))
    {
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $d = new DateTime( date('Y-m-d H:i:s.'.$micro,$t) );
    fwrite($fp,$d->format("Y-m-d H:i:s.u")." ".$Trace.$Msg."\n");
    fclose($fp);
    }
  //--
  unset($TraceBackArray);
  }
//you should capture a log to the DB here if the $link isresource to trace any errors
}
//---------------------------------------------------------
function GetFuncTrace(&$ItemsArray)
{
$CallFile=$ItemsArray['file'];
$CallFile=str_replace('\\','/',$CallFile);
$CallFile=substr($CallFile,strrpos($CallFile,'/')+1);
$CallFile=substr($CallFile,0,strrpos($CallFile,'.'));
$CallFunc=".".$ItemsArray['function'];
if (trim($CallFunc)==".ELog") $CallFunc="";
$CallLine=$ItemsArray['line'];
return($CallFile.$CallFunc."(".$CallLine.")-->");
}
//---------------------------------------------------------
?>
