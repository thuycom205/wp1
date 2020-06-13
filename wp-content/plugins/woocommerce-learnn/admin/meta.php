<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 05/04/2016
 * Time: 14:16
 */
if (! defined ( 'ABSPATH' ))
    exit (); // Exit if accessed directly LEARN_TEXT_DOMAIN
class Magenest_Learn_Metabox {

    public function __construct() {
        add_action ( 'add_meta_boxes', array($this,'subscription_meta_boxes') );

    }

    public function subscription_meta_boxes() {
        global $post;

        add_meta_box ( 'subscription_data', __ ( 'Subscription Data', LEARN_TEXT_DOMAIN ), array($this,'subscription_data'), 'shop_question', 'normal', 'high' );
        add_meta_box ( 'cartoon_vol', __ ( 'Vocabulary', LEARN_TEXT_DOMAIN ), array($this,'vocabulary'), 'shop_question', 'normal', 'high');
        add_meta_box ( 'subscription_item', __ ( 'Subscription Items', LEARN_TEXT_DOMAIN ), array($this,'subscription_item'), 'shop_question', 'normal', 'high');
        add_meta_box ( 'hung_listen', __ ( 'Listen', LEARN_TEXT_DOMAIN ), array($this,'listen_part'), 'shop_question', 'normal', 'high');
        add_action('wp_enqueue_scripts', array($this,'load_custom_scripts'));
        remove_meta_box ( 'woothemes-settings', 'shop_subscription', 'normal' );
        remove_meta_box ( 'commentstatusdiv', 'shop_subscription', 'normal' );
        remove_meta_box ( 'commentsdiv', 'shop_subscription', 'normal' );
        remove_meta_box ( 'slugdiv', 'shop_subscription', 'normal' );


        add_meta_box ( 'illu_data', __ ( 'Illustration Data', LEARN_TEXT_DOMAIN ), array($this,'i_data'), 'illustration', 'normal', 'high' );
        add_meta_box ( 'illu__item', __ ( 'Illustration Items', LEARN_TEXT_DOMAIN ), array($this,'i_item'), 'illustration', 'normal', 'high');

        remove_meta_box ( 'woothemes-settings', 'illustration', 'normal' );
        remove_meta_box ( 'commentstatusdiv', 'illustration', 'normal' );
        remove_meta_box ( 'commentsdiv', 'illustration', 'normal' );
        remove_meta_box ( 'slugdiv', 'illustration', 'normal' );
    }

    public function load_custom_scripts() {
        wp_enqueue_style('magenest-frontend-style', SUBSCRIPTION_URL.'/assets/css/fontend.css');
        wp_enqueue_script('jquery-chosen-js',  SUBSCRIPTION_URL.'/assetsjs/chosen.jquery.js');

    }

    public function listen_part() {
        global $post;
        ?>
        <h2>Listen section</h2>
        <div>
            <label>Listen id</label>
            <input class="text" name="_listen_id" value="<?php echo get_post_meta($post->ID, '_listen_id' ,true)?>" />

        </div>


    <?php
    }
    public function subscription_data() {
        global $post;
        ?>
        <h2>Subscription #329 details</h2>
        <div class="order_data_column_container" style="margin-left: 20px; ">
            <div class="order_data_column" style=" width: 31%; padding: 0 2% 0 0; display: inline-block; vertical-align: top;">
                <p class="form-field mn-customer-user">
                    <label>Question: </label> </br>
                    <input type="text" name="_learn_question" value="<?php echo get_post_meta($post->ID, '_learn_question' ,true)?>"/>

                </p>

                <p class="form-field mn-customer-user">
                    <label>Video Link: </label> </br>
                    <input type="text" name="_learn_video" value="<?php echo esc_html(get_post_meta($post->ID, '_learn_video' ,true))?>" />

                </p>
                <p class="form-field mn-subscription-status">
                    <label>Image </label></br>
                   <input type="file" name="_learn_illustration" />
                </p>
            </div>


        </div>




    <?php

    }
    public function i_data() {
        global $post;
        ?>
        <h2>Question details</h2>
        <div class="order_data_column_container" style="margin-left: 20px; ">
            <div class="order_data_column" style=" width: 31%; padding: 0 2% 0 0; display: inline-block; vertical-align: top;">
                <p class="form-field mn-customer-user">
                    <label>Question: </label> </br>
                    <input type="text" name="_learn_question" value="<?php echo get_post_meta($post->ID, '_learn_question' ,true)?>"/>

                </p>
                <p class="form-field mn-customer-user">

                    <div data-role="digit-match" >Digit</div>
                </p>
                <p class="form-field mn-customer-user">

                    <div data-role="word-match" >Word</div>
                </p>
                <p class="form-field mn-customer-user">
                    <label>Question Advanced: </label> </br>
                    <textarea  name="_learn_question_advanced">
                        <?php echo get_post_meta($post->ID, '_learn_question_advanced' ,true)?>
                        </textarea>

                </p>
                <p class="form-field mn-customer-user">
                    <label>Video Link: </label> </br>
                    <input type="text" name="_learn_video" value="<?php echo esc_html(get_post_meta($post->ID, '_learn_video' ,true))?>" />

                </p>
                <p class="form-field mn-subscription-status">
                    <label>Image </label></br>
                   <input type="file" name="_learn_illustration" />
                </p>
                <p class="form-field mn-subscription-status">
                    <label>Video ID </label></br>
                   <input type="text" name="_learn_video_id" value="<?php echo esc_html(get_post_meta($post->ID, '_learn_video_id' ,true))?>" />
                </p>
                <p class="form-field mn-subscription-status">
                    <label>Video Start Second </label></br>
                   <input type="text" name="_learn_start" value="<?php echo esc_html(get_post_meta($post->ID, '_learn_start' ,true))?>" />
                </p>
                <p class="form-field mn-subscription-status">
                    <label>Video End Second </label></br>
                   <input type="text" name="_learn_end" value="<?php echo esc_html(get_post_meta($post->ID, '_learn_end' ,true))?>" />
                </p>
            </div>


        </div>




    <?php

    }

    public function vocabulary() {
        global $post;
        ?>
        <div class="mn-table-order-totals" id="learn-answer-vol">
            <div>
                <div>
                    <label>Longman</label>
                    <inpput type="radio" name="dictionary" value="longman"/>
                </div>
                <div>
                    <label>Oxford</label>
                    <inpput type="radio" name="dictionary" value="oxford"/>
                </div>
            </div>
            <table class="wp-list-table widefat fixed striped mn-order-total">
                <thead>
                <tr>
                    <th><input type="checkbox" class="check-column"></th>
                    <th class="sortable-item" ><?php _e('Id', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-float"><?php _e('English', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-quantity"><?php _e('Vietnamese', LEARN_TEXT_DOMAIN); ?></th>
                </tr>
                </thead>
                <tbody data-bind="foreach: answers, visible: vols().length > 0">

                <tr>
                    <td ></td>

                    <td class="label">
                        <span data-bind="text:en"> </span>
                        <input type="hidden"  data-bind="attr: { name: 'vol[' + $index() + '][en]'}, value:en">
                        <input type="hidden"  data-bind="attr: { name: 'vol[' + $index()+ '][vn]'}, value:vn">
                        <input type="hidden"  data-bind="attr: { name: 'vol[' + $index() + '][id]'}, value:id">
                        <input type="hidden"  data-bind="attr: { name: 'vol[' + $index() + '][post_id]'}" value="<?php echo $post->ID ?>">

                    </td>
                    <td>
                        <span data-bind="text:vn"> </span>

                    </td>

                    <td><span data-bind="click:$parent.removeVol"> Delete</span></td>

                </tr>


                </tbody>

                <tfoot>
                <tr>
                    <td></td>

                    <td><input type="text" id="en" data-bind="value:newEnVol"></td>
                    <td><input type="text" id="vn" data-bind="value:newVnVol"></td>
                </tr>

                <tr>
                    <td><a><span class="a-btn" data-bind="click:addVol"> Add</span></a></td>
                </tr>
                </tfoot>

            </table>
        </div>

    <?php
    }
    public function subscription_item() {
        global $post;
        ?>
        <div class="mn-table-order-totals" id="learn-answer">
            <table class="wp-list-table widefat fixed striped mn-order-total">
                <thead>
                <tr>
                    <th><input type="checkbox" class="check-column"></th>
                    <th class="sortable-item" ><?php _e('Id', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-float"><?php _e('English', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-quantity"><?php _e('Vietnamese', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-total" ><?php _e('Correct', LEARN_TEXT_DOMAIN); ?></th>
                </tr>
                </thead>
                <tbody data-bind="foreach: answers, visible: answers().length > 0">

                <tr>
                    <td ></td>

                    <td class="label">
                        <span data-bind="text:en"> </span>
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][en]'}, value:en">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index()+ '][vn]'}, value:vn">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][id]'}, value:id">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][correct]'}, value:correct">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][post_id]'}" value="<?php echo $post->ID ?>">

                    </td>
                    <td>
                        <span data-bind="text:vn"> </span>

                    </td>
                    <td class="total">
                        <input type="checkbox" data-bind="checked:correct" />
                    </td>
                    <td><span data-bind="click:$parent.removeAnswer"> Delete</span></td>

                </tr>


                </tbody>

                <tfoot>
                <tr>
                    <td></td>

                    <td><input type="text" id="en" data-bind="value:newEn"></td>
                    <td><input type="text" id="vn" data-bind="value:newVn"></td>
<!--                    <textarea id="en" name="Text1" cols="40" rows="5" data-bind="value: newEn" ></textarea>-->
<!--                    <textarea id="vn" name="Text1" cols="40" rows="5" data-bind="value:newVn" ></textarea>-->
<!--
                    <td><input type="text" id="en" data-bind="value:newEn"></td>
                    <td><input type="text" id="vn" data-bind="value:newVn"></td> -->
                    <td><input type="checkbox" id="correct" data-bind="value:newCorrect"></td>
                </tr>

                <tr>
                    <td><a><span class="a-btn" data-bind="click:addAnswer"> Add</span></a></td>
                </tr>
                </tfoot>

            </table>
        </div>


        <script type="text/javascript">

            var postId = '<?php echo $post->ID ?>';
        </script>

    <?php

    }

    public function i_item() {
        global $post;
        ?>
        <div class="mn-table-order-totals" id="learn-answer">
            <table class="wp-list-table widefat fixed striped mn-order-total">
                <thead>
                <tr>
                    <th><input type="checkbox" class="check-column"></th>
                    <th class="sortable-item" ><?php _e('Id', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-float"><?php _e('English', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-quantity"><?php _e('Vietnamese', LEARN_TEXT_DOMAIN); ?></th>
                    <th class="sortable-total" ><?php _e('Correct', LEARN_TEXT_DOMAIN); ?></th>
                </tr>
                </thead>
                <tbody data-bind="foreach: answers, visible: answers().length > 0">

                <tr>
                    <td ></td>

                    <td class="label">
                        <span data-bind="text:en"> </span>
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][en]'}, value:en">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index()+ '][vn]'}, value:vn">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][id]'}, value:id">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][correct]'}, value:correct">
                        <input type="hidden"  data-bind="attr: { name: 'answer[' + $index() + '][post_id]'}" value="<?php echo $post->ID ?>">

                    </td>
                    <td>
                        <span data-bind="text:vn"> </span>

                    </td>
                    <td class="total">
                        <input type="checkbox" data-bind="checked:correct" />
                    </td>
                    <td><span data-bind="click:$parent.removeAnswer"> Delete</span></td>

                </tr>


                </tbody>

                <tfoot>
                <tr>
                    <td></td>

                    <td><input type="text" id="en" data-bind="value:newEn"></td>
                    <td><input type="text" id="vn" data-bind="value:newVn"></td>
                    <td><input type="checkbox" id="correct" data-bind="value:newCorrect"></td>
                </tr>

                <tr>
                    <td><a><span class="a-btn" data-bind="click:addAnswer"> Add</span></a></td>
                </tr>
                </tfoot>

            </table>
        </div>


        <script type="text/javascript">

            var postId = '<?php echo $post->ID ?>';

            <?php
            $site_url = parse_url( site_url() );
            $path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';

            $ajax_path = "$path/wp-admin/admin-ajax.php";
                    ?>

            jQuery(document).ready(function() {
                jQuery('div[data-role="digit-match"]').click(function() {

                 var selection =   jQuery('textarea[name="_learn_question_advanced"]').getSelection();
                    var selectedText = selection.text;
                    console.log(selectedText);

                    //post to server

                    var requestUrl = '<?php echo $ajax_path ?>' + '?action=learn_ana&type=digit&text='+ selectedText;


                    console.log(requestUrl);
                    var jqxhr = jQuery.post( requestUrl, function() {
                       // alert( "success" );
                    })
                        .done(function(response) {
                            //alert( "second success" );
                                $ob = JSON.parse(response);
                               // alert($ob.text);
                                // console.log(response);
                               jQuery('textarea[name="_learn_question_advanced"]').replaceSelectedText($ob.text, "select");

                            })
                        ;

                });

                //whole words
                jQuery('div[data-role="word-match"]').click(function() {

                    var selection =   jQuery('textarea[name="_learn_question_advanced"]').getSelection();
                    var selectedText = selection.text;
                    console.log(selectedText);

                    //post to server
                    var requestUrl = '<?php echo $ajax_path ?>' + '?action=learn_ana&type=word&text='+ selectedText;


                    console.log(requestUrl);
                    var jqxhr = jQuery.post( requestUrl, function() {
                            // alert( "success" );
                        })
                            .done(function(response) {
                                //alert( "second success" );
                                $ob = JSON.parse(response);
                                // alert($ob.text);
                                // console.log(response);
                                jQuery('textarea[name="_learn_question_advanced"]').replaceSelectedText($ob.text, "select");

                            })
                        ;

                });
            });
        </script>

    <?php

    }

}

return new Magenest_Learn_Metabox();




?>
