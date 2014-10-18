<?php
return array(
    'lk_balance' => array(
        'user_id' => array('int', 11, 'null' => 0),
        'ref_balance' => array('float', 'null' => 0),
        'dep_balance' => array('float', 'null' => 0),
        ':keys' => array(
        ),
    ),
    'lk_pay_operations' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'login' => array('varchar', 32, 'null' => 0),
        'summ' => array('float', 'null' => 0),
        'type' => array('int', 1, 'null' => 0),
        'timestamp' => array('timestamp', 'null' => 0, 'default' => 'CURRENT_TIMESTAMP'),
        'tariff_id' => array('int', 5),
        'merchant' => array('int', 1, 'null' => 0),
        'status' => array('int', 1, 'null' => 0),
        'hash' => array('varchar', 255, 'null' => 0),
        ':keys' => array(
            'PRIMARY' => 'id',
            'id' => array('id', 'unique' => 1),
            'login' => array('login', 'timestamp', 'tariff_id'),
            'merchant' => 'merchant',
            'hash' => 'hash',
        ),
    ),
    'lk_pay_tariff' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'min' => array('int', 5, 'null' => 0),
        'max' => array('int', 5, 'null' => 0),
        'period' => array('int', 3, 'null' => 0),
        'persent' => array('int', 3, 'null' => 0),
        'name' => array('varchar', 255, 'null' => 0),
        ':keys' => array(
            'PRIMARY' => 'id',
        ),
    ),
    'lk_pay_types' => array(
        'id' => array('int', 11, 'null' => 0),
        'name' => array('varchar', 255, 'null' => 0),
        ':keys' => array(
            'id' => 'id',
        ),
    ),
    'lk_referals' => array(
        'id' => array('int', 11, 'null' => 0),
        'user_id' => array('int', 11, 'null' => 0),
        'ref_id' => array('int', 11, 'null' => 0),
        'date' => array('timestamp', 'null' => 0, 'default' => 'CURRENT_TIMESTAMP'),
        ':keys' => array(
            'ref_id' => array('ref_id', 'unique' => 1),
            'user_id' => 'user_id',
        ),
    ),
);