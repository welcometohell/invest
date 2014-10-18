<?php

class guestbookFrontendLayout extends waLayout
{
    // Здесь определяются блоки, доступные в шаблоне
    public function execute()
    {
        $this->setThemeTemplate('index.html');
        parent::execute();
    }

}
