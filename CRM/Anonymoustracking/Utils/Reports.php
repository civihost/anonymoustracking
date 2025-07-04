<?php

use CRM_Anonymoustracking_ExtensionUtil as E;

class CRM_Anonymoustracking_Utils_Reports
{
  /**
   * Modifies the report data for anonymous tracking mailing reports
   *
   * Called via hook_civicrm_pageRun for page class = 'CRM_Mailing_Page_Report'
   * Only called if mailing has anonymous tracking
   *
   * @param array $report
   * @param int $mailing_id
   */
  public static function overrideReportWithAnonymousTracking(&$report, $mailing_id)
  {
    // Declaration of anonymous tracking data inspired by the `report` method in CRM/Mailing/BAO/Mailing.php
    $dao = CRM_Core_DAO::executeQuery("
        SELECT COUNT(DISTINCT delivered.id) AS deliveries
        FROM   civicrm_mailing_event_delivered delivered
        INNER JOIN civicrm_mailing_event_queue queue
                ON delivered.event_queue_id = queue.id
        INNER JOIN civicrm_mailing_job job
                ON queue.job_id = job.id
        WHERE  job.mailing_id = %1
          AND  job.is_test = 0", [
        1 => [$mailing_id, 'Positive'],
      ]);
    if ($dao->fetch()) {

      $report['event_totals']['opened'] = CRM_Anonymoustracking_BAO_MailingOpened::getTotalCount($mailing_id, NULL, TRUE);
      $report['event_totals']['opened_rate'] = $dao->deliveries ? $report['event_totals']['opened'] / $dao->deliveries * 100 : 0;
      $report['event_totals']['total_opened'] = CRM_Anonymoustracking_BAO_MailingOpened::getTotalCount($mailing_id, NULL);

      // url is a number
      $report['event_totals']['url'] = CRM_Anonymoustracking_BAO_MailingUrlOpen::getTotalCount($mailing_id, NULL);
      $report['event_totals']['clickthrough_rate'] = $dao->deliveries ? $report['event_totals']['url'] / $dao->deliveries * 100 : 0;

      $report['click_through'] = self::getClickThroughs($mailing_id, $dao->deliveries);
    }
  }

  public static function getClickThroughs($mailing_id, $deliveries)
  {
    $dao = new CRM_Core_DAO();

    $clickTable = CRM_Anonymoustracking_BAO_MailingUrlOpen::getTableName();
    $urlTable = CRM_Mailing_BAO_MailingTrackableURL::getTableName();

    $query = "
            SELECT      {$urlTable}.url,
                        {$urlTable}.id,
                        COUNT({$clickTable}.id) as clicks,
                        COUNT(DISTINCT {$clickTable}.anonymous_id) as unique_clicks
            FROM        {$urlTable}
            LEFT JOIN   {$clickTable}
                    ON  {$clickTable}.trackable_url_id = {$urlTable}.id
            WHERE       {$clickTable}.mailing_id = $mailing_id
            GROUP BY    {$urlTable}.id
            ORDER BY    unique_clicks DESC";

    $dao->query($query);

    $click_through = [];
    $path = 'civicrm/mailing/report/event/anon';

    while ($dao->fetch()) {
      $click_through[] = [
        'url' => $dao->url,
        'link' => CRM_Utils_System::url($path, "reset=1&event=click&mid=$mailing_id&uid={$dao->id}"),
        'link_unique' => CRM_Utils_System::url($path, "reset=1&event=click&mid=$mailing_id&uid={$dao->id}&distinct=1"),
        'clicks' => $dao->clicks,
        'unique' => $dao->unique_clicks,
        'rate' => !empty($deliveries) ? (100.0 * $dao->unique_clicks) / $deliveries : 0,
        'report' => CRM_Report_Utils_Report::getNextUrl('mailing/clicks', "reset=1&mailing_id_value={$mailing_id}&url_value=" . rawurlencode($dao->url), FALSE, TRUE),
      ];
    }
    return $click_through;
  }
}
