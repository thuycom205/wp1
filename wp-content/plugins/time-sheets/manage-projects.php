<?php
class time_sheets_manage_projects {
	function main() {
		$main = new time_sheets_main();
		$queue_invoice = new time_sheets_queue_invoice();
		$queue_approval = new time_sheets_queue_approval();

		$invoicer=$queue_invoice->employee_invoicer_check();
		$approver=$queue_approval->employee_approver_check('false');
		
		$client_managers = new time_sheets_client_managers();

		echo "<P><form method='GET'>
		<input type='hidden' value='timesheet_manage_clients' name='page'>";
		if (($client_managers->client_manager_check()==1) || ($approver==1)) {
			echo "<input type='submit' value='New Client' name='menu' class='button-primary'>&nbsp;
			<input type='submit' value='Edit Client' name='menu' class='button-primary'>&nbsp;";
		}
		if (($client_managers->client_manager_check()==1) || ($invoicer==1)) {
			echo "<input type='submit' value='Setup Monthlies' name='menu' class='button-primary'>&nbsp;";
		}
		if (($client_managers->client_manager_check()==1) || ($approver==1)) {
			echo "<input type='submit' value='New Project' name='menu' class='button-primary'>&nbsp;
			<input type='submit' value='Edit Project' name='menu' class='button-primary'>";
		}
		      echo "</form>";

		if ($_GET['menu']=='New Client') {
			$this->add_client();
		}
		if ($_GET['menu']=='Edit Client') {
			$this->add_client_users();
		}
		if ($_GET['menu']=='Setup Monthlies') {
			$this->setup_monthlies();
		}
		if ($_GET['menu']=='New Project') {
			$this->NewProject();
		}
		if ($_GET['menu']=='Edit Project') {
			$this->EditProject();
		}
		if ($_GET['menu']=='view_timesheets_for_project') {
			$this->view_timesheets_for_project();
		}
	}

	function view_timesheets_for_project(){
		$db = new time_sheets_db();
		global $wpdb;
		$user_id = get_current_user_id();

		$sql = "select t.timesheet_id, t.start_date, t.total_hours, invoiced, approved, EmbargoPendingProjectClose, u.display_name
			from {$wpdb->prefix}timesheet t
			join {$wpdb->users} u on t.user_id = u.ID
			where ProjectId = %d
			order by start_date, display_name";
		$parms = array(intval($_GET['ProjectId']));

		$timesheets = $db->get_results($sql, $parms);

		if ($timesheets) {
			echo "<p><table border='1' cellpadding='0' cellspacing='1'><tr><td>Timesheet ID</td><td>Week Starting</td><td>Employee</td><td>Hours Invoiced</td><td>Is Approved</td><td>Is Invoiced</td><td>Is Embargoed</td></tr>";
			$totalhours = 0;
			foreach ($timesheets as $timesheet) {
				if ($timesheet->invoiced=="1") {
					$is_invoiced=plugins_url( 'check.png' , __FILE__);
				} else {
					$is_invoiced=plugins_url( 'x.png' , __FILE__);
				}
				if ($timesheet->approved=="1") {
					$is_approved=plugins_url( 'check.png' , __FILE__);
				} else {
					$is_approved= plugins_url( 'x.png' , __FILE__) ;
				}
				if ($timesheet->EmbargoPendingProjectClose=="1") {
					$is_Embargoed= plugins_url( 'check.png' , __FILE__) ;
				} else {
					$is_Embargoed= plugins_url( 'x.png' , __FILE__);
				}
				$totalhours = $totalhours + $timesheet->total_hours;
				$start_date = date('Y-m-d', strtotime($timesheet->start_date));
				echo "<tr><td align='center'><a href='admin.php?page=enter_timesheet&timesheet_id={$timesheet->timesheet_id}'>{$timesheet->timesheet_id}</a></td>
				<td align='center'>{$start_date}</td>
				<td>{$timesheet->display_name}</td>
				<td align='center'>{$timesheet->total_hours}</td>
				<td align='center'><img src='{$is_approved}' width='15' height='15'></td>
				<td align='center'><img src='{$is_invoiced}' width='15' height='15'></td>
				<td align='center'><img src='{$is_Embargoed}' width='15' height='15'></td>
				</tr>";
			}
			echo "</table>
			<BR>
			Total Hours To Date: {$totalhours}";
		} else {
			echo "There are no timesheets for this project at this time.";
		}
	}

	function setup_monthlies() {
		$db = new time_sheets_db();
		global $wpdb;
		$user_id = get_current_user_id();
		$common = new time_sheets_common();

		if ($_POST['BillOnProjectCompletion']) {
			$BillOnProjectCompletion = 1;
		} else {
			$BillOnProjectCompletion = 0;
		}

		
		if ($_POST['sub_action']=='update_client') {
			$sql = "select * from {$wpdb->prefix}timesheet_recurring_invoices_monthly where client_id = %d";
			$parms = array($_POST['ClientId']);
			$client = $db->get_row($sql, $parms);

			if ($_POST['enabled']) {
				if ($client->client_id) {
					$sql = "update {$wpdb->prefix}timesheet_recurring_invoices_monthly
						SET MonthlyHours = %d, HourlyRate = %d, Notes = '%s', BillOnProjectCompletion = %d
						WHERE client_id = %d";
					$parm = array(intval($_POST['MonthlyHours']), $_POST['HourlyRate'], sanitize_text_field($_POST['notes']), $BillOnProjectCompletion, intval($_POST['ClientId']));

					$db->query($sql, $parm);
					echo '<div if="message" class="updated"><p>Client Updated.</p></div>';
				} else {
					$db->query("insert into {$wpdb->prefix}timesheet_recurring_invoices_monthly
						(client_id, MonthlyHours, HourlyRate, Notes, BillOnProjectCompletion )
					values (%d, %d, %d, '%s', %d)", array(intval($_POST['ClientId']), intval($_POST['MonthlyHours']), $_POST['HourlyRate'], sanitize_text_field($_POST['notes']), $BillOnProjectCompletion));
					echo '<div if="message" class="updated"><p>Client Added.</p></div>';
				}
			}
			if (!$_POST['enabled']) {
				$db->query("delete from {$wpdb->prefix}timesheet_recurring_invoices_monthly
					where client_id = %d", array(intval($_POST['ClientId'])));
				echo '<div if="message" class="updated"><p>Client Removed.</p></div>';
			}
		}

		echo "<form name='new_client' method='POST'>
			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='Setup Monthlies' name='menu'>";
		echo "<table border='0' cellpadding='3' cellspacing='3'><tr><td>Select Client:</td><td>";
		$clients = $db->get_results("select ClientName, tc.ClientId, im.client_id IsMonthly
					from {$wpdb->prefix}timesheet_clients tc
					left outer join {$wpdb->prefix}timesheet_recurring_invoices_monthly im on tc.clientid = im.client_id
					order by ClientName");
		if ($clients) {
			echo "<select name='ClientId'>";
			foreach ($clients as $client) {
				echo "<option value='{$client->ClientId}'";
				if ($_POST["ClientId"]==$client->ClientId) {
					echo " selected";
				}
				echo ">{$client->ClientName}</option>";
			}
			echo "</select>";
		}
		echo "</td></tr></table><input type='submit' value='Show Client Settings' name='submit' class='button-primary'>";
		echo "<input type='hidden' name='action' value='select_client'>";
		echo "</form>";
		if ($_POST['action']=='select_client') {
			$sql = "select * from {$wpdb->prefix}timesheet_recurring_invoices_monthly where client_id = %d";
			$parms = array(intval($_POST['ClientId']));
			$client = $db->get_row($sql, $parms);

			echo "<p><p><form name='update_client' method='POST'>
			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='Setup Monthlies' name='menu'>";
			echo "<table border='0'>";
			echo "<tr><td>Enabled:</td><td><input type='checkbox' name='enabled' value='yes'";
			if ($client->client_id) {
				echo " checked";
			}
			echo "></td></tr>";
			echo "<tr><td>Hours Included:</td><TD><input type='text' name='MonthlyHours' size='3' value='{$client->MonthlyHours}'></td></tr>";
			echo "<tr><td>Hourly Rate:</td><TD><input type='text' size='3' name='HourlyRate' value='{$client->HourlyRate}'></td></tr>";
			echo "<tr><td>Notes:</td><td><textarea rows='4' cols='20' name='notes'>{$client->Notes}</textarea></td></tr>";
			echo "<tr><td colspan='2'><input type='checkbox' value='true' name='BillOnProjectCompletion'";
				if ($client->BillOnProjectCompletion == 1) {
					echo " checked";
				}
			echo "> Bill On Project Completion</td></tr>";
			echo "</table>";
			echo "<input type='hidden' name='action' value='select_client'>";
			echo "<input type='hidden' name='sub_action' value='update_client'>";
			echo "<input type='hidden' name='ClientId' value='{$_POST['ClientId']}'>";
			echo "<input type='submit' value='Update Settings' name='submit' class='button-primary'>";
			echo "</form>";
		}
	}


	function add_client_users() {
		$db = new time_sheets_db();
		$common = new time_sheets_common();
		global $wpdb;
		$user_id = get_current_user_id();

		if ($_POST['ClientId'] && $_POST['action']=='update_client1') {
			$sql = "select ID
				from {$wpdb->users} u";
			$users = $db->get_results($sql);

			foreach ($users as $user) {
				$id = "user_{$user->ID}";

				if ($_POST[$id]) {
					$sql = "insert into {$wpdb->prefix}timesheet_clients_users
					(ClientId, user_id)
					values (%d, %d)";

					$db->query($sql, array(intval($_POST['ClientId']), $user->ID));
				} else {
					$sql = "delete from {$wpdb->prefix}timesheet_clients_users
					where ClientId = %d and user_id = %d";

					$db->query($sql, array(intval($_POST['ClientId']), $user->ID));
				}
			}

			$sql = "update {$wpdb->prefix}timesheet_clients set ClientName = %s, sales_person_id = %d where ClientId = %d";
			$parms = array(sanitize_text_field($_POST['ClientName']), intval($_POST['sales_person_id']), intval($_POST['ClientId']));

			$db->query($sql, $parms);

			echo '<div if="message" class="updated"><p>Client Updated.</p></div>';
		}

		echo "<form name='new_client' method='POST'>
			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='Edit Client' name='menu'>";
		echo "<table><tr><td>Client Name</td>";
		$clients = $db->get_results("select ClientName, tc.ClientId, tc.sales_person_id
					from {$wpdb->prefix}timesheet_clients tc
					order by ClientName");

		if ($clients) {
			echo "<td><select name='ClientId'>";
			foreach ($clients as $client) {
				echo "<option value='{$client->ClientId}'";
				if ($client->ClientId==$_POST['ClientId']) {
					echo " selected";
				}
				echo ">{$client->ClientName}</option>";
			}
			echo "</select>";
		}
		echo "</td></tr><tr><td colspan='2'><input type='submit' value='Select Client' name='submit' class='button-primary'></td></tr></table>";
		echo "<input type='hidden' name='action' value='update_client'>";
		echo "</form>";

		if ($_POST['ClientId']) {
			$sql = "select u.user_login, u.ID, tcu.user_id cu, u.display_name
				from {$wpdb->users} u
				left outer join {$wpdb->prefix}timesheet_clients_users tcu on u.id = tcu.user_id
				and tcu.ClientId = %d
				order by u.display_name";

			$users = $db->get_results($sql, array(intval($_POST['ClientId'])));

			$sql = "select * from {$wpdb->prefix}timesheet_clients where ClientId = %d";

			$client = $db->get_row($sql, array(intval($_POST['ClientId'])));

			echo "<form method='POST' name='new_post'>

			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='Edit Client' name='menu'>";
			echo "<BR><table><tr><td>Client Name:</td><td><input type='text' name='ClientName' value='{$client->ClientName}'></td></tr><tr><td>Sales Person</td><td>";
			$users = $common->return_employee_list();
			echo "<select name='sales_person_id'>
				<option value='-10'>No Sales Person</option>";
			foreach ($users as $user) {
				echo "<option value='{$user->id}'{$common->is_match($client->sales_person_id, $user->id, ' selected', 0)}>{$user->display_name}</option>";
			}
			echo "</select></td></tr>";
			echo "</table><br>
			<table border='1' cellspacing='0'><tr><td>&nbsp;</td><td align='center'>User Name</td><td align='center'>User</td></tr>";
			foreach ($users as $user) {
				echo "<tr><td><input type='checkbox' name='user_{$user->ID}' value='checked'";
				if ($user->cu) {
					echo " checked";
				}
				echo "></td><td>{$user->user_login}</td><td>{$user->display_name}</td></tr>";
			}
			echo "</table>";
			echo "</td></tr></table><BR><input type='submit' value='Update Client' name='submit' class='button-primary'>";
			echo "<input type='hidden' value='update_client1' name='action'>";
			echo "<input type='hidden' value='{$_POST['ClientId']}' name='ClientId'>";
			echo "</form>";
		}

	}


	function add_client() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$common = new time_sheets_common();

		if ($_POST['action']=='save_client' && $_POST['ClientName'] != '') {
			$ClientId=$db->get_var("select ClientId from {$wpdb->prefix}timesheet_clients where ClientName=%s", array(sanitize_text_field($_POST['ClientName'])));

			if (!$ClientId) {
				$db->query("insert into {$wpdb->prefix}timesheet_clients
					(ClientName, Active, sales_person_id)
					values
					(%s, 1, %d)", array(sanitize_text_field($_POST['ClientName']), intval($_POST['sales_person_id'])));

				$ClientId=$db->get_var("select ClientId from {$wpdb->prefix}timesheet_clients where ClientName=%s", array(sanitize_text_field($_POST['ClientName'])));

				$db->query("insert into {$wpdb->prefix}timesheet_clients_users
					(ClientId, user_id)
					values
					($ClientId, $user_id)");

				echo '<div if="message" class="updated"><p>Client Added.</p></div>';
			} else {
				$db->query("insert into {$wpdb->prefix}timesheet_clients_users
					(ClientId, user_id)
					values
					($ClientId, $user_id)");
				echo '<div if="message" class="error"><p>Client already exists.<BR>Access granted to client.</p></div>';
			}
		}

		echo "<form name='new_client' method='POST'>
			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='New Client' name='menu'>";
		echo "<table><tr><td>Client Name</td><td><input type='text' name='ClientName'></td></tr>";
		echo "<tr><td>Sales Person</td><td>";
		$users = $common->return_employee_list();
		echo "<select name='sales_person_id'>
				<option value='-10'>No Sales Person</option>";
		foreach ($users as $user) {
			echo "<option value='{$user->id}'>{$user->display_name}</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td colspan='2'><input type='submit' value='Save Client' name='submit' class='button-primary'></td></tr>";

		echo "<input type='hidden' name='action' value='save_client'>";
		echo "</form>";
	}


	function NewProject() {
		global $wpdb;
		$db = new time_sheets_db();
		$options = get_option('time_sheets');
		$common = new time_sheets_common();

		if ($_GET['action']=='Save Project'){
			$this->SaveNewProject();
		}

		if ($_GET['IsRetainer']==1) {
			$IsRetainer = ' checked';
		}
		if ($_GET['Active']==1) {
			$Active = ' checked';
		}
		if ($_GET['BillOnProjectCompletion']==1) {
			$BillOnProjectCompletion = 'checked';
		}
		if ($_GET['MaxHours']) {
			$MaxHours = intval($_GET['MaxHours']);
		}

		if ($_GET['flat_rate']==1) {
			$flat_rate = " checked";
		}

		echo "<form method='GET'>
			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='New Project' name='menu'>
			<table><tr><td>Select Client:</td><td>";
			$this->GetClient();
			echo "</td></tr>
			<tr><td>Project Name:</td><td><input type='text' name='ProjectName' value='{$common->esc_textarea($_GET['ProjectName'])}'></td></tr>
			<tr><td>PO Number:</td><td><input type='text' name='po_number' value='{$common->esc_textarea($_GET['po_number'])}'></td></tr><tr><td>Sales Person:</td><td>";
			$users = $common->return_employee_list();
			echo "<select name='sales_person_id'>
				<option value='-10'>No Sales Person</option>";
			foreach ($users as $user) {
				echo "<option value='{$user->id}'>{$user->display_name}</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td colspan='2'>Is Retainer Project <input type='checkbox' name='IsRetainer' value='1'{$IsRetainer}></td><tr>
			<tr><td>Max Project Hours:</td><td><input type='text' name='MaxHours' size='5' value='{$MaxHours}'></td></tr>
			<tr><td colspan='2'>Active / Visable <input type='checkbox' name='Active' value='1'{$Active}></td></tr>";
			if (!$options['remove_embargo']) {
				echo "
<tr><td colspan='2'>Bill at end of project <input type='checkbox' name='BillOnProjectCompletion' value='1'{$BillOnProjectCompletion}></td></tr>";
			}
			echo "<tr><td colspan='2'>Flat rate billing project <input type='checkbox' name='flat_rate' value='1'{$flat_rate}> Bypasses invoicing queue for this project</td></tr>";
			echo "<tr><td>Notes:</td><td><textarea rows='4' cols='50' name='notes'>{$common->esc_textarea($_GET['notes'])}</textarea></td></tr>
			<tr><td colspan='2'>";

			echo "<input type='submit' value='Save Project' name='action' class='button-primary'";
			if ($_GET['action']=='Save Project'){
				echo " disabled";
			}
			echo ">";

			echo "</td></tr>
			</table>";
	}

	function SaveNewProject() {
		if ($this->ValidateProject(true)==false) {
			return;
		}
		global $wpdb;

		$db = new time_sheets_db();

		if ($_GET['IsRetainer']) {
			$IsRetainer=1;
		} else {
			$IsRetainer=0;
		}

		if ($_GET['MaxHours']) {
			$MaxHours = intval($_GET['MaxHours']);
		} else {
			$MaxHours = 0;
		}

		if ($_GET['Active']) {
			$Active = 1;
		} else {
			$Active = 0;
		}

		if ($_GET['flat_rate']) {
			$flat_rate = 1;
		} else {
			$flat_rate = 0;
		}

		if ($_GET['BillOnProjectCompletion']) {
			$BillOnProjectCompletion = 1;
		} else {
			$BillOnProjectCompletion = 0;
		}

		$sql = "INSERT INTO {$wpdb->prefix}timesheet_client_projects
			(ClientId, ProjectName, IsRetainer, MaxHours, HoursUsed, Active, Notes, BillOnProjectCompletion, flat_rate, po_number, sales_person_id)
			values
			(%d, %s, %d, %d, %d, %d, %s, %d, %d, %s, %d)";

		$parms = array(intval($_GET['ClientId']), sanitize_text_field($_GET['ProjectName']), $IsRetainer, $MaxHours, 0, $Active, sanitize_textarea_field($_GET['notes']), $BillOnProjectCompletion, $flat_rate, sanitize_text_field($_GET['po_number']), intval($_GET['sales_person_id']));
		
		$db->query($sql, $parms);

		echo '<div if="message" class="updated"><p>Project added.</p></div>';
		
	}

	function ValidateProject($existsCheck) {
		global $wpdb;
		$db = new time_sheets_db();
		$valid = true;
		if (!$_GET['ClientId']) {
			echo '<div if="message" class="error"><p>The selected client is invalid.</p></div>';
			$valid=false;
		}

		if (!$_GET['ProjectName']) {
			echo '<div if="message" class="error"><p>The name of the project is required.</p></div>';
			$valid=false;
		}

		if ($_GET['IsRetainer']) {
			if ($_GET['ProjectId']) {
				$sql = "select * from {$wpdb->prefix}timesheet_client_projects where ClientId=%d and IsRetainer=1 and ProjectId<>%d";
				$parms = array(intval($_GET['ClientId']), intval($_GET['ProjectId']));
			} else {
				$sql = "select * from {$wpdb->prefix}timesheet_client_projects where ClientId=%d and IsRetainer=1";
				$parms = array(intval($_GET['ClientId']));
			}

			$project = $db->get_row($sql, $parms);
			if ($project) {
				echo "<div if='message' class='error'><p>There is already a retainer project for this client. Change the retainer status for '{$project->ProjectName}' to correct this.</p></div>";
				$valid=false;
			}
		}
	
		if ($existsCheck==true) {
			$sql = "select count(*) from {$wpdb->prefix}timesheet_client_projects where ClientId=%d and ProjectName=%s";
			$params = array(intval($_GET['ClientId']), intval($_GET['ProjectName']));
			$ct = $db->get_var($sql, $params);
			if ($ct!=0) {
				echo '<div if="message" class="error"><p>This project already exists for this client.</p></div>';
				$valid=false;
			}
		}
		return $valid;
	}

	function EditProject() {
		global $wpdb;
		$db = new time_sheets_db();
		$common = new time_sheets_common();
		$options = get_option('time_sheets');

		if ($_GET['subaction']=='Save Project')
		{
			$this->SaveExistingProject();
		}

		$this->GetProjects();
		echo "<form method='GET' name='editproject'>
			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='Edit Project' name='menu'>
			<table><tr><td>Select Client:</td><td>";
			$this->GetClient();
			echo "</td></tr>
			<tr><td>Project Name:</td><td><select name='ProjectId'></select></td></tr>
			<tr><td colspan='2'><input type='submit' value='Select Project' name='action' class='button-primary'></td></tr>
			</form>
			<script>resetProject();</script>";
			

		if ($_GET['action']=='Select Project') {

			$project = $db->get_row("select * from {$wpdb->prefix}timesheet_client_projects tcp where ProjectId=%d order by ProjectName", array(intval($_GET['ProjectId'])));

			if ($project->IsRetainer==1) {
				$IsRetainer=" checked";
				$DisableHours="disabled";
			}
			if ($project->Active==1) {
				$Active=" checked";
			}
			if ($project->BillOnProjectCompletion==1) {
				$BillOnProjectCompletion = " checked";
			}
			if ($project->flat_rate==1) {
				$flat_rate = " checked";
			}

			$hoursused = $project->MaxHours-$project->HoursUsed;
			echo "<form method='GET' name='projectprops'>
			<input type='hidden' value='timesheet_manage_clients' name='page'>
			<input type='hidden' value='Edit Project' name='menu'>
			<input type='hidden' value='Select Project' name='action'>
			<input type='hidden' value='{$common->intval($_GET['ProjectId'])}' name='ProjectId'>
			<input type='hidden' value='{$common->intval($_GET['ClientId'])}' name='ClientId'>
			<tr><td>Project Name:</td><td><input type='text' name='ProjectName' value='{$common->clean_from_db($project->ProjectName)}'></td></tr>
			<tr><td>PO Number:</td><td><input type='text' name='po_number' value='{$common->clean_from_db($project->po_number)}'></td></tr><tr><td>Sales Person:</td><td>";
			$users = $common->return_employee_list();
			echo "<select name='sales_person_id'>
				<option value='-10'>No Sales Person</option>";
			foreach ($users as $user) {
				echo "<option value='{$user->id}'{$common->is_match($project->sales_person_id, $user->id, ' selected', 0)}>{$user->display_name}</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td colspan='2'>Is Retainer Project <input type='checkbox' name='IsRetainer' value='1'{$IsRetainer} onClick='disableMaxHours()'></td><tr>
			<tr><td>Max Project Hours:</td><td><input type='text' name='MaxHours{$DisableHours}' size='5' value='{$project->MaxHours}' {$DisableHours}></td></tr>
			<tr><td>Hours Used:</td><td><a href='admin.php?page=timesheet_manage_clients&menu=view_timesheets_for_project&ProjectId={$common->intval($_GET['ProjectId'])}'>{$project->HoursUsed}</a></td></tr>
			<tr><td>Hours Remaining:</td><td>{$hoursused}</td></tr>
			<tr><td>Notes:</td><td><textarea rows='4' cols='50' name='notes'>{$common->clean_from_db($project->notes)}</textarea></td></tr>";
			if (!$options['remove_embargo']) {
				echo "
<tr><td colspan='2'>Bill on project completion <input type='checkbox' name='BillOnProjectCompletion' value='1' {$BillOnProjectCompletion}></td></tr>";
			}
			echo "<tr><td colspan='2'>Active / Visable <input type='checkbox' name='Active' value='1'{$Active}></td></tr>
			<tr><td colspan='2'>Flat rate billing project <input type='checkbox' name='flat_rate' value='1'{$flat_rate}> Bypasses invoicing queue for this project</td></tr>
			<tr><td colspan='2'><input type='submit' value='Save Project' name='subaction' class='button-primary'></td></tr>
			</table>";
			if ($DisableHours) {
				echo "<input type='hidden' name='MaxHours' value='{$project->MaxHours}'>";
			}
			echo "</form>
<script>
function disableMaxHours() {
	projectprops.MaxHours{$DisableHours}.disabled=false;
}
</script>
";
		}

	}

	function SaveExistingProject() {
		if ($this->ValidateProject(false)==false) {
			return;
		}
		global $wpdb;

		$db = new time_sheets_db();

		if ($_GET['IsRetainer']) {
			$IsRetainer=1;
		} else {
			$IsRetainer=0;
		}

		if ($_GET['MaxHours']) {
			$MaxHours = intval($_GET['MaxHours']);
		} else {
			$MaxHours = 0;
		}

		if ($_GET['Active']) {
			$Active = 1;
		} else {
			$Active = 0;
		}

		if ($_GET['BillOnProjectCompletion']) {
			$BillOnProjectCompletion = 1;
		} else {
			$BillOnProjectCompletion = 0;
		}


		if ($_GET['flat_rate']) {
			$flat_rate = 1;
		} else {
			$flat_rate = 0;
		}

		$sql = "UPDATE {$wpdb->prefix}timesheet_client_projects
			SET ClientId=%d, 
				ProjectName=%s, 
				IsRetainer=%d, 
				MaxHours=%d, 
				Active=%d,
				Notes=%s,
				BillOnProjectCompletion=%d,
				flat_rate = %d,
				po_number = %s,
				sales_person_id = %d
			WHERE ProjectId=%d";

		$parms = array(intval($_GET['ClientId']), sanitize_text_field($_GET['ProjectName']), $IsRetainer, $MaxHours, $Active, sanitize_textarea_field($_GET['notes']), $BillOnProjectCompletion, $flat_rate, sanitize_text_field($_GET['po_number']), intval($_GET['sales_person_id']), intval($_GET['ProjectId']) );
		$db->query($sql, $parms);

		echo '<div if="message" class="updated"><p>Project updated.</p></div>';
		
	}

	function GetClient() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$common = new time_sheets_common();


		$clients = $db->get_results("
					select ClientName, tc1.ClientId
					from {$wpdb->prefix}timesheet_clients tc1
					order by ClientName");

		echo "<select name='ClientId' onclick='resetProject()'>";
			foreach ($clients as $client) {
				echo "<option value='{$client->ClientId}'{$common->is_match($client->ClientId, intval($_GET['ClientId']), ' selected')}>{$client->ClientName}</option>";
			}
			echo "</select>";
	}

	function GetProjects() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$common = new time_sheets_common();


		$projects = $db->get_results("select tcp.ClientId, ProjectId, concat(ProjectName, case when Active = 0 then ' (Inactive)' else ' (Active)' end) as ProjectName from {$wpdb->prefix}timesheet_client_projects tcp order by Active desc, ProjectName");

		foreach ($projects as $project) {
			$project->ProjectName = $common->clean_from_db($project->ProjectName);
			$clean_projects[] = $project;
		}


		$js_projects = json_encode($clean_projects);

		echo "<script>
function resetProject(){
	var projectlist = {$js_projects};
	editproject.ProjectId.options.length = 0;

	var numberOfProjects = projectlist.length;
	//alert(numberOfProjects);
	for (var i = 0; i < numberOfProjects; i++) {
		project = projectlist[i];
		if (project['ClientId']==editproject.ClientId.value) {
			var opt = document.createElement('option');
			opt.value = project['ProjectId'];
			opt.innerHTML = project['ProjectName'];
			editproject.ProjectId.appendChild(opt);
		}
	  //alert(project['ProjectName']);
	}";

	if ($_GET['ProjectId']) {
		echo "
	editproject.ProjectId.value={$_GET['ProjectId']};";
	}
	echo "
}
		</script>";

		
	}

}
