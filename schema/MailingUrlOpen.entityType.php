<?php
use CRM_Anonymoustracking_ExtensionUtil as E;

return [
  'name' => 'MailingUrlOpen',
  'table' => 'civicrm_anonymoustracking_mailing_url_open',
  'class' => 'CRM_Anonymoustracking_DAO_MailingUrlOpen',
  'getInfo' => fn() => [
    'title' => E::ts('MailingUrlOpen'),
    'title_plural' => E::ts('MailingUrlOpens'),
    'log' => TRUE,
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'anonymous_id' => [
      'title' => E::ts('Anonymous ID'),
      'sql_type' => 'varchar(32)',
      'input_type' => 'Text',
      'required' => TRUE,
      'description' => E::ts('Unique anonymous ID'),
      'input_attrs' => [
        'label' => E::ts('Anonymous ID'),
      ],
    ],
    'trackable_url_id' => [
      'title' => ts('Trackable Url ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'required' => TRUE,
      'description' => ts('FK to TrackableURL'),
      'input_attrs' => [
        'label' => ts('Mailing Link'),
      ],
      'entity_reference' => [
        'entity' => 'MailingTrackableURL',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'time_stamp' => [
      'title' => ts('Timestamp'),
      'sql_type' => 'timestamp',
      'input_type' => 'Date',
      'required' => TRUE,
      'description' => ts('When this trackable URL open occurred.'),
      'default' => 'CURRENT_TIMESTAMP',
      'input_attrs' => [
        'label' => ts('Opened Date'),
      ],
    ],
  ],
  'getIndices' => fn() => [],
  'getPaths' => fn() => [],
];
