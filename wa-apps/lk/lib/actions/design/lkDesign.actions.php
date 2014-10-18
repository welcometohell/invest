<?php

class lkDesignActions extends waDesignActions
{

    public function __construct()
    {
        // check access rights
        if (!$this->getRights('design')) {
            throw new waRightsException(_ws("Access denied"));
        }
    }


    public function defaultAction()
    {
        $this->setLayout(new lkBackendLayout());
        parent::defaultAction();
    }
}