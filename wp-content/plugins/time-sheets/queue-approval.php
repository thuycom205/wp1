<?php
class time_sheets_queue_approval {
	function count_pending_approval() {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "select sum(case when (EmbargoPendingProjectClose = 0 or EmbargoPendingProjectClose IS NULL) and tcp.IsRetainer=1 then 1 else 0 end) Retainer, sum(case when (EmbargoPendingProjectClose = 0 or EmbargoPendingProjectClose IS NULL) and tcp.IsRetainer=0 then 1 else 0 end) NotRetainer,
		sum(case when (EmbargoPendingProjectClose = 1) then 1 else 0 end) Embargoed
		from {$wpdb->prefix}timesheet t
		join {$wpdb->prefix}timesheet_approvers_approvies aa ON aa.approvie_user_id = t.user_id
		join {$wpdb->prefix}timesheet_approvers ta on aa.approver_user_id = ta.user_id 
			and (ta.user_id = {$user_id} OR (ta.backup_user_id = {$user_id} and ta.backup_expires_on > now()))
		join {$wpdb->prefix}timesheet_client_projects tcp on t.ProjectId = tcp.ProjectId
		where approved = 0 and week_complete = 1";

		$count = $db->get_row($sql);
		
		return $count;
	}

	function employee_approver_check($includeBackups=true) {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		if ($includeBackups=='true') {
			$sql = "select count(*) from {$wpdb->prefix}timesheet_approvers where (user_id = {$user_id}) OR (backup_user_id = {$user_id} and backup_expires_on > now())";
		} else {
			$sql = "select count(*) from {$wpdb->prefix}timesheet_approvers where user_id = {$user_id}";
		}

		$count = $db->get_var($sql);
		
		return $count;
	}


	function do_timesheet_approval_processing() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();

		check_admin_referer( 'timesheet_approval_processing');

		$timesheets = $this->return_approval_list();

		if ($timesheets) {
			foreach ($timesheets as $timesheet) {
				$objname = "sheet_{$timesheet->timesheet_id}";

				$value = $_GET[$objname];

				if ($value == "approve") {
					$this->do_timesheet_approval($timesheet->timesheet_id);
				}
				if ($value == "reject") {
					$this->do_timesheet_rejectapproval($timesheet->timesheet_id);
				}
			}
		}
		echo "<div if='message' class='updated'><p>Timesheet(s) updated.</p></div>";
	}

	function email_on_approval($timesheet_id) {
		$options = get_option('time_sheets');
		$common = new time_sheets_common();

		if (!$options['enable_email']) {
			return;
		}

		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select display_name 
			from {$wpdb->prefix}timesheet t
			join {$wpdb->users} u on u.ID = t.user_id
			where t.timesheet_id = %d";
		$params = array($timesheet_id);
		$user_login = $db->get_var($sql, $params);

		$sql = "select user_email
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet_invoicers a on u.ID = a.user_id";

		$users = $db->get_results($sql);

		$subject = "There are time sheet(s)  pending invoicing.";
		$body = "A time sheet has been entered by {$user_login} which is approved and needs to be invoiced to the client..

It can be viewed and invoiced from the <a href='http://$_SERVER[HTTP_HOST]/wp-admin/admin.php?page=invoice_timesheet'>invoicing menu</a>.";

		foreach ($users as $user) {
			$common->send_email ($user->user_email, $subject, $body);
		}
	}

	function do_timesheet_split() {
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "SELECT GROUP_CONCAT(distinct COLUMN_NAME) columns 
			FROM `INFORMATION_SCHEMA`.`COLUMNS` 
			WHERE `TABLE_NAME`='{$wpdb->prefix}timesheet'
				and COLUMN_NAME not in ('timesheet_id', 'monday_hours', 'tuesday_hours', 'wednesday_hours',
					'thursday_hours', 'friday_hours', 'saturday_hours', 'sunday_hours', 'total_hours',
					'monday_desc', 'tuesday_desc', 'wednesday_desc', 'thursday_desc', 
					'friday_desc', 'saturday_desc', 'sunday_desc', 'per_diem_days', 'hotel_charges',
					'rental_car_charges', 'tolls', 'other_expenses', 'other_expenses_notes', 'mileage', 
					'flight_cost')";
		$cols = $db->get_var($sql);

		if ($_GET['new_start_date'] ==1) {
			$cols = "{$cols}, tuesday_hours, wednesday_hours, thursday_hours, friday_hours, saturday_hours, sunday_hours, tuesday_desc, wednesday_desc, thursday_desc, friday_desc, saturday_desc, sunday_desc";
			$update_cols = "tuesday_hours = 0, wednesday_hours=0, thursday_hours=0, friday_hours=0, saturday_hours=0, sunday_hours=0, tuesday_desc='', wednesday_desc='', thursday_desc='', friday_desc='', saturday_desc='', sunday_desc=''";
		}

		if ($_GET['new_start_date'] ==2) {
			$cols = "{$cols}, wednesday_hours, thursday_hours, friday_hours, saturday_hours, sunday_hours, wednesday_desc, thursday_desc, friday_desc, saturday_desc, sunday_desc";
			$update_cols = "wednesday_hours=0, thursday_hours=0, friday_hours=0, saturday_hours=0, sunday_hours=0, wednesday_desc='', thursday_desc='', friday_desc='', saturday_desc='', sunday_desc=''";
		}

		if ($_GET['new_start_date'] ==3) {
			$cols = "{$cols}, thursday_hours, friday_hours, saturday_hours, sunday_hours, thursday_desc, friday_desc, saturday_desc, sunday_desc";
			$update_cols = "thursday_hours=0, friday_hours=0, saturday_hours=0, sunday_hours=0, thursday_desc='', friday_desc='', saturday_desc='', sunday_desc=''";
		}

		if ($_GET['new_start_date'] ==4) {
			$cols = "{$cols}, friday_hours, saturday_hours, sunday_hours, friday_desc, saturday_desc, sunday_desc";
			$update_cols = "friday_hours=0, saturday_hours=0, sunday_hours=0, friday_desc='', saturday_desc='', sunday_desc=''";
		}

		if ($_GET['new_start_date'] ==5) {
			$cols = "{$cols}, saturday_hours, sunday_hours, saturday_desc, sunday_desc";
			$update_cols = "saturday_hours=0, sunday_hours=0, saturday_desc='', sunday_desc=''";
		}

		if ($_GET['new_start_date'] ==6) {
			$cols = "{$cols}, sunday_hours, sunday_desc";
			$update_cols = "sunday_hours=0, sunday_desc=''";
		}

		$sql = "INSERT INTO {$wpdb->prefix}timesheet
			({$cols})
			SELECT {$cols} FROM {$wpdb->prefix}timesheet WHERE timesheet_id = %d";
		$parms = array(intval($_GET['timesheet_id']));
		$db->query($sql, $parms);

		$sql = "UPDATE {$wpdb->prefix}timesheet set {$update_cols} WHERE timesheet_id = %d";
		$db->query($sql, $parms);

		echo '<div if="message" class="updated"><p>Timesheet has been split.</p></div>';

	}

	function split_timesheet(){
		global $wpdb;
		$db = new time_sheets_db();
		$common = new time_sheets_common();
		
		$sql = "select start_date from {$wpdb->prefix}timesheet where timesheet_id = %d";
		$parms = array(intval($_GET['timesheet_id']));

		$start_date = $db->get_var($sql, $parms);
		echo "<form name='splittimesheet' method='get'><br>Date selected below and later will be moved to a new timesheet.  Expenses will be left on the origional timesheet.<br>
		<input type='hidden' name='timesheet_id' value='{$common->intval($_GET['timesheet_id'])}'>
		<input type='hidden' name='page' value='approve_timesheet'>
		<input type='hidden' name='action' value='split2'>
		<input type='hidden' name='subaction' value='finish'>
		<table><tr><td>Select date to split timesheet:</td>
			<td><select name='new_start_date'>";

		$i = 1;
		$n = "";

		while ($i <= 7)
		{
			echo "<option value='{$i}'>{$common->add_days_to_date($start_date, $i, $n)}</option>";
			$i++;
		}

		echo "</select></td></tr><tr><td colspan='2'><input type='submit' name='submit' value='Split Timesheet' class='button-primary'></td></tr></table></form>";
	}

	function approve_timesheet(){
		$common = new time_sheets_common();
		$entry = new time_sheets_entry();
		$options = get_option('time_sheets');

		if ($_GET['action']=='preapprove') {
			$entry->show_timesheet();
		} elseif ($_GET['action']=='split') {
			$this->split_timesheet();
		} else {
			echo "<br><form name='show_filter'>";
			echo "<input type='hidden' name='page' value='approve_timesheet'>";
			echo "<table>";
			if (!$options['remove_embargo']) {
				echo "<tr><td><input type='checkbox' name='show_embargoed' value='checked' {$common->esc_textarea($_GET['show_embargoed'])}> Show Embargoed Entries</td><tr>";
			}
			echo "<tr><td><input type='checkbox' name='show_retainers' value='checked' {$common->esc_textarea($_GET['show_retainers'])}> Show Retainers</td></tr>";
			echo "<tr><td><input type='checkbox' name='hide_nonretainers' value='checked' {$common->esc_textarea($_GET['hide_nonretainers'])}> Hide Non-Retainers</td></tr>";

			echo "<tr><td><input type='submit' name='submit' value='Filter List' class='button-primary'></tr><td></table></form>";
			echo "<br><table border='0'><tr><td valign='top'>";
			$this->show_approval_list();
			echo "</td><td valign='top'>";
			$common->show_clients_on_retainer();
			echo "</td></tr>";
		}
	}
	

	function do_timesheet_rejectapproval($timesheet_id) {
		global $wpdb;
		$db = new time_sheets_db();
		$common = new time_sheets_common();

			$sql = "update {$wpdb->prefix}timesheet
				set marked_complete_by=NULL,
					marked_complete_date = NULL,
					week_complete=0
				where timesheet_id=%d";
				$params=array($timesheet_id);

				$db->query($sql, $params);

		$subject = "Your timesheet(s) need to be reviewed and updated";
		$body = "Timesheet {$timesheet_id} needs to be reviewed and updated";

		$sql = "select user_email
			from {$wpdb->users} u
			join {$wpdb->prefix}timesheet t ON u.ID = t.user_id
			where t.timesheet_id = %d";

		$email = $db->get_var($sql, $params);

		$common->send_email ($email, $subject, $body);
	}


	function return_approval_list() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$options = get_option('time_sheets');

		$sql = "select t.timesheet_id, u.user_login, t.start_date, c.ClientName client_name, cp.ProjectName project_name, EmbargoPendingProjectClose, cp.notes, u.display_name,
date_add(t.start_date, INTERVAL CASE WHEN monday_hours != 0 then 0 when tuesday_hours != 0 then 1 when wednesday_hours != 0 then 2 when thursday_hours != 0 then 3 when friday_hours != 0 then 4 when saturday_hours != 0 then 5 when sunday_hours != 0 then 6 else 0 end DAY) first_billed_day
		from {$wpdb->prefix}timesheet t
		JOIN {$wpdb->prefix}timesheet_clients c ON t.ClientId = c.ClientId
		JOIN {$wpdb->prefix}timesheet_client_projects cp ON t.ProjectId = cp.ProjectId
		join {$wpdb->users} u on t.user_id = u.ID
		join {$wpdb->prefix}timesheet_approvers_approvies aa ON aa.approvie_user_id = u.ID
		join {$wpdb->prefix}timesheet_approvers ta on aa.approver_user_id = ta.user_id 
			and (ta.user_id = {$user_id} OR (ta.backup_user_id = {$user_id} and ta.backup_expires_on > now()))
		where t.week_complete = 1 and approved = 0 ";

		if (!$_GET['show_embargoed']) {
			$sql = "$sql and (EmbargoPendingProjectClose = 0 or EmbargoPendingProjectClose IS NULL)";
		}

		$IsRetainer = "-1";
		if ($_GET['show_retainers']) {
			$IsRetainer = $IsRetainer . ", 1";
		}
		if (!$_GET['hide_nonretainers']) {
			$IsRetainer = $IsRetainer . ", 0";
		}
			$sql = $sql . " and cp.ProjectId in (select ProjectId from {$wpdb->prefix}timesheet_client_projects where IsRetainer in ({$IsRetainer})) order by t.start_date";

		$timesheets = $db->get_results($sql);

		return $timesheets;
	}


	function do_timesheet_approval($timesheet_id){
		global $wpdb;
		$db = new time_sheets_db();
		$options = get_option('time_sheets');
		$user_id = get_current_user_id();
		$entry = new time_sheets_entry();
		$invoice = new time_sheets_queue_invoice();

		$sql = "select p.*
			from {$wpdb->prefix}timesheet_client_projects p
			join {$wpdb->prefix}timesheet t on t.ProjectId = p.ProjectId
			where t.timesheet_id = %d";
		$params = array($timesheet_id);
		$project = $db->get_row($sql, $params);

		$sql = "update {$wpdb->prefix}timesheet
			set approved = 1,
				approved_by = $user_id,
				approved_date = CURDATE()
			where timesheet_id = %d";
		$params = array($timesheet_id);
		$db->query($sql, $params);

		if ($project->flat_rate==1) {
			$invoice->do_timesheet_invoicing_one_timesheet($timesheet_id, 1, $user_id, 0);
		}

		if ($options['queue_order']=='parallel') {
			$payrolled = $invoice->should_be_payrolled($timesheet_id);
			$sql = "update {$wpdb->prefix}timesheet
					set payrolled = {$payrolled}
				where timesheet_id = %d";
			$params = array($timesheet_id);
			$db->query($sql, $params);
		}

		$this->email_on_approval($timesheet_id);

		$entry->check_overages($timesheet_id);
	}

	function show_approval_list() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$common = new time_sheets_common();

		$timesheets = $this->return_approval_list();		

		echo "<script type='text/javascript'>

			function approve_all() {
				";

				foreach ($timesheets as $timesheet) {
					echo "document.getElementById('approve_{$timesheet->timesheet_id}').checked = true;";
				}
				echo "
			}
			
			function reject_all() {
				";

				foreach ($timesheets as $timesheet) {
					echo "document.getElementById('reject_{$timesheet->timesheet_id}').checked = true;";
				}
				echo "
			}
			
			function hold_all() {
				";

				foreach ($timesheets as $timesheet) {
					echo "document.getElementById('hold_{$timesheet->timesheet_id}').checked = true;";
				}
				echo "
			}
		</script>
		";
		
		if ($_GET['show_embargoed']) {
			 $embargod_parm = "&show_embargoed=1";
		}
		if ($_GET['show_retainers']) {
			$retainers_parm = "&show_retainers=checked";
		}
		if ($_GET['hide_nonretainers']) {
			$retainers_parm = $retainers_parm . "&hide_nonretainers=checked";
		}

		if ($timesheets) {
			echo "<form name='approve_timesheets'>";
			if (count($timesheets) >= 15) {
				echo "<input type='submit' name='submit2' value='Record Invoicing' class='button-primary'><BR><BR>";
			}

			echo "<table border='1' cellspacing='0' width='100%'><tr><td>Timesheet</td><td>View</td><td><a href='#' onclick='approve_all()'>Approve</a></td><td><a href='#' onclick='reject_all()'>Reject</a></td><td><a href='#' onclick='hold_all()'>Hold</a></td><td>Split</td>";
			if ($_GET['show_embargoed']) {
				echo "<td>Embargoed</td>";
			}
			echo "<td>User</td><td>Start Date</td><td>First Billed Date</td><td>Client</td><td>Project Name</td></tr>";
			foreach ($timesheets as $timesheet) {
				echo "<td align='center'>{$timesheet->timesheet_id}</td>
					<td><a href='./admin.php?page=approve_timesheet&timesheet_id={$timesheet->timesheet_id}&action=preapprove' align='center'><img src='". plugins_url( 'view.png' , __FILE__) ."' width='15' height='15'></a></td>
					<td align='center'><input type='radio' id='approve_{$timesheet->timesheet_id}' name='sheet_{$timesheet->timesheet_id}' value='approve'></td><td align='center'><input type='radio' id='reject_{$timesheet->timesheet_id}' name='sheet_{$timesheet->timesheet_id}' value='reject'></td>
					<td align='center'><input type='radio' id='hold_{$timesheet->timesheet_id}' name='sheet_{$timesheet->timesheet_id}' value='hold' checked></td>
					<td align='center'><a href='./admin.php?page=approve_timesheet&timesheet_id={$timesheet->timesheet_id}&action=split{$embargod_parm}{$retainers_parm}'><img src='". plugins_url( 'split.png' , __FILE__) ."' width='15' height='15'></a></td>";
		if ($_GET['show_embargoed']) {
			echo "<td align='center'>";
			if ($timesheet->EmbargoPendingProjectClose==1) {
				echo "<img src='". plugins_url( 'check.png' , __FILE__) ."' width='15' height='15'>";
			} else {
				echo "<img src='". plugins_url( 'x.png' , __FILE__) ."' width='15' height='15'>";
			}
			echo "</td>";
		}
		echo "<td>{$common->replace(' ', '&nbsp;', $timesheet->display_name)}</td><td>{$common->f_date($timesheet->start_date)}</td><td>{$common->f_date($timesheet->first_billed_day)}</td><td>{$common->clean_from_db($timesheet->client_name)}</td><td>{$common->clean_from_db($timesheet->project_name)}</td></tr>";
			}
			echo "</table><br><input type='submit' name='submit' value='Record Approvals' class='button-primary'><input type='hidden' name='page' value='approve_timesheet'>";
			if ($_GET['show_retainers']) {
				echo "<input type='hidden' name='show_retainers' value='{$common->esc_textarea($_GET['show_retainers'])}'>";
			}
			if ($_GET['hide_nonretainers']) {
				echo "<input type='hidden' name='hide_nonretainers' value='{$common->esc_textarea($_GET['hide_nonretainers'])}'>";
			}
			if ($_GET['show_embargoed']) {
				echo "<input type='hidden' name='show_embargoed' value='{$common->esc_textarea($_GET['show_embargoed'])}'>";	
			}

			wp_nonce_field( 'timesheet_approval_processing' );
			
			echo "<input type='hidden' name='action' value='approve'></form>";
		} else {
			echo "No time sheets to approve.";
		}
	}
}
