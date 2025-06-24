<?php

use CRM_Anonymoustracking_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Anonymoustracking_Upgrader extends \CRM_Extension_Upgrader_Base
{
  public function install(): void {}

  public function uninstall()
  {
    try {
      $group = civicrm_api3('CustomGroup', 'get', [
        'name' => 'anonymoustracking_mailing',
      ]);

      if (!empty($group['count'])) {
        $groupId = array_keys($group['values'])[0];
        civicrm_api3('CustomGroup', 'delete', [
          'id' => $groupId,
        ]);
      }
    } catch (Exception $e) {
      Civi::log()->warning('Error during uninstallation: ' . $e->getMessage());
    }
    return TRUE;
  }

  /**
   * Example: Work with entities usually not available during the install step.
   *
   * This method can be used for any post-install tasks. For example, if a step
   * of your installation depends on accessing an entity that is itself
   * created during the installation (e.g., a setting or a managed entity), do
   * so here to avoid order of operation problems.
   */
  public function postInstall(): void
  {
    $customGroupParams = [
      'title' => 'Anonymous Tracking Options',
      'name' => 'anonymoustracking_mailing',
      'extends' => 'Mailing',
      'style' => 'Tab',
      'is_active' => 1,
      'collapse_display' => 0,
      'version' => 3,
    ];

    $customGroup = CRM_Core_BAO_CustomGroup::create($customGroupParams);

    $customFieldParams = [
      'custom_group_id' => $customGroup->id,
      'label' => 'Enable Anonymous Tracking',
      'name' => 'enable_anonymous_tracking',
      'data_type' => 'Boolean',
      'html_type' => 'CheckBox',
      'serialize' => 0,
      'is_active' => 1,
    ];
    CRM_Core_BAO_CustomField::create($customFieldParams);
  }


  /**
   * Example: Run a simple query when a module is enabled.
   */
  // public function enable(): void {
  //  CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
  // }

  /**
   * Example: Run a simple query when a module is disabled.
   */
  // public function disable(): void {
  //   CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  // }

  /**
   * Example: Run a couple simple queries.
   *
   * @return TRUE on success
   * @throws CRM_Core_Exception
   */
  // public function upgrade_4200(): bool {
  //   $this->ctx->log->info('Applying update 4200');
  //   CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
  //   CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
  //   return TRUE;
  // }

  /**
   * Example: Run an external SQL script.
   *
   * @return TRUE on success
   * @throws CRM_Core_Exception
   */
  // public function upgrade_4201(): bool {
  //   $this->ctx->log->info('Applying update 4201');
  //   // this path is relative to the extension base dir
  //   $this->executeSqlFile('sql/upgrade_4201.sql');
  //   return TRUE;
  // }

  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk.
   *
   * @return TRUE on success
   * @throws CRM_Core_Exception
   */
  // public function upgrade_4202(): bool {
  //   $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

  //   $this->addTask(E::ts('Process first step'), 'processPart1', $arg1, $arg2);
  //   $this->addTask(E::ts('Process second step'), 'processPart2', $arg3, $arg4);
  //   $this->addTask(E::ts('Process second step'), 'processPart3', $arg5);
  //   return TRUE;
  // }
  // public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  // public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  // public function processPart3($arg5) { sleep(10); return TRUE; }

  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws CRM_Core_Exception
   */
  // public function upgrade_4203(): bool {
  //   $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

  //   $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
  //   $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
  //   for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
  //     $endId = $startId + self::BATCH_SIZE - 1;
  //     $title = E::ts('Upgrade Batch (%1 => %2)', array(
  //       1 => $startId,
  //       2 => $endId,
  //     ));
  //     $sql = '
  //       UPDATE civicrm_contribution SET foobar = apple(banana()+durian)
  //       WHERE id BETWEEN %1 and %2
  //     ';
  //     $params = array(
  //       1 => array($startId, 'Integer'),
  //       2 => array($endId, 'Integer'),
  //     );
  //     $this->addTask($title, 'executeSql', $sql, $params);
  //   }
  //   return TRUE;
  // }

  //public function upgrade_1001(): bool
  //{
  //  //CRM_Upgrade_Incremental_Base::createEntityTable(NULL, 'upgrade/AnonymoustrackingMailingUrlOpen.entityType');
  //  //CRM_Upgrade_Incremental_Base::createEntityTable(NULL, 'upgrade/AnonymoustrackingMailingUrlOpen.entityType');
  //  //return TRUE;
  //}
}
