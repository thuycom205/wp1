<?php

/**
 * User's tags
 *
 * The class handles storage of user's tags
 *
 */
class WTBP_WPTAO_Users_Tags extends WTBP_WPTAO_Tags {
	/*
	 * All tags
	 */

	public $tags;

	/**
	 * WTBP_WPTAO_Events_Tags Constructor.
	 */
	public function __construct() {

		global $wpdb;

		parent::__construct();

		$this->table_name	 = $wpdb->prefix . 'wptao_users_tags';
	}
}
