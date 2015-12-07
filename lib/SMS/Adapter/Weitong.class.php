<?php

class SMS_Adapter_Weitong implements SMS_Adapter_Model{

    public $weitong;

    public function __construct(){

        $this->weitong = new Platform_Weitong();

    }

    public function sendSMS($message,$tos){

        return $this->weitong->sendSMS($message,$tos);
    }
}