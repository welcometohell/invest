<?php 

/**
 * Default backend action
 * Read more about actions and controllers at
 * http://www.webasyst.com/framework/docs/dev/controllers/
 * 
 * Processes requests in backend at URL dummy/
 * Read more about request routing in backend at
 * http://www.webasyst.com/framework/docs/dev/backend-routing/
 *
 * Экшен бекенда по умолчанию
 * Подробнее о экшенах и контроллерах:
 * http://www.webasyst.com/ru/framework/docs/dev/controllers/
 * 
 * Доступен в бэкенде по урлу dummy/
 * Подробнее о маршрутизации в бэкенде:
 * http://www.webasyst.com/ru/framework/docs/dev/backend-routing/
 */
class lkBackendAction extends waViewAction
{
	/**
	 * This is the action's entry point
	 * Here all business logic should be implemented and data for templates should be prepared
	 *
	 * Это "входная точка" экшена
	 * Здесь должна быть реализована вся бизнес-логика и подготовлены данные для шаблона
	 */ 
	public function execute()
	{
		// Obtaining the right to add new records
		// Получаем право на добавление новых записей
		//$right_add = $this->getRights('add');
		// Passing data to template (actions/backend/Backend.html)
		// Передаем данные в шаблон (actions/backend/Backend.html)
		$this->setLayout(new lkBackendLayout());
		$right_add = true;
		$this->view->assign('right_add', $right_add);
		//$this->view->assign('records', $this->getConfig()->getRecords(true));
		$model = new lkModel();
		//$stat = $model->GetStat();
		$in = $model->GetInSumm();
                $in_after = $model->GetInSummAfter();
                $in_today = $model->GetInSummToday();
		$out = $model->GetOutSumm();
                $out_after = $model->GetOutSummAfter();
                $out_today = $model->GetOutSummToday();
		//$this->view->assign('stat', $stat['invest']);
                $this->view->assign('statout', $stat['out']);
		$this->view->assign('in', (int)$in);
		$this->view->assign('out', (int)$out);
		$this->view->assign('in_after', (int)$in_after);
		$this->view->assign('in_today', (int)$in_today);
		$this->view->assign('out_after', (int)$out_after);
		$this->view->assign('out_today', (int)$out_today);
	}
}