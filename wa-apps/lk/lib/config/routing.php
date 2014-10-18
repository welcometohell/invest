<?php

/**
 * Here internal request routing rules for frontend are defined (URL => MODULE/[ACTION])
 * Здесь задаются правила внутренней маршрутизации для фронтенда URL => MODULE/[ACTION]
 * @see http://www.webasyst.com/framework/docs/dev/routing/
 */
return array(
	'login/' => 'login',
	'login' => 'login',
	'forgotpassword/' => 'forgotpassword',
	'forgotpassword' => 'forgotpassword',
	'signup/' => 'signup',
	'signup' => 'signup',
	'logout/' => 'frontend/logout',
        'pay' => 'pay',
        'pay/' => 'pay',
        'pay/success' => 'pay/success',
        'pay/success/' => 'pay/success',
        'pay/fail' => 'pay/fail',
        'pay/fail/' => 'pay/fail',
        'all/' => 'all',
        'all' => 'all',
        'all/' => 'all',
        'all/out' => 'all/out',
        'all/out/' => 'all/out',
        'all/listout' => 'all/listout',
        'all/listout/' => 'all/listout',
        'all/in/' => 'all/in',
        'all/in' => 'all/in',
	'all/ref' => 'all/ref',
	'all/ref/' => 'all/ref',
	'all/reinvest' => 'all/reinvest',
	'all/reinvest/' => 'all/reinvest',
    	'all/user' => 'all/user',
	'all/user/' => 'all/user',
        'my/profile/?' => array(
            'url' => 'my/profile/?',
            'module' => 'frontend',
            'action' => 'myProfile',
            'secure' => true,
        ),
);