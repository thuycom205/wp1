<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 05/04/2016
 * Time: 14:16
 */
if (! defined ( 'ABSPATH' ))
    exit (); // Exit if accessed directly LEARN_TEXT_DOMAIN

if (!class_exists('Magenest_Learn_Question')) include_once LEARN_PATH.'question.php';

class Magenest_Learn_Model
{

    public function __construct()
    {
        add_action( 'save_post', 'Magenest_Learn_Model::save', 20, 2 );

    }

    public static function save($postId, $object) {
        if (isset($_POST['_learn_question']))     update_post_meta($postId,'_learn_question' , $_POST['_learn_question']);
        if (isset($_POST['_learn_question_advanced']))     update_post_meta($postId,'_learn_question_advanced' , $_POST['_learn_question_advanced']);
        if (isset($_POST['_learn_video']))    update_post_meta($postId,'_learn_video' , $_POST['_learn_video']);
        if (isset($_POST['_learn_image']))   update_post_meta($postId,'_learn_image' , $_POST['_learn_image']);

        if (isset($_POST['_learn_video_id']))    update_post_meta($postId,'_learn_video_id' , $_POST['_learn_video_id']);
        if (isset($_POST['_listen_id']))    update_post_meta($postId,'_listen_id' , $_POST['_listen_id']);
        if (isset($_POST['_learn_start']))   update_post_meta($postId,'_learn_start' , $_POST['_learn_start']);
        if (isset($_POST['_learn_end']))   update_post_meta($postId,'_learn_end' , $_POST['_learn_end']);
//need to upload the iamges to some folder
        //save the question

        if (isset($_POST['answer'])) {


        $anwers = $_POST['answer'];
        if ($anwers) {

            //first delete all the old post
            $questions = Magenest_Learn_Question::getAnswerByPostId($postId);

            if ($questions) {
                foreach ($questions as $question) {

                    Magenest_Learn_Question::deleteQuestion($question);
                }
            }
            foreach ($anwers as $id=>$value) {

                $value['post_id'] = $postId;
                if (isset($anwer['id']) && $anwer['id'] && false)  {
                   //update the answer
                    Magenest_Learn_Question::updateQuestion($value);
                } else {
                    //save the answer
                    Magenest_Learn_Question::saveQuestion($value);
                    self::main($value['en']);
                   // self::mainLongman($value['en']);

                }

            }
        }
        }
    }
    public  static  function get_web_page( $url )
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    public static function mainLongman($word) {
        //'http://www.oxfordlearnersdictionaries.com/definition/american_english/living_1?q=living'
        $url = "https://d27ucmmhxk51xv.cloudfront.net/media/english/ameProns/{$word}?version=1.1.64";

        $saveto = LEARN_PATH .'audio';
        $fileName = $word.'.mp3';
        $saveto .='/'.$fileName;
        /* if ( preg_match( "/\d+$/", $url, $matches ) ) {
             $saveto .='/'. $matches[0];
         }*/
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(!file_exists($saveto)){
            $fp = fopen($saveto,'x');
            fwrite($fp, $raw);
            fclose($fp);
        }
    }

    public static function main($word) {
        //'http://www.oxfordlearnersdictionaries.com/definition/american_english/living_1?q=living'
        $url = 'http://www.oxfordlearnersdictionaries.com/definition/american_english/' . $word. '_1' . '?q=' .$word;
        $result = Magenest_Learn_Model::get_web_page( $url );

        $page = $result['content'];


        $pattern = "/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i";
        $input = preg_replace_callback($pattern, ['self','replaceUrlWithGA'] , $page) ;

        // echo $input;
    }
    /**
     * @param $input
     * @return string
     */
    public static function replaceUrlWithGA($input) {
        if (isset($input[0]) ) {
            if (strpos($input[0], 'mp3')) {
               // echo $input[0] ;
               self::grab_image($input[0], 'living.mp3');
            }

        } else {
            return '';
        }
    }

    public static function grab_image($url,$saveto){
        $saveto = LEARN_PATH .'audio';
        $fileName = substr($url, strrpos($url, '/') + 1);

        $parseArr = explode('__', $fileName);
        $parseFileName = $parseArr[0]. '.mp3';
        $saveto .='/'.$parseFileName;
       /* if ( preg_match( "/\d+$/", $url, $matches ) ) {
            $saveto .='/'. $matches[0];
        }*/
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(!file_exists($saveto)){
            $fp = fopen($saveto,'x');
            fwrite($fp, $raw);
            fclose($fp);
        }

    }

}

return new Magenest_Learn_Model();