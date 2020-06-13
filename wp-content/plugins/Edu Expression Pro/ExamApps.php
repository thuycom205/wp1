<?php
include('PageControlClass.php');
include('gump.class.php');
include('autoInsert.class.php');
class ExamApps
{
    function __construct()
    {
	global $wpdb;
        $this->autoInsert=new autoInsert();
        $this->tableNameConfig = $wpdb->prefix."emp_configurations";
        $this->wpdb=$wpdb;
    }
    public function globalSanitize($post)
    {
	/*$gump = new GUMP();
        $afterPost=$gump->sanitize($post);
	return$afterPost;*/
	return$post;
    }
    public function getCurrentUserId()
    {
	return get_current_user_id();
    }
    public function showMessage($message,$type='info')
    {
        $msg='<div class="mrg"><div class="alert alert-'.$type.'"><button type="button" class="close" data-dismiss="alert">x</button>
        <center>'.__($message).'</center></div></div>';
        return $msg;
    }
    ################ file upload ################################################
    function uploadFile($photoPath,$file,$flName=null,$extArr,$mimeArr,$fileTypeArr=array())
    {
	
	$uploadPath=plugin_dir_path( __FILE__ ).'/'.$photoPath.'/';
	$fileArr=array();
	if(strlen($_FILES[$file]['name'])>0)
	{
	    $ext=strtolower(substr($_FILES[$file]['name'],strrpos($_FILES[$file]['name'],"."),5));
	    if($flName!=null)
	    $fileName=$flName;
	    else
	    $fileName=str_replace("(","",str_replace(" ","-",substr($_FILES[$file]['name'],0,strlen($_FILES[$file]['name'])-5)));
	    if($this->isvalidFileType($ext,$extArr)==-1)
	    {
		
		$msg="Unsupported File Type <b>'$ext'</b> File Name : <b>".$_FILES[$file]['name']."</b>";
		$fileArr['errorMsg']=$msg;    
		return $fileArr;
	    }	    
	    $nm1=rand();    
	    $nm=$fileName."-".$nm1.$ext;
	    $onm=$_FILES[$file]['name'];
	    $imageSizeArr=getimagesize($_FILES[$file]['tmp_name']);
	    if(!in_array($imageSizeArr['mime'],$mimeArr))
	    {
		$fileArr['errorMsg']="Invalid file";
		return $fileArr;
	    }
	    if(count($fileTypeArr)>0)
	    {
		if($fileTypeArr['maxWidth']<$imageSizeArr[0] || $fileTypeArr['maxHeight']<$imageSizeArr[1])
		{
		    $fileArr['errorMsg']="Invalid width or height of image";
		    return $fileArr;
		}
		if($fileTypeArr['maxSize']*1024<$_FILES[$file]['size'])
		{
		    $fileArr['errorMsg']="Invalid file size";
		    return $fileArr;
		}
	    }
	    if(!(move_uploaded_file($_FILES[$file]['tmp_name'],$uploadPath.$nm))) 
	    {
		$msg="Invalid file format!"; 
		$fileArr['errorMsg']=$msg;    
		return $fileArr;
	    }	    
	    chmod($uploadPath.$nm,0644);	    
	    $fileArr['fileName']=$onm;
	    $fileArr['uploadFileName']=$nm;	    
	    return $fileArr;
	}
	return $fileArr;
    }
    ################ end file upload ############################################
    #checks wheter passed in file type is valid or not
    function isvalidFileType($ext,$extArr)
    {    
	if(in_array($ext,$extArr))
	{
	    return 1;
	}
	else
	{
	    return -1;
	}
	    
    }
    #deletes the passed in up_id from upload and file from disk
    function deleteUpload($deletePath,$filename)
    {   
	$targetFile=plugin_dir_path( __FILE__ ).'/'.$deletePath.'/'.$filename;
	if(file_exists($targetFile))
	{
	    unlink($targetFile);
	}	
	return 1;
    }
    ################ pagination function #########################################
    public function paginateFunction(&$paginateArr,$num_record,$itemPerPage,$page,$isSearch="Yes",$isnum_drop="Yes")
    {
        $itemPerPage_opt="";
	$itemPerPage_arr=array("5","10","20","25","30","50","100","200","500");
	foreach($itemPerPage_arr as $v)
	{
	    if(!isset($_POST['nrpp']))
	    {
		if($itemPerPage==$v)
		$nr_sel="selected";
		else
		$nr_sel="";
	    }
	    else
	    {
		if($_POST['nrpp']==$v)
		$nr_sel="selected";
		else
		$nr_sel="";		
	    }
	    $itemPerPage_opt.="<option value=\"$v\" date-page=\"$v\" $nr_sel>$v</option>";
	}    
        $pagecon = new PageControl($num_record, $itemPerPage,$page);
        //Print a list of pages
        $link= $pagecon->getList(array(
                'link'		=>	"<li><a href=\"javascript:void(0)\" class=\"inav\" data-page='#PAGE#';\">#PAGE#</a></li>",	//the link of each page, #PAGE# is the page number
                'current'	=>	"<li class=\"active\"><span>#PAGE# </span></li>",	//how the current page is showed
                'neighbor'	=>	4,	//how many pages next to the current page will be shown
                'headfoot'	=>	false,	//show the first and the last page number
                'skipped'	=>	"<li class=\"disabled\"><span>...</span></li>",	//what replaces the skipped pages replace
                'previous'	=>	"<li><a href=\"javascript:void(0)\" class=\"inav\" data-page='#PAGE#'>&larr; ".__('Previous')."</a></li>",	//show the "previous" button, #PAGE# is the page number
                'next'		=>	"<li><a href=\"javascript:void(0)\" class=\"inav\" data-page='#PAGE#'>".__('Next')."&rarr;</a></li>",	//show the "next" button, #PAGE# is the page number
		'disprevious'	=>	"<li class=\"disabled\"><span>&larr; ".__('Previous')." </span></li>",	//show the "previous disblaed" button, #PAGE# is the page number
		'disnext'	=>	"<li class=\"disabled\"><span>".__('Next')." &rarr;</span></li>"	//show the "next disabled" button, #PAGE# is the page number
        ));
        $co=0;
        if(($page=="")||($page==0))
        {
            $co=0;
            $limit1=0;
            $l1=1;
            $show_page=1;
        }
        else
        {
	    $co=$itemPerPage*($page-1);
	    $l1=$pagecon->getEntryFrom();
	    $limit1=$l1-1;
            $show_page=$page;
        }
        $st_lim=--$show_page*$itemPerPage+1;
        $end_lim=++$show_page*$itemPerPage;	
	if($isnum_drop=="Yes")
	$num_drop="<div class=\"col-md-2\"><label><small>Show&nbsp;</small><select name=\"tSortable_length\" class=\"ipage\" class=\"input-sm-small\">$itemPerPage_opt</select> <small>&nbsp;entries</small></label></div>";
	else
	$num_drop="";
	if($isSearch=="Yes")
	{
	    $form="<form action=\"#\" id=\"SearchForm\" method=\"post\" accept-charset=\"utf-8\">
	    <div class=\"input-group\"><input name=\"keyword\" value=\"".$_POST['keyword']."\" class=\"form-control Keyword\" placeholder=\"".__('Search')."\" type=\"text\"/>
	    <span class=\"input-group-btn\"><button class=\"btn btn-success btn-sm search-btn-main\" type=\"button\"><span class=\"fa fa-search\"></span></button></span>
	    </div></form>";
	}
	else
	$form="";
        $show_link="<div class=\"col-md-12\"><div class=\"row\">$num_drop<div class=\"col-md-5\">
	<ul class=\"pagination pagination-sm\">$link</ul></div>
	<div class=\"col-md-3\"><small>Showing $st_lim to $end_lim of <strong>$num_record</strong> entries</small></div>
        <div class=\"col-md-2\">
        $form
	</div></div></div>";
        $limit=$limit="LIMIT $limit1,$itemPerPage";
        $paginateArr=array($show_link,$limit,$l1); 
    }
    public function configuration()
    {
	$SQL= "SELECT * from `".$this->tableNameConfig."` WHERE `id`=1";
	$this->autoInsert->iFetch($SQL,$result);
        return$result;
    }
    public function getPaginateSetting($post,$configuration)
    {
        if(isset($post['page']))
        {
            $pageNumber = filter_var($post['page'], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
            if(!is_numeric($pageNumber)){die('Invalid page number!');}
        }
        else
        {
            $pageNumber = 1;
        }
        if(isset($post['ipage']))
        {
            $itemPerPage = filter_var($post['ipage'], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
            if(!is_numeric($itemPerPage)){die('Invalid page number!');}
        }
        else
        {
            if($configuration['min_limit']>$configuration['max_limit'])
            $itemPerPage = $configuration['max_limit'];
            else
            $itemPerPage = $configuration['min_limit'];
        }
        return array('pageNumber'=>$pageNumber,'itemPerPage'=>$itemPerPage);
    }
    public function getRecordSet($SQL,$itemPerPage,$pageNumber,$fieldObject,$SQLc=null)
    {
        $SQLArr=explode('FROM',$SQL);
	$SQLArrFn=explode("ORDER BY",$SQLArr[1]);
        if($SQLc==null)
	$SQLc='SELECT COUNT(DISTINCT('.$fieldObject.')) as `count` FROM'.$SQLArrFn[0];
        $this->autoInsert->iFetchCount($SQLc,$getTotalRows);
        $totalPages = ceil($getTotalRows/$itemPerPage);
        $pagePosition = (($pageNumber-1) * $itemPerPage);
        $SQL= $SQL." LIMIT ".$pagePosition.",".$itemPerPage;
        $this->autoInsert->iWhileFetch($SQL,$result);
        return array('result'=>$result,'getTotalRows'=>$getTotalRows);
    }
    public function getAdvancedSearch(&$condition_arr,$field_name,$fieldValue,$operator,$extra_field_value="",$extra_field_name1="",$extra_field_name2="")
    {
        $condition="";
	$page_value="";
        if(strlen($fieldValue)>0)
        {
            if($operator=="=")
            {
                $condition="AND $field_name$operator'$fieldValue'";
                $page_value="&$field_name=$fieldValue";
            }
            elseif($operator=="LIKE")
            {
                $condition="AND $field_name LIKE '%$fieldValue%'";
                $page_value="&$field_name=$fieldValue";
            }
            elseif($operator=="COND")
            {
                $condition=" AND $field_name>=$fieldValue AND $field_name<=$extra_field_value ";
                $page_value="&$extra_field_name1=$fieldValue&$extra_field_name2=$extra_field_value";
            }
            elseif($operator=="RANGE")
            {
                $condition="AND ($field_name>='$fieldValue' AND $extra_field_name1<='$extra_field_value') OR($field_name<='$extra_field_value' AND $extra_field_name1>='$fieldValue')";
                $page_value="&$field_name=$fieldValue&$extra_field_name1=$extra_field_value";
            }
            elseif($operator=="DATE-RANGE")
            {
                $date_to=$fieldValue." 0:0:0";
                $date_from=$extra_field_value." 23:59:59";
                $condition="AND $field_name BETWEEN '$date_to' AND '$date_from'";
                $page_value="&$extra_field_name1=$fieldValue&$extra_field_name2=$extra_field_value";
            }
            else
            {
                $tm=server_time();
                $tot_sec=$tm-$fieldValue*60*60*60;
                $condition=" AND $field_name>='$tot_sec'";
                $page_value="&$field_name=$fieldValue";
            }
        }
        return $condition_arr=array('condition'=>$condition,'pageValue'=>$page_value);
    }
    public function getDropdownArray($sel_id,&$option,$array_name)
    {
	foreach($array_name as $k=>$v)
	{
	    if($sel_id==$k)
	    {
		$sel="Selected";
	    }
	    else
	    {
		$sel="";
	    }
	    $option.="<option value=\"$k\" $sel>$v</option>";
	}
    }
    public function getDropdownDb($selId,&$option,$table,$fieldId,$fieldValue,$where="",$joinIdName="",$mergeField="")
    {
        if(strlen($mergeField)>0)
        {
            $SQL="SELECT `PrimaryTable`.`".$fieldId."`,`PrimaryTable`.`".$fieldValue."`,$mergeField AS `MergeField` FROM `".$table."` AS `PrimaryTable` ".$where;
        }
        else
        $SQL="SELECT `PrimaryTable`.`".$fieldId."`,`PrimaryTable`.`".$fieldValue."` FROM `".$table."` AS `PrimaryTable` ".$where;
        $this->autoInsert->iWhileFetch($SQL,$records);
        foreach($records as $record)
        {
            if(strlen($joinIdName)>0)
            $fieldId=$joinIdName;
	    if($selId==$record[$fieldId])
            $selected="selected";
            else
            $selected="";	
            if(strlen($mergeField)>0)
            $option.="<option value=\"$record[$fieldId]\" $selected >".$this->h($record[$fieldValue]).".($record[MergeField])</option>";
            else
            $option.="<option value=\"$record[$fieldId]\" $selected >".$this->h($record[$fieldValue])."</option>";
        }
    }
    public function getMultipleDropdownDb($selId,$table,$fieldId,$fieldValue,$where="",$joinIdName="",$mergeField="")
    {
        if(strlen($mergeField)>0)
        {
            $SQL="SELECT `PrimaryTable`.`".$fieldId."`,`PrimaryTable`.`".$fieldValue."`,$mergeField AS `MergeField` FROM `".$table."` AS `PrimaryTable` ".$where;
        }
        else
        $SQL="SELECT `PrimaryTable`.`".$fieldId."`,`PrimaryTable`.`".$fieldValue."` FROM `".$table."` AS `PrimaryTable` ".$where;
        $this->autoInsert->iWhileFetch($SQL,$records);
        foreach($records as $record)
        {
            if(strlen($joinIdName)>0)
            $fieldId=$joinIdName;
	    if(!is_array($selId))
	    $selId=array();
            if(in_array($record[$fieldId],$selId))
            $selected="selected";
            else
            $selected="";	
            if(strlen($mergeField)>0)
            $option.="<option value=\"$record[$fieldId]\" $selected>".$this->h($record[$fieldValue])."($record[MergeField])</option>";
            else
            $option.="<option value=\"$record[$fieldId]\" $selected>".$this->h($record[$fieldValue])."</option>";
        }
        return$option;
    }
    function getRadioArray($selId,$arrayName,&$option,$compName,$isValid="No")
    {
	foreach($arrayName as $k=>$v)
	{
	    if($selId==$k)
	    {
		$sel="Checked";
	    }
	    else
	    {
		$sel="";
	    }
	    if($isValid=="Yes")
	    $valid="class=\"validate[required]\"";
	    else
	    $valid="";
	    $option.="<label class=\"checkbox inline\"><input type=\"radio\" name=\"$compName\" id=\"$compName\" $sel $valid value=\"$k\" /> $v </label>";
	}
    }
    function getCheckboxArray($selId,$arrayName,&$option,$compName,$isValid="No")
    {
	foreach($arrayName as $k=>$v)
	{
	    if($selId==$k)
	    {
		$sel="Checked";
	    }
	    else
	    {
		$sel="";
	    }
	    if($isValid=="Yes")
	    $valid="required";
	    else
	    $valid="";
	    $option.="<label class=\"checkbox inline\"><input type=\"checkbox\" name=\"$compName\" id=\"$compName\" $sel $valid value=\"$k\" /> $v </label>";
	}
    }
    public function getGroupName($primaryTable,$foreignTable,$fieldId,$id)
    {
	$primaryTableObject=$this->getTableObject($primaryTable);
        $foreignTableObject=$this->getTableObject($foreignTable);
	$userGroupWiseCond=$this->userGroupWiseIn("`$primaryTableObject`".".`group_id`");
        $SQL="SELECT * FROM `".$this->wpdb->prefix.$primaryTable."` as `".$primaryTableObject."` LEFT JOIN `".$this->wpdb->prefix.$foreignTable."` as `".$foreignTableObject."` ON `".$foreignTableObject."`.`id`=`".$primaryTableObject."`.`group_id` WHERE `".$primaryTableObject."`.`".$fieldId."`=".$id.$userGroupWiseCond;
        $this->autoInsert->iWhileFetch($SQL,$groupArr);
        return$groupArr;
    }
    public function getTableObject($name)
    {
        $name=rtrim(str_replace(" ","",ucwords(str_replace('_'," ",$name))),'s');
        return$name;
    }
    public function showGroupName($primaryTable,$foreignTable,$fieldId,$id,$string=" | ")
    {
        $groupArr=$this->getGroupName($primaryTable,$foreignTable,$fieldId,$id);
        $groupNameArr=array();
        foreach($groupArr as $groupName)
        {
            $groupNameArr[]=$groupName['group_name'];
        }
        unset($groupName);
        $showGroup= implode($string,$groupNameArr);
        unset($groupNameArr);
        return $this->h($showGroup);
    }
    public function h($s)
    {
        return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
    }
    public function secondsToWords($seconds,$msg="Unlimited")
    {
        $ret = "";
        if($seconds>0)
        {
            /*** get the hours ***/
            $hours = intval(intval($seconds) / 3600);
            if($hours > 0)
            {
                $ret .= $hours.' '.__('Hours').' ';
            }
            /*** get the minutes ***/
            $minutes = bcmod((intval($seconds) / 60),60);
            if($hours > 0 && $minutes > 0)
            {
                $ret .= $minutes.' '.__('Mins').' ';
            }
            $tarMinutes = bcmod((intval($seconds)),60);
            if(strlen($ret)==0 || $tarMinutes>0)
            {
                if($tarMinutes>0)
                $ret .= $tarMinutes.' '.__('Sec');
                else
                $ret .= $seconds.' '.__('Sec');
            }
        }
        else
        {
            $ret=$msg;
        }
        return $ret;
    }
    public function secondsToHourMinute($seconds)
    {
        $ret = "";
        if($seconds>0)
        {
            /*** get the hours ***/
            $hours = intval(intval($seconds) / 3600);
            if($hours > 0)
            {
                $ret .= "$hours.";
            }
            /*** get the minutes ***/
            $minutes = bcmod((intval($seconds) / 60),60);
            if($hours > 0 || $minutes > 0)
            {
                $ret .= "$minutes";
            }
        }
        else
        {
            $ret="";
        }
        return (float) $ret;
    }
    function recordSorting($request,$name)
    {
        if($request['orderby']==$name && $request['order']=="asc")
        {
            $sortable="sorted asc";
        }
        elseif($request['orderby']==$name && $request['order']=="desc")
        {
            $sortable="sorted desc";
        }
        else
        {
            $sortable="sortable desc";
        }
        return $sortable;
    }
    function sortableOrder($request)
    {
        if(isset($request['order']) && $request['order']=="asc")
        $order="desc";
        else
        $order="asc";
        return$order;
    }
    function sortableString($request)
    {
        if(isset($request['order']))
        {
            if($request['order']=="asc")
            $order='"order":"asc"';
            else
            $order='"order":"desc"';
            $orderClause=',"orderby":"'.$request['orderby'].'",';
        }
        else
        {
            $order=null;
            $orderClause=null;
        }
        return array('order'=>$order,'orderClause'=>$orderClause);
    }
    function sortedQuery($post,$sortedString='id')
    {
        $sortedString=str_replace('.','`.`',$sortedString);
        $post['orderby']=str_replace('.','`.`',$post['orderby']);
        if(isset($post['order']))
        {
            if($post['order']=="asc")
            $orderBy="ORDER BY `".$post['orderby']."` ASC";
            else
            $orderBy="ORDER BY `".$post['orderby']."` DESC";
        }
        else
        {
            $orderBy="ORDER BY `".$sortedString."` DESC";
        }
        return$orderBy;
    }
    public function dateTimeFormat($date)
    {
	return date(get_option('date_format').' '.get_option('time_format'),strtotime($date));
    }
    public function dateFormat($date)
    {
	return date(get_option('date_format'),strtotime($date));
    }
    public function datePickerFormat()
    {
	return 'YYYY-MM-DD';
    }
    public function datePickerValueFormat($date)
    {
	return date('Y-m-d',strtotime($date));
    }
    public function dateTimePickerValueFormat($date)
    {
	return date('Y-m-d H:i',strtotime($date));
    }
    public function dateFormatBeforeSave($dateString)
    {
      return date('Y-m-d', strtotime($dateString));
    }
    public function dateTimeFormatBeforeSave($dateString)
    {
      return date('Y-m-d H:i:s', strtotime($dateString));
    }
    public function currentDateTime($currentTime=null)
    {
	if($currentTime==null)
	return current_time('Y-m-d H:i:s');
	else
	return date('Y-m-d H:i:s',$currentTime);
    }
    public function currentDate($currentDate=null)
    {
	if($currentDate==null)
	return current_time('Y-m-d');
	else
	return date('Y-m-d',$currentDate);
    }
    public function getStringDateFormat($string,$currentDate=null)
    {
	if($currentDate==null)
	return current_time($string);
	else
	return date($string,$currentDate);
    }
    public function studentAverageResult($examId)
    {        
	$sql="SELECT COUNT(*) AS `count`  FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$examId;
	$this->autoInsert->iFetch($sql,$totalAttempt);
	$sql1="SELECT SUM(`ExamResult`.`percent`) AS `total` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$examId;
        $this->autoInsert->iFetch($sql1,$totalPercent);
        if($totalAttempt['count']>0)
        $averagePercent=number_format($totalPercent['total']/$totalAttempt['count'],2);
        else
        $averagePercent=0;
        return($averagePercent);
        
    }
    public function examTotalAbsent($examId,$findMethod='count')
    {       
	if($findMethod=='count')
	$field="COUNT(DISTINCT(`Student`.`id`)) as `count`";
	else
	$field="`User`.`ID` AS `ID`,`User`.`display_name` AS `name`,`User`.`user_email` AS `email`";
	$sql="SELECT $field FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`)  INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON (`StudentGroup`.`group_id`=`ExamGroup`.`group_id`)  INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON (`Student`.`student_id`=`StudentGroup`.`student_id`)
	INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`Student`.`student_id`)  LEFT JOIN `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` ON (`ExamResult`.`student_id`=`Student`.`student_id` AND `ExamResult`.`exam_id`=`Exam`.`id`) WHERE `Exam`.`status`='Closed' AND `Exam`.`id`=".$examId ." AND `ExamResult`.`id` IS NULL AND `Exam`.`end_date`>`User`.`user_registered`";
	if($findMethod=='count')
	{
	    $this->autoInsert->iFetchCount($sql,$count);
	    if($count==null)
	    $count=0;
	    return $count;
	}
	else
	{
	    $this->autoInsert->iWhileFetch($sql,$record);
	    return$record;
	}
    }
    public function studentStat($examId,$type,$findMethod='count')
    {
	if($findMethod=='count')
	$field="COUNT(*) as `count`";
	else
	$field="`User`.`ID` AS `ID`,`User`.`display_name` AS `name`,`User`.`user_email` As `email`,`ExamResult`.`percent`,`ExamResult`.`result`";
	$sql="SELECT $field  FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON (`Student`.`student_id`=`ExamResult`.`student_id`) INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`Student`.`student_id`) where `ExamResult`.`exam_id`=".$examId." and `ExamResult`.`result`='$type'";
	if($findMethod=='count')
	{
	    $this->autoInsert->iFetchCount($sql,$count);
	    if($count==null)
	    $count=0;
	    return $count;
	}
	else
	{
	    $this->autoInsert->iWhileFetch($sql,$record);
	    return$record;
	}
    }
    public function userGroupWiseId()
    {
	$record=array();
	$userId=$this->getCurrentUserId();
	$sql="SELECT `UserGroup`.`group_id` FROM `".$this->wpdb->prefix."users` as `User` INNER JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON(`User`.`id`=`UserGroup`.`user_id`) WHERE `User`.`ID`=".$userId;
	$this->autoInsert->iWhileFetch($sql,$record);
	$userGroupArr=array();
        foreach($record as $v)
        {
            $userGroupArr[]=implode(",",$v);
        }
        $userGroupList="";
        if(is_array($userGroupArr))
        $userGroupList=implode(",",$userGroupArr);
        if(strlen($userGroupList)==0)
        $userGroupList="''";
        return$userGroupList;
    }
    public function userGroupWise()
    {
	$condition=null;
	if(current_user_can('contributor'))
	$condition=" AND `UserGroup`.`user_id`=".$this->getCurrentUserId();
	return$condition;
    }
    public function userGroupWiseIn($primaryTableObject)
    {
	$userGroupWiseId=$this->userGroupWiseId();
	$userGroupWiseCond=null;
	if(current_user_can('contributor'))
	$userGroupWiseCond=" AND ".$primaryTableObject." IN(".$userGroupWiseId.")";
	return$userGroupWiseCond;
    }
    public function WalletInsert($student_id,$amount,$amount_type,$date,$type,$remarks,$user_id=null)
    {
        $in_amount=null;
        $out_amount=null;
        if($amount_type=="Added")
        $in_amount=$amount;
        else
        $out_amount=$amount;
        if($in_amount==null && $out_amount==null)
        {
            return false;
        }
        elseif($amount<=0)
        {
            return false;
        }
        else
        {
            $sql="SELECT SUM(`Wallet`.`in_amount`) AS `in_amount`,sum(`Wallet`.`out_amount`) AS `out_amount` from `".$this->wpdb->prefix."emp_wallets` AS `Wallet` where  `Wallet`.`student_id`=".$student_id;
	    $this->autoInsert->iFetch($sql,$AmountArr);
	    $total_in_amount=$AmountArr['in_amount'];
            $total_out_amount=$AmountArr['out_amount'];
            if($total_in_amount=="")
            $total_in_amount=0;
            if($total_out_amount=="")
            $total_out_amount=0;
            $balance=$total_in_amount-$total_out_amount+$in_amount-$out_amount;
            $record_arr=array('student_id'=>$student_id,'in_amount'=>$in_amount,'out_amount'=>$out_amount,'balance'=>$balance,'date'=>$date,'type'=>$type,
                              'remarks'=>$remarks,'user_id'=>$user_id);
	              
            if($this->autoInsert->iInsert($this->wpdb->prefix."emp_wallets",$record_arr))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    public function WalletBalance($studentId)
    {
	$SQL="SELECT `balance` FROM `".$this->wpdb->prefix."emp_wallets` AS `Wallet` WHERE `student_id`=".$studentId." ORDER BY `id` DESC LIMIT 1";
	$this->autoInsert->iFetch($SQL,$balanceWallet);
        $balance="0.00";
        if(count($balanceWallet)>0)
        {
            $balance=$balanceWallet['balance'];
        }
        return $balance;
    }
    public function sendSms($mobileNo,$message)
    {
	$this->autoInsert->iFetch("SELECT * FROM `".$this->wpdb->prefix."emp_smssettings` WHERE `id`=1",$smsArr);
        $url=$smsArr['api'];
        $postData=array($smsArr['husername']=>$smsArr['username'],$smsArr['hpassword']=>$smsArr['password'],$smsArr['hsenderid']=>$smsArr['senderid'],$smsArr['hmobile']=>$mobileNo,$smsArr['hmessage']=>$message);
        
        /*$file = fopen("D:\\xampp\\htdocs\\exam\\wordpress\\wp-content\\plugins\\examapp\\sms.txt","a+");
        fwrite($file,$url.'\n'.$mobileNo.'\n'.$message.'\n');
	fclose($file);*/
        
        $ch = curl_init();
        curl_setopt_array($ch, array(CURLOPT_URL => $url,CURLOPT_RETURNTRANSFER => true,CURLOPT_POST => true,CURLOPT_POSTFIELDS => $postData));
        $output = curl_exec($ch);
        curl_close($ch);
    }
    public function showRank($rank)
    {
        if($rank==1)
        $rank="1<sup>".__('st')."</sup>";
        elseif($rank==2)
        $rank="2<sup>".__('nd')."</sup>";
        elseif($rank==3)
        $rank="3<sup>".__('rd')."</sup>";
        else
        $rank="$rank<sup>".__('th')."</sup>";
        return$rank;
    }
    public function getCurrencyCode()
    {
	$configuration=$this->configuration();
	$this->autoInsert->iFetch("SELECT * FROM `".$this->wpdb->prefix."emp_currencies` WHERE `id`=".$configuration['currency'],$currencyArr);
	$currencyCode=$currencyArr['short'];
	return $currencyCode;
    }
    public function getCurrency()
    {
	$configuration=$this->configuration();
	$this->autoInsert->iFetch("SELECT * FROM `".$this->wpdb->prefix."emp_currencies` WHERE `id`=".$configuration['currency'],$currencyArr);
	$currency=$this->getImage('img/currencies/'.$currencyArr['photo']).'&nbsp;';
	return $currency;
    }
    public function getImage($image,$attribute=array())
    {
	if($attribute)
	{
	    foreach($attribute as $key=>$value)
	    {
		$attr.=$key.'="'.$value.'"';
		unset($key,$value);
	    }
	}
	return'<img src="'.plugin_dir_url(__FILE__).$image.'" '.$attr.'> ';
    }
    public function getUserRole($id)
    {
	$userInfo = get_userdata($id);
	$userRole = implode(', ',$userInfo->roles);
	return $userRole;
    }
    public function getDiffLevel($type)
    {
	$SQL="SELECT `diff_level` FROM `".$this->wpdb->prefix."emp_diffs` AS `DiffLevel` WHERE `type`='".$type."'";
	$this->autoInsert->iFetch($SQL,$record);
	$diffLevel=$this->h($record['diff_level']);
	return$diffLevel;
    }
    function checkPaidStatus($examId,$studentId)
    {
        $sql="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam`  WHERE `Exam`.`id`=".$examId." AND `Exam`.`status`='Active'";
	$this->autoInsert->iFetch($sql,$post);
        $attemptCount=$post['attempt_count'];
        $paidexam=$post['paid_exam'];
        $expiry=$post['expiry'];
        $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$examId." AND `ExamResult`.`student_id`=".$studentId;
	$this->autoInsert->iFetchCount($sql,$totalExam);
        $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_orders` AS `ExamOrder` WHERE `ExamOrder`.`exam_id`=".$examId." AND `ExamOrder`.`student_id`=".$studentId;
	$this->autoInsert->iFetchCount($sql,$countExamOrder);
        $ispaid=false;
        if($paidexam==1)
        {
            if($countExamOrder>0 && $attemptCount==0)
            {
                $ispaid=true;
            }
            else
            {
                if($countExamOrder*$attemptCount>$totalExam)
                {
                    $ispaid=true;
                }
            }
        }
        else
        {
            $ispaid=true;  
        }
        if($expiry>0)
        {
	    $sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_orders` AS `ExamOrder` WHERE `exam_id`=".$examId." AND `student_id`=".$studentId." ORDER BY `id` DESC";
	    $this->autoInsert->iFetch($sql,$examOrder);
            if($this->currentDate()>$examOrder['expiry_date'])
            {
                $ispaid=false;
            }
        }
        return$ispaid;
    }
    public function wpdocs_set_html_mail_content_type()
    {
        return 'text/html';
    }
}
?>