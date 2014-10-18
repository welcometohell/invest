<?php 
return array(
      new waContactStringField('prig', 'Логин пригласителя', array('required' => true)),
      new waContactStringField('perfect', 'Кошелек Perfect Money (U0000000)',
              array('required' => true,
                    'validators' => array(
                        new waStringValidator(array('max_lenght'=>'8')),
                        new waRegexValidator(array('pattern'=>'/^U(\d){7}$/')),
                    )
                  )
       ),
      new waContactStringField('skype', 'Skype', array('required' => true)),
    );

