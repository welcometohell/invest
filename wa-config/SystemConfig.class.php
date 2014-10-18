<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);b 

require_once dirname(__FILE__).'/../wa-system/autoload/waAutoload.class.php';
waAutoload::register();

class SystemConfig extends waSystemConfig
{
   public function getSysOption ($name) {
      return  $this->getOption($name);
   }
}
