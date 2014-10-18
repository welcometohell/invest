<?php
class lkViewHelper extends waAppViewHelper
{
	function getStat() {
		// статистика
		// дейсвия по начислению с депозитов
		$model = new lkModel();
		/*$perfect_payments = $model->getPayments(1);
		// расплатимся с перфектом
		foreach ($perfect_payments as $payment) {
			if (strtotime($payment['deposite_next'])<=time()) {
				$deposite_summ = $payment['percent']/100*$payment['summ'];
                                $deposite_summ = round($deposite_summ, 2);
				$model->updateDepBalance($payment['user_id'],$payment['login'],$deposite_summ);
                                $user = array('id'=>$payment['user_id'],'login'=>$payment['login']);
                                $model->AddPayOperation ($user,$deposite_summ,1,1,1,'','');
                                $model->updatePayOperationStatus(3,$payment['id']);
			}
		}*/
		$model = new lkModel();
		$users = $model->GetCountUsers();
		$sDate1 = date("Y-m-d H:i:s",time());
		$sDate2 =  "2013-08-02 17:00:00";
                $sDate3 =  "2014-09-03 23:30:00";
		$d = new DateTime($sDate1);
		$interval = $d->diff( new DateTime($sDate2) );
                $hours = $interval->y*365*24 + $interval->m*30*24 + $interval->d*24 + $interval->h;
                $plus_people = $hours*2; // по две регистрации в час с 16.00 2-го мая
                $plus_people1 = (int)$hours*1.9; // по две регистрации в час с 16.00 2-го мая
                // прибавляем сумму
                $interval1 = $d->diff( new DateTime($sDate3) );
                $hours = $interval1->y*365*24 + $interval1->m*30*24 + $interval1->d*24 + $interval1->h;
                $plus_in = $hours * 150;
           
                $in = $model->GetInSumm();
                $out = $model->GetInSumm();
		return array('users'=>$users+$plus_people,"users1"=>(int)$plus_people1,"invest"=>$in+2266+$plus_in,"out"=>$out);
	}
        
        function isStart () {
            $nowDate = new DateTime(date("Y-m-d H:i:s",time()));
		$sconf = new SystemConfig();
		$sconf->init();
            $startDate = new DateTime( $sconf->getSysOption('date_start'));
            $number1 = (int)$nowDate->format('U');
            $number2 = (int)$startDate->format('U');
            return (int)($number2 - $number1)/60;
        }
        
        function getMenu() {
            $menu = include wa()->getConfig()->getConfigPath('sidebar.php',true,'lk');
            $params = waRequest::param();
            foreach ($menu as $key=>$child) {
                $request_parts = explode("/",$child['id']);
                if ($params['app'] == $request_parts['1']) {
                    if (!isset($params['module']) && !$request_parts['2']) {
                        $menu[$key]['active'] = 1;
                        break;
                    }
                    elseif (isset($params['module']) && isset($request_parts['2'])) {
                        if ($params['module'] == $request_parts['2']) {
                            if (!isset($params['action']) && !isset($request_parts['3'])) {
                                $menu[$key]['active'] = 1;
                                break;
                            }
                            elseif(isset($params['action']) && isset($request_parts['3']) && $params['action'] == $request_parts['3']) {
                                $menu[$key]['active'] = 1;
                                break;
                            }
                        }
                    }
                }
            }
            return $menu;
        }
}
