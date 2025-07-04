<?php

use CRM_Anonymoustracking_ExtensionUtil as E;

class CRM_Anonymoustracking_Form_Report_Clicks extends CRM_Report_Form_Mailing_Clicks
{
  protected $_customGroupExtends = [];

  public function __construct()
  {
    $this->optimisedForOnlyFullGroupBy = FALSE;
    $this->_columns = [];

    $this->_columns['civicrm_mailing'] = [
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => [
        'mailing_name' => [
          'name' => 'name',
          'title' => ts('Mailing Name'),
          'default' => TRUE,
        ],
        'mailing_name_alias' => [
          'name' => 'name',
          'required' => TRUE,
          'no_display' => TRUE,
        ],
        'mailing_subject' => [
          'name' => 'subject',
          'title' => ts('Mailing Subject'),
          'default' => TRUE,
        ],
      ],
      'filters' => [
        'mailing_id' => [
          'name' => 'id',
          'title' => ts('Mailing Name'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'type' => CRM_Utils_Type::T_INT,
          'options' => CRM_Mailing_BAO_Mailing::getMailingsList(),
          'operator' => 'like',
        ],
        'mailing_subject' => [
          'name' => 'subject',
          'title' => ts('Mailing Subject'),
          'type' => CRM_Utils_Type::T_STRING,
          'operator' => 'like',
        ],
      ],
      'order_bys' => [
        'mailing_name' => [
          'name' => 'name',
          'title' => ts('Mailing Name'),
        ],
        'mailing_subject' => [
          'name' => 'subject',
          'title' => ts('Mailing Subject'),
        ],
      ],
      'grouping' => 'mailing-fields',
    ];

    $this->_columns['civicrm_mailing_trackable_url'] = [
      'dao' => 'CRM_Mailing_DAO_MailingTrackableURL',
      'fields' => [
        'url' => [
          'title' => ts('Click through URL'),
        ],
      ],
      // To do this filter should really be like mailing id filter a multi select, However
      // Not clear on how to make filter dependant on selected mailings at this stage so have set a
      // text filter which works for now
      'filters' => [
        'url' => [
          'title' => ts('URL'),
          'type' => CRM_Utils_Type::T_STRING,
          'operator' => 'like',
        ],
      ],
      'order_bys' => [
        'url' => ['title' => ts('Click through URL')],
      ],
      'grouping' => 'mailing-fields',
    ];

    $this->_columns['civicrm_anonymoustracking_trackable_url'] = [
      'dao' => 'CRM_Anonymoustracking_DAO_MailingUrlOpen',
      'fields' => [
        'anonymous_id' => [
          'title' => E::ts('Anonymous ID'),
          'required' => TRUE,
        ],
        'id' => [
          'required' => TRUE,
          'no_display' => TRUE,
          'dbAlias' => CRM_Utils_SQL::supportsFullGroupBy() ? 'ANY_VALUE(anonymoustracking_trackable_url_civireport.id)' : NULL,
        ],
        'time_stamp' => [
          'title' => E::ts('Click Date'),
          'default' => TRUE,
        ],
      ],
      'filters' => [
        'time_stamp' => [
          'title' => E::ts('Click Date'),
          'operatorType' => CRM_Report_Form::OP_DATE,
          'type' => CRM_Utils_Type::T_DATE,
        ],
        'unique_opens' => [
          'title' => E::ts('Unique Clicks'),
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'pseudofield' => TRUE,
        ],
      ],
      'order_bys' => [
        'time_stamp' => [
          'title' => E::ts('Click Date'),
        ],
      ],
      'grouping' => 'mailing-fields',
    ];

    // Add charts support
    $this->_charts = [
      '' => ts('Tabular'),
      'barChart' => ts('Bar Chart'),
      'pieChart' => ts('Pie Chart'),
    ];

    $this->_groupFilter = TRUE;
    $this->_tagFilter = TRUE;
    CRM_Report_Form::__construct();
  }

  public function select()
  {
    $select = [];
    $this->_columnHeaders = [];
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (
            !empty($field['required']) ||
            !empty($this->_params['fields'][$fieldName])
          ) {
            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'] ?? NULL;
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['no_display'] = $field['no_display'] ?? NULL;
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'] ?? NULL;
          }
        }
      }
    }

    if (!empty($this->_params['charts'])) {
      $select[] = "COUNT({$this->_aliases['civicrm_anonymoustracking_trackable_url']}.id) as civicrm_mailing_opened_count";
      $this->_columnHeaders["civicrm_mailing_opened_count"]['title'] = ts('Click Count');
    }

    $this->_selectClauses = $select;
    $this->_select = "SELECT " . implode(', ', $select) . " ";
  }

  public function from()
  {
    $this->_from = "
      FROM civicrm_anonymoustracking_mailing_url_open {$this->_aliases['civicrm_anonymoustracking_trackable_url']}
      INNER JOIN civicrm_mailing_trackable_url {$this->_aliases['civicrm_mailing_trackable_url']}
        ON {$this->_aliases['civicrm_anonymoustracking_trackable_url']}.trackable_url_id = {$this->_aliases['civicrm_mailing_trackable_url']}.id
      INNER JOIN civicrm_mailing {$this->_aliases['civicrm_mailing']}
        ON {$this->_aliases['civicrm_anonymoustracking_trackable_url']}.mailing_id = {$this->_aliases['civicrm_mailing']}.id
    ";
  }

  public function groupBy()
  {
    $this->_groupBy = '';
    $groupBys = [];
    // Do not use group by clause if distinct = 0 mentioned in url params. flag is used in mailing report screen, default value is TRUE
    // this report is used to show total opened and unique opened
    if (CRM_Utils_Request::retrieve('distinct', 'Boolean', CRM_Core_DAO::$_nullObject, FALSE, TRUE)) {
      $groupBys = empty($this->_params['charts']) ? [] : ["{$this->_aliases['civicrm_mailing']}.id"];
      if (!empty($this->_params['unique_opens_value'])) {
        $groupBys[] = "{$this->_aliases['civicrm_anonymoustracking_trackable_url']}.anonymous_id";
      }
    }
    if (!empty($groupBys)) {
      $this->_groupBy = "GROUP BY " . implode(', ', $groupBys);
    }
  }
}
