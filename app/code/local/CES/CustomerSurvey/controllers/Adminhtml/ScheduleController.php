<?php

/**
 * Class CES_CustomerSurvey_Adminhtml_IndexController
 *
 * @author Hieu Nguyen <hieu.nguyen@codeenginestudio.com>
 */
class CES_CustomerSurvey_Adminhtml_ScheduleController extends Mage_Adminhtml_Controller_Action
{
    public function resendSurveyEmailAction()
    {
        $customersurveyHelper = Mage::helper('customersurvey/data');
        $orderNumber = $this->getRequest()->getParam('order_number');
        $scheduleModel = Mage::getModel('customersurvey/schedule');
        $schedule = $scheduleModel->load($orderNumber, 'order_number');
        if (!$schedule->getEmail()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
            $schedule = $customersurveyHelper->saveSchedule($order);
        }
        try {
            $customersurveyHelper->sendSurvey($schedule);
            $this->_getSession()->addSuccess($this->__('Survey Email has been sent Successfully.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Something went wrong!'));
        }
        $this->_redirectReferer();
    }
}
