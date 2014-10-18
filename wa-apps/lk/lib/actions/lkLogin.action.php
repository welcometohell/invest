<?php

/**
 * Login form action /login
 * Экшен формы логина /login
 * @see https://www.webasyst.com/framework/docs/dev/auth-frontend/
 */
class lkLoginAction extends waLoginAction
{

    public function execute()
    {
        $this->setLayout(new lkFrontendLayout());
        $this->setThemeTemplate('login.html');
        parent::execute();
    }

}