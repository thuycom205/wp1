<?php
$dir = plugin_dir_path(__FILE__);
class Diff extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post);
        $gump->validation_rules(array(
                'diff_level'    => 'required|alphaNumericCustom'
                ));
        $gump->filter_rules(array(
                'diff_level' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("diff_level", "Diffculty Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>