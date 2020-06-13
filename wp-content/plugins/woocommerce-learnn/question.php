<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/04/2016
 * Time: 21:13
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Magenest_Learn_Question {
    public static function saveQuestion($data) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $tbl = "{$prefix}magenest_learn_answer";
        $wpdb->insert($tbl,$data);
    }
    public static function saveQuestionWithTag($data) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $tbl = "{$prefix}magenest_learn_answer";
        $wpdb->insert($tbl,$data);
    }

    public static function updateQuestion($data) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $tbl = "{$prefix}magenest_learn_answer";
        $wpdb->update($tbl,$data, array('id' =>$data['id']));
    }

    public static function deleteQuestion($data) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $tbl = "{$prefix}magenest_learn_answer";
        $wpdb->delete($tbl,array('id' =>$data['id']));
    }

    public static function getAnswerByPostId($postId) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $tbl = "{$prefix}magenest_learn_answer";

        //query
        $query = "select * from {$tbl} where post_id={$postId}";
        $rows = $wpdb->get_results($query,ARRAY_A);
        return $rows;
    }

}