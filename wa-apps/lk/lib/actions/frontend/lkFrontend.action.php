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
class lkFrontendAction extends waViewAction
{
	public function execute()
	{
                $params = waRequest::get();
		if($params['ref']) {
			try {
                            $contact = new waContact($params['ref']);
			    $login = $contact->get("login"); 
			    if ($login) {
						setcookie('ref', $login , time()+60*60*48, '/', $host = waRequest::server("HTTP_HOST"));
						setcookie('date', date('Y-m-d', time()), time()+60*60*48, '/', $host = waRequest::server("HTTP_HOST"));
				}
			    $this->redirect(wa()->getRouteUrl('all/'));
			} 
			catch (Exception $e) {
			    echo "Даже не думай...";
			}
		}
        else {
            $this->redirect(wa()->getRouteUrl('/'.wa()->getAppUrl('lk').'/all/'));
        }
	}
}