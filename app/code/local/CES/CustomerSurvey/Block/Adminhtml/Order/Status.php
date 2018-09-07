<?php

class CES_CustomerSurvey_Block_Adminhtml_Order_Status extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    protected function _beforeToHtml()
    {
        $this->getOrder();
        parent::_beforeToHtml();
    }

    protected $_order;

    public function getOrder()
    {
        if (is_null($this->_order)) {
            if (Mage::registry('current_order')) {
                $order = Mage::registry('current_order');
            }
            elseif (Mage::registry('order')) {
                $order = Mage::registry('order');
            }
            else {
                $order = new Varien_Object();
            }

            $this->_order = $order;
        }

        return $this->order;
    }

    public function canShow() {
        if($this->_order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE) {
            return true;
        }
        return false;
    }

    public function getResendSurveyLink() {
        return Mage::helper("adminhtml")->getUrl("adminhtml/schedule/resendSurveyEmail", array("order_number"=> $this->_order->getIncrementId()));
    }

    public function surveyStatus() {
        $orderNumber = $this->_order->getIncrementId();
        $scheduleModel = Mage::getModel('customersurvey/schedule');
        return $scheduleModel->load($orderNumber, 'order_number');
    }

}
