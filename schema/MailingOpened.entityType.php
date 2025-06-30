<?php
use CRM_Anonymoustracking_ExtensionUtil as E;

return [
  'name' => 'MailingOpened',
  'table' => 'civicrm_anonymoustracking_mailing_opened',
  'class' => 'CRM_Anonymoustracking_DAO_MailingOpened',
  'getInfo' => fn() => [
    'title' => E::ts('MailingOpened'),
    'title_plural' => E::ts('MailingOpeneds'),
    'log' => TRUE,
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'mailing_id' => [
      'title' => ts('Mailing ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'required' => TRUE,
      'description' => ts('The ID of a the mailing.'),
      'input_attrs' => [
        'label' => ts('Mailing'),
      ],
      'entity_reference' => [
        'entity' => 'Mailing',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'anonymous_id' => [
      'title' => E::ts('Anonymous ID'),
      'sql_type' => 'varchar(64)',
      'input_type' => 'Text',
      'required' => TRUE,
      'description' => E::ts('Unique anonymous ID'),
      'input_attrs' => [
        'label' => E::ts('Anonymous ID'),
      ],
    ],
    'time_stamp' => [
      'title' => ts('Timestamp'),
      'sql_type' => 'timestamp',
      'input_type' => NULL,
      'required' => TRUE,
      'description' => ts('When this open event occurred.'),
      'default' => 'CURRENT_TIMESTAMP',
    ],
  ],
  'getIndices' => fn() => [],
  'getPaths' => fn() => [],
];
