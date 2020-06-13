<?php

/**
 * Event's meta
 *
 * The class handles storage of event's meta data
 *
 */
class WTBP_WPTAO_Events_Meta extends WTBP_WPTAO_Meta {

	/**
	 * WTBP_WPTAO_Events_Meta Constructor.
	 */
	public function __construct() {

		global $wpdb;

		parent::__construct();

		$this->table_name	 = $wpdb->prefix . 'wptao_events_meta';
		$this->version		 = '1.1';
		$this->primary_key	 = 'id';
		$this->field		 = 'event';
	}

}
