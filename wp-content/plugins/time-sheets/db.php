<?php
class time_sheets_db {

	function get_row($sql, $params = NULL) {
		global $wpdb;
		if ($params == NULL) {
			$var=$wpdb->get_row($sql);
		} else {
			$var=$wpdb->get_row(
				$wpdb->prepare($sql,$params)
			);
		}

		return $var;
	}

	function query($sql, $params = NULL){
		global $wpdb;
		if ($params == NULL) {
			$wpdb->query($sql);
		} else {
			$wpdb->query(
				$wpdb->prepare($sql,$params)
			);
		}

	}

	function get_var($sql, $params = NULL) {
		global $wpdb;
		if ($params == NULL) {
			$var = $wpdb->get_var($sql);
		} else {
			$var = $wpdb->get_var(
				$wpdb->prepare($sql,$params)
			);
		}
		return $var;
	}

	function get_results ($sql, $params = NULL) {
		global $wpdb;

		if ($params == NULL) {
			$results = $wpdb->get_results($sql);

		} else {
			$results = $wpdb->get_results(
				$wpdb->prepare($sql,$params)
			);
		}

		return $results;
	}
}