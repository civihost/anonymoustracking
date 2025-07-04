<?php

use CRM_Anonymoustracking_ExtensionUtil as E;

// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'CRM_Anonymoustracking_Form_Report_Clicks',
    'entity' => 'ReportTemplate',
    'params' => [
      'version' => 3,
      'label' => E::ts('Anonymous Mail Clickthroughs Report'),
      'description' => E::ts('Display anonymous clicks from each mailing'),
      'class_name' => 'CRM_Anonymoustracking_Form_Report_Clicks',
      'report_url' => 'Anonymous/Mailing/clicks',
      'component' => 'CiviMail',
    ],
  ],
];
