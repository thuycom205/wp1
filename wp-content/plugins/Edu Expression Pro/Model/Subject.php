<?php
$dir = plugin_dir_path(__FILE__);
class Subject extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post);// You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'subject_name'    => 'required|alphaNumericCustom'
                ));
        $gump->filter_rules(array(
                'subject_name' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("subject_name", "Subject Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>