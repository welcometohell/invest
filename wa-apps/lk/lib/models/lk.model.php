<?php

/**
 * Model for managing the database tables of lk
 * Модель для работы с таблицами lk
 * @see http://www.webasyst.com/ru/framework/docs/dev/model/
 */
class lkModel extends waModel
{
    /**
     * @var string table name | имя таблицы
     */
	protected $table = 'lk_pay_operations';
	protected $table_types = 'lk_pay_types';
        protected $table_tariff = ' lk_pay_tariff';
        protected $table_balance = 'lk_balance';
        protected $table_ref = 'lk_referals';
        protected $table_out = 'lk_pay_out';
        
    /**
     * @TODO
     * Возвращает платежные операции пользователя
     *
     * @param varchar $login - логин пользователя
     * @return array - array of records | массив записей
     */
    public function getUserHistory ($login,$from,$per_page) 
    {
        $sql = "SELECT p.*, t.operation_name as type_name, (b.ref_balance+b.dep_balance) as balance
                FROM ".$this->table." p
                LEFT JOIN  lk_operation_type t  ON p.operation_type_id = t.id
                LEFT JOIN ".$this->table_balance." b  ON b.user_login = p.login
		WHERE login = s:login  AND p.operation_type_id <> '1' AND p.operation_type_id <> '3'
                ORDER BY p.date DESC LIMIT i:from,i:to";
        return $this->query($sql, array('login' => $login,'from' =>$from ,'to'=>$per_page))->fetchAll();
    }
    
    public function getUserHistoryOut ($login,$from,$per_page) 
    {
        $sql = "SELECT p.*, status.name as status_name, (b.ref_balance+b.dep_balance) as balance
                FROM ".$this->table_out." p
                LEFT JOIN  lk_pay_out_status status  ON p.status = status.id
                LEFT JOIN ".$this->table_balance." b  ON b.user_login = p.login
		WHERE login = s:login
                ORDER BY p.date DESC LIMIT i:from,i:to";
        return $this->query($sql, array('login' => $login,'from' =>$from ,'to'=>$per_page))->fetchAll();
    }
    
    /**
     * 
     * Возвращает массив тарифов
     *
     * 
     * @return array - array of records | массив тарифов
     */
    public function getTariffs () 
    {
        $sql = "SELECT *
                FROM ".$this->table_tariff." 
		WHERE 1";
        return $this->query($sql)->fetchAll();
    }
    
     /**
     * 
     * Возвращает данные тарифа
     *
     * @param int $id - id тарифа
     * @return array - array of records | массив тарифов
     */
    public function getTariff ($id) 
    {
        $sql = "SELECT *
                FROM ".$this->table_tariff." 
		WHERE id=s:id";
        return $this->query($sql,array('id'=>$id))->fetch();
    }
    
    public function  getAllBalance() 
    {
        $sql = "SELECT (SUM(b.ref_balance) + SUM(b.dep_balance)) as balance
                FROM  lk_balance b
		WHERE 1";
        return $this->query($sql)->fetchField("balance");
    }
    
    
    public function  doOperation($percent) 
    {
        $allusers = $this->getUsersWithBalance();
        foreach ($allusers as $user) {
            $this->plusPercent($user,$percent);
        }
        return true;
    }
    
    public function  plusPercent($user,$percent) 
    {
        $balance = $this->getBalance($user['user_login']);
        $userr = new waContact($user['user_id']);
        $CrDT = new DateTime($userr->get('create_datetime'));
        $nowDT = new DateTime(date("Y-m-d H:i:s",time()));
        $CrDT->modify('+1 day');
        if ($CrDT<=$nowDT) {
            $plus = $balance/100*$percent;
            $sql = "UPDATE  lk_balance SET `dep_balance`=`dep_balance`+f:plus WHERE `user_login`=s:login";
            $this->query($sql, array('plus'=>$plus,'login'=>$user['user_login']));
            $user['id'] = $user['user_id'];
            $user['login'] = $user['user_login'];
            $this->AddPayOperation($user,$plus,1,1,1,"",$percent);
        }
    }
    
    
    
    public function getUsersWithBalance() 
    {
        $sql = "SELECT b.* , c.login as clogin
                FROM  lk_balance b
                LEFT JOIN wa_contact c ON b.user_id = c.id
		WHERE c.login IS NOT NULL ";
        return $this->query($sql)->fetchAll();
    }
    
  public function getLastPercent()
    {
        $sql = "SELECT *
                FROM  lk_pay_operations WHERE operation_type_id = '1'
		ORDER BY date DESC LIMIT 1";
        return $this->query($sql)->fetch();
    }
    
    public function getAllPercent() {
        //$sql = "SELECT SUM(percent) as percent
        //        FROM lk_pay_operations 
	//	WHERE operation_type_id='1' GROUP BY date";
        //return $this->query($sql)->fetchField("percent");
    }
    
    
    /**
     * 
     * Возвращает общее количество платежных операций
     * @param varchar $login - логин пользователя
     * @return int - количество платежных операций
     */
    public function getCountPayOperationsByLogin($login)
    {
        $sql = "SELECT COUNT(p.id) as count
                FROM ".$this->table." p
		WHERE  p.login=s:login  AND p.operation_type_id <> '1'
                ORDER BY p.date DESC";
        return $this->query($sql,array("login"=>$login))->fetch();
    }
    
    public function getCountPayOperationsOutByLogin($login)
    {
        $sql = "SELECT COUNT(p.id) as count
                FROM ".$this->table_out." p
		WHERE  p.login=s:login 
                ORDER BY p.date DESC";
        return $this->query($sql,array("login"=>$login))->fetch();
    }
    
    /**
     * 
     * Возвращает платежные операции
     *
     * @param int $from - начальная запись для пагинатора
     * @param int $per_page - количество записей для пагинатора
     * @return array - array of records | массив записей
     */
    public function getPayOperations ($from,$per_page) 
    {
        $sql = "SELECT p.*, t.operation_name as type_name, (b.ref_balance+b.dep_balance) as balance
                FROM ".$this->table." p
                LEFT JOIN lk_operation_type t  ON p.operation_type_id = t.id
                LEFT JOIN ".$this->table_balance." b  ON b.user_login = p.login
		WHERE (p.status = '1' OR p.status = '3')  AND p.operation_type_id <> '1'
                ORDER BY p.date DESC LIMIT i:from,i:to";
        return $this->query($sql, array('from' =>$from ,'to'=>$per_page))->fetchAll();
    }
    
    public function getPayOperationsOut ($from,$per_page) 
    {
        $sql = "SELECT p.*, status.name as status_name, (b.ref_balance+b.dep_balance) as balance
                FROM ".$this->table_out." p
                LEFT JOIN lk_pay_out_status status  ON status.id = p.status
                LEFT JOIN ".$this->table_balance." b  ON b.user_login = p.login
                    WHERE 1
                ORDER BY p.date DESC LIMIT i:from,i:to";
        return $this->query($sql, array('from' =>$from ,'to'=>$per_page))->fetchAll();
    }
    /**
     * 
     * Возвращает общее количество платежных операций
     *
     * @return int - количество платежных операций
     */
    public function getCountPayOperations()
    {
        $sql = "SELECT COUNT(p.id) as count
                FROM ".$this->table." p
		WHERE p.status = '1' AND p.operation_type_id <> '1'
                ORDER BY p.date DESC";
        return $this->query($sql)->fetch();
    }
    
    public function getCountPayOperationsOut()
    {
        $sql = "SELECT COUNT(p.id) as count
                FROM ".$this->table_out." p
		WHERE 1
                ORDER BY p.date DESC";
        return $this->query($sql)->fetch();
    }
    
    
    public function getUserIdByLogin ($login) 
    {
        $sql = "SELECT id
                FROM wa_contact 
		WHERE login = s:login";
        return $this->query($sql, array('login' => $login))->fetchField("id");
    }
    
    public function GetInSumm () {
            $sql = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table." p
                    WHERE p.status = '1' AND p.operation_type_id='2'";
            return $this->query($sql)->fetchField("summ");
    }
    // сегодня
    public function GetInSummToday () {
            $sql = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table." p
                   WHERE p.status = '1' AND p.operation_type_id='2' AND p.date >= CURDATE()";
            
            
            
            return $this->query($sql)->fetchField("summ");
    }
    
    // вчера
    public function GetInSummAfter () {
            $sql = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table." p
                    WHERE p.status = '1'  AND p.operation_type_id='2'  AND p.date >= (CURDATE()-1) AND p.date < CURDATE()";
            return $this->query($sql)->fetchField("summ");
    }
    
    
    /** @TODO */
    public function GetOutSumm ($date="") {
        if (!$date) {
            $sql = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table_out." p
                    WHERE p.status = '2'";
            $order = $this->query($sql)->fetchField("summ");
            $sql2 = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table." p
                    WHERE  p.operation_type_id='3' AND p.status = '1'";
            $order2 = $this->query($sql2)->fetchField("summ");
            
            return $order+$order2;
        }
    }
    // сегодня
    public function GetOutSummToday ($date="") {
        if (!$date) {
            $sql = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table_out." p
                    WHERE p.status = '2' AND p.date >= CURDATE()";
            $order = $this->query($sql)->fetchField("summ");
            
            $sql2 = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table." p
                    WHERE  p.operation_type_id='3' AND p.status = '1'  AND p.date >= CURDATE()";
            $order2 = $this->query($sql2)->fetchField("summ");
            
            return $order+$order2;
        }
    }
    // вчера
    public function GetOutSummAfter ($date="") {
        if (!$date) {
            $sql = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table_out." p
                    WHERE p.status = '2' AND p.date >= (CURDATE()-1) AND p.date < CURDATE()";
            $order = $this->query($sql)->fetchField("summ");
            
            $sql2 = "SELECT SUM(p.summ) as summ
                    FROM ".$this->table." p
                    WHERE  p.operation_type_id='3' AND p.status = '1' AND p.date >= (CURDATE()-1) AND p.date < CURDATE()";
            $order2 = $this->query($sql2)->fetchField("summ");
            
            return $order+$order2;
        }
    }
     /**
     * 
     * Добаляет новую платежную операцию
     *
     * @param 
     * @return bool
     */
    public function AddPayOperation ($user,$summ,$type,$merchant,$status,$hash/*,$tariff*/) 
    {
        if (is_array($user)) {
            $login = $user['login'];
            $user_id = $user['id'];
        }
        elseif ($user instanceof waAuthUser) {
            $login = $user->getLogin();
            $user_id = $user->getId();
        }
        /*$tariff_data = $this->getTariff($tariff);*/
        $deposite_next = date("Y-m-d H:i:s",time()+$tariff_data['period']*24*60*60);
        $sql = "INSERT INTO ".$this->table." (`login`,`user_id`,`summ`,`deposite_next`,`operation_type_id`,`merchant_id`,`status`,`date`,`hash`) VALUES (s:login,i:user_id,f:summ,s:deposite_next,i:operation_type_id,i:merchant,i:status,s:date,s:hash)"; /*,f:tariff)";*/
        return $this->query($sql, array('login'=>$login,'user_id'=>$user_id,'summ'=>$summ,'operation_type_id'=>$type,'merchant'=>$merchant,'status'=>$status,'date'=> date("Y-m-d H:i:s"),'hash' => $hash,/*'tariff' => $tariff,*/'deposite_next' =>$deposite_next));
    }
     
    public function AddPayOperationOut ($user,$summ,$account,$merchant_id,$status) 
    {
        if (is_array($user)) {
            $login = $user['login'];
            $user_id = $user['id'];
        }
        elseif ($user instanceof waAuthUser) {
            $login = $user->getLogin();
            $user_id = $user->getId();
        }
        $sql = "INSERT INTO ".$this->table_out." (`login`,`user_id`,`summ`,`account`,`merchant_id`,`status`,`date`) VALUES (s:login,i:user_id,f:summ,s:account,i:merchant_id,i:status,s:date)";
        return $this->query($sql, array('login'=>$login,'user_id'=>$user_id,'summ'=>$summ,'account'=>$account,'merchant_id'=>$merchant_id,'status'=>$status,'date'=> date("Y-m-d H:i:s"),'hash' => $hash,'tariff' => $tariff,'deposite_next' =>$deposite_next,'date'=>date("Y-m-d H:i:s")));
    }
    
    public function ProcessPayOperationOut ($id) 
    {
        $sql = "UPDATE ".$this->table_out." SET `status`=i:status, `processed_date`=i:processed_date WHERE `id`=s:id";
        return $this->query($sql, array('status'=>2,'processed_date'=>date("Y-m-d H:i:s"),'id'=>$id));
    }
    
    public function ArchivePayOperationOut ($id) 
    {
        $sql = "UPDATE ".$this->table_out." SET `status`=i:status, `processed_date`=i:processed_date WHERE `id`=s:id";
        return $this->query($sql, array('status'=>3,'processed_date'=>date("Y-m-d H:i:s"),'id'=>$id));
    }
    
     /**
     * 
     * Возвращает массив всех предстоящих атоматических выплат по переданной платежной системе
     *
     * @param string $merchant - платежная система (принятый в системе строковый эквивалент - liberty, perfect)
     * @return array -  массив всех предстоящих выплат
     */
    public function getPayments ($merchant) 
    {
	if ($merchant == "perfect") $merchant = 1;
        $sql = "SELECT op.*, tar.persent as percent  FROM ".$this->table." op LEFT JOIN ".$this->table_tariff." tar ON op.tariff_id=tar.id WHERE op.`merchant_id`=s:merchant AND op.`operation_type_id`='2'  AND op.`status`='1'";
        return $this->query($sql, array('merchant'=>$merchant))->fetchAll();
    } 
     /**
     * @TODO
     * Выставить платежной операции статус
     *
     * @param int $status - статус
     * @param string $batch - поле hash операции
     * @param string $login - логин пользователя
     * @return bool
     */
    public function setPayOperationStatus ($status,$hash,$batch,$login) 
    {
        $sql = "UPDATE ".$this->table." SET `status`=i:status, `batch`=i:batch WHERE `hash`=s:hash AND `login`=s:login";
        return $this->query($sql, array('status'=>$status,'hash'=>$hash,'login'=>$login,'batch'=>$batch));
    }
    
    public function updatePayOperationStatus ($status,$id) 
    {
        $sql = "UPDATE ".$this->table." SET `status`=i:status WHERE `id`=s:id";
        return $this->query($sql, array('status'=>$status,'id'=>$id));
    }
     /**
     * 
     * Возвращает историю операций для пользователя
     *
     * @param string $login - логин пользователя
     * @return array - Массив операций по вкладам
     */
    public function getDeposite ($login) 
    {
        $sql = "SELECT op.*, status.status_name, type.operation_name, merchant.merchant_name, tariff.name as tariff_name  FROM ".$this->table." op 
            LEFT JOIN lk_status_name status ON op.status=status.id 
            LEFT JOIN  lk_operation_type type ON op.operation_type_id=type.id 
            LEFT JOIN  lk_merchant_name merchant ON op.merchant_id=merchant.id 
            LEFT JOIN  lk_pay_tariff tariff ON op.tariff_id=tariff.id
            WHERE op.`login`=s:login ORDER BY op.`date` DESC";
        return $this->query($sql, array('login'=>$login))->fetchAll();
    }
    
    public function getOut ($login) 
    {
        $sql = "SELECT op.*, status.name as status_name, merchant.merchant_name FROM ".$this->table_out." op 
            LEFT JOIN lk_pay_out_status status ON op.status=status.id 
            LEFT JOIN  lk_merchant_name merchant ON op.merchant_id=merchant.id 
            WHERE op.`login`=s:login ORDER BY op.`date` DESC";
        return $this->query($sql, array('login'=>$login))->fetchAll();
    }
    
    
     /**
     * 
     * Добаляет к реферальному балансу пригласителей их проценты от суммы вклада
     *
     * @param string $login - логин реферала, сделавшего вклад
     * @param float $summ - сумма вклада
     * @return bool - Удачность запроса
     */
    public function setRefBalanceToLevels ($login, $summ) 
    {
        $level1_summ = $summ*0.05;
        $level2_summ = $summ*0.03;
        $level3_summ = $summ*0.02;
        $ok1 = $ok2 = $ok3 = 1;
        // апдэйт  пригласителей
            // найдем пригласителя 1 уровня
            $sub_sql1 = "SELECT ref.user_login FROM ".$this->table_ref." ref  WHERE ref.`ref_login`=s:login";
                // найдем пригласителя 2 уровня
                $sub_sql2 = "SELECT ref1.user_login FROM ".$this->table_ref." ref JOIN ".$this->table_ref." ref1 ON ref.user_login=ref1.ref_login WHERE ref.`ref_login`=s:login";
                    // найдем пригласителя 3 уровня
                    $sub_sql3 = "SELECT ref2.user_login FROM ".$this->table_ref." ref JOIN ".$this->table_ref." ref1 ON ref.user_login=ref1.ref_login JOIN ".$this->table_ref." ref2 ON ref1.user_login=ref2.ref_login WHERE ref.`ref_login`=s:login";
        if ($u_login1 = $this->query($sub_sql1, array('login'=>$login))->fetch()) {
            $user_id1 = $this->getUserIdByLogin($u_login1[0]);    
            $sql1 = "".
                " INSERT INTO ".$this->table_balance." (`user_id`,`user_login`,`ref_balance`) VALUES ".
                " (s:user_id,s:login,".$this->table_balance.".`ref_balance`+f:level1_summ) ".
                " ON DUPLICATE KEY UPDATE ".$this->table_balance.".ref_balance = ".$this->table_balance.".`ref_balance`+f:level1_summ";
            $ok1 = $this->query($sql1, array('level1_summ'=>$level1_summ,'login'=>$u_login1[0],'user_id'=>$user_id1));
            
            $sql_11 = "".
                " UPDATE ".$this->table_ref." SET summ = summ+f:level1_summ WHERE `ref_login`=s:login";
            $this->query($sql_11, array('level1_summ'=>$level1_summ,'login'=>$login));
        }
        if ($u_login2 = $this->query($sub_sql2, array('login'=>$login))->fetch()) {
            $user_id2 = $this->getUserIdByLogin($u_login2[0]); 
            $sql2 = "".
                " INSERT INTO ".$this->table_balance." (`user_id`,`user_login`,`ref_balance`) VALUES ".
                " (s:user_id,s:login,".$this->table_balance.".`ref_balance`+f:level2_summ) ".
                " ON DUPLICATE KEY UPDATE ".$this->table_balance.".ref_balance = ".$this->table_balance.".`ref_balance`+f:level2_summ";
            $ok2 = $this->query($sql2, array('level2_summ'=>$level2_summ,'login'=>$u_login2[0],'user_id'=>$user_id2));
        }
        if ($u_login3 = $this->query($sub_sql3, array('login'=>$login))->fetch()) {
            $user_id3 = $this->getUserIdByLogin($u_login3[0]); 
            $sql3 = "".
                " INSERT INTO ".$this->table_balance." (`user_id`,`user_login`,`ref_balance`) VALUES ".
                " (s:user_id,s:login,".$this->table_balance.".`ref_balance`+f:level3_summ) ".
                " ON DUPLICATE KEY UPDATE ".$this->table_balance.".ref_balance = ".$this->table_balance.".`ref_balance`+f:level3_summ";
            $ok3 = $this->query($sql3, array('level3_summ'=>$level3_summ,'login'=>$u_login3[0],'user_id'=>$user_id3));
        }
        if ($ok1 && $ok2 && $ok3) return true;
        else return false;
    }
    
     /**
     * 
     * Добаляет к депозит-балансу пользователя переданную сумму
     *
     * @param string $login - логин пользователя
     * @param float $plus - сумма, которую нужно добавить
     * @return bool
     */
    public function updateDepBalance ($user_id,$user_login,$plus) 
    {
        $sql = "".
                " INSERT INTO ".$this->table_balance." (`user_id`,`user_login`,`dep_balance`) VALUES ".
                " (s:user_id,s:login,f:plus) ".
                " ON DUPLICATE KEY UPDATE ".$this->table_balance.".dep_balance = ".$this->table_balance.".`dep_balance`+f:plus";
        return $this->query($sql, array('user_id'=>$user_id,'login'=>$user_login,'plus'=>$plus));
    }
    
     /**
     * 
     * Возвращает баланс пользователя по логину
     *
     * @param string $login - логин пользователя
     * @return float - Баланс
     */
    public function getBalance ($login) 
    {
        $sql = "SELECT (ref_balance+dep_balance) as balance FROM ".$this->table_balance." WHERE `user_login`=s:login";
        return $this->query($sql, array('login'=>$login))->fetchField("balance");
    }
    
     /**
     * 
     * Добаляет реферала пользователю
     *
     * @param string $login - логин пользователя
     * @param string $plogin - логин пригласившего
     * @return bool - результат выполнения запроса
     */
    public function setReferal ($login,$plogin) 
    {	
        $sql = "INSERT INTO ".$this->table_ref." (user_login,ref_login,date) VALUES (s:plogin,s:login,s:date)";
        return $this->query($sql, array('plogin'=>$plogin,'login'=>$login,'date'=>date("Y-m-d H:i:s")));
    }
    
     /**
     * 
     * Возвращает массив рефералов (логинов) пользователя
     *
     * @param string $login - логин пользователя
     * @return array - массив рефералов (логинов) пользователя
     */
    public function getReferal ($login) 
    {
        $sql = "SELECT ref_login, summ FROM ".$this->table_ref." WHERE user_login=s:login ";
        return $this->query($sql, array('login'=>$login))->fetchAll();
    }
    
    public function getRefBalance ($login) 
    {
        $sql = "SELECT ref_balance FROM ".$this->table_balance." WHERE user_login=s:login ";
        return $this->query($sql, array('login'=>$login))->fetch();
    }
    

	function GetCountUsers() {
        $sql = "SELECT COUNT(id) as count FROM  wa_contact WHERE 1";
        $users = $this->query($sql)->fetch();
		return $users['count'];
	}

	function GetLastUser() {
        $sql = "SELECT `login` FROM `wa_contact` WHERE `create_app_id`='lk' ORDER BY `create_datetime` DESC LIMIT 1";
        $users = $this->query($sql)->fetch();
		return $users['login'];
	} 
	function GetLastIn() {
        $sql = "SELECT `summ`, `login` FROM ".$this->table." WHERE `status`='2' AND `type`='2' ORDER BY `date` DESC LIMIT 1";
        $users = $this->query($sql)->fetch();
		return $users['summ']."$ (".$users['login'].")";
	}
        
	function GetStat () {
            $sql = "SELECT * FROM ".$this->table." WHERE operation_type_id='2' AND merchant_id='1' AND status='1'";
            $users = $this->query($sql)->fetchAll();
		$i = 1;

		$sDate1 = date("Y-m-d H:i:s",time());
		$sconf = new SystemConfig();
		$sconf->init();
		$sDate2 =  $sconf->getSysOption('date_start');
		$f_d = new DateTime($sDate2);
		$l_d = new DateTime($sDate2);
		$l_d->modify('+1 hour');
		$sDate1 = date("Y-m-d H:i:s",time());
		$sconf = new SystemConfig();
		$sconf->init();
		$d = new DateTime($sDate1);
                $razn = $d->diff( new DateTime($sDate2) );
                $hours1 = $razn->days*24;
		$hours = $d->diff( new DateTime($sDate2) )->format("%h")+1;
		$hours = $hours + $hours1;
		for ($i=1;$i<=(int)$hours;$i++) {
			foreach ($users as $dep) {
				$cur_date = new DateTime($dep['date']);
                                //echo "cur_date:".$cur_date->format("Y-m-d H:i:s").",f_d:".$f_d->format("Y-m-d H:i:s").",l_d:".$l_d->format("Y-m-d H:i:s")."<br>";
				if ($cur_date>=$f_d && $cur_date<=$l_d) {
					$result[$i] = $result[$i]+$dep['summ'];
				}
			}
			$f_d->modify('+1 hour');
			$l_d->modify('+1 hour');
		}
                $f_d = new DateTime($sDate2);
		$l_d = new DateTime($sDate2);
		$l_d->modify('+1 hour');
                    $sql = "SELECT * FROM ".$this->table." WHERE operation_type_id='3' AND merchant_id='1' AND status='1'";
                    $users = $this->query($sql)->fetchAll();
                    
		for ($i=1;$i<=(int)$hours;$i++) {
			foreach ($users as $dep) {
				$cur_date = new DateTime($dep['date']);
                                //echo "cur_date:".$cur_date->format("Y-m-d H:i:s").",f_d:".$f_d->format("Y-m-d H:i:s").",l_d:".$l_d->format("Y-m-d H:i:s")."<br>";
				if ($cur_date>=$f_d && $cur_date<=$l_d) {
					$result1[$i] = $result1[$i]+$dep['summ'];
				}
			}
			$f_d->modify('+1 hour');
			$l_d->modify('+1 hour');
		}
                $result['invest'] = $result;
                $result['out'] = $result1;
		return $result;
	}
        
}