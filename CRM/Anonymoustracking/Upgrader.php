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
      'html_type' => 'Radio',
      'default_value' => 0,
      'serialize' => 0,
      'is_active' => 1,
    ];
    CRM_Core_BAO_CustomField::create($customFieldParams);
  }

  public function upgrade_1001(): bool
  {
    $this->ctx->log->info('Applying Update 1001 - create anonymous report instances');
    try {
      civicrm_api4('ReportInstance', 'create', [
        'values' => [
          'domain_id' => CRM_Core_Config::domainID(),
          'title' => E::ts('Anonymous Mail Opened Report'),
          'report_id' => 'Anonymous/Mailing/opened',
          'permission' => 'access CiviMail',
          'is_active' => TRUE,
          'description' => E::ts('Displays anonymous IDs of contacts who opened mailings'),
        ],
        'checkPermissions' => FALSE,
      ]);

      civicrm_api4('ReportInstance', 'create', [
        'values' => [
          'domain_id' => CRM_Core_Config::domainID(),
          'title' => E::ts('Anonymous Mail Clickthroughs Report'),
          'report_id' => 'Anonymous/Mailing/clicks',
          'permission' => 'access CiviMail',
          'is_active' => TRUE,
          'description' => E::ts('Displays anonymous IDs of contacts who click mailings'),
        ],
        'checkPermissions' => FALSE,
      ]);
    } catch (CRM_Core_Exception $e) {
      Civi::log()->error("Failed to create report instance: " . $e->getMessage());
      return FALSE;
    }
    return TRUE;
  }
}
