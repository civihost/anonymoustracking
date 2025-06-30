<?php
use CRM_Contactlayout_ExtensionUtil as E;

return [
  'anonymous_tracking_default' => [
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'anonymous_tracking_default',
    'type' => 'Boolean',
    'html_type' => 'checkbox',
    'quick_form_type' => 'CheckBox',
    'default' => 0,
    'title' => E::ts('Enable anonymous tracking by default'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('If checked, tracking in CiviMail will be anonymized by default.'),
    'settings_pages' => ['mailing' => ['weight' => 145]],
  ],
];