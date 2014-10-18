<?php

class lkFrontController extends waFrontController
{
    public function execute($plugin = null, $module = null, $action = null, $default = false)
    {
	$this->system->getResponse()->setTitle("Личный кабинет");
        parent::execute($plugin, $module, $action, $default);

    }

}