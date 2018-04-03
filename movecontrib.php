<?php

require_once 'movecontrib.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function movecontrib_civicrm_config(&$config) {
  _movecontrib_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function movecontrib_civicrm_xmlMenu(&$files) {
  _movecontrib_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function movecontrib_civicrm_uninstall() {
  _movecontrib_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function movecontrib_civicrm_enable() {
  _movecontrib_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function movecontrib_civicrm_disable() {
  _movecontrib_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function movecontrib_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _movecontrib_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function movecontrib_civicrm_managed(&$entities) {
  _movecontrib_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function movecontrib_civicrm_caseTypes(&$caseTypes) {
  _movecontrib_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function movecontrib_civicrm_angularModules(&$angularModules) {
_movecontrib_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function movecontrib_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _movecontrib_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function movecontrib_civicrm_searchColumns($objectName, &$headers, &$rows, &$selector) {
  /*Civi::log()->debug('movecontrib_civicrm_searchColumns', array(
    'objectName' => $objectName,
    '$headers' => $headers,
    '$rows' => $rows,
    //'$selector' => $selector,
  ));*/

  if ($objectName == 'contribution') {
    foreach ($rows as &$row) {
      //action column is either a series of links, or a series of links plus a subset
      //unordered list (more button) -- all of which is enclosed in a span
      //we want to inject our option at the end, regardless, so we look for the existence
      //of a <ul> tag and adjust our injection accordingly
      $url = CRM_Utils_System::url('civicrm/movecontrib', "reset=1&id={$row['contribution_id']}");
      $urlLink = "<a href='{$url}' class='action-item crm-hover-button medium-popup move-contrib'>Move</a>";
      if (strpos($row['action'], '</ul>') !== FALSE) {
        $row['action'] = str_replace('</ul></span>', '<li>'.$urlLink.'</li></ul></span>', $row['action']);
      }
      else {
        $row['action'] = str_replace('</span>', $urlLink.'</span>', $row['action']);
      }
    }
  }
}

function movecontrib_civicrm_searchTasks($objectType, &$tasks) {
  /*Civi::log()->debug('movecontrib_civicrm_searchTasks', array(
    '$objectType' => $objectType,
    '$tasks' => $tasks,
  ));*/

  if ($objectType == 'contribution') {
    $tasks[] = array(
      'title' => 'Move contributions',
      'class' => 'CRM_LCD_MoveContrib_Form_Task',
      'result' => TRUE,
    );
  }
}
