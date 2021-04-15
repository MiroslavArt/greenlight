<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => NULL,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/insurance-companies/#',
    'RULE' => '',
    'ID' => 'itrack:companies',
    'PATH' => '/insurance-companies/index.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/settings/users/#',
    'RULE' => '',
    'ID' => 'itrack:users',
    'PATH' => '/settings/users/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/adjusters/#',
    'RULE' => '',
    'ID' => 'itrack:companies',
    'PATH' => '/adjusters/index.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/clients/#',
    'RULE' => '',
    'ID' => 'itrack:companies',
    'PATH' => '/clients/index.php',
    'SORT' => 100,
  ),
  6 => 
  array (
    'CONDITION' => '#^/losts/#',
    'RULE' => '',
    'ID' => 'itrack:losts',
    'PATH' => '/losts/index.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
);
