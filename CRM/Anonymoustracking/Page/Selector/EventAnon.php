<?php

use CRM_Anonymoustracking_ExtensionUtil as E;

class CRM_Anonymoustracking_Page_Selector_EventAnon extends CRM_Mailing_Selector_Event
{
  private $_event_type;
  private $_mailing_id;
  private $_is_distinct;
  private $_url_id;

  public function __construct($event, $distinct, $mailing, $job = NULL, $url = NULL)
  {
    $this->_event_type = $event;
    $this->_is_distinct = $distinct;
    $this->_mailing_id = $mailing;
    $this->_url_id = $url;
    parent::__construct($event, $distinct, $mailing, $job, $url);
  }
  public function &getColumnHeaders($action = NULL, $output = NULL)
  {
    $this->_columnHeaders = parent::getColumnHeaders($action, $output);

    if (isset($this->_columnHeaders['sort_name'])) {
      $this->_columnHeaders['sort_name'] = [
        'name' => E::ts('Anonymous ID'),
        'sort' => 'anonymous_id',
        'direction' => CRM_Utils_Sort::ASCENDING,
      ];
    }
    //unset($this->_columnHeaders['email']);

    if (isset($this->_columnHeaders['date'])) {
      $dateSort = NULL;
      switch ($this->_event_type) {
        case 'opened':
          $dateSort = 'civicrm_mailing_event_opened.time_stamp';
          break;
        case 'click':
          $dateSort = CRM_Anonymoustracking_BAO_MailingUrlOpen::getTableName() . '.time_stamp';
          break;
      }
      if ($dateSort) {
        $this->_columnHeaders['date']['sort'] = $dateSort;
      }
    }
    return $this->_columnHeaders;
  }


  /**
   * Returns total number of rows for the query.
   *
   * @param string $action
   *
   * @return int
   *   Total number of rows
   */
  public function getTotalCount($action)
  {
    switch ($this->_event_type) {
      case 'opened':
        $event = new CRM_Anonymoustracking_BAO_MailingOpened();
        $result = $event->getTotalCount(
          $this->_mailing_id,
          NULL,
          $this->_is_distinct
        );
        return $result;
      case 'click':
        $event = new CRM_Anonymoustracking_BAO_MailingUrlOpen();
        $result = $event->getTotalCount(
          $this->_mailing_id,
          NULL,
          $this->_is_distinct,
          $this->_url_id
        );
        return $result;

      default:
        return parent::getTotalCount($action);
    }
  }

  public function &getRows($action, $offset, $rowCount, $sort, $output = NULL)
  {
    switch ($this->_event_type) {
      case 'opened':
        $rows = CRM_Anonymoustracking_BAO_MailingOpened::getRows(
          $this->_mailing_id,
          NULL,
          $this->_is_distinct,
          $offset,
          $rowCount,
          $sort
        );
        return $rows;

      case 'click':
        $rows = CRM_Anonymoustracking_BAO_MailingUrlOpen::getRows(
          $this->_mailing_id,
          NULL,
          $this->_is_distinct,
          $this->_url_id,
          $offset,
          $rowCount,
          $sort
        );
        return $rows;

      default:
        return parent::getRows($action, $offset, $rowCount, $sort, $output);
    }
  }
}
