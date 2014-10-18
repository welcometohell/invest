<?php

/**
 * Signup action /signup
 * Экшен регистрации /signup
 * @see https://www.webasyst.com/framework/docs/dev/auth-frontend/
 */
class lkSignupAction extends waSignupAction
{
    public function execute()
    {
        // setting the frontend layout
        // устанавливаем лайаут фронтенда
            $this->setLayout(new lkFrontendLayout());
            $this->setThemeTemplate('signup.html');
        //$this->setThemeTemplate('signup.html');
        // calling the parent's method
        // запускаем выполнение родительского метода
	$ip = waRequest::server("REMOTE_ADDR");
	$this->view->assign('ip', $ip);
        parent::execute();
    }

    /**
     * This method is called upon successful creation of a new contact
     * It sends a welcome message to the new user
     *
     * Этот метод вызывается после успешного создания нового контакта
     * В нём будет отправлено приветственное письмо новому пользователю
     *
     * @param waContact $contact
     */
    public function afterSignup(waContact $contact)
    {
        $contact->addToCategory($this->getAppId());
        $email = $contact->get('email', 'default');
        if (!$email) {
            return;
        }
	$main_config = include wa()->getConfig()->getAppConfigPath('main');
        // Формируем тело письма
	$post = waRequest::post();
        $body = "<p>Вы зарегестрировали на сайте ".$main_config["name"].".</p><p>Данные для входа в систему:</p><p>Логин: ".$contact->get('login')."</p><p>Пароль: ".$post['data']['password']."</p>";
        $subject = "Регистрация на сайте ".$main_config["name"];
        // Отправляем письмо
        $message = new waMailMessage($subject, $body);
        $message->setTo($email, $contact->get('name'));
		$message->setFrom('noreply@forex-optionss.com', 'Почтовик '.$main_config["name"]);
        $message->send();
	$model = new lkModel();
        if ($contact->get("prig")) {
            $model->setReferal($contact->get("login"),$contact->get("prig"));
        }
        elseif (waRequest::cookie('ref') && waRequest::cookie('ref') != NULL) {
                $model->setReferal($contact->get("login"),waRequest::cookie('ref'));
        }
        $model->updateDepBalance($contact->getId(),$contact->get('login'),5);
    }

}