<?php 
/*
 * Separating out this section allows devs to update the way data is loaded into the calendar
 *
 */

  $date='';
  $hours='';
  $hourtype='';
  $time_in='';
  $time_out='';
  $note='';
  
  if($entries):
    foreach($entries as $row): 
    $date=$date."'".$row->Date."',";
    $hours=$hours."'".$row->Hours."',";
    $hourtype=$hourtype."'".$row->HourType."',";
    $time_in=$time_in."'".$row->TimeIn."',";
    $time_out=$time_out."'".$row->TimeOut."',";
    $note=$note."'".addslashes($row->Note)."',";
    endforeach;
  endif;

?>

<meta name="viewport" content="width=device-width, user-scalable=no" />
<script type="text/javascript">
  var input_saved="<?php echo $input_saved;?>";
  var setup_path="<?php echo get_admin_url(null,'admin.php?page=dynamic-time');?>";
  var rate="<?php echo $rate;?>";
  var prompt="<?php echo $prompt;?>";
  var notes="<?php echo $notes;?>";
  var exempt="<?php echo $exempt;?>";
  var period="<?php echo $period;?>";
  var weekbegin="<?php echo $weekbegin;?>";
  var currency="<?php echo $currency;?>";

  var period_end=<?php echo '['.substr($period_end,0,-1).']';?>;
  var period_rate=<?php echo '['.substr($period_rate,0,-1).']';?>;
  var period_bonus=<?php echo '['.substr($period_bonus,0,-1).']';?>;
  var period_note=<?php echo '['.substr($period_note,0,-1).']';?>;
  var submitted=<?php echo '['.substr($submitted,0,-1).']';?>;
  var submitter=<?php echo '['.substr($submitter,0,-1).']';?>;
  var approved=<?php echo '['.substr($approved,0,-1).']';?>;
  var approver=<?php echo '['.substr($approver,0,-1).']';?>;
  var processed=<?php echo '['.substr($processed,0,-1).']';?>;

  var db_date=<?php echo '['.substr($date,0,-1).']';?>;
  var db_hours=<?php echo '['.substr($hours,0,-1).']';?>;
  var db_hourtype=<?php echo '['.substr($hourtype,0,-1).']';?>;
  var db_time_in=<?php echo '['.substr($time_in,0,-1).']';?>;
  var db_time_out=<?php echo '['.substr($time_out,0,-1).']';?>;
  var db_note=<?php echo '['.substr($note,0,-1).']';?>;
  
  var dyt_interval=setInterval(function() {if(document.readyState==='complete') { clearInterval(dyt_interval); dyt_load();}},100);
</script>
