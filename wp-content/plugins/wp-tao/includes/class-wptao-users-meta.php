<?php

/**
 * User's meta
 *
 * The class handles storage of user's meta data
 *
 */
class WTBP_WPTAO_Users_Meta extends WTBP_WPTAO_Meta {

	/**
	 * WTBP_WPTAO_Users_Meta Constructor.
	 */
	public function __construct() {

		global $wpdb;

		parent::__construct();

		$this->table_name	 = $wpdb->prefix . 'wptao_users_meta';
		$this->version		 = '1.1';
		$this->primary_key	 = 'id';
		$this->field		 = "user";
	}

}
