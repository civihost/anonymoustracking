<?php
use CRM_Contribute_ExtensionUtil as E;

// This enables custom fields for ContributionPage entities
return [
  [
    'name' => 'cg_extend_objects:Mailing',
    'entity' => 'OptionValue',
    'cleanup' => 'always',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'cg_extend_objects',
        'label' => E::ts('Mailing'),
        'value' => 'Mailing',
        'name' => 'civicrm_mailing',
        'is_reserved' => TRUE,
        'is_active' => TRUE,
        'grouping' => 'open_tracking',
      ],
      'match' => [
        'name',
        'option_group_id',
      ],
    ],
  ],
];
