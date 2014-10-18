<?php 

/**
 * Default frontend action
 * Read more about actions and controllers at
 * http://www.webasyst.com/framework/docs/dev/controllers/
 * 
 * Read more about request routing in frontend at
 * http://www.webasyst.com/framework/docs/dev/routing/
 *
 * Экшен фронтенда по умолчанию
 * Подробнее о экшенах и контроллерах:
 * http://www.webasyst.com/ru/framework/docs/dev/controllers/
 * 
 * Подробнее о маршрутизации во фронтенде:
 * http://www.webasyst.com/ru/framework/docs/dev/routing/
 */
class lkPayActions extends waViewActions
{
    
         // Этот метод вызывается до выполнения любого экшена
        public function preExecute()
        {
            $this->setLayout(new lkFrontendLayout());
        }

	public function defaultAction()
	{    
                // запись в лог
                ob_start();
                print_r(waRequest::post());
                $result = ob_get_clean();
                $fp = fopen("log.txt", "a"); // Открываем файл в режиме записи
                $test = fwrite($fp, $result); // Запись в файл
                fclose($fp); //Закрытие файла
                
                // а теперь серьезно
                $main_config = include wa()->getConfig()->getAppConfigPath('main');
                $model = new lkModel();
                extract(waRequest::post());
                $admin = new waContact("1");
                // если это perfect
                if (waRequest::post('V2_HASH')) {
                    $operation = $model->getByField("hash",$PAYMENT_ID);
                    // формируем hash
                    $admin_payee_account = $admin->get("perfect");
                    if ($PAYEE_ACCOUNT == $main_config['perfect']) {
                        $altpass = $main_config['altpass'];
                        $alt_hash = strtoupper(md5($altpass));
                        $str_for_hash = $PAYMENT_ID.":".$PAYEE_ACCOUNT.":".(round((float)$PAYMENT_AMOUNT,2)).":".$PAYMENT_UNITS.":".$PAYMENT_BATCH_NUM.":".$PAYER_ACCOUNT.":".$alt_hash.":".$TIMESTAMPGMT;
                        $hash = strtoupper(md5($str_for_hash));
                        
                         $fp = fopen("log.txt", "a"); // Открываем файл в режиме записи
                        $test = fwrite($fp,  $str_for_hash."\n".$hash."\nop_summ=".$operation['summ']."\npayee_account=".$main_config['perfect']."\naltpass=".$altpass); // Запись в файл
                        fclose($fp); //Закрытие файла
                        
                        if (
                            $operation && 
                            $PAYMENT_AMOUNT == $operation['summ'] && 
                            $V2_HASH == $hash && 
                            $PAYMENT_UNITS == "USD"
                        )
                        {
                            $model->setPayOperationStatus(1,$operation['hash'],$PAYMENT_BATCH_NUM,$LOGIN);
                            $model->setRefBalanceToLevels($LOGIN, $PAYMENT_AMOUNT);
                            $user_id = $model->getUserIdByLogin($LOGIN);
                            $model->updateDepBalance($user_id,$LOGIN,$PAYMENT_AMOUNT);
                        }
                    }
                }  
	}
        
        public function successAction()
	{
		$this->getResponse()->setTitle("Зачисление успешно проведено!");
                $this->setThemeTemplate('PaySuccess.html');
		if ($user = $this->getUser()) {
                        $test = "success";
			$this->view->assign('test', $test);
		}
	}

        public function failAction()
	{
		$this->getResponse()->setTitle("Платеж не был проведен!");
                $this->setThemeTemplate('PayFail.html');
		if ($user = $this->getUser()) {
			$this->view->assign('test', $test);
		}
	}
}