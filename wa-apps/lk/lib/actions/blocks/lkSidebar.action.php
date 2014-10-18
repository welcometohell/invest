<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class lkSidebarAction extends waViewAction {
        
        
    	public function execute()
	{
            $this->setLayout(new lkFrontendLayout());
            $this->setThemeTemplate('sidebar.html');
            $menu = include wa()->getConfig()->getAppConfigPath('sidebar');
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
            $this->view->assign('menu', $menu);
	}
}
?>
