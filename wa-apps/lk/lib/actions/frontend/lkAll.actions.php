<?php 

/**
*
 */
class lkAllActions extends waViewActions
{ 
         // Этот метод вызывается до выполнения любого экшена 
        public function preExecute()
        {
            $this->setLayout(new lkFrontendLayout());
        }
	public function defaultAction()
	{
                $this->setThemeTemplate('alldefault.html');
		$this->getResponse()->setTitle("Депозиты");
		$user = $this->getUser();
		if ($user->getId()) {
                    $model = new lkModel();
                    $operations = $model->getDeposite($user->getLogin());
                    $this->view->assign('operations', $operations);
		}
	}
	
	
	public function userAction()
	{
		$this->getResponse()->setTitle("Данные пользователя");
                $this->setThemeTemplate('AllUser.html');
		$user = $this->getUser();
                $this->view->assign('user', $user);
	}
        
        public function outAction()
	{
            $summ_to_min =(float)waRequest::post("summ");
            $summ = round($summ_to_min, 1);
                    $ok = "";
                    $error_all = "";
            $this->getResponse()->setTitle("Запрос на вывод стредств");
            $this->setThemeTemplate('AllOut.html');
            $user = $this->getUser();
            $contact = new waContact($user->getId());
            $main_config = include wa()->getConfig()->getAppConfigPath('main');
            $model = new lkModel();
            
            if ($summ) {
		if ($summ<0)
                    $error_all = "Введеное значение не должно быть отрицательным!";
		if ((int)$summ<50) 
                    $error_all = "Нельзя вывести сумму меньше 50 USD!";
		if ((float)$model->getBalance($user->get("login"))<(float)$summ)
                     $error_all = "Сумма для вывода должна быть меньше либо равна вашему балансу!";
		if (preg_match("/^U\d{7}$/", $user->get('perfect')))echo "";
		else 
                    $error_all = "Введеные вами при регистрации кошелек Perfect Money не соответсвует формату! Изменить можно в разделе 'Персональные данные'";  
            }
            if ($summ <= 300 && !$error_all) {
		$user = $this->getUser();
                    if ($user->getId()) {
                        if ($summ>=50 && $model->getBalance($user->getLogin())>=$summ) {
                            if (waRequest::post("merchant")=="perfect") {
                                // trying to open URL to process PerfectMoney Spend request
                                $url = 'https://perfectmoney.is/acct/confirm.asp?AccountID='.$main_config['perfect_id'].'&PassPhrase='.$main_config['perfect_password'].'&Payer_Account='.$main_config['perfect'].'&Payee_Account='.$user->get('perfect').'&Amount='.str_replace(",", ".", $summ);
                                //echo $url;
                                // 1. инициализация
                                $ch = curl_init();
                                // 2. указываем параметры, включая url
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_HEADER, 0);
                                // 3. получаем HTML в качестве результата
                                $output = curl_exec($ch);
                                // 4. закрываем соединение
                                curl_close($ch);
                                // getting data
                                if (!$output) {
                                    $outputt="";
                                    $error_all = "Нет доступа к сервису Perfect money либо ошибка в файле конфигурации";
                                }else {
                                    // searching for hidden fields
                                    if(!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $output, $result, PREG_SET_ORDER)){
                                        echo 'Ivalid output';
                                        exit;
                                    }
                                    $ar="";
                                    foreach($result as $item){
                                        $key=$item[1];
                                        $ar[$key]=$item[2];
                                    }
                                    if ($ar['ERROR'])
                                        $error_all = $ar['ERROR'];
                                    else {
                                        $this->view->assign('error_all', $error_all);
                                        $model->updateDepBalance($user->getId(),$user->getLogin(),$summ*-1);
                                        $model->AddPayOperation($user,$summ,3,1,1,"");
                                        $ok = "Вы успешно вывели ".$summ." USD";
                                    }
                                }
                            }
                        }
                         $this->view->assign('error_all', $error_all);
                         $this->view->assign('ok', $ok);
                    }
            }elseif ($summ > 300) {
                    if ($user->getId()) {
                        $ok = "";
                        $error_all = "";
                        $main_config = include wa()->getConfig()->getAppConfigPath('main');
                        $model = new lkModel();
                        $summ_to_min =(float)waRequest::post("summ");
                        $summ = round($summ_to_min, 1);
                        if ($summ) {
                            if ($summ<0)
                                $error_all = "Введеное значение не должно быть отрицательным!";
                            if ((int)$summ<50) 
                                $error_all = "Нельзя вывести сумму меньше 50 USD!";
                            if ((float)$model->getBalance($user->get("login"))<(float)$summ)
                                $error_all = "Сумма для вывода должна быть меньше либо равна вашему балансу!";
                            if (preg_match("/^U\d{7}$/", $user->get('perfect')))echo "";
                            else 
                            $error_all = "Введеные вами при регистрации кошелек Perfect Money не соответсвует формату! Изменить можно в разделе 'Персональные данные'"; 
                            if (!$error_all) {
                                $model->updateDepBalance($user->getId(),$user->getLogin(),$summ*-1);
                                $model->AddPayOperationOut($user, $summ, $contact->get('perfect'), 1, 1);
                                $ok = "Ваша заявка на вывод ".$summ." USD принята и будет обработана в ближайшее время!";
                            }
                        }
                    }
            }
                         $this->view->assign('error_all', $error_all);
                         $this->view->assign('ok', $ok);
	}
        
	public function listoutAction()
	{
                $this->setThemeTemplate('alloutlist.html');
		$this->getResponse()->setTitle("Заявки на вывод");
		$user = $this->getUser();
		if ($user->getId()) {
                    $model = new lkModel();
                    $operations = $model->getOut($user->getLogin());
                    $this->view->assign('operations', $operations);
		}
	} 
        
        public function inAction()
	{
		$this->getResponse()->setTitle("Сделать вклад");
                $this->setThemeTemplate('AllIn.html');
                $model = new lkModel();
		$user = $this->getUser();
                /*$tariff = $model->getTariffs(); */// список тарифов
		if ($user->getId()) { 
                    // извлекаем данные из post
                    $summ = waRequest::post("summ");
                    $merchant = waRequest::post("merchant");
                    /*$tariff_id = waRequest::post("tariff");*/
                    
                    if ($merchant && $summ/* && $tariff_id*/) {
                        // усли все параметры платежа присланы, то проверим сумму на соответствие пределам тарифа
                       /*$tariff_data = $model->getTariff($tariff_id);*/
                        if ((float)$summ >= 20 && (float)$summ<=1000) {
                            // все в нужных пределах, продолжим
                            if ($merchant == 1) {
                                $perfect = 1;
                                $mercant_name = "perfect";
                            }
                            elseif ($merchant == 2) {
                                $liberty = 1;
                                $mercant_name = "liberty";
                            }
                            $main_config = include wa()->getConfig()->getAppConfigPath('main');
                            $payee_account = $main_config[$mercant_name]; // PAYEE_ACCOUNT
                            $batch = time();
                            // тут если что логика зачисления
                            $amount = (float)$summ;
                            // запишем платежную операцию
                            $model->AddPayOperation($user,$amount,2,$merchant,2,$batch/*,$tariff_data['id']*/);       
                        }
                        elseif ((float)$summ < 20)
                              $error_all = "Сумма взноса меньше минимальной (20 USD)!";
                        elseif ((float)$summ > 1000)
                              $error_all = "Сумма взноса больше максимальной (1000 USD)!";
                    }
                    else {
                        if (waRequest::post("merchant") && !waRequest::post("summ")) $error = "Вы не указали сумму взноса!";
                    }
                    
                    // передаем переменные в шаблон
                    $this->view->assign('param', waRequest::param());
                    $this->view->assign('tariff', $tariff);
                    $this->view->assign('error', $error);
                    $this->view->assign('error_all', $error_all);
                    $this->view->assign('summ', $summ);
                    $this->view->assign('merchant', $merchant);
                    $this->view->assign('perfect', $perfect);
                    $this->view->assign('payee_account', $payee_account);
                    $this->view->assign('amount', $amount);
                    $this->view->assign('sitename', $main_config['name']);
                    $this->view->assign('payment_id', $batch);  
                    
		}
                $this->view->assign('server', $_SERVER['SERVER_ADDR']);
	}
	
        public function reinvestAction()
	{
            /*
		$this->getResponse()->setTitle("Сделать вклад с баланса системы");
		$user = $this->getUser();
		if ($user->getId()) {
                    $model = new lkModel();
                    $tariff = $model->getTariffs(); // список тарифов
                    // извлекаем данные из post
                    $summ = waRequest::post("summ");
                    $tariff_id = waRequest::post("tariff");
                    $merchant = 3;
                    if ($model->getBalance($user->getLogin())>=$summ)
			$ok = 1;
                    if ($merchant && $summ && $tariff_id && $ok) {
                        // усли все параметры платежа присланы, то проверим сумму на соответствие пределам тарифа
                        $tariff_data = $model->getTariff($tariff_id);
                        if ((float)$summ >= (float)$tariff_data['min'] && (float)$summ<=(float)$tariff_data['max']) {
                            // все в нужных пределах, продолжим
                            // вычтем из баланса
			    $model->updateDepBalance($user->getLogin(),$summ*-1);
			    // запишем платежную операцию
                            $model->AddPayOperation($user->getLogin(),$summ,2,$tariff_id,$merchant,1,"");  
                        }
                        elseif ((float)$summ <= (float)$tariff_data['min'])
                              $error_all = "Сумма взноса меньше минимальной!";
                        elseif ((float)$summ >= (float)$tariff_data['max'])
                              $error_all = "Сумма взноса больше максимальной!";
                    }
		    
                    elseif ($tariff_id && !waRequest::post("summ")) $error = "Вы не указали сумму взноса!";
                    elseif (!$ok) $error_all = "Сумма взноса не может быть больше баланса!";
                    
                    // передаем переменные в шаблон
                    $this->view->assign('param', waRequest::param());
                    $this->view->assign('tariff', $tariff);
                    $this->view->assign('error', $error);
                    $this->view->assign('error_all', $error_all);
                    $this->view->assign('summ', $summ);
                    $this->view->assign('merchant', $merchant);
                    $this->view->assign('summ', $summ);      
		}*/
	}
	public function refAction() {
	    $this->getResponse()->setTitle("Реферальная программа"); 
            $this->setThemeTemplate('AllRef.html');
	    $user = $this->getUser();
	    if ($user->getId()) {
		$model = new lkModel();
	        $referals = $model->getReferal($user->get("login"));
                $ref_balance  = $model->getRefBalance($user->get("login"));
		if ($referals[0]) {
		    foreach ($referals as $ref) {
			$result[] = array('login'=>$ref['ref_login'],'summ'=>$ref['summ']);
		    }
		    $this->view->assign("referals", $result);
                    $this->view->assign("ref_balance", $ref_balance);
                    
		}
		$protocol = substr(waRequest::server("SERVER_PROTOCOL"),0,strpos(waRequest::server("SERVER_PROTOCOL"),"/"));
		$host = waRequest::server("HTTP_HOST");
		$this->view->assign("host", strtolower($protocol)."://".$host);
	   }
	}
}