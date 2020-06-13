<?php
class time_sheets_setup {

	function create_db_objects() {
		$this->create_tables();
	}

	function create_tables() {
		global $wpdb;

		$charset_collate = $this->get_charset();

		$db_ver = get_site_option( 'timesheet_db_version', '0' );

		if ($db_ver=='0') {

			add_site_option( 'timesheet_db_version', '0' );

			$sql = 	"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}timesheet_approvers`(
				user_id bigint(20),
				PRIMARY KEY (user_id)
				) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}timesheet_invoicers` (
				user_id bigint(20),
				PRIMARY KEY (user_id)
				) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}timesheet_users` (
				user_id bigint(20),
				PRIMARY KEY (user_id)
				) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}timesheet_recurring_invoices_monthly` (
				client_id bigint(20) not null,
				MonthlyHours bigint(20) NULL,
				HourlyRate bigint(20) NULL,
				Notes mediumtext NULL,
				BillOnProjectCompletion tinyint(1) NULL,
				PRIMARY KEY (client_id)
				) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}timesheet` (
				timesheet_id bigint(20) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				start_date datetime NOT NULL,
				entered_date datetime NOT NULL,
				client_name mediumtext NOT NULL,
				project_name mediumtext NULL,
				monday_hours numeric(12,2) NOT NULL,
				tuesday_hours numeric(12,2) NOT NULL,
				wednesday_hours numeric(12,2) NOT NULL,
				thursday_hours numeric(12,2) NOT NULL,
				friday_hours numeric(12,2) NOT NULL,
				saturday_hours numeric(12,2) NOT NULL,
				sunday_hours numeric(12,2) NOT NULL,
				total_hours numeric(12,2) NOT NULL,
				monday_desc mediumtext NULL,
				tuesday_desc mediumtext NULL,
				wednesday_desc mediumtext NULL,
				thursday_desc mediumtext NULL,
				friday_desc mediumtext NULL,
				saturday_desc mediumtext NULL,
				sunday_desc mediumtext NULL,
				per_diem_days numeric(6,2) NULL,
				hotel_charges numeric(12,2) NULL,
				rental_car_charges numeric(12,2) NULL,
				tolls numeric(12,2) NULL,
				other_expenses numeric(12,2) NULL,
				other_expenses_notes longtext NULL,
				week_complete tinyint(1) NOT NULL,
				marked_complete_by bigint(20) NULL,
				marked_complete_date datetime NULL,
				approved tinyint(1) NOT NULL,
				approved_by bigint(20) NULL,
				approved_date datetime NULL,
				invoiced tinyint(1) NOT NULL,
				invoiced_by bigint(20) NULL,
				invoiced_date datetime NULL,
				invoiceid bigint(20) NULL,
				ClientId bigint(20) NULL,
				mileage bigint(20) NULL,
				EmbargoPendingProjectClose tinyint(1) NULL,
				project_complete tinyint(1) NULL,
				ProjectId bigint(20),
				PRIMARY KEY (timesheet_id),
				INDEX IX_user_id_start_date (user_id, start_date),
				INDEX IX_approved (approved),
				INDEX IX_invoiced (invoiced)
				) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}timesheet_clients` (
				ClientId bigint(20) NOT NULL AUTO_INCREMENT,
				ClientName mediumtext NOT NULL,
				Active tinyint(1) NULL,
				FinalProjectEnd datetime NULL,
				PRIMARY KEY (ClientId),
				INDEX IX_FinalProjectEnd_Active (FinalProjectEnd, Active)) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}timesheet_clients_users (
				ClientId bigint(20) NOT NULL,
				user_id bigint(20) NOT NULL,
				PRIMARY KEY (ClientId, user_id),
				INDEX IX_user_id_ClientId (user_id, ClientId)) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}timesheet_approvers_approvies (
				approver_user_id bigint(20) NOT NULL,
				approvie_user_id bigint(20) NOT NULL,
				PRIMARY KEY (approver_user_id, approvie_user_id)) $charset_collate";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}timesheet_client_projects (
				ProjectId bigint(20) NOT NULL AUTO_INCREMENT,
				ClientId bigint(20) NOT NULL,
				ProjectName mediumtext NOT NULL,
				IsRetainer tinyint(1) NOT NULL,
				MaxHours bigint(20) NOT NULL,
				HoursUsed bigint(20) NOT NULL,
				Active bit NOT NULL,
				notes mediumtext NULL,
				PRIMARY KEY (ProjectId),
				INDEX ix_ClientId (ClientId)) $charset_collate";
			$wpdb->query($sql);

			$db_ver = .5;
		}


		if ($db_ver=='.5') {
			$sql = "INSERT INTO {$wpdb->prefix}timesheet_client_projects
				(ClientId, ProjectName, IsRetainer, MaxHours, HoursUsed, Active)
				SELECT ClientId, case when project_name = '' then 'Not Specified' else project_name end, CASE WHEN project_name = 'Retainer' then 1 else 0 end, 1000, sum(total_hours), 1
				FROM {$wpdb->prefix}timesheet a
				WHERE NOT EXISTS (SELECT * FROM {$wpdb->prefix}timesheet_client_projects b where a.ClientId = b.ClientId AND b.ProjectName = case when project_name = '' then 'Not Specified' else project_name end)
				GROUP BY ClientId, case when project_name = '' then 'Not Specified' else project_name end, CASE WHEN project_name = 'Retainer' then 1 else 0 end, 1";
			$wpdb->query($sql); //Only needed if using prerelease schema.

			$sql = "UPDATE {$wpdb->prefix}timesheet b
				inner join {$wpdb->prefix}timesheet_client_projects a on b.ClientId = a.ClientId 
					AND ProjectName = case when project_name = '' then 'Not Specified' else project_name end
				SET b.ProjectId = a.ProjectId
				WHERE b.ProjectId IS NULL";
			$wpdb->query($sql);

			$sql = "INSERT INTO {$wpdb->prefix}timesheet_clients
				(ClientName, Active, FinalProjectEnd)
				SELECT DISTINCT client_name, 1, NULL
				FROM {$wpdb->prefix}timesheet
				WHERE client_name NOT IN (SELECT ClientName FROM {$wpdb->prefix}timesheet_clients)";
			$wpdb->query($sql);

			$sql = "UPDATE {$wpdb->prefix}timesheet t
				JOIN {$wpdb->prefix}timesheet_clients c ON t.client_name = c.ClientName
				SET t.ClientId = c.ClientId
				WHERE t.ClientId IS NULL";
			$wpdb->query($sql);

			$sql = "INSERT INTO {$wpdb->prefix}timesheet_clients_users
				(ClientId, user_id)
				SELECT DISTINCT ClientId, user_id
				FROM {$wpdb->prefix}timesheet t
				WHERE NOT EXISTS (SELECT * FROM {$wpdb->prefix}timesheet_clients_users c WHERE t.ClientId = c.ClientId AND t.user_id = c.user_id)";
			$wpdb->query($sql);

			$db_ver = 1;
		}

		if ($db_ver==1) {
			$sql = "ALTER TABLE `{$wpdb->prefix}timesheet`
				ADD COLUMN payrolled tinyint(1) DEFAULT 0,
				ADD COLUMN payrolled_on datetime,
				ADD COLUMN payrolled_by bigint(20)
				";
			$wpdb->query($sql);

			$sql = "ALTER TABLE `{$wpdb->prefix}timesheet`
				ADD INDEX IX_invoiced_payrolled (invoiced, payrolled)";
			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}timesheet_payrollers` (
				user_id bigint(20),
				PRIMARY KEY (user_id)
				) $charset_collate";
			$wpdb->query($sql);

			$db_ver = 2;
		}

		if ($db_ver==2) {
			$sql = "ALTER TABLE `{$wpdb->prefix}timesheet`
				ADD COLUMN flight_cost decimal(12,2)";
			$wpdb->query($sql);

			$db_ver = 3;
		}

		if ($db_ver==3) {
			$sql = "CREATE TABLE `{$wpdb->prefix}timesheet_emailqueue`
				(email_id  bigint(20) NOT NULL AUTO_INCREMENT,
				send_to varchar(255),
				send_from_email varchar(255),
				send_from_name  varchar(255),
				subject  varchar(255),
				message_body mediumtext not null,
				entered_on datetime,
				PRIMARY KEY (email_id),
				INDEX ix_bigindex (entered_on, send_to, send_from_email, send_from_name, subject),
				INDEX ix_entered_on (entered_on)
				) $charset_collate";
			$wpdb->query($sql);

			$db_ver = 4;
		}

		if ($db_ver==4) {
			$sql = "ALTER TABLE `{$wpdb->prefix}timesheet`
				ADD COLUMN isPerDiem tinyint(2)";
			$wpdb->query($sql);

			$sql = "update {$wpdb->prefix}timesheet
				set isPerDiem = 1
				where isPerDiem IS NULL";
			$wpdb->query($sql);

			$db_ver = 5;
		}

		if ($db_ver==5) {
			$sql = "ALTER TABLE {$wpdb->prefix}timesheet_client_projects
				ADD COLUMN BillOnProjectCompletion tinyint(1)";
			$wpdb->query($sql);

			$db_ver = 6;
		}

		if ($db_ver==6) {
			$sql = "CREATE TABLE `{$wpdb->prefix}timesheet_employee_always_to_payroll`
				(user_id  bigint(20) NOT NULL) $charset_collate";
			$wpdb->query($sql);

			$db_ver = 7;
		}
		if ($db_ver==7) {
			$sql = "ALTER TABLE `{$wpdb->prefix}timesheet`
				ADD COLUMN perdiem_city varchar(255)";
			$wpdb->query($sql);

			$db_ver = 8;
		}

		if ($db_ver==8) {
			$sql = "ALTER TABLE {$wpdb->prefix}timesheet_approvers
				ADD COLUMN backup_user_id bigint(20),
				ADD COLUMN backup_expires_on datetime";

			$wpdb->query($sql);

			$sql = "CREATE TABLE {$wpdb->prefix}timesheet_manage_client_users
					(user_id bigint(20)) $charset_collate";


			$wpdb->query($sql);

			$db_ver = 9;
		}


		if ($db_ver==9) {
			$sql = "ALTER TABLE {$wpdb->prefix}timesheet_client_projects
				ADD COLUMN flat_rate tinyint(1)";

			$wpdb->query($sql);

			$db_ver = 10;
		}


		if ($db_ver==10) {
			$sql = "ALTER TABLE {$wpdb->prefix}timesheet_client_projects
				ADD COLUMN po_number varchar(255)";

			$wpdb->query($sql);

			$db_ver = 11;
		}
		
		if ($db_ver==11) {
			$sql = "ALTER TABLE {$wpdb->prefix}timesheet_client_projects
				ADD COLUMN sales_person_id bigint(20)";
			
			$wpdb->query($sql);

			$sql = "ALTER TABLE {$wpdb->prefix}timesheet_clients
				ADD COLUMN sales_person_id bigint(20)";
			
			$wpdb->query($sql);
			
			$db_ver = 12;
		}

		update_site_option( 'timesheet_db_version', $db_ver );

	}

	function get_charset() {
		global $wpdb;

		$charset_collate = '';
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";

		return $charset_collate;
	}
}