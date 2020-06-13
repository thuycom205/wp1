<?php
$dir = plugin_dir_path(__FILE__);
class Question extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'marks'    => 'required|numeric',
                'negative_marks'    => 'numeric',
                
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("marks", "Marks");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>