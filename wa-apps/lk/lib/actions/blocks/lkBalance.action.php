<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class lkBalanceAction extends waViewAction {
        
        
    	public function execute()
	{   
            if ($user = $this->getUser()) {
                $model = new lkModel();
                $bal = $model->getBalance ($user->getLogin());
                $this->setLayout(new lkFrontendLayout());
                $this->setThemeTemplate('Balance.html');
                $this->view->assign('bal', round($bal, 2));
            }
	}
}
?>
