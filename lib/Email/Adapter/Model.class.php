<?php
interface  Email_Adapter_Model{
	
	public function send($subject,$content,$tos,$from = null);

}