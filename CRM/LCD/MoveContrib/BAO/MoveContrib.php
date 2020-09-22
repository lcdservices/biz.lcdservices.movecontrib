<?php

/**
 * Class CRM_LCD_MoveContrib_Form_MoveContrib
 */
class CRM_LCD_MoveContrib_BAO_MoveContrib {

  static function moveContribution($params) {
    $id = array('contribution' => $params['contribution_id']);
    $params['id'] = $params['contribution_id'];
    $contribution = CRM_Contribute_BAO_Contribution::create($params);

    // record activity for moving contribution
    if ($contribution) {
      $subject = "Contribution #{$params['contribution_id']} Moved";
      $details = "Contribution #{$params['contribution_id']} was moved from contact #{$params['current_contact_id']} to contact #{$params['change_contact_id']}.";

      $activityTypeID = CRM_Core_OptionGroup::getValue('activity_type',
        'contribution_reassignment',
        'name'
      );

      $activityParams = array(
        'source_contact_id' => $params['current_contact_id'],
        'activity_type_id' => $activityTypeID,
        'activity_date_time' => date('YmdHis'),
        'subject' => $subject,
        'details' => $details,
        'status_id' => 2,
      );

      $session = CRM_Core_Session::singleton();
      $id = $session->get('userID');

      if ($id) {
        $activityParams['source_contact_id'] = $id;
        $activityParams['target_contact_id'][] = $params['current_contact_id'];
        $activityParams['target_contact_id'][] = $params['change_contact_id'];
      }

      try {
        CRM_Activity_BAO_Activity::create($activityParams);
      }
      catch (CiviCRM_API3_Exception $e) {}

      return TRUE;
    }

    return FALSE;
  }
}
