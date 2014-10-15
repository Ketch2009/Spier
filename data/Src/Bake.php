<?php
$dir        = '../DBs';
//--------------------------------------------------------------------------------
if ( isset($_POST['action'],$_SERVER['HTTP_X_REQUESTED_WITH']) )
  {
  switch($_POST['action'])
    {
    case 'GetTables':
      $TblArray=FillTableArray($dir,es($_POST['fname'],'trim,tags,HTE'));
      $Tbls="";
      foreach ($TblArray as $tbl)
        {
        $Tbls.="<option value='".$tbl."'>".$tbl."</option>";
        }
      echo "$('#inpTable').html(\"".$Tbls."\")|";
      break;
    }
  die;
  }
if ( isset($_POST['GetCode'],$_SERVER['HTTP_X_REQUESTED_WITH']) )
  {
  $FormFields=GetFormFields($dir,es($_POST['fname'],'trim,tags,HTE'),es($_POST['TblName'],'trim,tags,HTE'));
  switch(es($_POST['GetCode'],'trim,tags,HTE'))
    {
    case '1':GetForm($FormFields);break;
    case '2':GetTableForm($FormFields);break;
    case '3':GetJS($FormFields);break;
    case '4':GetPHPDB($FormFields);break;
    case '5':GetLoopDB($FormFields);break;
    case '6':GetModal($FormFields);break;
    }
  die;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="The Red Cross Team">
  <title>Red Cross Care Code facility</title>
  <!-- CSS -->
  <link href="../../web/assets/css/bootstrap.min.css" rel="stylesheet" media="screen">
  <link href="../../web/assets/css/font-awesome.min.css" rel="stylesheet" media="screen">
  <script src="../../web/assets/js/jquery-1.11.1.min.js"></script>
  <script src="../../web/assets/js/bootstrap.min.js"></script>
  <script src="../../web/assets/js/jquery.base64.min.js"></script>
  <script src="../../web/assets/js/AjaxHandler.js"></script>
  <link href="data:image/x-icon;base64,AAABAAEAEBAAAAAAAABoBQAAFgAAACgAAAAQAAAAIAAAAAEACAAAAAAAAAEAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAD/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAQEBAAAAAAAAAAAAAAAAAQEBAQAAAAAAAAAAAAAAAAEBAQEAAAAAAAAAAAAAAAABAQEBAAAAAAAAAAABAQEBAQEBAQEBAQEAAAAAAQEBAQEBAQEBAQEBAAAAAAEBAQEBAQEBAQEBAQAAAAABAQEBAQEBAQEBAQEAAAAAAAAAAAEBAQEAAAAAAAAAAAAAAAABAQEBAAAAAAAAAAAAAAAAAQEBAQAAAAAAAAAAAAAAAAEBAQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP//AAD//wAA/D8AAPw/AAD8PwAA/D8AAMADAADAAwAAwAMAAMADAAD8PwAA/D8AAPw/AAD8PwAA//8AAP//AAA=" rel="icon" type="image/x-icon" />
</head>
<body style="background:url('../../web/assets/images/bgfusion.png');">
  <?php
  ShowStarter($dir);
  ?>
</body>
<code>
</code>
</html>
<?php
//--------------------------------------------------------------------------------
function ShowStarter($dir)
{
function Usrsort($a,$b) { return strcasecmp($a[0], $b[0]);}
$dh         = opendir($dir);
$filesArray = array();
if (is_resource($dh))
  {
  while (false !== ($filename = readdir($dh))) {if ( (is_file($dir.'/'.$filename)) && (substr_count(strtolower($filename),'.sql')>0) ) $filesArray[] = $filename;}
  closedir($dh);
  usort($filesArray, "Usrsort");
  } else die('cant open dir');
echo "
  <div class='container' style='margin-top:10px;background-color:#fff;'>
    <div class='row'>
      <div class='col-md-12' style='padding:20px;border:1px solid #1a1a1a;box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.6);border-radius: 4px;'>
        <form class='form-horizontal' role='form'>
          <!-- xxx -->
          <div class='form-group' id='selDB'>
            <label class='col-xs-2 control-label' for='inpDatabase'>Database</label>
            <div class='col-xs-5'>
              <select class='form-control' id='inpDatabase'>";
foreach ($filesArray as $DB) echo "<option value='".$DB."'>".str_replace('.sql','',$DB)."</option>";
echo "
              </select>
            </div>
          </div>
          <!-- xxx -->
          <div class='form-group' id='Stage2Tbl' style='display:none;'>
            <label class='col-xs-2 control-label' for='inpTable'>Table</label>
            <div class='col-xs-5'>
              <select class='form-control' id='inpTable'>
              </select>
            </div>
          </div>
          <!-- xxx -->
          <div class='form-group' id='Stage3Tbl' style='display:none;'>
            <label class='col-xs-2 control-label' for='inpCode'>Code Option</label>
            <div class='col-xs-5'>
              <select class='form-control' id='inpCode'>
                <option value='1'>Show form on screen</option>
                <option value='2'>table display of info</option>
                <option value='3'>javascript for table</option>
                <option value='4'>php - select, update, delete - off form</option>
                <option value='5'>php - select in loop</option>
                <option value='6'>Show form modal</option>
              </select>
            </div>
          </div>
          <!-- xxx -->
        </form>
        <button id='btnStage1' class='btn btn-primary pull-right' type='button'>Next <i class='fa fa-arrow-right'></i></button>
        <button id='btnStage2' class='btn btn-primary pull-right' type='button' style='display:none;'>Generate <i class='fa fa-arrow-right'></i></button>
        <button id='btnClrCode' class='btn btn-default' type='button' style='display:none;'>Clear <i class='fa fa-trash'></i></button>
      </div>
    </div>
  </div>
<script>
  $(document).ready(function()
  {
  //--
  $('#btnStage1').click(function(e)
    {
    e.preventDefault();
    $('#selDB').slideUp();
    $('#btnStage1').hide();
    $('#Stage2Tbl').show();
    $('#Stage3Tbl').show();
    $('#btnStage2').show();$('#btnClrCode').show();
    jQuery.post('".$_SERVER['PHP_SELF']."',
      {
      'action'     : 'GetTables',
      'fname'      : $('#inpDatabase').val()
      }, function(data) { HandleAjaxResponse(data); });
    });
  //--
  $('#btnStage2').click(function(e)
    {
    e.preventDefault();
    jQuery.post('".$_SERVER['PHP_SELF']."',
      {
      'GetCode'      : $('#inpCode').val(),
      'fname'        : $('#inpDatabase').val(),
      'TblName'      : $('#inpTable').val()
      }, function(data) { HandleAjaxResponse(data); });
    });
  //--
  $('#btnClrCode').click(function(e)
    {
    e.preventDefault();
    $('code').html('');
    });
  //--
  });
</script>
";
}
//--------------------------------------------------------------------------------
function FillTableArray($dir,$fname)
{
function FuncFileSort($a, $b) {return strcmp(strtolower($a), strtolower($b));};
$RetArray=array();
$inpSQLstruct=$dir.'/'.$fname;
if (file_exists($inpSQLstruct))
  {
  $fp=fopen($inpSQLstruct,'r'); // read only
  if ($fp)
    {
    while ( ($tmp=fgets($fp)) !== false)
      {
      if (substr_count($tmp,'CREATE TABLE IF NOT EXISTS ')>0)
        {
        $tmp=trim($tmp); // I added this line for Windows
        $tmp=substr($tmp,0,-3); // I changed '-4' to '-3'
        $tmp=substr($tmp, strrpos($tmp,"`")+1);
        $RetArray[]=$tmp;
        }
      }
    fclose($fp);
    } else die('cant open the file for reading');
  }
usort($RetArray, "FuncFileSort");
return($RetArray);
}
//--------------------------------------------------------------------------------
function es($string,$whattodo) // eg $aa=es($_POST['aa'],'trim,tags,HTE');
{
// *** you may need to use the QUOTE function as a replacement for realescapestring
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
//--------------------------------------------------------------------------------
function GetFormFields($dir,$fname,$TblName)
{ //array(FieldName=>array(0=>Form Display, 1=>SelectDBName, 2=>PHPName, 3=>IsPri, 4=>IsFormField, 5=>FieldType, 6=>FieldLen))
//--
function SplitByUpperCase($In)
{
$Out="";
for ($lp=0;$lp<strlen($In);$lp++)
  {
  $ThisChar=substr($In,$lp,1);
  if (($ThisChar==strtoupper($ThisChar)) && ($lp!=0)) $Out.=' ';
  $Out.=$ThisChar;
  }
$Out=str_replace('  ',' ',$Out);
$Out=str_replace('  ',' ',$Out);
return($Out);
}
//--
function GetFieldName($line) //--   `idTestsTree` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
{
$line=substr($line,strpos($line,"`")+1);
$line=substr($line,0,strpos($line,"`"));
return(trim($line));
}
//--
function GetFieldInfo($FieldName,$line) //--   `idTestsTree` INT UNSIGNED NOT NULL AUTO_INCREMENT ,   `flgUsedGoogle` VARCHAR(1) NULL,
{
$InfoArray=array();
$InfoArray[0]='yada';
$InfoArray[1]=$FieldName;
$InfoArray[2]=$FieldName;
$InfoArray[3]='N';
$InfoArray[4]='Y';
if ($FieldName=='flgDeleted')      $InfoArray[4]='N';
if ($FieldName=='CreatedOn')       $InfoArray[4]='N';
if ($FieldName=='LastUpdated')     $InfoArray[4]='N';
if ($FieldName=='idUpdatedByUser') $InfoArray[4]='N';
$InfoArray[6]=0;
//--
$line=str_replace($FieldName,'',$line);
$line=str_replace("`",'',$line);
$line=str_replace("UNSIGNED",'',$line);
$line=str_replace("NOT",'',$line);
$line=str_replace("NULL",'',$line);
$line=str_replace("AUTO_INCREMENT",'',$line);
$line=trim($line);
//--
$InfoArray[6]=0;
if (substr_count($line,"(")>0) {$St=strpos($line,'(');$Nd=strpos($line,')')-1;$InfoArray[6]=substr($line,$St+1,$Nd-$St);$line=substr($line,0,$St);}
//--
$InfoArray[5]=strtolower($line);
//--
if ( (substr($FieldName,0,4)=='bopt') && (substr_count( $InfoArray[5],'varchar')>0) ) {$InfoArray[5]='Buttons'; $InfoArray[2]=substr($FieldName,4);}
if ( (substr($FieldName,0,3)=='opt')  && (substr_count( $InfoArray[5],'varchar')>0) ) {$InfoArray[5]='DDown'; $InfoArray[2]=substr($FieldName,3);}
if (substr($FieldName,0,3)=='flg')  {$InfoArray[5]='Option';$InfoArray[2]=substr($FieldName,3);}
if (substr($FieldName,0,4)=='idLk') {$InfoArray[5]='DDown';$InfoArray[2]=substr($FieldName,4);}
if ( (substr($FieldName,0,2)=='id') && (substr_count( $InfoArray[5],'int')>0) ) {$InfoArray[5]='id'; $InfoArray[2]=substr($FieldName,2);}
if ( (substr($FieldName,0,4)!='idLk') && (substr_count( $InfoArray[5],'int')>0) ) {$InfoArray[5]='id'; $InfoArray[2]=substr($FieldName,4);}
if (substr_count( strtolower($FieldName),'idusers')>0) $InfoArray[5]='UserLkp';
//--
$InfoArray[0]=SplitByUpperCase($InfoArray[2]);
//--
return($InfoArray);
}
//--
$inpSQLstruct = $dir.'/'.$fname;
$RetArray     = array();
if (@file_exists($inpSQLstruct))
  {
  $fp=fopen($inpSQLstruct,'r'); // read only
  if ($fp)
    {
    while ( ($tmp=fgets($fp)) !== false) { if (substr_count($tmp,"`".$TblName."`")>0) break; }
    if (substr_count($tmp,"`".$TblName."`")>0)
      {
      fgets($fp);fgets($fp);
      while ( ($tmp=fgets($fp)) !== false)
        {
        if (substr_count($tmp,'PRIMARY KEY')>0)
          {
          $RetArray[GetFieldName($tmp)][3]='Y';$RetArray[GetFieldName($tmp)][4]='N';
          break;
          }
        if (substr_count($tmp,'INDEX ')>0) break;
        //--
        $FieldName=GetFieldName($tmp);
        $RetArray[$FieldName]=GetFieldInfo($FieldName,$tmp);
        //--
        }
      }
    fclose($fp);
    }
  }
return($RetArray);
}
//--------------------------------------------------------------------------------
function GetForm($FormFields)
{
$MaxDisplayLen=0;
foreach($FormFields as $FieldArray)
  {
  $box    = @imageTTFBbox(12,0,'verdana.ttf',$FieldArray[2]);
  $width  = abs($box[4] -$box[0]);
  if ( $MaxDisplayLen < $width ) $MaxDisplayLen=$width;
  }
if ($MaxDisplayLen>120) die('need more class defs');
if ($MaxDisplayLen<120) {$ClLbl="col-xs-12 col-sm-2 col-md-3 col-lg-2";$ClDiv="col-xs-12 col-sm-9 col-md-9 col-lg-6";}
if ($MaxDisplayLen<100) {$ClLbl="col-xs-6  col-sm-4 col-md-2 col-lg-2";$ClDiv="col-xs-6 col-sm-9 col-md-10 col-lg-6";}
if ($MaxDisplayLen<80 ) {$ClLbl="col-xs-6  col-sm-3 col-md-1 col-lg-1";$ClDiv="col-xs-6 col-sm-9 col-md-7 col-lg-6";}
if ($MaxDisplayLen<50 ) {$ClLbl="col-xs-2  col-sm-2 col-md-1 col-lg-1";$ClDiv="col-xs-9 col-sm-9 col-md-7 col-lg-6";}
$a="
<div class='CornerAll lbk' style='padding:10px;'>
  <form class='form-horizontal' role='form'>
";
foreach($FormFields as $FieldArray)
  {
  if ($FieldArray[4]=='N') continue;

  switch($FieldArray[5])
    {
    case 'varchar':$a.="
    <!-- xxx -->
    <div class='form-group'>
      <label class='".$ClLbl." control-label' for='inp".$FieldArray[2]."'>".$FieldArray[0]."</label>
      <div class='".$ClDiv."'>
        <input type='text' class='form-control' id='inp".$FieldArray[2]."' maxlength='".$FieldArray[6]."' placeholder='".$FieldArray[2]."' />
      </div>
    </div>";
break;
    default:echo "$('body').append('".'Type:'.$FieldArray[5].' not known for '.$FieldArray[1].' on '.__LINE__."')|";die;
    }
  }
$a.="
    <!-- xxx -->
  </form>
<button type='button' class='btn btn-default' id='btnLogout'><i class='fa fa-coffee'></i> Logout</button>&nbsp;<button data-loading-text='loading...' class='btn disabled' id='imgWorking' disabled='disabled' style='display:none;'><img src='Pics/working.gif' /></button>
<button type='button' class='btn btn-primary pull-right' id='btnSave' style='display:none;'><i class='fa fa-check'></i> Save</button>
";



echo "$('code').append(jQuery.base64Decode('".base64_encode(nl2br(str_replace(' ','&nbsp;',htmlentities($a))))."'))|";

/*

Array
(
    [idLookups] => Array
        (
            [0] => Lookups
            [1] => idLookups
            [2] => Lookups
            [3] => Y
            [4] => N
            [6] => 0
            [5] => id
        )

    [Type] => Array
        (
            [0] => Type
            [1] => Type
            [2] => Type
            [3] => N
            [4] => Y
            [6] => 6
            [5] => varchar
        )

    [LkVal] => Array
        (
            [0] => Lk Val
            [1] => LkVal
            [2] => LkVal
            [3] => N
            [4] => Y
            [6] => 10
            [5] => varchar
        )

    [LkDesc] => Array
        (
            [0] => Lk Desc
            [1] => LkDesc
            [2] => LkDesc
            [3] => N
            [4] => Y
            [6] => 200
            [5] => varchar
        )

)
*/

}
//--------------------------------------------------------------------------------
function GetModal($FormFields)
{
}
//--------------------------------------------------------------------------------
function GetTableForm($FormFields)
{

}
//--------------------------------------------------------------------------------
function GetJS($FormFields)
{
}
//--------------------------------------------------------------------------------
function GetPHPDB($FormFields)
{
}
//--------------------------------------------------------------------------------
function GetLoopDB($FormFields)
{
}
//--------------------------------------------------------------------------------
?>
