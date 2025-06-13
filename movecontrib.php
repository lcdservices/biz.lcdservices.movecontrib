<?php

require_once 'movecontrib.civix.php';

use CRM_LCD_MoveContrib_ExtensionUtil as E;
/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function movecontrib_civicrm_config(&$config) {
  _movecontrib_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function movecontrib_civicrm_install() {
  _movecontrib_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function movecontrib_civicrm_enable() {
  _movecontrib_civix_civicrm_enable();
}

function movecontrib_civicrm_searchTasks($objectType, &$tasks) {
  if ($objectType === 'contribution' && CRM_Core_Permission::check('allow Move Contribution')) {
    $tasks[] = [
      'title' => E::ts('Move Contribution'),
      'class' => 'CRM_LCD_MoveContrib_Form_Task',
      // Code suggests result key is likely meaningless.
      'result' => TRUE,
      'is_single_mode' => FALSE,
      'name' => E::ts('Move Contribution'),
      'url' => 'civicrm/contribute/task?reset=1&task_item=move',
      'key' => 'move',
      'weight' => 130,
    ];
  }
}
function movecontrib_civicrm_links(string $op, ?string $objectName, $objectID, array &$links, ?int &$mask, array &$values): void {
  if ($objectName !== 'Contribution' || $op !== 'contribution.selector.row' || !CRM_Core_Permission::check('allow Move Contribution')) {
    return;
  }
  $links[] = [
    'name' => E::ts('Move Contribution'),
    'url' => 'civicrm/movecontrib',
    "qs" => "reset=1&task_item=move&id=%%id%%&cid=%%cid%%&context=%%cxt%%",
    'title' => E::ts('Move Contribution'),
    'weight' => 130,
  ];
}
/**
 *Implementation of hook_civicrm_permission
 * @param array $permissions
 */
function movecontrib_civicrm_permission(&$permissions) {
  $permissions['allow Move Contribution'] = [
    'label' => E::ts('CiviCRM: Allow Move Contributions'),
    'description' => E::ts('Allow Move Contribution')
   ];
}
