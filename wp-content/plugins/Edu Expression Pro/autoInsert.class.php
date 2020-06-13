<?php
class autoInsert
{
	var $db_conn;
	var $is_debug=false;
	public function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
	}
	public function iInsert($table, $postData = array(),$html_spl='No')
	{
		$sql = "DESC $table";
		$getFields = array();		
		$fieldArr = $this->wpdb->get_results($sql);
		foreach($fieldArr as $field)
		{
			$field=json_decode(json_encode($field),true);
			$getFields[sizeof($getFields)] = $field['Field'];
		}
		$fields = "";
		$values = "";
		if (sizeof($getFields) > 0)
		{				
			foreach($getFields as $k)
			{
				if (isset($postData[$k]))
				{
					if($html_spl=='No')
					{
						$postData[$k] = $postData[$k];
					}
					else
					{
						$postData[$k] = htmlspecialchars($postData[$k]);
					}
					$fields .= "`$k`, ";
					$values .= "'$postData[$k]', ";
				}
			}			
			$fields = substr($fields, 0, strlen($fields) - 2);
			$values = substr($values, 0, strlen($values) - 2);
			$insert = "INSERT INTO $table ($fields) VALUES ($values)";
			if($this->iQuery($insert,$rs))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}	
	public function iEdit(&$record,$table,$conditions=array(),$list_order="")
	{
		$conds="";
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$edit="SELECT * FROM $table WHERE $conds $list_order";
		if($this->iFetch($edit,$record))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function iEditSingle(&$field_value,$table,$field_id,$conditions=array(),$list_order="")
	{
		$conds="";
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$edit="SELECT $field_id FROM $table WHERE $conds $list_order";
		if($this->iFetch($edit,$record))
		{
			$field_value=$record[$field_id];
			return true;
		}
		else
		{
			return false;
		}			
	}	
	public function iUpdate($table, $postData = array(),$conditions = array(),$html_spl='No')
	{
		$sql = "DESC $table";
		$getFields = array();		
		$fieldArr = $this->wpdb->get_results($sql);
		foreach($fieldArr as $field)
		{
			$field=json_decode(json_encode($field),true);
			$getFields[sizeof($getFields)] = $field['Field'];
		}
		$fields = "";
		$values = "";
		if (sizeof($getFields) > 0)
		{
			foreach($getFields as $k)
			{
				if (isset($postData[$k]))
				{		
					if($html_spl=='No')
					{
						$postData[$k] = $postData[$k];
					}
					else
					{
						$postData[$k] = htmlspecialchars($postData[$k]);
					}
					$values .= "`$k` = '$postData[$k]', ";
				}
			}			
			$values = substr($values, 0, strlen($values) - 2);			
			foreach($conditions as $k => $v)
			{
				$v = htmlspecialchars($v);			
				$conds .= "$k = '$v'";
			}			
			$update = "UPDATE $table SET $values WHERE $conds";
			if($this->iQuery($update,$rs))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	public function iUpdateArray($table, $postData = array(),$conditions = array(),$html_spl='No')
	{		
		foreach($postData as $k=>$value)
		{				
			if($html_spl=='Yes')
			{
				$value = htmlspecialchars($value);
			}
			if($value==NULL)
			$values .= "`$k` = NULL, ";
			else
			$values .= "`$k` = '$postData[$k]', ";
		}
		$values = substr($values, 0, strlen($values) - 2);
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);			
			$conds .= "$k = '$v'";
		}			
		$update = "UPDATE `$table` SET $values WHERE $conds";
		if($this->iQuery($update,$rs))
		{
			return true;
		}
		else
		{
			return false;
		}		
	}
	
	public function iDelete($table,$conditions = array())
	{
		$conds="";
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$delete="DELETE FROM $table WHERE $conds";
		if($this->iQuery($delete,$rs))
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
	public function iCheck($table,$conditions = array())
	{
		$conds="";
		$success;			
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$select="SELECT * FROM $table WHERE $conds";
		if($this->iQuery($select,$num_row))
		{
			if($num_row==0)
			{
				$this->success = true;
			}
			else
			{
				$this->success = false;
			}
		}
		else
		{
			$this->success = false;
		}	
	}	
	public function iCheckSingle($table,$field_id,$conditions=array())
	{			
		$conds="";
		$success;
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$select="SELECT $field_id FROM $table WHERE $conds";
		if($this->iQuery($select,$num_row))
		{
			if($num_row>0)
			{
				$this->success = true;
			}
			else
			{
				$this->success = false;
			}
		}
		else
		{
			$this->success = false;
		}			
	}	
	public function iNumRow(&$num_row,$table,$conditions = array())
	{
		$conds="";
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$select="SELECT * FROM $table WHERE 1 $conds";
		if($this->iQuery($select,$num_row))
		return true;
		else
		return false;
	}	
	public function iSum(&$total_record,$table,$field_id,$conditions = array())
	{
		$conds="";
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$select="SELECT SUM(`$field_id`) as total_rec FROM `$table` WHERE $conds";
		$this->iFetch($select,$record);
		$total_record=$record['total_rec'];
	}
	public function iCount(&$total_record,$table,$field_id,$conditions = array())
	{
		$conds="";
		foreach($conditions as $k => $v)
		{
			$v = htmlspecialchars($v);
			$conds .= "$k = '$v'";
		}
		$select="SELECT COUNT($field_id) as total_rec FROM $table WHERE $conds";
		$this->iFetch($select,$record);
		$total_record=$record['total_rec'];
	}
	public function iFetchCount($SQL,&$totalRecord)
	{
		if($SQL)
		{
			if($this->is_debug==true)
			{
				$this->iQuery($SQL,$rs);
			}
			$record = array_shift($this->wpdb->get_results($SQL));
			$record=json_decode(json_encode($record),true);
			$totalRecord=$record['count'];
			return true;
		}
		else
		{
			return false;
		}
	}
	public function iFetch($SQL,&$record)
	{
		if($SQL)
		{
			if($this->is_debug==true)
			{
				$this->iQuery($SQL,$rs);
			}
			$record = array_shift($this->wpdb->get_results($SQL));
			$record=json_decode(json_encode($record),true);
			return true;
		}
		else
		{
			return false;
		}
	}
	public function iWhileFetch($SQL,&$record)
	{
		if($SQL)
		{
			if($this->is_debug==true)
			{
				$this->iQuery($SQL,$rs);
			}
			$record = $this->wpdb->get_results($SQL);
			$record=json_decode(json_encode($record),true);
			return true;
		}
		else
		{
			return false;
		}		
	}
	public function iLastID()
	{
		$last_id=$this->wpdb->insert_id;
		return $last_id;
	}
	public function iQuery($SQL,&$rs)
	{
		if($this->iMainQuery($SQL,$rs))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function iMainQuery($SQL,&$rs)
	{
		$rs=$this->wpdb->query($SQL);
		if($this->is_debug==true && $this->wpdb->last_error)
		{
			echo"<p style=\"color:#cc3f44;\"><strong>Start User Custom Error:<br/> </strong>".$this->wpdb->last_error."</p><p style=\"color:#72a230;\">".$this->wpdb->last_query."<br/><strong>End User Custom Error</strong></p>";
		}
		if($this->wpdb->last_error)
		return false;
		else
		return true;
	}
}
?>