<?php 

class lkPaymentsCli extends waCliController
{

    public function execute()
    {
	$model = new lkModel();
	$perfect_payments = $model->getPayments("perfect");
	//$liberty_payments = $model->getPayments("liberty");
                // запись в лог
                ob_start();
				echo "-----------------------------------------".date("Y-m-d H:i:s",time());
               // print_r($perfect_payments);
                $result = ob_get_clean();
                $fp = fopen("log.txt", "a"); // Открываем файл в режиме записи
                $test = fwrite($fp, $result); // Запись в файл
                fclose($fp); //Закрытие файла

	// расплатимся с перфектом
	foreach ($perfect_payments as $payment) {
	    if (strtotime($payment['deposite_next'])<=time()) {
		$deposite_summ = $payment['percent']/100*$payment['summ'];
		 $model->updateDepBalance($payment['login'],$deposite_summ);
		 $model->updatePayOperationDep($payment['id'],$deposite_summ);
		 $model->AddPayOperation($payment['login'],$deposite_summ,3,$payment['tariff_id'],$payment['merchant'],1,""); 
	    }
	}
	// расплатимс с либерти
// 	foreach ($liberty_payments as $payment) {
// 	    if (strtotime($payment['deposite_next'])<=time()) {
// 		$deposite_summ = $payment['percent']/100*$payment['summ'];
// 		 $model->updateDepBalance($payment['login'],$deposite_summ);
// 		 $model->updatePayOperationDep($payment['id'],$deposite_summ);
// 		 $model->AddPayOperation($payment['login'],$deposite_summ,3,$payment['tariff_id'],$payment['merchant'],1,""); 
// 	    }
// 	}
    }

}
