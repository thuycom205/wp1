<?php
$dir = plugin_dir_path(__FILE__);
class Iequestion extends ExamApps
{
    public function importInsert($rowData,$groupArr,$subjectId)
    {
        foreach($rowData as $dataValue)
        {
            $dataValue=array_shift($dataValue);
            if($dataValue[0]=="M")
            $dataValue[0]=1;
            elseif($dataValue[0]=="T")
            $dataValue[0]=2;
            elseif($dataValue[0]=="F")
            $dataValue[0]=3;
            elseif($dataValue[0]=="S")
            $dataValue[0]=4;
            else
            $dataValue[0]=1;
            
            if($dataValue[1]=="E")
            $dataValue[1]=1;
            elseif($dataValue[1]=="M")
            $dataValue[1]=2;
            elseif($dataValue[1]=="H")
            $dataValue[1]=3;					
            else
            $dataValue[1]=1;
            $recordArr=array('qtype_id'=>$dataValue[0],'subject_id'=>$subjectId,'diff_id'=>$dataValue[1],'question'=>$dataValue[2],'option1'=>$dataValue[3],'option2'=>$dataValue[4],
                                      'option3'=>$dataValue[5],'option4'=>$dataValue[6],'option5'=>$dataValue[7],'option6'=>$dataValue[8],'marks'=>$dataValue[9],
                                      'negative_marks'=>$dataValue[10],'hint'=>$dataValue[11],'explanation'=>$dataValue[12],'answer'=>$dataValue[13],'true_false'=>$dataValue[14],'fill_blank'=>$dataValue[15]);
            
            if($this->autoInsert->iInsert($this->wpdb->prefix."emp_questions",$recordArr))
            {
                $lastId=$this->autoInsert->iLastID();
                foreach($groupArr as $value)
                {
                        $this->autoInsert->iInsert($this->wpdb->prefix."emp_question_groups",array('group_id'=>$value,'question_id'=>$lastId));
                }
            }
            else
            {
                return false;
            }
        }
        return true;
    }
    public function exportData($userGroupWise)
    {
        try
        {
            $SQL="SELECT *,`Question`.`id` as `id` FROM `".$this->wpdb->prefix."emp_questions` AS `Question`
            LEFT JOIN `".$this->wpdb->prefix."emp_question_groups` AS `QuestionGroup` ON (`Question`.`id`=`QuestionGroup`.`question_id`)
            LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`QuestionGroup`.`group_id`=`UserGroup`.`group_id`)
            LEFT JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Question`.`subject_id`=`Subject`.`id`)
            INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)
            INNER JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON(`Diff`.`id`=`Question`.`diff_id`)
            WHERE  1=1 ".$userGroupWise." GROUP BY `Question`.`id` ";
            $this->autoInsert->iWhileFetch($SQL,$post);
            $data=$this->showQuestionData($post);
            return $data;
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),"danger");
        }    
    }
    public function showQuestionData($post)
    {
        $ExamApp=new ExamApps();
        $showData=array(array('Groups','Subject','Difficulty Level','Question Type','Question','Option1','option2','option3',
                                                  'Option4','Option5','Option6','Marks','Negative Marks','Hint','Explanation','Correct Answer','True & False','Fill in the blanks'));
        foreach($post as $value)
        {
            $showData[]=array('groups'=>$ExamApp->showGroupName("emp_question_groups","emp_groups","question_id",$value['id']),
                                              'subject'=>$value['subject_name'],
                                              'diff'=>$value['diff_level'],
                                              'qtype'=>$value['question_type'],
                                              'question'=>$value['question'],'option1'=>$value['option1'],'option2'=>$value['option2'],'option3'=>$value['option3'],
                                              'option4'=>$value['option4'],'option5'=>$value['option5'],'option6'=>$value['option6'],
                                              'marks'=>$value['marks'],'negative_marks'=>$value['negative_marks'],'hint'=>$value['hint'],
                                              'explanation'=>$value['explanation'],'answer'=>$value['answer'],'true_false'=>$value['true_false'],
                                              'fill_blank'=>$value['fill_blank']);
        }
        return$showData;
    }
}
?>