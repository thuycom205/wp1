<?php
class time_sheets_entry {

	function show_nothing() {
		echo "";
	}

	function process_timesheets() {
		if ($_GET['action'] == "approve" || $_GET['action'] == "reject" || $_GET['action'] == "split2" || $_POST['action'] == "approve" || $_POST['action'] == "reject" ) {
			$entry = new time_sheets_entry();
			$time_sheets_main = new time_sheets_main();
			$queue_payroll = new time_sheets_queue_payroll();
			$queue_invoice = new time_sheets_queue_invoice();
			$queue_approval = new time_sheets_queue_approval();

			add_filter('admin_footer_text', array($entry, 'show_nothing'));
			add_filter( 'update_footer', array($entry, 'show_nothing'));

			remove_filter('admin_footer_text', array($time_sheets_main, 'show_footer'));
			remove_filter( 'update_footer', array($time_sheets_main, 'show_footer_version'));


			if ($_GET['action'] == "approve" || $_POST['action'] == "approve") {
				if ($_GET['page'] == "payroll_timesheet") {
					$queue_payroll->do_timesheet_payroll();
				}
				if ($_GET['page'] == "invoice_timesheet") {
					$queue_invoice->do_timesheet_invoicing();
				}
				if ($_GET['page'] == "approve_timesheet") {
					$queue_approval->do_timesheet_approval_processing();
				}
			} elseif ($_GET['action'] == "split2") {
				if ($_GET['page'] == "approve_timesheet") {
					$queue_approval->do_timesheet_split();
				}
			} else {
				if ($_GET['page'] == "payroll_timesheet") {
					$queue_payroll->do_timesheet_rejectpayroll();
				}
				if ($_GET['page'] == "invoice_timesheet") {
					$queue_invoice->do_timesheet_rejectinvoicing();
				}
			}
		}
	}


	function email_on_submission($timesheet_id) {
		$options = get_option('time_sheets');

		if (!$options['enable_email']) {
			return;
		}

		global $wpdb;
		$db = new time_sheets_db();
		$common = new time_sheets_common();
		$user_id = get_current_user_id();

		$sql = "select user_login 
			from {$wpdb->prefix}timesheet t
			join {$wpdb->users} u on u.ID = t.user_id
			where t.timesheet_id = %d";
		$params = array($timesheet_id);
		$user_login = $db->get_var($sql, $params);

		$sql = "select user_email, display_name
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet_approvers_approvies aa on u.ID = aa.approver_user_id
			and aa.approvie_user_id = {$user_id}";

		$users = $db->get_results($sql);

		$sql = "select display_name
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet t on u.ID = t.user_id
		where t.timesheet_id = {$timesheet_id}";

		$display_user = $db->get_var($sql);

		$subject = "There are time sheet(s) pending approval.";
		$body = "A time sheet has been entered by {$display_user} and is pending approval.

It can be approved from the <a href='http://$_SERVER[HTTP_HOST]/wp-admin/admin.php?page=approve_timesheet'>approval menu</a>.";

		foreach ($users as $user) {
			$common->send_email ($user->user_email, $subject, $body);
		}

	}

	function enter_timesheet() {
		if ($_POST['action'] == 'save') {
			if ($this->validate_timesheet()=="true") {
				$timesheet_id = $this->save_timesheet();
			}
		}
		$this->show_timesheet($timesheet_id);
	}


	function check_overages($timesheet_id) {
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select MaxHours, HoursUsed, ProjectName
		from {$wpdb->prefix}timesheet_client_projects a
		join {$wpdb->prefix}timesheet b on a.ProjectId = b.ProjectId
		 where timesheet_id=%d";
		$params=array($timesheet_id);
		$project = $db->get_row($sql, $params);

		if ($project->HoursUsed > $project->MaxHours && $project->MaxHours<>0) {
			echo "<div if='message' class='update-nag'><p>The project {$project->ProjectName} has run over on hours. Please inform management that a new SOW may be required.</p></div><br>";
		}
	}

	function validate_timesheet() {
		$passed = true;

		if (!$_POST['start_date']) {
			echo '<div if="message" class="error"><p>The week start date is required.</p></div>';
			$passed="false";
		}

		if (!$_POST['ClientId']) {
			echo '<div if="message" class="error"><p>The client name is required.</p></div>';
			$passed="false";
		}

		if (!$_POST['ProjectId']) {
			echo '<div if="message" class="error"><p>A project is required.</p></div>';
			$passed="false";
		}

		return $passed;
	}

	function int_to_weekday($week_starts, $days_to_inc) {
		$value = $week_starts+$days_to_inc;
		if ($value > 6) {
			$value = $value-7;
		}

		if ($value==0) {
			$weekday = 'Sunday';
		}elseif ($value==1) {
			$weekday = 'Monday';
		}elseif ($value==2) {
			$weekday = 'Tuesday';
		}elseif ($value==3) {
			$weekday = 'Wednesday';
		}elseif ($value==4) {
			$weekday = 'Thursday';
		}elseif ($value==5) {
			$weekday = 'Friday';
		}elseif ($value==6) {
			$weekday = 'Saturday';
		}

		return $weekday;
	}


	function show_timesheet($timesheet_id=0) {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$main = new time_sheets_main();
		$settings = new time_sheets_settings();
		$options = get_option('time_sheets');
		$common = new time_sheets_common();

		$common->show_datestuff();

		$queue_approval = new time_sheets_queue_approval();
		$queue_invoice = new time_sheets_queue_invoice();
		$queue_payroll = new time_sheets_queue_payroll();

		
		if ($user_id == 0) {
			echo "<div if='message' class='error'><p>You must be logged in to view this page.</p></div>";
			return;
		}

		if (!$timesheet_id) {
			if ($_POST['timesheet_id']) {
				$timesheet_id = $_POST['timesheet_id'];
			} else {
				$timesheet_id = $_GET['timesheet_id'];
			}
		}

		$this->check_overages(intval($timesheet_id));

		$sql = "select * from {$wpdb->prefix}timesheet t join {$wpdb->users} u on t.user_id = u.ID left outer join {$wpdb->prefix}timesheet_client_projects tcp on t.ProjectId = tcp.ProjectId where timesheet_id=%d";
		$params = array($timesheet_id);

		if ($timesheet_id) {
			$timesheet = $db->get_row($sql, $params);
			$timesheet_user = $timesheet->display_name;
			$timesheet_user_id = $timesheet->user_id;
			$timesheet_id_int = $timesheet_id;
			
					
			if ((!$queue_approval->employee_approver_check('true') && !$queue_invoice->employee_invoicer_check() && !$queue_payroll->employee_payroll_check() ) && $timesheet_user_id <> $user_id) {
				echo "Only supervisors can view other employees time sheets.";
				return;
			}
		} else {
			#if ($_POST['submit']) {
			#	$sql = "select * from {$wpdb->prefix}timesheet where user_id=%d and start_date = %s";
			#	$params = array($user_id, $_POST['start_date']);

			#	$timesheet = $db->get_row($sql, $params);

			#} else {
			$timesheet = (object) $_POST;
			$timesheet_user_id = $user_id;
			#}
			$timesheet_id_int = 0;
			
		}

		echo "<br><H2>Weekly Time Sheet</H2>";

		$disable_object="";
		if ($timesheet->week_complete=='1') {
				$disable_object= " disabled";
		}
	
		echo "<form name='timesheet' method='POST' class='ws-validate'>";

		wp_nonce_field( 'timesheet_entry_'. $timesheet_id);

		if ($timesheet_user) {
			echo "Timesheet Number: {$timesheet_id}<BR>";
			echo "Timesheet For:  {$timesheet_user}<BR>";
		}
		if (!$timesheet->start_date) {
			$defaultDate = get_user_option('timesheet_defaultdate', $user_id);
			if (!$defaultDate) {
				$defaultDate='Monday Last Week';
			}
			$start_date = date('Y-m-d', strtotime($defaultDate));
			$clientid = -1;
		} else {
			$start_date = date("Y-m-d", strtotime($timesheet->start_date));
			$clientid = $timesheet->ClientId;
		}

		if (date('w', strtotime($start_date)) !=  $options['week_starts']) {
			echo "<div if='message' class='update-nag'><p>This time sheet does not start on a {$this->int_to_weekday($options['week_starts'], 0)}. Please change the date to a {$this->int_to_weekday($options['week_starts'], 0)}.</p></div>";
		}

		echo "<table><tr><td>Week Start Date:</td><td><div class='form-row show-inputbtns'><input type='date' name='start_date' value='{$start_date}'{$disable_object} data-date-inline-picker='false' data-date-open-on-focus='true' onChange='setupDates()' /></div></td></tr>";

		if (!$timesheet->ProjectId) {
			$ProjectId = -1;
		} else {
			$ProjectId = $timesheet->ProjectId;
		}

		$js_projects = $common->draw_clients_and_projects($clientid, $ProjectId, $timesheet_id_int, $disable_object, $timesheet, $clientList);

		if ($timesheet->notes) {
			echo "<tr><td>Project Notes:</td><td><textarea disabled>{$common->esc_textarea($timesheet->notes)}</textarea></td></tr>";
		}
		echo "</table>";
		echo "<table><tr align='center'><td></td><td>{$this->int_to_weekday($options['week_starts'], 0)}</td><td>{$this->int_to_weekday($options['week_starts'], 1)}</td><td>{$this->int_to_weekday($options['week_starts'], 2)}</td><td>{$this->int_to_weekday($options['week_starts'], 3)}</td><td>{$this->int_to_weekday($options['week_starts'], 4)}</td><td>{$this->int_to_weekday($options['week_starts'], 5)}</td><td>{$this->int_to_weekday($options['week_starts'], 6)}</td></tr>";
		echo "<tr align='center'><td></td><td><input type='text' name='monday_date' value='' size='5' disabled></td>
<td><input type='text' name='tuesday_date' value='' size='5' disabled></td>
<td><input type='text' name='wednesday_date' value='' size='5' disabled></td>
<td><input type='text' name='thursday_date' value='' size='5' disabled></td>
<td><input type='text' name='friday_date' value='' size='5' disabled></td>
<td><input type='text' name='saturday_date' value='' size='5' disabled></td>
<td><input type='text' name='sunday_date' value='' size='5' disabled></td></tr>";
		echo "<tr><td>Hours</td><td><input type='text' name='monday_hours' size='5' maxlength='5' value='{$timesheet->monday_hours}'></td>";
		echo "<td><input type='text' name='tuesday_hours' size='5' maxlength='5' value='{$timesheet->tuesday_hours}'></td>";
		echo "<td><input type='text' name='wednesday_hours' size='5' maxlength='5' value='{$timesheet->wednesday_hours}'></td>";
		echo "<td><input type='text' name='thursday_hours' size='5' maxlength='5' value='{$timesheet->thursday_hours}'></td>";
		echo "<td><input type='text' name='friday_hours' size='5' maxlength='5' value='{$timesheet->friday_hours}'></td>";
		echo "<td><input type='text' name='saturday_hours' size='5' maxlength='5' value='{$timesheet->saturday_hours}'></td>";
		echo "<td><input type='text' name='sunday_hours' size='5' maxlength='5' value='{$timesheet->sunday_hours}'></td></tr>
		</table>";

		if ($settings->user_can_enter_notes($timesheet_user_id, 'display_notes_toggle', 'display_notes_list')==1) {
			echo "Daily Notes";
			echo "<table><tr><td>{$this->int_to_weekday($options['week_starts'], 0)}</td><td><textarea name='monday_desc' cols='40' rows='2'>{$common->esc_textarea($timesheet->monday_desc)}</textarea></td></tr>";
			echo "<tr><td>{$this->int_to_weekday($options['week_starts'], 1)}</td><td><textarea name='tuesday_desc' cols='40' rows='2'>{$common->esc_textarea($timesheet->tuesday_desc)}</textarea></td></tr>";
			echo "<tr><td>{$this->int_to_weekday($options['week_starts'], 2)}</td><td><textarea name='wednesday_desc' cols='40' rows='2'>{$common->esc_textarea($timesheet->wednesday_desc)}</textarea></td></tr>";
			echo "<tr><td>{$this->int_to_weekday($options['week_starts'], 3)}</td><td><textarea name='thursday_desc' cols='40' rows='2'>{$common->esc_textarea($timesheet->thursday_desc)}</textarea></td></tr>";
			echo "<tr><td>{$this->int_to_weekday($options['week_starts'], 4)}</td><td><textarea name='friday_desc' cols='40' rows='2'>{$common->esc_textarea($timesheet->friday_desc)}</textarea></td></tr>";
			echo "<tr><td>{$this->int_to_weekday($options['week_starts'], 5)}</td><td><textarea name='saturday_desc' cols='40' rows='2'>{$common->esc_textarea($timesheet->saturday_desc)}</textarea></td></tr>";
			echo "<tr><td>{$this->int_to_weekday($options['week_starts'], 6)}</td><td><textarea name='sunday_desc' cols='40' rows='2'>{$common->esc_textarea($timesheet->sunday_desc)}</textarea></td></tr></table>";
		}

		if ($settings->user_can_enter_notes($timesheet_user_id, 'display_expenses_toggle', 'display_expenses_list')==1) {
			echo "Additional Information";
			echo "<table border='1' cellspacing='0'><tr><td>
				<select name='isPerDiem' onChange='enablePerDiem()'>
					<option value='1'{$common->is_match($timesheet->isPerDiem, '1', ' selected')}>Days of Per Diem</option>
					<option value='0'{$common->is_match($timesheet->isPerDiem, '0', ' selected')}>Actual Food Costs</option>
				</select><br>
			</td><td>Per Diem City</td><td>Flight/Train Costs</td><td>Hotel Charges</td><td>Rental Car Charges</td><td>Tolls</td><td>Mileage<br>(Miles Driven)</td><td>Other Expenses</td></tr>";
			echo "<tr><td align='center'><input type='text' name='per_diem_days' size='8' maxlength='9' value='{$timesheet->per_diem_days}'></td>";
			echo "<td align='center'><input type='text' name='perdiem_city' size='15' value='{$common->clean_from_db($timesheet->perdiem_city)}'></td>";
			echo "<td align='center'><input type='text' name='flight_cost' size='8' maxlength='9' value='{$timesheet->flight_cost}'></td>";
			echo "<td align='center'><input type='text' name='hotel_charges' size='8' maxlength='9' value='{$timesheet->hotel_charges}'></td>";
			echo "<td align='center'><input type='text' name='rental_car_charges' size='8' maxlength='9' value='{$timesheet->rental_car_charges}'></td>";
			echo "<td align='center'><input type='text' name='tolls' size='8' maxlength='9'  value='{$timesheet->tolls}'></td>";
			echo "<td align='center'><input type='text' name='mileage' size='8' maxlength='9' value='{$timesheet->mileage}'></td>";
			echo "<td align='center'><input type='text' name='other_expenses' size='8' maxlength='9' value='{$timesheet->other_expenses}'></td></tr>";
			echo "<tr><td valign='top'>Other Expense Notes:</td><td colspan='7'><textarea name='other_expenses_notes' cols='50' rows='8'>{$common->esc_textarea($timesheet->other_expenses_notes)}</textarea></td></tr></table>";
		}

		echo "<table border='0' cellpadding='0' cellspacing='0'><tr><td>Week Complete:</td><td><input type='checkbox' name='week_complete' value='1'";
		if ($timesheet->week_complete=='1') {
			echo " checked disabled";
		}
		echo "></td></tr>";
		if (!$options['remove_embargo']) {
			echo "<tr><td>Project Complete:</td><td><input type='checkbox' name='project_complete' value='1'{$disable_object}";
			if ($timesheet->project_complete=='1') {
				echo " checked";
			}
			echo "> (If this is available, all project workers must select this for client to be billed as client is only billed upon project completion.)</td></tr>";
		}
		echo "</table>";

		if ($timesheet->week_complete!='1') {
			if ($timesheet->user_id) {
				if ($timesheet->user_id == $user_id) {
					echo "<br><input type='submit' value='Save Timesheet' name='submit' class='button-primary'>";
				}
			} else {
				echo "<br><input type='submit' value='Save Timesheet' name='submit' class='button-primary'>";
			}
		}

		echo "<input type='hidden' name='action' value='save'>";
		echo "<input type='hidden' name='timesheet_id' value='{$timesheet->timesheet_id}'>";
		echo "<input type='hidden' name='may_embargo' value='0'>";
		echo "</form>";

		echo "


<script type='text/javascript'>

function setupDates() {
	var fake_date = new Date(timesheet.start_date.value);
	var js_start_date = new Date(fake_date.getFullYear(), fake_date.getMonth(), fake_date.getDate());

	//I have no idea why I have to do this, but it's wrong otherwise.
	//I think it's because javascript sucks.

	var n = js_start_date.getTimezoneOffset();
	
	if (n > 0) {
		js_start_date.setDate(js_start_date.getDate() + 1);
		//Need to figure this out, it's an issue with timezones. Until then, it'll just be broken for some people.
	}

	//I also have no idea why I need the +1 after getMonth(), but I do. I hate JavaScript.

	timesheet.monday_date.value = js_start_date.getMonth()+1 + '-' + js_start_date.getDate();
	js_start_date.setDate(js_start_date.getDate() + 1);
	timesheet.tuesday_date.value = js_start_date.getMonth()+1 + '-' + js_start_date.getDate();

	js_start_date.setDate(js_start_date.getDate() + 1);
	timesheet.wednesday_date.value = js_start_date.getMonth()+1 + '-' + js_start_date.getDate();

	js_start_date.setDate(js_start_date.getDate() + 1);
	timesheet.thursday_date.value = js_start_date.getMonth()+1 + '-' + js_start_date.getDate();

	js_start_date.setDate(js_start_date.getDate() + 1);
	timesheet.friday_date.value = js_start_date.getMonth()+1 + '-' + js_start_date.getDate();

	js_start_date.setDate(js_start_date.getDate() + 1);
	timesheet.saturday_date.value = js_start_date.getMonth()+1 + '-' + js_start_date.getDate();

	js_start_date.setDate(js_start_date.getDate() + 1);
	timesheet.sunday_date.value = js_start_date.getMonth()+1 + '-' + js_start_date.getDate();
}
";

$common->client_and_projects_javascript($js_projects, 'timesheet', 1, $timesheet);

echo "
function isProjectBilled() {
";
	if (!$options['remove_embargo']) {
		echo "
	//var clients = '';
	var clients = {$clientList};

	if (clients.indexOf(timesheet.ClientId.value) != -1) {
		timesheet.project_complete.disabled = false;
		timesheet.may_embargo.value = 1;
	} else {
		timesheet.project_complete.disabled = true;
		timesheet.may_embargo.value = 0;
		timesheet.project_complete.checked = false;
	}";
	}
	echo "
}

function isClientProjectBilled() {
	var projectlist = {$js_projects};

	var numberOfProjects = projectlist.length;
	//alert(numberOfProjects);
	for (var i = 0; i < numberOfProjects; i++) {
		project = projectlist[i];
		if (project['ProjectId'] == document.timesheet.elements['ProjectId'].value) { //timesheet.ProjectId.value) {
	//alert ('found project');
";
	if (!$options['remove_embargo']) {
	echo "		if (project['BillOnProjectCompletion'] == 1) {
	//alert('project is post billed');
				timesheet.project_complete.disabled = false;
				timesheet.may_embargo.value = 1;
			}";
	}
echo "
		}
	}
}

function ProjectChange() {
	isProjectBilled();
	isClientProjectBilled();
}

setupDates()
isProjectBilled()
";
	if ($options['hide_client_project'] && count($clients)==1 && count($projects)==1) {
		#Nothing to do here as we're hiding those fields
	} else {
		echo "resetProject()";
	}
echo "
isClientProjectBilled()
";
	if ($settings->user_can_enter_notes($timesheet_user_id, 'display_expenses_toggle', 'display_expenses_list') == 1) {
		echo "
function enablePerDiem() {
	if (timesheet.isPerDiem.value==1) {
		timesheet.perdiem_city.disabled=false;
	} else {
		timesheet.perdiem_city.disabled=true;
	}
}

enablePerDiem()";
	}
echo "
</script>";

	}

	function save_timesheet() {
		global $wpdb;
		$user_id = get_current_user_id();

		if ($user_id == 0) {
			echo "<div if='message' class='error'><p>You must be logged in to view this page.</p></div>";
			return;
		}

		if (!$_POST['ProjectId']) {
			echo '<div if="message" class="error"><p>No Project Was Selected. A project must be selected. If no projects are available contact your lead.</p></div>';
			return;
		}
		check_admin_referer( 'timesheet_entry_'. $_POST['timesheet_id'] );
		

		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$monday_hours = floatval($_POST['monday_hours']);
		$tuesday_hours = floatval($_POST['tuesday_hours']);
		$wednesday_hours = floatval($_POST['wednesday_hours']);
		$thursday_hours = floatval($_POST['thursday_hours']);
		$friday_hours = floatval($_POST['friday_hours']);
		$saturday_hours = floatval($_POST['saturday_hours']);
		$sunday_hours = floatval($_POST['sunday_hours']);

		if ($_POST['timesheet_id']) {

			$sql = "select user_id from {$wpdb->prefix}timesheet where timesheet_id = %d";	
			$parm = array(intval($_POST['timesheet_id']));	
			$timesheet = $db->get_row($sql, $parm);

			if ($timesheet) {
				//This is an update. Make sure the person updating is this person.
				if ($timesheet->user_id != $user_id) {
					echo '<div if="message" class="error"><p>You are attempting this submit a timesheet for a different user. This is not allowed.</p></div>';
					return;
				}
			} else {
				echo '<div if="message" class="error"><p>You appear to be updating a timesheet which does not exist.</p></div>';
				return;
			}
		}

		$may_embargo = ($_POST['may_embargo'] == 1 )? 1 : 0 ;
		$project_complete = ($_POST['project_complete'] == 1 )? 1 : 0 ;

		$EmbargoPendingProjectClose = $db->get_var("select BillOnProjectCompletion FROM {$wpdb->prefix}timesheet_recurring_invoices_monthly where client_id = %d", array(intval($_POST['ClientId'])));

		$EmbargoPendingProjectClose2 = $db->get_var("select BillOnProjectCompletion FROM {$wpdb->prefix}timesheet_client_projects where ProjectId = %d", array(intval($_POST['ProjectId'])));

		if ($EmbargoPendingProjectClose2==1) {
			$EmbargoPendingProjectClose=1;
		}

		if ($EmbargoPendingProjectClose<>1) {
			$EmbargoPendingProjectClose=0;
		}

		if ($project_complete<>1) {
			$project_complete=0;
		}

		if (!$monday_hours) {
			$monday_hours=0;
		}
		if (!$tuesday_hours) {
			$tuesday_hours=0;
		}
		if (!$wednesday_hours) {
			$wednesday_hours=0;
		}
		if (!$thursday_hours) {
			$thursday_hours=0;
		}
		if (!$friday_hours) {
			$friday_hours=0;
		}
		if (!$saturday_hours) {
			$saturday_hours=0;
		}
		if (!$sunday_hours) {
			$sunday_hours=0;
		}

		$total_hours = $monday_hours + $tuesday_hours + $wednesday_hours + $thursday_hours + $friday_hours + $saturday_hours + $sunday_hours;

		if ($_POST['week_complete']==1) {
			$week_complete=1;
		} else {
			$week_complete=0;
		}

		$start_date = date("Y-m-d", strtotime($_POST['start_date']));

		if ($_POST['timesheet_id']) { //UPDATE

			$timesheet_id = $_POST['timesheet_id'];

			$sql = "update {$wpdb->prefix}timesheet
					SET start_date=%s,
					ClientId=%d,
					ProjectId=%d,
					monday_hours=%s,
					tuesday_hours=%s,
					wednesday_hours=%s,
					thursday_hours=%s,
					friday_hours=%s,
					saturday_hours=%s,
					sunday_hours=%s,
					total_hours=%s,
					monday_desc=%s,
					tuesday_desc=%s,
					wednesday_desc=%s,
					thursday_desc=%s,
					friday_desc=%s,
					saturday_desc=%s,
					sunday_desc=%s,
					per_diem_days=%s,
					hotel_charges=%s,
					rental_car_charges=%s,
					tolls=%s,
					other_expenses=%s,
					other_expenses_notes=%s,
					week_complete=%d,
					mileage=%d,
					EmbargoPendingProjectClose=%d,
					project_complete=%d,
					flight_cost=%s,
					isPerDiem=%d,
					perdiem_city=%s
				WHERE timesheet_id=%d";

			$params=array($start_date, intval($_POST['ClientId']), intval($_POST['ProjectId']), floatval($monday_hours), floatval($tuesday_hours), floatval($wednesday_hours), floatval($thursday_hours), floatval($friday_hours), floatval($saturday_hours), floatval($sunday_hours), $total_hours, sanitize_textarea_field($_POST['monday_desc']), sanitize_textarea_field($_POST['tuesday_desc']), sanitize_textarea_field($_POST['wednesday_desc']), sanitize_textarea_field($_POST['thursday_desc']), sanitize_textarea_field($_POST['friday_desc']), sanitize_textarea_field($_POST['saturday_desc']), sanitize_textarea_field($_POST['sunday_desc']), floatval($_POST['per_diem_days']), floatval($_POST['hotel_charges']), floatval($_POST['rental_car_charges']), floatval($_POST['tolls']), floatval($_POST['other_expenses']), sanitize_textarea_field($_POST['other_expenses_notes']), $week_complete, floatval($_POST['mileage']), $EmbargoPendingProjectClose, $project_complete, floatval($_POST['flight_cost']), intval($_POST['isPerDiem']), sanitize_text_field($_POST['perdiem_city']), intval($_POST['timesheet_id']));

			$db->query($sql, $params);

			if ($week_complete==1) {
				$sql = "update {$wpdb->prefix}timesheet
				set marked_complete_by=$user_id,
					marked_complete_date = CURDATE()
				where timesheet_id=%d";
				$params=array(intval($_POST['timesheet_id']));

				$db->query($sql, $params);

				if ($EmbargoPendingProjectClose==0) {
					$this->email_on_submission(intval($_POST['timesheet_id']));
				}

				$sql = "update {$wpdb->prefix}timesheet_client_projects
					set HoursUsed=HoursUsed+$total_hours
					where ProjectId=%d";
				$params=array(intval($_POST['ProjectId']));

				$db->query($sql, $params);
			}
		
			echo '<div if="message" class="updated"><p>Timesheet updated.</p></div>';
		} else { //INSERT
			$sql = "insert into {$wpdb->prefix}timesheet (user_id, start_date, entered_date, ClientId, ProjectId, monday_hours, tuesday_hours, wednesday_hours, thursday_hours, friday_hours, saturday_hours, sunday_hours, total_hours, monday_desc, tuesday_desc, wednesday_desc, thursday_desc, friday_desc, saturday_desc, sunday_desc, per_diem_days, hotel_charges, rental_car_charges, tolls, other_expenses, other_expenses_notes, week_complete, approved, invoiced, mileage, EmbargoPendingProjectClose, project_complete, flight_cost, isPerDiem, perdiem_city) values (%d, %s, now(), %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, 0, 0, %d, %d, %d, %s, %d, %s)";

			$params=array($user_id, $start_date, intval($_POST['ClientId']), intval($_POST['ProjectId']), floatval($monday_hours), floatval($tuesday_hours), floatval($wednesday_hours), floatval($thursday_hours), floatval($friday_hours), floatval($saturday_hours), floatval($sunday_hours), $total_hours, sanitize_textarea_field($_POST['monday_desc']), sanitize_textarea_field($_POST['tuesday_desc']), sanitize_textarea_field($_POST['wednesday_desc']), sanitize_textarea_field($_POST['thursday_desc']), sanitize_textarea_field($_POST['friday_desc']), sanitize_textarea_field($_POST['saturday_desc']), sanitize_textarea_field($_POST['sunday_desc']), floatval($_POST['per_diem_days']), floatval($_POST['hotel_charges']), floatval($_POST['rental_car_charges']), floatval($_POST['tolls']), floatval($_POST['other_expenses']), sanitize_textarea_field($_POST['other_expenses_notes']), $week_complete, $_POST['mileage'], $EmbargoPendingProjectClose,  $project_complete, floatval($_POST['flight_cost']), floatval($_POST['isPerDiem']), sanitize_text_field($_POST['perdiem_city']));

			$db->query($sql, $params);

			$sql = "select max(timesheet_id) 
			from {$wpdb->prefix}timesheet
			where user_id={$user_id}";

			$timesheet_id = $db->get_var($sql);

			$this->check_overages($timesheet_id);

			if ($week_complete==1) {
				$sql = "update {$wpdb->prefix}timesheet
				set marked_complete_by=$user_id,
					marked_complete_date = CURDATE()
				where timesheet_id=$timesheet_id";

				$db->query($sql);
				if ($EmbargoPendingProjectClose==0) {
					$this->email_on_submission($timesheet_id);
				}

				$sql = "update {$wpdb->prefix}timesheet_client_projects
					set HoursUsed=HoursUsed+$total_hours
					where ProjectId=%d";
				$params=array(intval($_POST['ProjectId']));

				$db->query($sql, $params);
			}

			echo '<div if="message" class="updated"><p>Timesheet saved.</p></div>';

		}

		if ($may_embargo==1) {
			if ($project_complete==1 && $week_complete==1) {
				$sql = "update {$wpdb->prefix}timesheet
						SET EmbargoPendingProjectClose = 0
					WHERE ProjectId=%d AND EmbargoPendingProjectClose = 1";
				$params = array(intval($_POST['ProjectId']));
					$db->query($sql, $params);
				echo "<div id='message' class='updated highlight'>Your timesheets for this client have been sent for processing.</p></div>";
				$this->email_on_submission(intval($timesheet_id));
			}
		}

		$sql = "select * from {$wpdb->prefix}timesheet_recurring_invoices_monthly where client_id = %d and MonthlyHours <> 0";
		$parm = array(intval($_POST['ClientId']));
		$client = $db->get_row($sql, $parm);

		if ($client) {
			$sql = "select sum(total_hours) total_hours from {$wpdb->prefix}timesheet where ClientId = %d and YEAR(start_date) = YEAR(NOW()) AND MONTH(start_date)=MONTH(NOW())";
			$parm = array(intval($_POST['ClientId']));
			$month_hours = $db->get_var($sql, $parm);

			if ($month_hours > $client->MonthlyHours) {
				echo "<div id='message' class='updated highlight'><p>Client may be over their retainer hours for this month.  Please ensure that client approves of hours over their monthly retainer of <B>{$client->MonthlyHours}</b> hours if they do not have an active SOW in place.</p></div>";
			}
		}

		return $timesheet_id;
	}

}	
