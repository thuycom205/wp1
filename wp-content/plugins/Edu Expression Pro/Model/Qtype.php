<?php
$dir = plugin_dir_path(__FILE__);
class Qtype extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'question_type'    => 'required|alphaNumericCustom'
                ));
        $gump->filter_rules(array(
                'question_type' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("question_type", "Question Type Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>