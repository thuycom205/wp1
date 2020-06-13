<?php
/**
 * /**
 * Pluugin Name: WooCommerce Learn
 * Plugin URI: http://store.magenest.com/woocommerce-plugins/woocommerce-media-pro.html
 * Description:Add video/image to website
 * Author: Magenest
 * Author URI: http://magenest.com
 * Version: 1.0
 * Text Domain: mediapro
 * Domain Path: /languages/
 *
 * Copyright: (c) 2011-2015 Hungnam. (info@hungnamecommerce.com)
 *
 *
 * @package   woocommerce-media-pro
 * @author    Hungnam
 * @category  Media pro
 * @copyright Copyright (c) 2014, Hungnam, Inc.

 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 05/04/2016
 * Time: 13:44
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if (! defined ('LEARN_TEXT_DOMAIN'))
    define ( 'LEARN_TEXT_DOMAIN', 'giftregistry' );

// Plugin Folder Path
if (! defined ('LEARN_PATH'))
    define ('LEARN_PATH', plugin_dir_path ( __FILE__ ) );

// Plugin Folder URL
if (! defined ('LEARN_URL'))
    define ('LEARN_URL', plugins_url ( 'woocommerce-learn', '' ) );

// Plugin Root File
if (! defined ('LEARN_FILE'))
    define ('LEARN_FILE', plugin_basename ( __FILE__ ) );
class Magenest_Learn_Main {
    private static $instance;

    /** plugin version number */
    const VERSION = '1.8';

    /** plugin text domain */
    const TEXT_DOMAIN = 'learn';

    public function __construct() {
        global $wpdb;

        register_activation_hook ( LEARN_FILE, array ($this,'install' ) );
        add_action ( 'init', array ($this,'load_text_domain' ), 1 );
        add_action ( 'init', array ($this,'create_post_type') );

        //add_action( 'init', array($this,'add_label_taxonomies'), 5 );
        add_action('wp_enqueue_scripts', array($this,'load_custom_scripts'));
        //add_action('wp_print_scripts', array($this,'add_media_script'));
        $this->include_for_frontend();
       // add_action( 'init', array('Magenest_Giftregistry_Shortcode','init'), 5 );
       // add_action( 'init', array('Magenest_Giftregistry_Form_Handler','init'), 5 );
      //  add_action('init',array($this,'register_session'));

        if (is_admin ()) {
            add_action ( 'admin_enqueue_scripts', array ($this,'load_admin_scripts' ), 99 );
           require_once plugin_dir_path ( __FILE__ ). 'admin/meta.php';
           require_once plugin_dir_path ( __FILE__ ). 'admin/savelearn.php';
           require_once plugin_dir_path ( __FILE__ ). '/question.php';

            add_action ( 'admin_menu', array ( $this, 'admin_menu' ), 5 );
        }

        add_action( 'wp_ajax_learn_answer',  array($this, 'learn_answer_event_json') );
        add_action( 'wp_ajax_nopriv_learn_answer',  array($this, 'learn_answer_event_json') );

        //learn-ana
        add_action( 'wp_ajax_learn_ana',  array($this, 'learn_ana') );
      //  add_action( 'wp_ajax_nopriv_learn_answer',  array($this, 'learn_answer_event_json') );
        //update information after a guest buy gift registry
        add_filter('single_template', array ( $this, 'question_template'));

    }

    public function learn_ana() {
        $type = $_REQUEST['type'];
        $text = $_REQUEST['text'];
        $html  ='';
        if ($type == 'digit') {
            $arr1 = str_split($text);

            if ($arr1) {
                foreach ($arr1 as $d) {
                    $html .= '<span class="placeholder digit" data-digit="'.$d.'" > . </span> ';
                }

            }

        } else  {
            $arr1 = explode(' ',$text);

            if ($arr1) {
                foreach ($arr1 as $d) {
                    if ($d)
                    $html .= '<span class="placeholder word" data-digit="'.$d.'" > . </span> ';
                }

            }
        }
      echo  json_encode(array('text'=>$html) );
       // error_log($html);
      //  echo $html;
        wp_die();
       // exit;
      }
    public function question_template($single) {
        global $wp_query, $post;

        $type = "video";

        if (isset($_GET['type'])) {

            $type = $_GET['type'];
        }

      //  $type = 'spell';
       // echo "type is ".$type;
        /* Checks for single template by post type */
        if ($post->post_type == "shop_question" ) {
        if (  $type == 'video'){
            if(file_exists(LEARN_PATH. 'templates/video.php'))
                return LEARN_PATH . 'templates/video.php';
        }
        elseif ($type == 'learn') {
            if(file_exists(LEARN_PATH. 'templates/learn.php'))
                return LEARN_PATH . 'templates/learn.php';
        } elseif ($type == 'flashcard') {
            if(file_exists(LEARN_PATH. 'templates/learn.php'))
                return LEARN_PATH . 'templates/flashcard.php';
        }
        elseif ($type == 'drag') {
            if(file_exists(LEARN_PATH. 'templates/learn.php'))
                return LEARN_PATH . 'templates/drag.php';
        }

        elseif ($type == 'scatter') {
            if(file_exists(LEARN_PATH. 'templates/learn.php'))
                return LEARN_PATH . 'templates/scatter_game.php';
        }
        elseif ($type == 'spell') {
            if(file_exists(LEARN_PATH. 'templates/learn.php'))
                return LEARN_PATH . 'templates/spell.php';
        } elseif ($type == 'match') {
            if(file_exists(LEARN_PATH. 'templates/learn.php'))
                return LEARN_PATH . 'templates/match.php';
        } elseif ($type == 'listen') {
            if(file_exists(LEARN_PATH. 'templates/learn.php'))
              return LEARN_PATH . 'templates/listen.php';
              // return LEARN_PATH . 'templates/writing.php';
               // return LEARN_PATH . 'templates/play_youtube.php';
        } elseif ($type == 'qa') {
            return LEARN_PATH . 'templates/qa.php';

        }

        else {
            if(file_exists(LEARN_PATH. 'templates/video.php'))
             return LEARN_PATH . 'templates/video.php';
        }
        }

        if ($post->post_type == "illustration" ) {

               //return LEARN_PATH . 'templates/drag.php';
            return LEARN_PATH . 'templates/writing.php';
           // return LEARN_PATH . 'templates/play_youtube.php';


        }
        return $single;
    }
    public function learn_answer_event_json() {
        global $wpdb;
        global $post;

        if (true) {
            $postId =$_REQUEST['post'];
            $tbl = $wpdb->prefix . 'magenest_learn_answer';
            $query ="select * from {$tbl} where post_id={$postId}";
            $rows  = $wpdb->get_results($query);

            $output = array('1'=>'test');
            echo json_encode($rows);
            wp_die();
        }

    }

    public function include_for_frontend() {

    }
    function load_text_domain() {
        load_plugin_textdomain ( LEARN_TEXT_DOMAIN, false, 'woocommerce-learn/languages/' );

    }

    public function load_admin_scripts($hook_suffix) {
        $hs = $hook_suffix;//post_new.php or edit.php or index.php
        global $wp;
        $varquery = $wp->query_vars;

       // if ($hook_suffix == 'post-new.php')  {
          //  wp_enqueue_style('knockout', LEARN_URL. '/assets/knockout-3.4.0');
            wp_enqueue_script('knockout', LEARN_URL. '/assets/knockout-3.4.0.js' , null, '3.4.0');
            wp_enqueue_script('knockoutanswer', LEARN_URL. '/assets/answer.js', null, '3.4.0', true);
            wp_enqueue_script('rangeinput', LEARN_URL. '/assets/rangyinputs/rangyinputs-jquery-src.js', null, '3.4.0', false);
            wp_enqueue_script('math', LEARN_URL. '/assets/math.min.js', null, '3.4.0', false);
///var/www/html/woomodule/fue/wp-content/plugins/woocommerce-learn/assets/rangyinputs/rangyinputs-jquery-src.js
        //}
    }
    public function install() {
        global $wpdb;

        if (!function_exists('dbDelta')) {
            include_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        }

        $table =$wpdb->prefix .'magenest_learn_answer';

        $query = "CREATE TABLE IF NOT EXISTS `{$table}` (
			`id` int(11) unsigned NOT NULL auto_increment,
			`post_id` int(11) NOT NULL,
			`en`  text NULL,
			`vn`  text NULL,
			`correct` varchar(255)  NULL,
			`extra` text  NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";

        dbDelta( $query );

        $table2 =$wpdb->prefix .'magenest_learn_quizz';

        $query2 = "CREATE TABLE IF NOT EXISTS `{$table2}` (
			`id` int(11) unsigned NOT NULL auto_increment,
			`post_id` int(11) NOT NULL,
			`question`  text NULL,
			`answer`  text NULL,
			`correct` varchar(255)  NULL,
			`extra` text  NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";

        dbDelta( $query2 );

    }
    public function create_post_type()
    {
        $show_in_menu = current_user_can ( 'manage_woocommerce' ) ? 'woocommerce' : true;
        $permalinks        = get_option( 'woocommerce_permalinks' );
        $product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'woocommerce' ) : $permalinks['product_base'];
        //$product_permalink ='lesson';

        register_post_type ( 'shop_question',
            array(
                'labels'              => array(
                    'name'                  => __( 'Questions', 'woocommerce' ),
                    'singular_name'         => __( 'Question', 'woocommerce' ),
                    'menu_name'             => _x( 'Questions', 'Admin menu name', 'woocommerce' ),
                    'add_new'               => __( 'Add Question', 'woocommerce' ),
                    'add_new_item'          => __( 'Add New Question', 'woocommerce' ),
                    'edit'                  => __( 'Edit', 'woocommerce' ),
                    'edit_item'             => __( 'Edit Question', 'woocommerce' ),
                    'new_item'              => __( 'New Question', 'woocommerce' ),
                    'view'                  => __( 'View Question', 'woocommerce' ),
                    'view_item'             => __( 'View Question', 'woocommerce' ),
                    'search_items'          => __( 'Search Questions', 'woocommerce' ),
                    'not_found'             => __( 'No Questions found', 'woocommerce' ),
                    'not_found_in_trash'    => __( 'No Questions found in trash', 'woocommerce' ),
                    'parent'                => __( 'Parent Question', 'woocommerce' ),
                    'featured_image'        => __( 'Question Image', 'woocommerce' ),
                    'set_featured_image'    => __( 'Set product image', 'woocommerce' ),
                    'remove_featured_image' => __( 'Remove product image', 'woocommerce' ),
                    'use_featured_image'    => __( 'Use as product image', 'woocommerce' ),
                ),
                'description'         => __( 'This is where you can add new products to your store.', 'woocommerce' ),
                'public'              => true,
                'show_ui'             => true,
                'capability_type'     => 'product',
                'map_meta_cap'        => true,
                'publicly_queryable'  => true,
                'exclude_from_search' => false,
                'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
                'query_var'           => true,
                'rewrite'             => $product_permalink ? array( 'slug' => untrailingslashit( $product_permalink ), 'with_front' => false, 'feeds' => true ) : false,

                'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
                'show_in_nav_menus'   => true,
                'taxonomies' => array('tag','category')

            )
        );

        register_post_status ( 'inactive', array (
            'label' => __ ( 'In active', 'LEARN_TEXT_DOMAIN' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop ( 'In active <span class="count">(%s)</span>', 'In active <span class="count">(%s)</span>' )
        ) );

        register_post_type ( 'illustration',
            array(
                'labels'              => array(
                    'name'                  => __( 'Illustration', 'woocommerce' ),
                    'singular_name'         => __( 'Illustration', 'woocommerce' ),
                    'menu_name'             => _x( 'Illustration', 'Admin menu name', 'woocommerce' ),
                    'add_new'               => __( 'Add Illustration', 'woocommerce' ),
                    'add_new_item'          => __( 'Add New Illustration', 'woocommerce' ),
                    'edit'                  => __( 'Edit', 'woocommerce' ),
                    'edit_item'             => __( 'Edit Illustration', 'woocommerce' ),
                    'new_item'              => __( 'New Illustration', 'woocommerce' ),
                    'view'                  => __( 'View Illustration', 'woocommerce' ),
                    'view_item'             => __( 'View Illustration', 'woocommerce' ),
                    'search_items'          => __( 'Search Illustration', 'woocommerce' ),
                    'not_found'             => __( 'No Illustration found', 'woocommerce' ),
                    'not_found_in_trash'    => __( 'No Illustration found in trash', 'woocommerce' ),
                    'parent'                => __( 'Parent Illustration', 'woocommerce' ),
                    'featured_image'        => __( 'Illustration Image', 'woocommerce' ),
                    'set_featured_image'    => __( 'Set Illustration image', 'woocommerce' ),
                    'remove_featured_image' => __( 'Remove Illustration image', 'woocommerce' ),
                    'use_featured_image'    => __( 'Use as Illustration image', 'woocommerce' ),
                ),
                'description'         => __( 'This is where you can add new products to your store.', 'woocommerce' ),
                'public'              => true,
                'show_ui'             => true,
                'capability_type'     => 'product',
                'map_meta_cap'        => true,
                'publicly_queryable'  => true,
                'exclude_from_search' => false,
                'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
                'query_var'           => true,
                'rewrite'             => $product_permalink ? array( 'slug' => untrailingslashit( $product_permalink ), 'with_front' => false, 'feeds' => true ) : false,

                'supports'            => array( 'title', 'editor',  'thumbnail', 'publicize', 'wpcom-markdown' ),
                'show_in_nav_menus'   => true,
                'taxonomies' => array('tag','category')

            )
        );

        register_post_status ( 'inactive', array (
            'label' => __ ( 'In active', 'LEARN_TEXT_DOMAIN' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop ( 'In active <span class="count">(%s)</span>', 'In active <span class="count">(%s)</span>' )
        ) );
    }

    public function admin_menu() {
        global $menu;
        //include_once GIFTREGISTRY_PATH .'admin/magenest-giftregistry-admin.php';

       // $admin = new Magenest_Giftregistry_Admin();
        add_menu_page(__('Gift registry'), __('Gift registry'), 'manage_woocommerce','gift_registry', array($this,'importQuestionAnswer' ));


    }
    public function load_custom_scripts($hook) {
        global $wp;
         $varquery = $wp->query_vars;
       // wp_enqueue_style('magenestgiftregistry' , GIFTREGISTRY_URL .'/assets/magenestgiftregistry.css');
        wp_enqueue_script('knockout', LEARN_URL. '/assets/knockout-3.4.0.js' , null, '3.4.0');
        wp_enqueue_script('createjs' , LEARN_URL .'/assets/easeljs-0.8.2.min.js');
        wp_enqueue_script('touch' , LEARN_URL .'/assets/Touch.js');

        wp_enqueue_script('math', LEARN_URL. '/assets/math.min.js', null, '3.4.0', false);

        // wp_enqueue_script('createjs' , GIFTREGISTRY_URL .'/assets/easeljs-0.8.2.min.js');

    }

    public function importQuestionAnswer() {
        echo "Import file";
      //  $filename = 'odoo.xml';
        $filename = 'network.xml';
        $uploaddir = wp_upload_dir();
        $uploadfile = $uploaddir['path'] . '/' . $filename;

        echo $uploadfile;

        $contents= file_get_contents($uploadfile);
     //   var_dump($contents);
 try {
     echo "point 1";
     $movies = new SimpleXMLElement($contents);
     echo "point 2";
     foreach ($movies->xpath('//question') as $character) {
         echo $character->q, ' played by ', $character->a, PHP_EOL;
//522
         $value  = ['post_id' => 717, 'en' => $character->q,'vn'=>$character->a, 'tag' =>$character->tag];
         Magenest_Learn_Question::saveQuestionWithTag($value);

     }

 } catch ( Error $error) {
     echo 'Caught exception: ',  $error->getMessage(), "\n";

 }



    }
    /**
     * @return Magenest_Learn_Main
     */
    public static function getInstance() {
        if (! self::$instance) {
            self::$instance = new Magenest_Learn_Main();
        }

        return self::$instance;
    }
}

$magenest_subscription_loaded = Magenest_Learn_Main::getInstance ();

//$magenest_subscription_loaded->importQuestionAnswer();

$x = 1;
