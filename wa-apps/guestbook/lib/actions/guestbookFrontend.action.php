<?php 
/**
 * Frontend action
 * Here the entire frontend logic of the guestbook app is implemented
 *
 * Экшен фронтенда
 * Здесь реализуется вся логика для фронтенда приложения гостевая книга
 *
 */
class guestbookFrontendAction extends waViewAction
{
    public function execute()
    {
        // Setting the frontend layout
        // Задаём лайаут для фронтенда
        $this->setLayout(new guestbookFrontendLayout());
        // Setting the theme template
        // Задаём шаблон темы
        $this->setThemeTemplate('guestbook.html');

        // if a POST request has been received then write a new record to the database
        // Если пришёл POST-запрос, то нужно записать в БД новую запись
        if (waRequest::method() == 'post') {
            $this->add();
        }
        // Creating a model instance for retrieving data from the database
        // Создаем экземпляр модели для получения данных из БД
        $model = new guestbookModel();

        // Retrieving the record count per page from the app's settings
        // Получаем количество записей на одной странице из настроек приложения
        $limit = $this->getConfig()->getOption('records_per_page');
        // Current page
        // Текущая страница
        $page = waRequest::param('page');
        if (!$page) {
            $page = 1;
        }
        $this->view->assign('page', $page);
        // Calculating offset
        // Вычисляем смещение
        $offset = ($page - 1) * $limit;
        // Retrieving all records from the database
        // Получаем записи гостевой книги из БД
        $records = $model->getRecords($offset, $limit);
        // Total record count
        // Всего записей
        $records_count = $model->countByField('published', '1');
        $pages_count = ceil($records_count / $limit);
        $this->view->assign('pages_count', $pages_count);
        // Preparing records for being passed to the theme template
        // Подготавливаем записи для передачи в шаблон темы
        foreach ($records as $key=>$r) {
            if ($r['login']) {
                $r['name'] = htmlspecialchars($r['name']);
                $r['text'] = nl2br(htmlspecialchars($r['text']));
                $contact = new waContact($r['user_id']);
                $records[$key]['name'] = $contact->get("name");
            }
        }
        unset($r);
        // Passing records to the template
        // Передаем записи в шаблон
        $this->view->assign('records', $records);
        $success = "";
        if (waRequest::get('ok')) 
            $success = "Отзыв отправлен на модерацию!";
        $this->view->assign('success', $success);
        // URL portion for links to pages
        // Часть урла для ссылок на страницы
        $this->view->assign('url', wa()->getRouteUrl('/frontend'));
    }

    /**
     * Adding a new record to the guestbook
     * Добавление новой записи в гостевую книгу
     */
    protected function add()
    {
        // Creating a model instance for retrieving data from the database
        // Создаем экземпляр модели для получения данных из БД
        $model = new guestbookModel();
        if ($text = waRequest::post('text')) {
            $data = array(
                'text' => $text,
                'datetime' => date('Y-m-d H:i:s')
            );
            if ($this->getUser()->getId()) {
                $data['user_id'] = $this->getUser()->getId();
                $data['name'] = waRequest::post('name');
            }
            // Inserting a new record into the database table
            // Вставляем новую запись в таблицу
            $model->insert($data);
        }
        // redirecting user to the first page to show the new message
        // редирект на первую страницу, чтобы показать новое сообщение
        $this->redirect(wa()->getRouteUrl('/frontend?ok=1'));
    }
}