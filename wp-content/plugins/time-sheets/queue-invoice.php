<?php
class time_sheets_queue_invoice {
	function show_invoicing_list() {
		global $wpdb;
		$db = new time_sheets_db();
		$main = new time_sheets_main();
		$queue_approval = new time_sheets_queue_approval();
		$approver = $queue_approval->employee_approver_check();
		$common = new time_sheets_common();
		$user_id = get_current_user_id();
		$options = get_option('time_sheets');
		
		$timesheets = $this->return_invoicing_list();

		$allow_show_record_invoicing = get_user_option('time_sheets_invoicing_allow_show_record_invoicing', $user_id);

		if ($_GET['show_retainers']) {
			$retainers_parm = "&show_retainers=checked";
		}
		if ($_GET['hide_nonretainers']) {
			$retainers_parm = $retainers_parm . "&hide_nonretainers=checked";
		}

		if ($timesheets) {

			echo "<form name='approve_timesheets' method='POST'>";

			if ($allow_show_record_invoicing != '') {
				echo "<input type='submit' name='submit2' value='Record Invoicing' class='button-primary'><BR><BR>";
			} else {
				if (count($timesheets) >= 15) {
					echo "<input type='submit' name='submit2' value='Record Invoicing' class='button-primary'><BR><BR>";
				}
			}

			echo "<table border='1' cellspacing='0'><tr><td>Timesheet</td>";
			if ($approver<>0) {
				echo "<td>Reject</td>";
			}
			echo "<td>View</td><td>Completed</td><td>Invoice Number</td><td>User</td><td>Start Date</td><td>First Billed Day</td><td>Client</td><td>Project Name</td><td>PO Number</td><td>Sales Person</td></tr>";
			foreach ($timesheets as $timesheet) {
				echo "<td align='center'>{$timesheet->timesheet_id}</td>";
				if ($approver<>0) {
					echo "<td align='center'><a href='./admin.php?page=invoice_timesheet&timesheet_id={$timesheet->timesheet_id}&action=reject{$retainers_parm}'><img src='". plugins_url( 'x.png' , __FILE__) ."' width='15' height='15'></a></td>";
				}
				$invoice_id = $timesheet->invoiceid;
				if ($invoice_id == 0) {
					$invoice_id = '';
				}
				echo "<td align='center'><a href='./admin.php?page=invoice_timesheet&timesheet_id={$timesheet->timesheet_id}&action=preinvoice' target='_blank'><img src='". plugins_url( 'view.png' , __FILE__) ."' width='15' height='15'></a></td><td align='center'><input type='checkbox' name='timesheet_{$timesheet->timesheet_id}' value=1></td><td align='center'><input type='text' name='timesheet_{$timesheet->timesheet_id}_invoice' value='{$invoice_id}' size='6' autocomplete='off'></td><td>{$common->replace(' ', '&nbsp;', $timesheet->display_name)}</td><td>{$common->f_date($timesheet->start_date)}</td><td>{$common->f_date($timesheet->first_billed_day)}</td><td>{$common->clean_from_db($timesheet->client_name)}</td><td>{$common->clean_from_db($timesheet->project_name)}</td><td>{$common->clean_from_db($timesheet->po_number)}</td><td>";

				if ($options['sales_override']=='project') {
					if ($timesheet->cps_display_name) {
						echo $timesheet->cps_display_name;
					} else {
						echo $timesheet->cs_display_name;
					}
				} else {
					if ($timesheet->cs_display_name) {
						echo $timesheet->cs_display_name;
					} else {
						echo $timesheet->cps_display_name;
					}
				}
				echo "</td></tr>";
			}
			echo "</table><br><input type='submit' name='submit' value='Record Invoicing' class='button-primary'><input type='hidden' name='page' value='invoice_timesheet'>";
			if ($_GET['show_retainers']) {
				echo "<input type='hidden' name='show_retainers' value='{$_GET['show_retainers']}'>";
			}
			if ($_GET['hide_nonretainers']) {
				echo "<input type='hidden' name='hide_nonretainers' value='{$_GET['hide_nonretainers']}'>";
			}

			wp_nonce_field( 'timesheet_invoice_processing' );

			echo "<input type='hidden' name='action' value='approve'></form>";
		} else {
			echo "No time sheets to invoice.";
		}
	}

	function invoice_timesheet(){
		$entry = new time_sheets_entry();
		$common = new time_sheets_common();

		if ($_GET['action']=='preinvoice') {
			$entry->show_timesheet();
		} else {
			echo "<br><form name='show_filter'>";
			echo "<input type='hidden' name='page' value='invoice_timesheet'>";
			echo "<table>";
			echo "<tr><td><input type='checkbox' name='show_retainers' value='checked' {$common->esc_textarea($_GET['show_retainers'])}> Show Retainers</td></tr>";
			echo "<tr><td><input type='checkbox' name='hide_nonretainers' value='checked' {$common->esc_textarea($_GET['hide_nonretainers'])}> Hide Non-Retainers</td></tr>";

			echo "<tr><td><input type='submit' name='submit' value='Filter List' class='button-primary'></tr><td></table></form>";
			echo "<br><table border='0'><tr><td valign='top'>";
			$this->show_invoicing_list();
			echo "</td><td valign='top'>";
			$common->show_clients_on_retainer();
			echo "</td></tr>";
		}
	}

	function do_timesheet_rejectinvoicing() {
		global $wpdb;
		$db = new time_sheets_db();

		$options = get_option('time_sheets');
		if ($options['queue_order']=='parallel') {
			echo '<div if="message" class="update-nag"><p>This timesheet may be in the payroll queue and may need to be adjusted.</p></div><br>';
		}

			$sql = "update {$wpdb->prefix}timesheet
				set approved_by=NULL,
					approved_date = NULL,
					approved=0
				where timesheet_id=%d";
				$params=array(intval($_GET['timesheet_id']));

				$db->query($sql, $params);
		echo '<div if="message" class="updated"><p>Timesheet has been rejected.</p></div>';
	}

	function return_invoicing_list() {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$IsRetainer = "-1";
		if ($_GET['show_retainers']) {
			$IsRetainer = $IsRetainer . ", 1";
		}
		if (!$_GET['hide_nonretainers']) {
			$IsRetainer = $IsRetainer . ", 0";
		}

		$primary_sort_col = get_user_option('time_sheets_invoicing_primary_sort_col', $user_id);
		$primary_sort_order = get_user_option('time_sheets_invoicing_primary_sort_order', $user_id);
		$secondary_sort_col = get_user_option('time_sheets_invoicing_secondary_sort_col', $user_id);
		$secondary_sort_order = get_user_option('time_sheets_invoicing_secondary_sort_order', $user_id);

		if ($primary_sort_col=='') {
			$primary_sort_col = 'c.ClientName';
		}
		if ($primary_sort_order=='') {
			$primary_sort_order='asc';
		}
		if ($secondary_sort_col=='') {
			$secondary_sort_col = 't.start_date';
		}
		if ($secondary_sort_order=='') {
			$secondary_sort_order='asc';
		}

		$sql = "select t.timesheet_id, u.user_login, t.start_date, c.ClientName client_name, cp.ProjectName project_name, t.invoiceid, cp.notes, u.display_name, cp.po_number,
date_add(t.start_date, INTERVAL CASE WHEN monday_hours != 0 then 0 when tuesday_hours != 0 then 1 when wednesday_hours != 0 then 2 when thursday_hours != 0 then 3 when friday_hours != 0 then 4 when saturday_hours != 0 then 5 when sunday_hours != 0 then 6 else 0 end DAY) first_billed_day, cs.display_name cs_display_name, cps.display_name cps_display_name
		from {$wpdb->prefix}timesheet t
		JOIN {$wpdb->prefix}timesheet_clients c ON t.ClientId = c.ClientId
		JOIN {$wpdb->prefix}timesheet_client_projects cp ON t.ProjectId = cp.ProjectId
		left outer join {$wpdb->users} cs on c.sales_person_id = cs.ID
		left outer join {$wpdb->users} cps on cp.sales_person_id = cps.ID
		join {$wpdb->users} u on t.user_id = u.ID
		where t.invoiced = 0 
			and approved = 1
			and cp.ProjectId in (select ProjectId from {$wpdb->prefix}timesheet_client_projects where IsRetainer in ({$IsRetainer})) 
		order by {$primary_sort_col} {$primary_sort_order}, {$secondary_sort_col} {$secondary_sort_order} ";

		$timesheets = $db->get_results($sql);

		return $timesheets;
	}

	function should_be_payrolled($timesheet_id) {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$options = get_option('time_sheets');

		$sql = "select * from {$wpdb->prefix}timesheet where timesheet_id = {$timesheet_id}";
		$timesheet = $db->get_row($sql);


		$sql = "select * from {$wpdb->prefix}timesheet_employee_always_to_payroll where user_id = {$timesheet->user_id}";
		$always_payrol = $db->get_row($sql);

		$payrolled = 1; //Default payrolled to 1, reset to 0 if needed

		if ($always_payrol) {
			$payrolled = 0;
		}

		if ($options['mileage'] && $timesheet->mileage!=0) {
			$payrolled = 0;
		}
		if ($options['per_diem'] && $timesheet->per_diem_days != 0) {
			$payrolled = 0;
		}
		if ($options['flight_cost'] && $timesheet->flight_cost != 0) {
			$payrolled = 0;
		}
		if ($options['hotel'] && $timesheet->hotel_costs != 0) {
			$payrolled = 0;
		}
		if ($options['rental_car'] && $timesheet->rental_car_costs!=0) {
			$payrolled = 0;
		}
		if ($options['tolls'] && $timesheet->tolls!=0) {
			$payrolled = 0;
		}
		if ($options['other_expenses'] && $timesheet->other_expenses!=0) {
			$payrolled = 0;
		}

		return $payrolled;
	}

	function do_timesheet_invoicing_one_timesheet($timesheet_id, $invoiced, $invoiced_by, $invoice_id) {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$options = get_option('time_sheets');


		if ($options['queue_order']!='parallel') {
			$payrolled = $this->should_be_payrolled($timesheet_id);
		
			$sql = "update {$wpdb->prefix}timesheet
			set payrolled = {$payrolled}
			where timesheet_id = {$timesheet_id}";
		}
		
		$sql = "update {$wpdb->prefix}timesheet
		set invoiced= {$invoiced},
			invoiced_by = {$user_id},
			invoiced_date = CURDATE(),
			invoiceid = {$invoice_id}
		where timesheet_id = {$timesheet_id}";

		$db->query($sql);
	}

	function do_timesheet_invoicing(){
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$options = get_option('time_sheets');

		check_admin_referer( 'timesheet_invoice_processing' );

		$timesheets = $this->return_invoicing_list();

		foreach ($timesheets as $timesheet) {
			$valuename = "timesheet_{$timesheet->timesheet_id}_invoice";
			$invoiceid = intval($_POST[$valuename]);

			$valuename = "timesheet_{$timesheet->timesheet_id}";

			if ($_POST[$valuename] == 1) {
				$invoiced = 1;
			} else {
				$invoiced = 0;
			}

			$this->do_timesheet_invoicing_one_timesheet($timesheet->timesheet_id, $invoiced, $user_id, $invoiceid);
		}



		echo '<div if="message" class="updated"><p>Timesheets have been updated.</p></div>';
	}

	function count_pending_invoice() {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "select sum(case when tcp.IsRetainer=1 then 1 else 0 end) Retainer, sum(case when tcp.IsRetainer=0 then 1 else 0 end) NotRetainer
			from {$wpdb->prefix}timesheet t
			join {$wpdb->prefix}timesheet_client_projects tcp on t.ProjectId = tcp.ProjectId
			where t.approved = 1 and t.invoiced = 0";

		$count = $db->get_row($sql);
		
		return $count;
	}

	function count_users_open_invoice() {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "select count(*) 
			from {$wpdb->prefix}timesheet t
			join {$wpdb->prefix}timesheet_client_projects tcp on t.ProjectId = tcp.ProjectId
			where t.week_complete = 0 and t.user_id = {$user_id}";

		$count = $db->get_var($sql);
		
		return $count;
	}

	function employee_invoicer_check () {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "select count(*) 
			from {$wpdb->prefix}timesheet_invoicers t
			where user_id = {$user_id}";

		$count = $db->get_var($sql);
		
		return $count;
	}

}
