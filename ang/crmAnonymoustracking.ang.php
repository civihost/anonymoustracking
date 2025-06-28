<?php
return [
  'js' => [
    'ang/crmAnonymoustracking.js',
  ],
  'css' => [],
  'partials' => [],
  'requires' => [
    'crmMailing',
  ],
  'settingsFactory' => ['CRM_Anonymoustracking_Utils', 'getAngularSettings'],
];