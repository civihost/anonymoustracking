<?php
use CRM_Contactlayout_ExtensionUtil as E;

return [
  'anonymoustracking_default' => [
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'anonymoustracking_default',
    'type' => 'Boolean',
    'html_type' => 'checkbox',
    'quick_form_type' => 'CheckBox',
    'default' => 0,
    'title' => E::ts('Enable anonymous tracking by default'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('If checked, tracking in CiviMail will be anonymized.'),
    'settings_pages' => ['mailing' => ['weight' => 145]],
  ],
];