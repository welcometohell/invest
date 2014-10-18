<?php

/**
 * Password recovery action /forgotpassword
 * Экшен восстановления пароля /forgotpassword
 * @see https://www.webasyst.com/framework/docs/dev/auth-frontend/
 */
class lkForgotpasswordAction extends waForgotPasswordAction
{
    public function execute()
    {
        $this->setLayout(new lkFrontendLayout());
        $this->setThemeTemplate('forgotpassword.html');
        parent::execute();
    }
}