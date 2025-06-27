<?php
// Declare contactlayout angular module
return [
  'js' => [
    'ang/crmAnonymoustracking.js',
  ],
  'css' => [],
  'partials' => [],
  'requires' => [
    'crmMailing',
    'crmUi',
    'crmUtil',
    'crmResource',
    'api4'
  ],
  'settingsFactory' => ['CRM_Anonymoustracking_Utils', 'getAngularSettings'],
];