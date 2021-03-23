<?php
$arUrlRewrite=array (
  3 => 
  array (
    'CONDITION' => '#^/insurance-companies/index.php/#',
    'RULE' => '',
    'ID' => 'itrack:companies',
    'PATH' => '/insurance-companies/index.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => NULL,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
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
  5 =>
  array (
    'CONDITION' => '#^/clients/#',
    'RULE' => '',
    'ID' => 'itrack:companies',
    'PATH' => '/clients/index.php',
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
