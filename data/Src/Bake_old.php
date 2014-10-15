<?php
if ($_SERVER['REMOTE_ADDR']=="127.0.0.1") {header("Pragma: nocache"); header("Cache-Control: no-cache");}
define('DoDebug',false);
define('DebugArea','All');
//--
if (@file_exists("/var/www/TestAuditor/Site/SiteVars.php")) include_once ("/var/www/TestAuditor/Site/SiteVars.php");
if (@file_exists("/var/www/Site/SiteVars.php")) include_once ("/var/www/Site/SiteVars.php");
include_once (constant("LibDir")."LibMain.php");
//--------------------------------------------------------------------------------
function FillTableArray()
{
function FuncFileSort($a, $b) {return strcmp(strtolower($a), strtolower($b));};
$RetArray=array();
$inpSQLstruct=str_replace('//','/',constant('DocRoot').constant('RunDir').'/Site/OriginalDB.sql');
if (@file_exists($inpSQLstruct))
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
function GetTableFields($TableName,$OnlyFormFields,$StripLen)
{
$RetArray=array();
if (@file_exists('Site/OriginalDB.sql'))
  {
  $fp=fopen(str_replace('//','/',constant('DocRoot').constant('RunDir').'/Site/OriginalDB.sql'),'r'); // read only
  if ($fp)
    {
    while ( ($tmp=fgets($fp)) !== false)
      {
      if (substr_count($tmp,'USE ')>0) // USE `ProjectTemplate` ;
        {
        if (!defined('DBName'))
          {
          if (substr_count($tmp,'TestAuditor')>0) define('DBName',"\".TestAuditor.\"."); else define('DBName',"\".$"."UsrArray['ProjectDB'].\".");
          }
        }
      if (substr_count($tmp,"`".$TableName."`")>0) break;
      }
    if (substr_count($tmp,"`".$TableName."`")>0)
      {
      fgets($fp);fgets($fp);
      while ( ($tmp=fgets($fp)) !== false)
        {
        if (substr_count($tmp,'PRIMARY KEY')>0) break;
        if (substr_count($tmp,'INDEX ')>0) break;
        //--
        //--   `idTestsTree` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
        $FieldName=GetFieldName($tmp);
        $FieldType=GetFieldType($tmp,$StripLen);

        if ($OnlyFormFields)
          {
          if ($FieldName=='flgDeleted')       continue;
          if ($FieldName=='CreatedOn')        continue;
          if ($FieldName=='LastUpdated')      continue;
          if ($FieldName=='idUpdatedByUser')  continue;
          }

        $RetArray[$FieldName]=$FieldType;
        //--
        }
      }
    fclose($fp);
    }
  }
return($RetArray);
}
//--------------------------------------------------------------------------------
function GetPri($TableName)
{
$Pri='';
if (@file_exists('Site/OriginalDB.sql'))
  {
  $fp=fopen(str_replace('//','/',constant('DocRoot').constant('RunDir').'/Site/OriginalDB.sql'),'r'); // read only
  if ($fp)
    {
    while ( ($tmp=fgets($fp)) !== false)
      {
      if (substr_count($tmp,"`".$TableName."`")>0) break;
      }
    if (substr_count($tmp,"`".$TableName."`")>0)
      {
      fgets($fp);fgets($fp);
      while ( ($tmp=fgets($fp)) !== false)
        {
        if (substr_count($tmp,'PRIMARY KEY')>0) break;
        }
      //--
      $Pri=GetFieldName($tmp);
      }
    fclose($fp);
    }
  }
return($Pri);
}
//--------------------------------------------------------------------------------
function GetFieldName($line)
//--   `idTestsTree` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
{
$line=substr($line,strpos($line,"`")+1);
$line=substr($line,0,strpos($line,"`"));
return(trim($line));
}
//--------------------------------------------------------------------------------
function GetFieldType($line,$StripLen)
//--   `idTestsTree` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
{
$line=substr($line,strrpos($line,"`")+1);
if (substr_count($line,"UNSIGNED")>0) { $line=str_replace("UNSIGNED", "", $line);}
if ($StripLen)
  {
  if (substr_count($line,"(")>0)
    {
    $line=substr($line,0,strpos($line,"("));
    }
  }
if (substr_count($line,"NOT")>0)  { $line=substr($line,0,strpos($line,"NOT"));  return(trim($line));}
if (substr_count($line,"NULL")>0) { $line=substr($line,0,strpos($line,"NULL")); return(trim($line));}
if (substr_count($line,",")>0)    { $line=substr($line,0,strpos($line,","));    return(trim($line));}
return(trim($line));
}
//--------------------------------------------------------------------------------
function AddFormEntry($fldtype,$fldName,$Pri)
{
$fldName=CleanedFName($fldName,$fldtype,$Pri);
//echo $fldName.':'.$fldtype.'<br />';
if ($fldName=='') return;
//--
$fldtype=str_replace(' unsigned','',strtolower($fldtype));
//--
$flen=0;
if (substr_count($fldtype,"(")>0)
  {
  $flen=substr($fldtype,strpos($fldtype,"(")+1,-1);
  $fldtype=substr($fldtype,0,strpos($fldtype,"("));
  }
//--
switch($fldtype)
  {
  case 'buttons' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls\">
                        <div class=\"btn-group\" data-toggle=\"buttons-radio\" id=\"inp".$fldName."\">
                          <button type='button' data-loading-text='loading...' class=\"btn active\" data-sel=\"A\">One</button>
                          <button type='button' data-loading-text='loading...' class=\"btn\" data-sel=\"B\">Two</button>
                          <button type='button' data-loading-text='loading...' class=\"btn\" data-sel=\"C\">Three</button>
                        </div>
                        <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                      </div>
                    </div>";break;
  case 'userlkp' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls\">
                        <input type=\"text\" class=\"input-medium\" placeholder=\"User Name\" id=\"inp".$fldName."\" data-provide=\"typeahead\" autocomplete=\"on\" />
                        <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                      </div>
                    </div>";break;
  case 'tinyint' :echo "\n                    <!-- xxx -->
                      <div class=\"control-group\">
                        <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                        <div class=\"controls controls-row\">
                          <input type=\"number\" id=\"inp".$fldName."\" class=\"input-mini\" maxlength=\"3\" min=\"0\" max=\"256\" placeholder=\"0\" /><div id=\"inp".$fldName."Slider\" style=\"vertical-align:middle;line-height:normal;margin-left:15px;width:150px;display:inline-block;\"></div>
                          <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                        </div>
                      </div>";break;
  case 'float'      :echo "\n                    <!-- xxx -->
                      <div class=\"control-group\">
                        <label class=\"control-label controls-row\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                        <div class=\"controls\">
                          <input type=\"number\" id=\"inp".$fldName."\" class=\"input-mini\" min=\"0\" maxlength=\"8\" placeholder=\"0\" /><div id=\"inp".$fldName."Slider\" style=\"vertical-align:middle;line-height:normal;margin-left:15px;width:150px;display:inline-block;\"></div>
                          <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                        </div>
                      </div>";break;
  case 'mediumint':
  case 'int'      :echo "\n                    <!-- xxx -->
                      <div class=\"control-group\">
                        <label class=\"control-label controls-row\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                        <div class=\"controls\">
                          <input type=\"number\" id=\"inp".$fldName."\" class=\"input-mini\" min=\"0\" maxlength=\"8\" placeholder=\"0\" /><div id=\"inp".$fldName."Slider\" style=\"vertical-align:middle;line-height:normal;margin-left:15px;width:150px;display:inline-block;\"></div>
                          <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                        </div>
                      </div>";break;
  case 'smallint' :echo "\n                    <!-- xxx -->
                      <div class=\"control-group\">
                        <label class=\"control-label controls-row\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                        <div class=\"controls\">
                          <input type=\"number\" id=\"inp".$fldName."\" class=\"input-mini\" min=\"0\" maxlength=\"5\" placeholder=\"0\" /><div id=\"inp".$fldName."Slider\" style=\"vertical-align:middle;line-height:normal;margin-left:15px;width:150px;display:inline-block;\"></div>
                          <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                        </div>
                      </div>";break;
  case 'decimal'  :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls\">
                        <input type=\"number\" id=\"inp".$fldName."\" class=\"input-small\" min=\"0\" placeholder=\"0.0\" />
                        <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                      </div>
                    </div>";break;

  case 'text' :
  case 'mediumtext' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls span7\"  style=\"margin-left:20px;\">
                        <textarea id=\"inp".$fldName."\"></textarea>
                      </div>
                    </div>";break;
  case 'ddown' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls\">
                        <select class=\"input-medium\" id=\"inp".$fldName."\">
                          <option value='aaa'>aaa</option>
                          <option value='bbb'>bbb</option>
                        </select>
                        <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                      </div>
                    </div>";break;
  case 'id'     :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls controls-row\">
                        <div class=\"input-append\">
                          <select class=\"input-medium\" id=\"inp".$fldName."\">
                            <option value='aaa'>aaa</option>
                            <option value='bbb'>bbb</option>
                          </select>
                          <button data-loading-text='loading...' class='btn' id='btnAdd".$fldName."'><i class='icon-plus'></i> Add</button>
                        </div>
                        <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                      </div>
                    </div>";break;
  case 'option'  :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls BigiPhone\">
                        <input type=\"checkbox\" checked=\"checked\" id=\"inp".$fldName."\" />
                        <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                      </div>
                    </div>";break;
  case 'varchar' :$inpCls='input-mini'; // default
                  if ($flen>8 )   $inpCls='input-small';
                  if ($flen>20 )  $inpCls='input-medium';
                  if ($flen>30 )  $inpCls='input-large';
                  if ($flen>60 )  $inpCls='input-xlarge';
                  if ($flen>100 ) $inpCls='input-xxlarge';
                  echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls\">
                        <input type=\"text\" class=\"".$inpCls."\" id=\"inp".$fldName."\" maxlength=\"".$flen."\" placeholder=\"\" />
                        <span class=\"help-inline\" id=\"Hlp".$fldName."\"></span>
                      </div>
                    </div>";break;
  case 'olddatetime' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls controls-row\">
                        <div class=\"btn-group\">
                          <div class=\"input-append date\" id=\"inp".$fldName."\" data-date=\"".date('d-m-Y')."\" data-date-format=\"dd-mm-yyyy\">
                            <input class=\"input-medium\" type=\"text\" value=\"".date('d-m-Y')."\" />
                            <span class=\"add-on\"><i class=\"icon-calendar\"></i></span>
                          </div>
                        </div>
                        <input id=\"inp".$fldName."Time\" type=\"text\" class=\"input-small\" /><i class=\"icon-time\" style=\"margin: -2px 0 0 -22.5px; pointer-events: none; position: relative; color:#aaa;\"></i>
                      </div>
                    </div>";break;

  case 'datetime' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls controls-row\">
                        <div id=\"inp".$fldName."\" class=\"input-append date\">
                          <input class=\"input-medium\" data-format=\"dd-MM-yyyy hh:mm:ss\" type=\"text\" />
                          <span class=\"add-on\">
                            <i data-time-icon=\"icon-time\" data-date-icon=\"icon-calendar\">
                            </i>
                          </span>
                        </div>
                      </div>
                    </div>";break;
  case 'date' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls\">
                        <div id=\"inp".$fldName."\" class=\"input-append\">
                          <input class=\"input-small\" data-format=\"dd/MM/yyyy\" type=\"text\" />
                          <span class=\"add-on\">
                            <i data-time-icon=\"icon-time\" data-date-icon=\"icon-calendar\">
                            </i>
                          </span>
                        </div>
                      </div>
                    </div>";break;
  case 'time' :echo "\n                    <!-- xxx -->
                    <div class=\"control-group\">
                      <label class=\"control-label\" for=\"inp".$fldName."\">".SplitByUpperCase($fldName)."</label>
                      <div class=\"controls controls-row\">
                        <div id=\"inp".$fldName."\" class=\"input-append\">
                          <input class=\"input-mini\" data-format=\"hh:mm:ss\" type=\"text\" />
                          <span class=\"add-on\">
                            <i data-time-icon=\"icon-time\" data-date-icon=\"icon-calendar\">
                            </i>
                          </span>
                        </div>
                      </div>
                    </div>";break;

  default:die('191: Type:'.$fldtype.' not known for '.$fldName.' on '.__LINE__);
  }
}
//--------------------------------------------------------------------------------
function AddFormJCode($fldtype,$fldName,$Pri)
{
$fldName=CleanedFName($fldName,$fldtype,$Pri);
if ($fldName=='') return;
//--
$fldtype=str_replace(' unsigned','',strtolower($fldtype));
//--
$flen=0;
if (substr_count($fldtype,"(")>0)
  {
  $flen=substr($fldtype,strpos($fldtype,"(")+1,-1);
  $fldtype=substr($fldtype,0,strpos($fldtype,"("));
  }
//--
switch($fldtype)
  {
  case 'userlkp'    :echo "
    $(\"#inp".$fldName."\").typeahead({  items:6, source: ['Aaaa', 'Abbb', 'Accc']  });
    ";break;
  case 'tinyint'    :echo "
    $(\"#inp".$fldName."\").blur(function()
      {
      if (!isNumber($('#inp".$fldName."').val())) $(\"#inp".$fldName."\").val(0); else $(\"#inp".$fldName."Slider\").slider({value: $(\"#inp".$fldName."\").val()});
      });
    $(\"#inp".$fldName."Slider\").slider({orientation: \"horizontal\",max: 255,value: 0, slide: Upd".$fldName.",change: Upd".$fldName." });
    ";break;
  case 'smallint'    :echo "
    $(\"#inp".$fldName."\").blur(function()
      {
      if (!isNumber($('#inp".$fldName."').val())) $(\"#inp".$fldName."\").val(0); else $(\"#inp".$fldName."Slider\").slider({value: $(\"#inp".$fldName."\").val()});
      });
    $(\"#inp".$fldName."Slider\").slider({orientation: \"horizontal\",max: 65535,value: 0, slide: Upd".$fldName.",change: Upd".$fldName." });
    ";break;
  case 'id'         :echo "
  $('#btnAdd".$fldName."').click(function(e) {e.preventDefault(); $('#Modal".$fldName."').modal('show'); });
  $('#btnMdl".$fldName."Save').click(function(e)
    {
    e.preventDefault();
    if ($('#FormFieldHerexxxxxxx').val()=='')
      {
      $.pnotify({title: 'Add...', text: 'Nothing to add :(',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8});
      return;
      }
    // add code for post here
    });
    ";break;
  case 'float'      :break;
  case 'decimal'    :break;
  case 'binary'     :break;
  case 'text'       :
  case 'mediumtext' :echo "
    $(\"#inp".$fldName."\").redactor({ autoresize: false, minHeight: 300,buttons: [ \"formatting\",  \" | \", \"bold\",  \"italic\",  \" | \",  \"unorderedlist\",  \"orderedlist\",  \"outdent\",  \"indent\",  \" | \", \"fontcolor\",  \"backcolor\",  \" | \",  \"alignment\",  \" | \",  \"horizontalrule\"]});
    ";break;
  case 'ddown'      :break;
  case 'option'     :echo "
    $(\"#inp".$fldName."\").iphoneStyle({ resizeContainer: false, checkedLabel: \"Yes\", uncheckedLabel: \"No\" ,onChange: function(elem, value)  {   }});
    ";break;
  case 'mediumint'  :break;
  case 'int'        :break;
  case 'varchar'    :break;
  case 'datetime'   :echo "
    $(\"#inp".$fldName."\").datetimepicker({format: \"dd-MM-yyyy hh:mm:ss\", language: \"en\"});
    ";break;
  case 'time'   :echo "
    $(\"#inp".$fldName."\").datetimepicker({pickDate: false });
    ";break;
  case 'date'       :echo "
    $(\"#inp".$fldName."\").datetimepicker({format: \"dd-MM-yyyy\",pickTime: false });
    ";break;
  case 'buttons'    :break;
  default:die('243:Type:'.$fldtype.' not known for '.$fldName.' on '.__LINE__);
  }
}
//--------------------------------------------------------------------------------
function AddFormFuncs($fldtype,$fldName,$Pri)
{
$fldName=CleanedFName($fldName,$fldtype,$Pri);
if ($fldName=='') return;
//--
$flen=0;
if (substr_count($fldtype,"(")>0)
  {
  $flen=substr($fldtype,strpos($fldtype,"(")+1,-1);
  $fldtype=substr($fldtype,0,strpos($fldtype,"("));
  }
//--
switch(strtolower($fldtype))
  {
  case 'userlkp'    :break;
  case 'tinyint'    :echo "
    function Upd".$fldName."()
    {
    $(\"#inp".$fldName."\").val(  $(\"#inp".$fldName."Slider\").slider( \"value\" ) );
    }
//-----------------------
                     ";break;
  case 'smallint'    :echo "
    function Upd".$fldName."()
    {
    $(\"#inp".$fldName."\").val(  $(\"#inp".$fldName."Slider\").slider( \"value\" ) );
    }
//-----------------------
                   ";break;
  case 'float'      :break;
  case 'decimal'    :break;
  case 'text'       :break;
  case 'mediumtext' :break;
  case 'ddown'      :break;
  case 'mediumint'  :break;
  case 'int'        :break;
  case 'id'         :break;
  case 'buttons'    :break;
  case 'option'     :break;
  case 'varchar'    :break;
  case 'datetime'   :break;
  case 'binary'     :break;
  case 'time'       :break;
  case 'date'       :break;
  default:die('285:Type:'.$fldtype.' not known for '.$fldName.' on '.__LINE__);
  }
if (substr_count(strtolower($fldtype),'int')>0)
echo "
//-----------------------
function isNumber(n)
{
return !isNaN(parseFloat(n)) && isFinite(n);
}
//-----------------------
";
}
//--------------------------------------------------------------------------------
function AddFormCSS($fldtype,$fldName,$Pri)
{
$fldName=CleanedFName($fldName,$fldtype,$Pri);
if ($fldName=='') return;
//--
$flen=0;
if (substr_count($fldtype,"(")>0)
  {
  $flen=substr($fldtype,strpos($fldtype,"(")+1,-1);
  $fldtype=substr($fldtype,0,strpos($fldtype,"("));
  }
//--
switch(strtolower($fldtype))
  {
  case 'userlkp'    :break;
  case 'int'        :break;
  case 'mediumint'  :break;
  case 'tinyint'    :break;
  case 'smallint'   :break;
  case 'float'      :break;
  case 'decimal'    :break;
  case 'text'       :break;
  case 'mediumtext' :break;
  case 'ddown'      :break;
  case 'buttons'    :break;
  case 'option'     :echo "
                      .BigiPhone .iPhoneCheckContainer {  width: 90px; display:inline-block; vertical-align: middle;line-height: normal;}
                      ";break;
  case 'varchar'    :break;
  case 'datetime'   :break;
  case 'date'       :break;
  case 'id'         :break;
  case 'time'       :break;
  case 'binary'     :break;
  default:die('317:Type:'.$fldtype.' not known for '.$fldName.' on '.__LINE__);
  }
}
//--------------------------------------------------------------------------------
function AddDialogs($fldtype,$fldName,$Pri)
{
$fldName=CleanedFName($fldName,$fldtype,$Pri);
if ($fldName=='') return;
//--
$flen=0;
if (substr_count($fldtype,"(")>0)
  {
  $flen=substr($fldtype,strpos($fldtype,"(")+1,-1);
  $fldtype=substr($fldtype,0,strpos($fldtype,"("));
  }
//--
switch(strtolower($fldtype))
  {
  case 'id'    :echo "

  <div id='Modal".$fldName."' class='modal hide fade img-rounded' tabindex='-1' role='dialog' aria-labelledby='ModalFeedbackLabel' aria-hidden='true'>
    <div class='modal-header img-rounded' style=\"background-image:url('<?"."php echo constant"."('Pic"."Dir'); ?".">bgfusion.png');\">
      <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
      <h3 id='ModalFeedbackLabel'>Add ".$fldName."</h3>
    </div>
    <!-- ------ -->
    <div class='modal-body'>
      <div class='row-fluid'>
        <form class='form-horizontal'>
          <!-- ------ -->
          fields from other table here
          <!-- ------ -->
          <?p"."hp echo AddHiddenFormField('id".$fldName."',0);?>
        </form>
      </div>
    </div>
    <!-- ------ -->
    <div class='modal-footer'>
      <button data-loading-text='loading...' class='btn pull-left' data-dismiss='modal' aria-hidden='true'><i class='icon-remove'></i> Cancel</button>
      <button data-loading-text='loading...' class='btn btn-primary' data-dismiss='modal' aria-hidden='true' id='btnMdl".$fldName."Save'><i class='icon-ok'></i> Save</button>
    </div>
  </div>

  ";break;
  default:break;
  }
}
//--------------------------------------------------------------------------------
function CleanedFName($FieldName,&$Type,$Pri)
{
if ($FieldName=='flgDeleted')       return('');
if ($FieldName=='CreatedOn')        return('');
if ($FieldName=='LastUpdated')      return('');
if ($FieldName=='idUpdatedByUser')  return('');
if (substr_count( strtolower($FieldName),'idusers')>0) {$Type='UserLkp';return($FieldName);}
if ( (substr($FieldName,0,4)=='bopt') && (substr_count( strtolower($Type),'varchar')>0) ) {$Type='Buttons'; return(substr($FieldName,4));}
if ( (substr($FieldName,0,3)=='opt') && (substr_count( strtolower($Type),'varchar')>0) ) {$Type='DDown'; return(substr($FieldName,3));}
if (substr($FieldName,0,3)=='flg')  {$Type='Option';return(substr($FieldName,3));}
if (substr($FieldName,0,4)=='idLk') {$Type='DDown';return(substr($FieldName,4));}
if ($FieldName==$Pri) return('');
if ( (substr($FieldName,0,2)=='id') && (substr_count( strtolower($Type),'int')>0) ) {$Type='id'; return(substr($FieldName,2));}
//--
if (substr_count( strtolower($Type),' int ')>0) {$Type='varchar(5)';return($FieldName);}
if (substr_count( strtolower($Type),'binary(16)')>0) {$Type='varchar(5)';return($FieldName);}
//--
return($FieldName);
}
//--------------------------------------------------------------------------------
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
//--------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Test Auditor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Le styles -->
    <link href="CSS/bootstrap.css" rel="stylesheet">
    <link href="CSS/bootstrap-responsive.css" rel="stylesheet">
    <link href="CSS/font-awesome.min.css" rel="stylesheet">
    <link href="CSS/bootstrap-responsive.css" rel="stylesheet">
    <link href="CSS/iphonecbox.css" rel="stylesheet">
    <link href="CSS/jquery-ui-1.10.0.custom.min.css" rel="stylesheet">
    <link href="CSS/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="CSS/prettify.css" rel="stylesheet">
    <link href="CSS/ui.totop.css" rel="stylesheet">
    <link href="CSS/redactor.css" rel="stylesheet">
    <link href="CSS/Std.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <style>
    <?php
    if (isset($_POST['optionsRadios']))
      if ($_POST['optionsRadios']=='option1')
        {
        $Pri=GetPri($_POST['inputTable']);
        //------
        $TFieldsArray=GetTableFields($_POST['inputTable'],true,true);
        foreach($TFieldsArray as $fldid => $fldtype) AddFormCSS($fldtype,$fldid,$Pri);
        unset($TFieldsArray);
        }
    //------
    ?>
    </style>
  </head>
  <body>
  <?php
  if (isset($_POST['optionsRadios']))
    {
    if ($_POST['optionsRadios']=='option1')
      {
      //------
      $TFieldsArray=GetTableFields($_POST['inputTable'],true,false);
      $Pri=GetPri($_POST['inputTable']);
      echo "<div class=\"container-fluid\">\n";
      echo "  <div class=\"row-fluid\">\n";
      echo "    <div class=\"span6\">\n";
      echo "      <form class=\"form-horizontal\">\n";
      foreach($TFieldsArray as $fldid => $fldtype) AddFormEntry($fldtype,$fldid,$Pri);
      echo "\n      </form>\n";
      echo "
      <div class='btn-toolbar'>
        <div class='btn-group'> <button data-loading-text='loading...' class='btn'                        style='display:none;' id='btnDel".$_POST['inputTable']."'><i class='icon-trash'></i> Delete</button></div>
        <div class='btn-group pull-right'> <button data-loading-text='loading...' class='btn btn-primary' style='display:none;' id='btnAdd".$_POST['inputTable']."'><i class='icon-plus'></i> Add</button></div>
        <div class='btn-group pull-right'> <button data-loading-text='loading...' class='btn'             style='display:none;' id='btnUpd".$_POST['inputTable']."'><i class='icon-ok'></i> Update</button></div>
      </div>\n";
      echo "    </div>\n";
      echo "  </div>\n";
      echo "</div>\n";
      unset($TFieldsArray);
      //------
      }
    }

  if (!isset($_POST['optionsRadios']))
    { ?>
    <div class="container-fluid" style="margin-top:50px;">
      <div class="row-fluid">
        <div class="span12">
          <form class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <fieldset>
            <legend>Please select your baking:</legend>
            <!-- xxx -->
            <div class="control-group">
              <label class="control-label" for="inputTable">Table Name</label>
              <div class="controls">
                <select class="input-medium" name="inputTable" id="inputTable">
<?php
                  $TableArray=FillTableArray();
                  foreach($TableArray as $table)
                    {
                    echo "                  <option value=\"".$table."\">".$table."</option>\n";
                    }
                  unset($TableArray);
                  ?>
                </select>
              </div>
            </div>
            <!-- xxx -->
            <label class="radio">
              <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
              Show form on screen
            </label>
            <label class="radio">
              <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
              javascript for table
            </label>
            <label class="radio">
              <input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
              php - select, update, delete - off form
            </label>
            <label class="radio">
              <input type="radio" name="optionsRadios" id="optionsRadios4" value="option4">
              php - select in loop
            </label>
            <!-- xxx -->
            <div class="control-group">
              <div class="controls">
                <button type="submit" value="Submit" class="btn">go <i class="icon-chevron-right"></i></button>
              </div>
            </div>
            <!-- xxx -->
            </fieldset>
            <input type="hidden" autocomplete="off" name="action" value="DoIt" />
          </form>
        </div>
      </div>
    </div>
 <?php } ?>

    <?php
    //------
    if (isset($_POST['optionsRadios']))
      if ($_POST['optionsRadios']=='option2')
        {
        echo "<pre class='prettyprint linenums language-js' id='tbltop'>";
        echo "</pre>";
        echo "<pre class='prettyprint linenums language-js' id='tblout'>";
        echo "</pre>";
        }
    //------
    if (isset($_POST['optionsRadios']))
      if ($_POST['optionsRadios']=='option3')
        {
        echo "<pre class='prettyprint linenums language-php' id='tblout'>";
        echo "</pre>";
        }
    //------
    if (isset($_POST['optionsRadios']))
      if ($_POST['optionsRadios']=='option4')
        {
        echo "<pre class='prettyprint linenums language-php' id='tblout'>";
        echo "</pre>";
        }
    //------
    if (isset($_POST['optionsRadios']))
      if ($_POST['optionsRadios']=='option1')
        {
        //------
        $TFieldsArray=GetTableFields($_POST['inputTable'],true,true);
        $Pri=GetPri($_POST['inputTable']);
        foreach($TFieldsArray as $fldid => $fldtype) AddDialogs($fldtype,$fldid,$Pri);
        unset($TFieldsArray);
        }
    //------
    ?>

  <!-- Le javascript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="JS/jquery-1.9.1.min.js"></script>
  <script src="JS/jquery-ui-1.10.0.custom.min.js"></script>
  <script src="JS/jquery.actual.min.js"></script>
  <script src="JS/iphone-style-checkboxes.js"></script>
  <script src="JS/bootstrap-transition.js"></script>
  <script src="JS/bootstrap-alert.js"></script>
  <script src="JS/bootstrap-modal.js"></script>
  <script src="JS/bootstrap-dropdown.js"></script>
  <script src="JS/bootstrap-scrollspy.js"></script>
  <script src="JS/bootstrap-tab.js"></script>
  <script src="JS/bootstrap-tooltip.js"></script>
  <script src="JS/bootstrap-popover.js"></script>
  <script src="JS/bootstrap-button.js"></script>
  <script src="JS/bootstrap-collapse.js"></script>
  <script src="JS/bootstrap-carousel.js"></script>
  <script src="JS/prettify.js"></script>
  <script src="JS/bootstrap-typeahead.js"></script>
  <script src="JS/jquery.ui.totop.js"></script>
  <script src="JS/bootstrap-datetimepicker.min.js"></script>
  <script src="JS/jquery.base64.min.js"></script>
  <script src="JS/redactor.js"></script>
  <script type="text/javascript">
  $(document).ready(function ()
    {
    <?php
    if (isset($_POST['optionsRadios']))
      if ($_POST['optionsRadios']=='option1')
        {
        //------
        $Pri=GetPri($_POST['inputTable']);
        $TFieldsArray=GetTableFields($_POST['inputTable'],true,true);
        foreach($TFieldsArray as $fldid => $fldtype) AddFormJCode($fldtype,$fldid,$Pri);
        unset($TFieldsArray);
        }
    //------
    ?>
    $().UItoTop({ easingType: 'easeOutQuart' });
    });
  //-------------
  <?php
  if (isset($_POST['optionsRadios']))
    if ($_POST['optionsRadios']=='option1')
      {
      //------
      $Pri=GetPri($_POST['inputTable']);
      $TFieldsArray=GetTableFields($_POST['inputTable'],true,true);
      foreach($TFieldsArray as $fldid => $fldtype) AddFormFuncs($fldtype,$fldid,$Pri);
      unset($TFieldsArray);
      }
  //------
  ?>
  </script>

  <?php
  //------
  if (isset($_POST['optionsRadios']))
    if ($_POST['optionsRadios']=='option2')
      { ?>
      <script>
      jQuery('#tbltop').html(jQuery.base64Decode('<?php echo ShowJSCodeTop($_POST['inputTable']); ?>')).show();
      jQuery('#tblout').html(jQuery.base64Decode('<?php echo ShowJSCode($_POST['inputTable']); ?>')).show();
      // @prettify
      !function ($) {
        $(function(){
        window.prettyPrint && prettyPrint()
        })
      }(window.jQuery);
      // #prettify
      </script>
     <?php }
  //------
  ?>

  <?php
  //------
  if (isset($_POST['optionsRadios']))
    if ($_POST['optionsRadios']=='option3')
      { ?>
      <script>
      jQuery('#tblout').html(jQuery.base64Decode('<?php echo ShowPHPCode($_POST['inputTable']); ?>')).show();
      // @prettify
      !function ($) {
        $(function(){
        window.prettyPrint && prettyPrint()
        })
      }(window.jQuery);
      // #prettify
      </script>
     <?php }
  //------
  ?>
  <?php
  //------
  if (isset($_POST['optionsRadios']))
    if ($_POST['optionsRadios']=='option4')
      { ?>
      <script>
      jQuery('#tblout').html(jQuery.base64Decode('<?php echo ShowPHPLoopSelectCode($_POST['inputTable']); ?>')).show();
      // @prettify
      !function ($) {
        $(function(){
        window.prettyPrint && prettyPrint()
        })
      }(window.jQuery);
      // #prettify
      </script>
     <?php }
  //------
  ?>
  </body>
</html>
<?php
//---------------------------------------------------------
function ShowPHPCode($tblname)
{
$TFields=GetTableFields($tblname,false,true);
$Pri=GetPri($tblname);
//--
$Code="";
$Code.=base64_encode(GetCodeFuncs($tblname,$TFields,$Pri));
//-------- now show it
unset($TFields);
return($Code);
}
//---------------------------------------------------------
function ShowJSCodeTop($tblname)
{
$TFields=GetTableFields($tblname,true,true);
$Pri=GetPri($tblname);
//--
$Code="";
$Code.=base64_encode(GetJSCodeTop($tblname,$TFields,$Pri));
//-------- now show it
unset($TFields);
return($Code);
}
//---------------------------------------------------------
function ShowJSCode($tblname)
{
$TFields=GetTableFields($tblname,true,true);
$Pri=GetPri($tblname);
//--
$Code="";
$Code.=base64_encode(GetJSCode($tblname,$TFields,$Pri));
//-------- now show it
unset($TFields);
return($Code);
}
//---------------------------------------------------------
function ShowPHPLoopSelectCode($tblname)
{
$TFields=GetTableFields($tblname,false,true);
$Pri=GetPri($tblname);
//--
$Code="";
$Code.=base64_encode(PHPLoopSelectCode($tblname,$TFields,$Pri));
//-------- now show it
unset($TFields);
return($Code);
}
//---------------------------------------------------------
function GetJSCodeTop($tblname,$TFieldsArray,$Pri)
{
$TFieldNamesArray=array_keys($TFieldsArray);
$DBInsertArray=array();$DBInsertFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=$fldname;                            $DBInsertArray[]=$tmp; $DBInsertFields.=$tmp.",";}$DBInsertFields=substr($DBInsertFields,0,-1);  // used for insert,
$DBSelectArray=array();$DBSelectFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakeSelName($fldtype,$fldname);      $DBSelectArray[]=$tmp; $DBSelectFields.=$tmp.",";}$DBSelectFields=substr($DBSelectFields,0,-1);  // used in select eg select date_format(aaa,"%y"),
$DollarArray=array();  $DollarFields='';  foreach($TFieldsArray as $fldname => $fldtype) {$tmp='$'.MakePHPName($fldtype,$fldname);  $DollarArray[]=$tmp;   $DollarFields.=$tmp.",";}  $DollarFields=substr($DollarFields,0,-1);  // php portion,
$PostArray=array();    $PostFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakePHPName($fldtype,$fldname);      $PostArray[]=$tmp;     $PostFields.=$tmp.",";}    $PostFields=substr($PostFields,0,-1);  // field names posted from jquery,
$FormArray=array();    $FormFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp='inp'.MakePHPName($fldtype,$fldname);$FormArray[]=$tmp;     $FormFields.=$tmp.",";}    $FormFields=substr($FormFields,0,-1);  // form inp fields,
$Code ="";
$MaxFldLen=0;foreach($TFieldNamesArray as $FieldName) if (strlen($FieldName)>$MaxFldLen) $MaxFldLen=strlen($FieldName);
$Code.="//---------------------------------------------------------\n";
$Code.="if ( isset($"."_POST['action'],$"."_SERVER['HTTP_X_REQUESTED_WITH']) ) // HTTP_X_REQUESTED_WITH may be browser specific\n";
$Code.="  {\n";
$Code.="  header('Pragma: nocache');\n";
$Code.="  //---\n";
$Code.="  switch(strip_tags($"."_POST['action']))\n";
$Code.="    {\n";
$Code.="    //-------------------\n";
$Code.="    case 'Initialise'      :Initialise($"."link,$"."UsrArray,$"."optUserType,$"."idRecord);\n";
$Code.="                            break;\n";
$Code.="    case 'Save".$tblname."'   :Upd".$tblname."($"."link,$"."UsrArray,\n";
$Tmp="";for ($lp=0;$lp<count($PostArray);$lp++) $Tmp.="                              mysql_real_escape_string(strip_tags($"."_POST['".$PostArray[$lp]."']),$"."link),\n";
if ($Tmp!="") $Tmp=substr($Tmp,0,-2)."\n";
$Code.=$Tmp;
$Code.="                              );\n";
$Code.="                            break;\n";
$Code.="    case 'Add".$tblname."'    :Add".$tblname."($"."link,$"."UsrArray,\n";
$Tmp="";for ($lp=0;$lp<count($PostArray);$lp++) $Tmp.="                              mysql_real_escape_string(strip_tags($"."_POST['".$PostArray[$lp]."']),$"."link),\n";
if ($Tmp!="") $Tmp=substr($Tmp,0,-2)."\n";
$Code.=$Tmp;
$Code.="                              );\n";
$Code.="                            break;\n";
$Code.="     case 'Pop".$tblname."'   :Pop".$tblname."($"."link,$"."UsrArray,mysql_real_escape_string(strip_tags($"."_POST['".$Pri."']),$"."link));\n";
$Code.="                            break;\n";
$Code.="     case 'Del".$tblname."'   :Del".$tblname."($"."link,$"."UsrArray,mysql_real_escape_string(strip_tags($"."_POST['".$Pri."']),$"."link));\n";
$Code.="                            break;\n";
$Code.="    //-------------------\n";
$Code.="    default:echo \"Action '\".strip_tags($"."_POST['action']).\"' not found\";\n"; // forgotten call or hacker\n";
$Code.="    }\n";
$Code.="  CloseLink($"."link,$"."result,constant('DBMainDBType'));\n";
$Code.="  die();\n";
$Code.="  }\n";
$Code.="CloseLink($"."link,$"."result,constant('DBMainDBType'));\n";
$Code.="//---------------------------------------------------------\n";
$Code.="function Initialise($"."link,$"."UsrArray)\n";
$Code.="{\n";
$Code.="echo \"SetMenu('\".((strpos($"."_SERVER['PH"."P_SELF'],\"/\")!==false)?substr($"."_SERVER['PH"."P_SELF'],strrpos($"."_SERVER['PH"."P_SELF'],'/')+1):$"."_SERVER['PH"."P_SELF']).\"')|\";\n";
$Code.="echo \"$('button').button('reset')|\";\n";
$Code.="//--\n";
for ($lp=0;$lp<count($TFieldsArray);$lp++)
  {
  //echo $TFieldNamesArray[$lp].":".substr($TFieldNamesArray[$lp],0,4)."\n";
  if (substr($TFieldNamesArray[$lp],0,4)=='idLk')
    {
    $ShortFieldName=substr($TFieldNamesArray[$lp],4);
    $Code.="GetDropDownOptList($"."link,\"SELECT a.DisplayName,a.idAllListValues from ".constant('DBName')."AllListValues a,".constant('DBName')."AllLists b where a.idAllLists=b.idAllLists and b.ListName='".$ShortFieldName."' and ifnull(a.flgDeleted,'0')!='1' order by a.DisplayOrder\",'".$FormArray[$lp]."','0','',false);\n";
    }
  }
$Code.="}\n";
$Code.="//---------------------------------------------------------\n";
//-----------------
$Code=htmlspecialchars($Code);
return($Code);
}
//---------------------------------------------------------
function GetJSCode($tblname,$TFieldsArray,$Pri)
{
$TFieldNamesArray=array_keys($TFieldsArray);
$DBInsertArray=array();$DBInsertFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=$fldname;                            $DBInsertArray[]=$tmp; $DBInsertFields.=$tmp.",";}$DBInsertFields=substr($DBInsertFields,0,-1);  // used for insert,
$DBSelectArray=array();$DBSelectFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakeSelName($fldtype,$fldname);      $DBSelectArray[]=$tmp; $DBSelectFields.=$tmp.",";}$DBSelectFields=substr($DBSelectFields,0,-1);  // used in select eg select date_format(aaa,"%y"),
$DollarArray=array();  $DollarFields='';  foreach($TFieldsArray as $fldname => $fldtype) {$tmp='$'.MakePHPName($fldtype,$fldname);  $DollarArray[]=$tmp;   $DollarFields.=$tmp.",";}  $DollarFields=substr($DollarFields,0,-1);  // php portion,
$PostArray=array();    $PostFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakePHPName($fldtype,$fldname);      $PostArray[]=$tmp;     $PostFields.=$tmp.",";}    $PostFields=substr($PostFields,0,-1);  // field names posted from jquery,
$FormArray=array();    $FormFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp='inp'.MakePHPName($fldtype,$fldname);$FormArray[]=$tmp;     $FormFields.=$tmp.",";}    $FormFields=substr($FormFields,0,-1);  // form inp fields,
$Code ="";
$MaxFldLen=0;foreach($TFieldNamesArray as $FieldName) if (strlen($FieldName)>$MaxFldLen) $MaxFldLen=strlen($FieldName);
$Code.="//---------------------------------------------------------\n";
$Code.="$('button').button('loading');\n";
//--
$Code.="$('#btnUpdate').click(function(e)\n";
$Code.="  {\n";
$Code.="  e.preventDefault();\n";
$Code.="  if ($('#FormFieldHerexxxxxxx').val()=='')\n";
$Code.="    {\n";
$Code.="    $.pnotify({title: 'No value', text: 'Please enter something to Add',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8});\n";
$Code.="    return;\n";
$Code.="    }\n";
$Code.="  jQuery.post('<?php echo GetSelfName($"."_SERVER['P"."HP_SELF'],'');?>',\n";
$Code.="    {\n";
$FillStr=str_repeat(' ',($MaxFldLen+2-strlen('action')));
$Code.="    'action'".$FillStr.": 'Save".$tblname."' ,\n";
for ($lp=0;$lp<count($TFieldsArray);$lp++)
  {
  $Code.=GetJSFields($TFieldNamesArray[$lp],$TFieldsArray[$TFieldNamesArray[$lp]],$FormArray[$lp],$PostArray[$lp],$MaxFldLen);
  }
$Code=substr(trim($Code),0,-1)."\n";
$Code.="    },  function(data) { HandleAjaxResponse(data); });\n";
$Code.="  });\n";
$Code.="//--\n";
$Code.="//---------------------------------------------------------\n";
$Code.="jQuery.post('<?php echo GetSelfName($"."_SERVER['P"."HP_SELF']'');?>', {'action' : 'Initialise'}, function(data) { HandleAjaxResponse(data); });\n";
$Code.="$().UItoTop({ easingType: 'easeOutQuart' });\n";
$Code.="//-----------------------\n";
$Code.="function Remove".$tblname."(".$Pri.")\n";
$Code.="{\n";
$Code.="$('#modalAreYouSure').modal('show');\n";
$Code.="$('#btnYes').unbind().click(function(e)\n";
$Code.="  {\n";
$Code.="  e.preventDefault();\n";
$Code.="  jQuery.post('<?"."php echo GetSelfName($"."_SERVER['PHP_SELF'],'');?".">',\n";
$Code.="    {\n";
$Code.="    'action'         : 'Del".$tblname."' ,\n";
$Code.="    '".$Pri."'       : ".$Pri."\n";
$Code.="    },  function(data) { HandleAjaxResponse(data); $"."('#btnYes').unbind(); });\n";
$Code.="  });\n";
$Code.="}\n";
$Code.="//-----------------------\n";
//-----------------
$Code=htmlspecialchars($Code);
return($Code);
}
//---------------------------------------------------------
function GetJSFields($FieldName,$Type,$FormName,$PostField,$MaxFldLen)
{
if ($FieldName=='flgDeleted') return;
if ($FieldName=='LastUpdated') return;
if (($Type=='MEDIUMTEXT') || ($Type=='TEXT'))
  {
  $FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
  return("    '".$PostField."'".$FillStr.": $.base64Encode($('#".$FormName."').getCode()),\n");
  }
if (substr($FieldName,0,3)=='flg')
  {
  $FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
  return("    '".$PostField."'".$FillStr.": ($('#".$FormName."').is(':checked')) ? '1' : '0',\n");
  }
if (substr($FieldName,0,4)=='idLk')
  {
  $FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
  return("    '".$PostField."'".$FillStr.": $('#".$FormName."').val(),\n");
  }
if (substr($FieldName,0,4)=='bopt')
  {
  $FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
  return("    '".$PostField."'".$FillStr.": $('#".$FormName." button.active').attr('data-sel'),\n");
  }
if ( substr_count(strtolower($Type),'int')>0)
  {
  $FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
  return("    '".$PostField."'".$FillStr.": parseInt( $('#".$FormName."').val() , 10 ),\n");
  }
if ( substr_count(strtolower($Type),'date')>0)
  {
  $FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
  return("    '".$PostField."'".$FillStr.": $.trim($('#".$FormName."').data('datetimepicker').getDate()),\n");
  }
if (substr($FieldName,0,3)=='opt')
  {
  $FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
  return("    '".$PostField."'".$FillStr.": $('#".$FormName."').val(),\n");
  }
$FillStr=str_repeat(' ',($MaxFldLen+2-strlen($PostField)));
return("    '".$PostField."'".$FillStr.": $.trim($('#".$FormName."').val()),\n");
}
//---------------------------------------------------------
function PHPLoopSelectCode($tblname,$TFieldsArray,$Pri)
{
$TFieldNamesArray=array_keys($TFieldsArray);
$DBInsertArray=array();$DBInsertFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=$fldname;                            $DBInsertArray[]=$tmp; $DBInsertFields.=$tmp.",";}$DBInsertFields=substr($DBInsertFields,0,-1);  // used for insert,
$DBSelectArray=array();$DBSelectFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakeSelName($fldtype,$fldname);      $DBSelectArray[]=$tmp; $DBSelectFields.=$tmp.",";}$DBSelectFields=substr($DBSelectFields,0,-1);  // used in select eg select date_format(aaa,"%y"),
$DollarArray=array();  $DollarFields='';  foreach($TFieldsArray as $fldname => $fldtype) {$tmp='$'.MakePHPName($fldtype,$fldname);  $DollarArray[]=$tmp;   $DollarFields.=$tmp.",";}  $DollarFields=substr($DollarFields,0,-1);  // php portion,
$PostArray=array();    $PostFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakePHPName($fldtype,$fldname);      $PostArray[]=$tmp;     $PostFields.=$tmp.",";}    $PostFields=substr($PostFields,0,-1);  // field names posted from jquery,
$FormArray=array();    $FormFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp='inp'.MakePHPName($fldtype,$fldname);$FormArray[]=$tmp;     $FormFields.=$tmp.",";}    $FormFields=substr($FormFields,0,-1);  // form inp fields,
$Code ="";
//-----------------
$Code.="$"."query =\"SELECT ".$DBSelectFields." from ".constant('DBName').$tblname."\";\n";
$Code.="$"."result=GetResult($"."link,constant('DBMainDBType'),$"."query);\n";
$Code.="while(list(".$DollarFields.")=GetRowArray($"."link,constant('DBMainDBType'),$"."result))\n";
$Code.="  {\n";
$Code.="  }\n";
$Code.="CleanUpResources(constant('DBMainDBType'),$"."result);\n";
//-----------------
$Code=htmlspecialchars($Code);
return($Code);}
//---------------------------------------------------------
function GetCodeFuncs($tblname,$TFieldsArray,$Pri)
{
$TFieldNamesArray=array_keys($TFieldsArray);$TFieldTypesArray=array_values($TFieldsArray);
$DBInsertArray=array();$DBInsertFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=$fldname;                            $DBInsertArray[]=$tmp; $DBInsertFields.=$tmp.",";}$DBInsertFields=substr($DBInsertFields,0,-1);  // used for insert,
$DBSelectArray=array();$DBSelectFields='';foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakeSelName($fldtype,$fldname);      $DBSelectArray[]=$tmp; $DBSelectFields.=$tmp.",";}$DBSelectFields=substr($DBSelectFields,0,-1);  // used in select eg select date_format(aaa,"%y"),
$DollarArray=array();  $DollarFields='';  foreach($TFieldsArray as $fldname => $fldtype) {$tmp='$'.MakePHPName($fldtype,$fldname);  $DollarArray[]=$tmp;   $DollarFields.=$tmp.",";}  $DollarFields=substr($DollarFields,0,-1);  // php portion,
$PostArray=array();    $PostFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp=MakePHPName($fldtype,$fldname);      $PostArray[]=$tmp;     $PostFields.=$tmp.",";}    $PostFields=substr($PostFields,0,-1);  // field names posted from jquery,
$FormArray=array();    $FormFields='';    foreach($TFieldsArray as $fldname => $fldtype) {$tmp='inp'.MakePHPName($fldtype,$fldname);$FormArray[]=$tmp;     $FormFields.=$tmp.",";}    $FormFields=substr($FormFields,0,-1);  // form inp fields,
$Code ="";
$Code.="//---------------------------------------------------------\n";
$Code.="function Add".$tblname."($"."link,$"."UsrArray,".$DollarFields.")\n";
$Code.="{\n";
$Code.="// use cookie to check this user has rights to add to table ".$tblname." !!\n";
foreach($TFieldsArray as $FieldName => $fldtype)
  if (substr($FieldName,0,4)=='idLk')
    {
    $ShortFieldName=substr($FieldName,4);
    $Code.="  //  **** Delete the 2 lines below if ".$ShortFieldName." is populated in function Initialise with 'GetDropDownOptList'. Only keep if 'GetAutoCompleteList' was used\n";
    $Code.="     $".$ShortFieldName."=SimpDB(\"SELECT DisplayName from ".constant('DBName')."AllListValues where idAllListValues=\",\"0\",constant('DBMainDBType'),$"."link);\n";
    $Code.="  OR $".$ShortFieldName."=CheckAndGetLookup($"."link,constant('DBMainDBType'),$".$ShortFieldName.",'".$ShortFieldName."');\n";
    }
$Code.="//--\n";
$Code.="// primary field is :".$Pri."\n";
$Code.="$"."query =\"INSERT into ".constant('DBName').$tblname."(".$DBInsertFields.")\";\n";
$Code.="$"."query.=\" values (\";\n";
for($lp=0;$lp<count($TFieldNamesArray);$lp++)
  {
  if (strtoupper($TFieldNamesArray[$lp])=="LASTUPDATED") {$Code.="$"."query.=\" sysdate(),\";\n";continue;}
  $Type=ucwords(strtolower($TFieldTypesArray[$lp]));
  switch($Type)
    {
    case 'Time'      :$Code.="$"."query.=\"'\".".$DollarArray[$lp].".\"',\";\n";
    case 'Varchar'   :$Code.="$"."query.=\"'\".".$DollarArray[$lp].".\"',\";\n";break;
    case 'Date'      :$Code.="if (trim(".$DollarArray[$lp].")==\"\") $"."query.=\"null,\"; else {\$"."a=strpos(".$DollarArray[$lp].",':');\$"."a=strpos(".$DollarArray[$lp].",' ',\$"."a);".$DollarArray[$lp]."=substr(".$DollarArray[$lp].",0,\$"."a);".$DollarArray[$lp]."=strtotime(".$DollarArray[$lp].");$"."query.=\"STR_TO_DATE('\".date('d-m-Y',".$DollarArray[$lp].").\"','%d-%m-%Y'),\";}\n";
                      break;
    case 'Datetime'  :if ( ($DBInsertArray[$lp]=='CreatedOn') || ($DBInsertArray[$lp]=='LastUpdated') )
                        $Code.="$"."query.=\" sysdate(),\";\n";
                      else
                        $Code.="if (trim(".$DollarArray[$lp].")==\"\") $"."query.=\"null,\"; else {\$"."a=strpos(".$DollarArray[$lp].",':');\$"."a=strpos(".$DollarArray[$lp].",' ',\$"."a);".$DollarArray[$lp]."=substr(".$DollarArray[$lp].",0,\$"."a);".$DollarArray[$lp]."=strtotime(".$DollarArray[$lp].");$"."query.=\"STR_TO_DATE('\".date('d-m-Y H:i',".$DollarArray[$lp].").\"','%d-%m-%Y %H:%i'),\";}\n";
                      break;
    case 'Int'       :if (substr($TFieldNamesArray[$lp],0,4)=='idLk') {$Code.="$"."query.=\"0\".".$DollarArray[$lp].".\",\";\n";break;}
    case 'Smallint'  :
    case 'Tinyint'   :
    case 'Mediumint' :
    case 'Float'     :
    case 'Decimal'   :$Code.="$"."query.=\"0\".".$DollarArray[$lp].".\",\";\n"; break;
    case 'Text'      :
    case 'Mediumtext':$Code.="$"."query.=\"'\".addslashes(base64_encode(".$DollarArray[$lp].")).\"',\";\n"; break;
    case 'Binary'    :$Code.="$"."query.=\"'\".".$DollarArray[$lp].".\"',\";\n";break;
    default:die('['.$Type.']659: unknown type :'.__FUNCTION__.' on '.__LINE__);
    }
  }
$Code=substr(trim($Code),0,-3)."\";\n$"."query.=\")\";\n";
$Code.="ELog('Add ".$tblname." : '.$"."query);\n";
$Code.="SingExec($"."query,$"."link,constant('DBMainDBType'));\n";
$Code.="$".$Pri."=SimpDB(\"select LAST_INSERT_ID()\",\"0\",constant(\"DBMainDBType\"),$"."link);\n";
$Code.="SetRecordLock($"."link,$"."UsrArray,'".$tblname."',$".$Pri.");\n";
$Code.="echo \"$.pnotify({title: 'Added', text: 'Adding complete',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8})|\";\n";
$Code.="}\n";
$Code.="//---------------------------------------------------------\n";
//-----------------
$Code.="function Upd".$tblname."($"."link,$"."UsrArray,".$DollarFields.")\n";
$Code.="{\n";
$Code.="// use cookie to check this user has rights to add to table ".$tblname." !!\n";
foreach($TFieldsArray as $FieldName => $fldtype)
  if (substr($FieldName,0,4)=='idLk')
    {
    $ShortFieldName=substr($FieldName,4);
    $Code.="  //  **** Delete the 2 lines below if ".$ShortFieldName." is populated in function Initialise with 'GetDropDownOptList'. Only keep if 'GetAutoCompleteList' was used\n";
    $Code.="     $".$ShortFieldName."=SimpDB(\"SELECT LkValue from Lookups where ListName='".substr($ShortFieldName,0,5)."' and LkOption='\".$".$ShortFieldName.".\"'\",\"0\",constant('DBMainDBType'),$"."link);\n";
    $Code.="  OR $".$ShortFieldName."=CheckAndGetLookup($"."link,constant('DBMainDBType'),$".$ShortFieldName.",'".substr($ShortFieldName,0,5)."');\n";
    }
$Code.="//--\n";
$Code.="// primary field is :".$Pri."\n";
$Code.="if (!isRecordLocked($"."link,$"."UsrArray,'".$tblname."',$".$Pri."))\n";
$Code.="  {\n";
$Code.="  echo \"$.pnotify({title: 'Update failed', text: 'Record is not locked by me',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8})|\";\n";
$Code.="  return;\n";
$Code.="  }\n";
$Code.="$"."query =\"UPDATE ".constant('DBName').$tblname." set \";\n";
for($lp=0;$lp<count($TFieldNamesArray);$lp++)
  {
  if ($TFieldNamesArray[$lp] == $Pri) continue;
  $Type=ucwords(strtolower($TFieldTypesArray[$lp]));
  switch($Type)
    {
    case 'Time'      :$Code.="$"."query.=\" ".$DBInsertArray[$lp]."='\".".$DollarArray[$lp].".\"',\";\n"; break;
    case 'Varchar'   :$Code.="$"."query.=\" ".$DBInsertArray[$lp]."='\".".$DollarArray[$lp].".\"',\";\n"; break;
    case 'Date'      :$Code.="$"."query.=\" ".$DBInsertArray[$lp]."=\";";$Code.="if (trim(".$DollarArray[$lp].")==\"\") \$"."query.=\"null,\"; else {\$"."a=strpos(".$DollarArray[$lp].",':');\$"."a=strpos(".$DollarArray[$lp].",' ',\$"."a);".$DollarArray[$lp]."=substr(".$DollarArray[$lp].",0,\$"."a);".$DollarArray[$lp]."=strtotime(".$DollarArray[$lp].");$"."query.=\"STR_TO_DATE('\".date('d-m-Y',".$DollarArray[$lp].").\"','%d-%m-%Y'),\";}\n";
                      break;
    case 'Datetime'  :if ( ($DBInsertArray[$lp]=='CreatedOn') || ($DBInsertArray[$lp]=='LastUpdated') )
                        $Code.="$"."query.=\" ".$DBInsertArray[$lp]."=sysdate(),\";\n";
                      else
                        {$Code.="$"."query.=\" ".$DBInsertArray[$lp]."=\";";$Code.="if (trim(".$DollarArray[$lp].")==\"\") \$"."query.=\"null,\"; else {\$"."a=strpos(".$DollarArray[$lp].",':');\$"."a=strpos(".$DollarArray[$lp].",' ',\$"."a);".$DollarArray[$lp]."=substr(".$DollarArray[$lp].",0,\$"."a);".$DollarArray[$lp]."=strtotime(".$DollarArray[$lp].");$"."query.=\"STR_TO_DATE('\".date('d-m-Y H:i',".$DollarArray[$lp].").\"','%d-%m-%Y %H:%i'),\";}\n";}
                      break;
    case 'Int'       :if (substr($FieldName,0,4)=='idLk') {$Code.="$"."query.=\" ".$DBInsertArray[$lp]."=\".".$DollarArray[$lp].".\",\";\n";break;}
    case 'Smallint'  :
    case 'Tinyint'   :
    case 'Float'     :
    case 'Mediumint' :
    case 'Decimal'   :$Code.="$"."query.=\" ".$DBInsertArray[$lp]."=0\".".$DollarArray[$lp].".\",\";\n"; break;
    case 'Text'      :
    case 'Mediumtext':$Code.="$"."query.=\" ".$DBInsertArray[$lp]."='\".addslashes(base64_encode(".$DollarArray[$lp].")).\"',\";\n"; break;
    case 'Binary'    :$Code.="$"."query.=\" ".$DBInsertArray[$lp]."='\".".$DollarArray[$lp].".\"',\";\n"; break;
    default:die($Type.': unknown type :'.__FUNCTION__.' on '.__LINE__);
    }
  }
$Code=substr(trim($Code),0,-3)."\";\n$"."query.=\" where ".$Pri."\".FixNull($".$Pri.");\n";
$Code.="ELog('Updating ".$tblname." : '.$"."query);\n";
$Code.="SingExec($"."query,$"."link,constant('DBMainDBType'));\n";
$Code.="echo \"$.pnotify({title: 'Updated', text: 'Update complete',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8})|\";\n";
$Code.="}\n";
$Code.="//---------------------------------------------------------\n";
//-----------------
$Code.="function Pop".$tblname."($"."link,$"."UsrArray,$".$Pri.") // select 1 record\n";
$Code.="{\n";
$Code.="// use cookie to check this user has rights to add to table ".$tblname." !!\n";
$Code.="if (($"."Updater=SetRecordLock($"."link,$"."UsrArray,'".$tblname."',$".$Pri."))!='') echo \"$.pnotify({title: 'Record Locked', text: 'This record is being updated by \".$"."Updater.\". Please try later.',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8})|\";\n";
$Code.="//--\n";
$Code.="$"."query =\"SELECT ".$DBSelectFields." from ".constant('DBName').$tblname." where ".$Pri."=\".$".$Pri.";\n";
$Code.="$"."result=GetResult($"."link,constant('DBMainDBType'),$"."query);\n";
$Code.="list(".$DollarFields.")=GetRowArray($"."link,constant('DBMainDBType'),$"."result);\n";
$Code.="CleanUpResources(constant('DBMainDBType'),$"."result);\n";
$Code.="//-- set lookups\n";
$Code.="//-- \n";
for($lp=0;$lp<count($TFieldNamesArray);$lp++)
  {
  if (substr($TFieldNamesArray[$lp],0,4)=='idLk')
    {
    //$Code.="echo \"$('#".$FormArray[$lp]."').val('\".".$DollarArray[$lp].".\"')|\";\n";
    $Code.="echo \"$('#".$FormArray[$lp]."').select2('val','\".".$DollarArray[$lp].".\"')|\";\n";
    continue;
    }
  if (substr($TFieldNamesArray[$lp],0,3)=='flg')
    {
    $Code.="if (".$DollarArray[$lp]."==1) echo \"$('#".$FormArray[$lp]."').prop('checked',true).iphoneStyle('refresh')|\"; else echo \"$('#".$FormArray[$lp]."').prop('checked',false).iphoneStyle('refresh')|\";\n";
    continue;
    }
  if (substr($TFieldNamesArray[$lp],0,4)=='bopt')
    {
    $Code.="echo \"$('#".$FormArray[$lp]." button.active').removeClass('active')|$('#".$FormArray[$lp]." button[data-sel=\\\"\".".$DollarArray[$lp].".\"\\\"]').addClass('active')|\";\n";
    continue;
    }
  if ($TFieldNamesArray[$lp]==$Pri)
    {
    $Code.="echo \"$('#".$FormArray[$lp]."').val('\".".$DollarArray[$lp].".\"')|\";\n";
    continue;
    }
  if ((ucwords(strtolower($TFieldTypesArray[$lp]))=='Mediumtext') || (ucwords(strtolower($TFieldTypesArray[$lp]))=='Text'))
    {
    $Code.="echo \"$('#".$FormArray[$lp]."').html( $.base64Decode($.base64Decode(".$DollarArray[$lp].")) )|\"; // or $('#".$FormArray[$lp]."').setCode($.base64Decode($.base64Decode(".$DollarArray[$lp]."))); \n";
    continue;
    }
//  if ((substr($TFieldNamesArray[$lp],0,2)=='id') && (substr($TFieldNamesArray[$lp],0,4)!='idLk'))
//    {
//    $Code.="echo \"$('#".$FormArray[$lp]." option:selected').removeAttr('selected')|$('#".$FormArray[$lp]."option[value=\\\"\".".$DollarArray[$lp].".\"\\\"]').attr('selected', 'selected')|\";\n";
//    continue;
//    }
  if (substr(strtolower($TFieldTypesArray[$lp]),0,4)=='date')
    {
    //$Code.="echo \"$('#".$FormArray[$lp]." option:selected').removeAttr('selected')|$('#".$FormArray[$lp]."option[value=\\\"\".".$DollarArray[$lp].".\"\\\"]').attr('selected', 'selected')|\";\n";
    $Code.="echo \"$('#".$FormArray[$lp]."').data('datetimepicker').setDate(new Date('\".".$DollarArray[$lp].".\"'))|\";\n";
    continue;
    }
  $Code.="echo \"$('#".$FormArray[$lp]."').val('\".".$DollarArray[$lp].".\"')|\";\n";
  }
$Code.="}\n";
$Code.="//---------------------------------------------------------\n";
//-----------------
$Code.="function Del".$tblname."($"."link,$"."UsrArray,$".$Pri.")\n";
$Code.="{\n";
$Code.="// use cookie to check this user has rights to delete from table ".$tblname." !!\n";
$Code.="if (!isRecordLocked($"."link,$"."UsrArray,'".$tblname."',$".$Pri."))\n";
$Code.="  {\n";
$Code.="  echo \"$.pnotify({title: 'Delete failed', text: 'Record is locked by another user',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8})|\";\n";
$Code.="  return;\n";
$Code.="  }\n";
$Code.="//--\n";
$Code.="$"."query =\"delete from ".constant('DBName').$tblname." where ".$Pri."\".FixNull($".$Pri.");\n";
$Code.="ELog('Deleting ".$tblname." : '.$"."query);\n";
$Code.="SingExec($"."query,$"."link,constant('DBMainDBType'));\n";
$Code.="echo \"$.pnotify({title: 'Delete', text: 'Delete complete',icon: 'icon-pushpin',type: 'info', addclass: 'stack-bottomleft', stack: stack_bottomleft,opacity: .8})|\";\n";
$Code.="// clear form or populate table\n";
$Code.="ClearAllUserLocks($"."link,$"."UsrArray['"."idUsers']);\n";
$Code.="}\n";
$Code.="//---------------------------------------------------------\n";
//-----------------
$Code=htmlspecialchars($Code);
return($Code);
}
//---------------------------------------------------------
function MakeSelName($Type,$FieldName)
{
$Res="";
switch(strtolower($Type))
  {
  case 'time'      :$Res="TIME_FORMAT(".$FieldName.",'%H:%i:%s')";break;
  case 'date'      :$Res="DATE_FORMAT(".$FieldName.",'%Y-%m-%d')";break;
  case 'datetime'  :$Res="DATE_FORMAT(".$FieldName.",'%Y-%m-%d %H:%i:%s')";break;
  default:$Res=$FieldName;
  }
// possible you want to check for "opt" and return CASE optJobType WHEN 'P' THEN 'Permanent' WHEN 'C' THEN 'Contract'  WHEN 'F' THEN 'Fixed Price'  WHEN 'T' THEN 'Fixed Term' else 'Unknown' END
if (substr($FieldName,0,3)=='flg')
  {
  $Res="ifnull(".$FieldName.",'0')";
  }
return($Res);
}
//---------------------------------------------------------
function MakePHPName($fldtype,$fldname)
{
//echo ">>".$fldtype.":".$fldname."<br />";
if ( (substr(strtolower($fldname),0,4)=='bopt') && ($fldtype=="VARCHAR") ) return(substr($fldname,4));
if ( (substr(strtolower($fldname),0,3)=='opt')  && ($fldtype=="VARCHAR") ) return(substr($fldname,3));
if (substr(strtolower($fldname),0,3)=='flg')                                                           return(substr($fldname,3));
if (substr(strtolower($fldname),0,4)=='idlk')                                                          return(substr($fldname,4));
return($fldname);
}
//---------------------------------------------------------
