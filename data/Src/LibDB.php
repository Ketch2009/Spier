<?php
if (!defined('SiteName')) {header('HTTP/1.0 404 Not Found');die;}
//--------------------------------------------------------------------------------------------
function FixNull($val)
{
$val=trim($val);
if (strtolower($val)=='null')
  return(' is null');
else
  {
  if (is_numeric($val)) return('=0'.$val);  else return("='".$val."'");
  }
}
//--------------------------------------------------------------------------------------------
function GetResult($DBType,$link,$Qry,$ValsArray)
{
if (substr_count($Qry,'?')!=count($ValsArray)) die('PDO :'.$Qry);
if ($link===false) return(false);
$SetDBStr=SetDBStr($Qry,$DBType);
$Stmt = $link->prepare($SetDBStr);
$Worked = $Stmt->execute($ValsArray); //ELog( print_r( $ValsArray, true));
//$count = $Stmt->rowCount();
if ($Worked)
  return($Stmt);
else
  {
  if (function_exists('ELog'))
    ELog(substr_count($SetDBStr,'?').':'.count($ValsArray).':'.$Qry.':'.$SetDBStr.':'.$Stmt->queryString);
  SendAlert(substr_count($SetDBStr,'?').':'.count($ValsArray).':'.$Qry.':'.$SetDBStr.':'.$Stmt->queryString);
  return(false);
  }
}
//--------------------------------------------------------------------------------------------
function GetRowArray($link,$Stmt)
{
if ($Stmt==false) {if (function_exists('ELog')) ELog('statement is false'); SendAlert('DB Error'); return(false);}
if (@get_class($Stmt)!='PDOStatement')
  {
  if (function_exists('ELog')) ELog('FATAL Error : Not a PDO statement');
  SendAlert('DB Error');
  return(false);
  }
$rowArray = $Stmt->fetch(PDO::FETCH_ASSOC); // **** you CANNOT have duplicate key names. ie [ SELECT Name,Name from ] will not work. You must use [ SELECT Name as 'N1',Name as 'N2' from ]

//ELog($Stmt->queryString);
if (is_array($rowArray)) return(array_values($rowArray)); else return(false);// ELog( print_r($rowArray,true).':'.$Stmt->queryString );
}
//--------------------------------------------------------------------------------------------
function CleanUpResources(&$Stmt)
{
$Res=true;
if ($Stmt!==false) $Res=$Stmt->closeCursor();
return($Res);
}
//--------------------------------------------------------------------------------------------
function SetDBStr($InpStr,$DBType)
{
$Res="";
switch(strtolower($DBType))  // die(SetDBStr('select abc from  def  where a=b order by jhi','MSSql'));
  {
  case 'mssql' :while (strpos(strtolower($InpStr),"from")!==False)
                  {
                  $InpStr=str_replace("  "," ",$InpStr);
                  $TblNameStartPos=strpos(strtolower($InpStr),"from");$TblNameStartPos+=5;
                  $Res.=substr($InpStr,0,$TblNameStartPos);
                  $TblNameLen=strlen($InpStr)-$TblNameStartPos;
                  if (strpos($InpStr," ",$TblNameStartPos+1)!==False)
                    {
                    $EP=strpos($InpStr," ",$TblNameStartPos+1);
                    $TblNameLen=$EP-$TblNameStartPos;
                    }
                  $TblString=substr($InpStr,$TblNameStartPos,$TblNameLen);
                  $InpStr=substr($InpStr,$TblNameStartPos+$TblNameLen);
                  $Res.=$TblString;
                  }
                //$Res.=substr($InpStr,$TblNameStartPos+$TblNameLen);
                $Res.=$InpStr;
                break;
  case 'oci'   :$Res=$InpStr;break;
  case 'mysql' :$Res="";$MovePos=0;$MyFlag=False;
                $InpStr=str_replace("'",'"',$InpStr);
                if (strpos($InpStr,"\"")!==False)
                  {
                  while (strpos($InpStr,"\"",$MovePos+1)!==False)
                    {
                    if (!$MyFlag)
                      $Res.=strtolower(substr($InpStr,$MovePos,strpos($InpStr,'"',$MovePos+1)-$MovePos));
                    else
                      $Res.=substr($InpStr,$MovePos,strpos($InpStr,'"',$MovePos+1)-$MovePos);
                    $MyFlag=(!$MyFlag);
                    $MovePos=strpos($InpStr,'"',$MovePos+1);
                    }
                  $Res.=strtolower(substr($InpStr,$MovePos));
                  }
                else
                  $Res=strtolower($InpStr);
                break;
  case 'Access':break;
  default:die($DBType.': unknown DB type :'.__FUNCTION__);
  }
//--------
return ($Res);
}
//--------------------------------------------------------------------------------------------
function SingExec($Qry,$link,$DBType,$ValsArray)
{
if ($link==false) return(false);
if (substr_count($Qry,'?')!=count($ValsArray)) die('PDO :'.$Qry);
switch(strtolower($DBType))
  {
  case 'mysql'    :$SetDBStr=SetDBStr($Qry,$DBType);
                   break;
  case 'oci'      :
  case 'pgsql'    :
  case 'mssql'    :
  case 'informix' :
  case 'sqlite'   :break;
  default:die($DBType.': unknown DB type :'.__FUNCTION__);
  }
$Stmt = $link->prepare($SetDBStr);
$Worked = $Stmt->execute($ValsArray);
if (!$Worked)
  {
  if (function_exists('ELog'))
    ELog('SingExec failed :'.substr_count($SetDBStr,'?').':'.count($ValsArray).':'.GetDBErrors($link,$DBType).':'.$Stmt->queryString.':'.$Qry);
  SendAlert('SingExec failed :'.substr_count($SetDBStr,'?').':'.count($ValsArray).':'.GetDBErrors($link,$DBType).':'.$Stmt->queryString.':'.$Qry);
  }
else
  CleanUpResources($Stmt);
return($Worked);
}
//--------------------------------------------------------------------------------------------
function SimpDB($Qry,$Dflt,$DBType,$link,$ValsArray)
{
if ($link==false) return(false);
//----
if (substr_count($Qry,'?')!=count($ValsArray)) die('PDO :'.$Qry);
switch(strtolower($DBType))
  {
  case 'mysql'    :$SetDBStr=SetDBStr($Qry,$DBType) . " limit 1";
                   break;
  case 'oci'      :
  case 'pgsql'    :
  case 'mssql'    :
  case 'informix' :
  case 'sqlite'   :break;
  default:die($DBType.': unknown DB type :'.__FUNCTION__);
  }
//----
$Stmt = $link->prepare($SetDBStr);
$Worked = $Stmt->execute($ValsArray);
if ((!$Worked) || (!$Stmt)) die('PDO :'.$SetDBStr);
$rowArray = $Stmt->fetch(PDO::FETCH_ASSOC);
$MyRetArray = GetRowArray($link,$Stmt);
if (is_array($rowArray))
  {
  $rowArray=array_values($rowArray);
  $MyRet=$rowArray[0];
  CleanUpResources($Stmt);
  }
else $MyRet=$Dflt;
return($MyRet);
}
//--------------------------------------------------------------------------------------------
function CloseLink(&$link,&$Stmt)
{
if ($Stmt) CleanUpResources($Stmt);
$link = null;
}
//--------------------------------------------------------------------------------------------
function GetMainLink()
{
$link=GetDBLink(constant("DBMainDBType"),
                constant("DBMainDBServ"),
                constant("DBMainDBName"),
                '',
                constant("DBMainDBUser"),
                constant("DBMainDBPWord"),
                constant("DBMainDBPort"),
                constant("DBCharset")
                );
if ($link===false)
  {
  SendAlert('Link is not a real resource',__FUNCTION__);
  if (function_exists('ELog'))
    ELog('Link is not a real resource - database link has failed'.constant("DBMainDBType").' '.constant("DBMainDBServ").' '.constant("DBMainDBName").' '.constant("DBMainDBUser").' '.constant("DBMainDBPWord").' '.constant("DBMainDBPort").' '.constant("DBCharset"));
  }
return($link);
}
//--------------------------------------------------------------------------------------------
function GetDBLink($DBType,$DBServerName,$DBMainDB,$DBSession,$DBUser,$DBPasswd,$DBPort,$Charset)
{
//----
$DBType=strtolower($DBType);
switch($DBType)
  {
  case 'mysql'    :if ($DBPort=="") $DBPort="3306";
                   $cs=$DBType.':host='.$DBServerName.';port='.$DBPort.';dbname='.$DBMainDB.';charset='.$Charset;
                   break;
  case 'oci'      :if ($DBPort=="") $DBPort="1521";
                   $cs=$DBType.':oci:dbname=//'.$DBServerName.':'.$DBPort.'/'.$DBSession.'/'.$DBMainDB.''; // //host:port/SID/INSTANCE_NAME
                   break;
  case 'pgsql'    :if ($DBPort=="") $DBPort="5432";
                   $cs=$DBType.':host='.$DBServerName.';port='.$DBPort.';dbname='.$DBMainDB.';charset='.$Charset;
                   break;
  case 'mssql'    :if ($DBPort=="") $DBPort="1433";
                   $cs=$DBType.':host='.$DBServerName.',port='.$DBPort.';dbname='.$DBMainDB;
                   break;
  case 'informix' :$cs=$DBType.':DSN='.$DBMainDB;
                   break;
  case 'sqlite'   :$cs=$DBType.':'.$DBMainDB;
                   break;
  default:die($DBType.': unknown DB type :'.__FUNCTION__);
  }
//----
//ELog($cs);
try
  {
  $link = new PDO($cs, $DBUser, $DBPasswd);
  $link->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
  // $link->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
  // $link->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
  // $link->_autoCommit = true;
  // setAttribute("PDO::MYSQL_ATTR_USE_BUFFERED_QUERY", true); - fix for error "Lost connection to MySQL server during query"
  return($link);
  }
catch(PDOException $e)
  {
  return(false);
  }
}
//--------------------------------------------------------------------------------------------
function GetDBErrors($link,$DBType)
{
$Errstr='';
$ErrArray=$link->errorInfo();
foreach( $ErrArray as $error ) $Errstr.=$error.' ';
$PhpVer = substr(preg_replace("/[^0-9.]/",'',phpversion()),0,5);
if ($PhpVer<'5.4')
  $Errstr=htmlspecialchars($Errstr, ENT_QUOTES, 'UTF-8');
else
  $Errstr=htmlspecialchars($Errstr, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$Errstr=str_replace('|','&#124;',$Errstr);
return(str_replace(array("\r\n", "\n", "\r","'"), '',$Errstr ));
}
//--------------------------------------------------------------------------------------------
?>
