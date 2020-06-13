<?php
class time_sheets_client_managers {
	function manage_client_managers() {
		if ($_GET['action']) {
			$this->update_client_manager_list();
		}

		$this->show_client_manager_list();

	}

	function show_client_manager_list() {
		global $wpdb;
		$user_id = get_current_user_id();
		$entry = new time_sheets_entry();
		$db = new time_sheets_db();
		$folder = plugins_url();

		$sql = "select u.ID, u.display_name, mcu.user_id, u.user_login
			from {$wpdb->users} u
			left outer join {$wpdb->prefix}timesheet_manage_client_users mcu on u.ID = mcu.user_id
			order by u.display_name";

		$users = $db->get_results($sql);

		echo "<br><table border='1' cellpadding='0' cellspacing='0'><tr><td><B>User</B></td><td><B>User Name</b></td><td><b>Current State</b></td><td><B>Add Permission</B></td><td><B>Remove Permission</B></td></tr>";
		foreach ($users as $user) {
			echo "<tr><td>{$user->display_name}</td>
				<td>{$user->user_login}</td>
				<td>";
				if ($user->ID != $user->user_id) {
					echo "No Access";
				} else {
					echo "Has Access";
				}
				echo "</td>
			<td align='center'>";
			if ($user->ID != $user->user_id) {
				echo "<a href='admin.php?page=time_sheets_client_managers&user_id={$user->ID}&action=add'><img src='{$folder}/time-sheets/check.png' width='15' height='15'></a>";
			}
			echo "</td>
			<td align='center'>";
			if ($user->ID == $user->user_id) {
				echo "<a href='admin.php?page=time_sheets_client_managers&user_id={$user->ID}&action=remove'><img src='{$folder}/time-sheets/x.png' width='15' height='15'></a>";
			}
			echo "</td></tr>";
		}
		echo "</td></tr></table>"; 
	}

	function update_client_manager_list() { 

		global $wpdb;
		$db = new time_sheets_db();

		if ($_GET['action']=='add') {
			$sql = "insert into {$wpdb->prefix}timesheet_manage_client_users (user_id) values (%d)";
		}

		if ($_GET['action']=='remove') {
			$sql = "delete from {$wpdb->prefix}timesheet_manage_client_users where user_id = %d";
		}
		$parms = array(intval($_GET['user_id']));

		$db->query($sql, $parms);

		echo '<div if="message" class="updated"><p>User updated.</p></div>';
	}

	function client_manager_check() {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "select count(*) from {$wpdb->prefix}timesheet_manage_client_users where user_id = {$user_id}";

		$count = $db->get_var($sql);
		
		return $count;
	}
}