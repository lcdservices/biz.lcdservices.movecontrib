<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_LCD_MoveContrib_Form_MoveContrib extends CRM_Core_Form {

  public function buildQuickForm() {
    $this->_contributionId = CRM_Utils_Request::retrieve('id', 'Positive', $this);

    $this->_contactId = civicrm_api3('contribution', 'getvalue', array(
      'id' => $this->_contributionId,
      'return' => 'contact_id',
    ));

    //get current client name.
    $this->assign('currentClientName', CRM_Contact_BAO_Contact::displayName($this->_contactId));

    $this->addEntityRef('change_client_id', ts('Select Contact'));
    $this->add('hidden', 'contact_id', '', array('id' => 'contact_id'));
    $this->add('hidden', 'contribution_id', $this->_contributionId, array('id' => 'contribution_id'));
    $this->add('hidden', 'current_contact_id', $this->_contactId, array('id' => 'current_contact_id'));
    $this->assign('contactId', $this->_contactId);

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    //Civi::log()->debug('postProcess', array('values' => $values));

    $params = array(
      'change_client_id' => $values['change_client_id'],
      'contact_id' => $values['change_client_id'],
      'contribution_id' => $values['contribution_id'],
      'current_contact_id' => $values['current_contact_id'],
    );
    $id = array('contribution' => $values['contribution_id']);
    $contribution = CRM_Contribute_BAO_Contribution::create($params, $id);

    // record activity for moving contribution
    if ($contribution) {
      $subject = "Contribution #{$values['contribution_id']} Moved";
      $details = "Contribution #{$values['contribution_id']} was moved from contact #{$values['current_contact_id']} to contact #{$values['change_client_id']}.";
      $activityTypeID = CRM_Core_OptionGroup::getValue('activity_type',
        'contribution_reassignment',
        'name'
      );
      $activityParams = array(
        'source_contact_id' => $values['current_contact_id'],
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
        $activityParams['target_contact_id'][] = $values['current_contact_id'];
        $activityParams['target_contact_id'][] = $values['change_client_id'];
      }

      CRM_Activity_BAO_Activity::create($activityParams);
    }

    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
