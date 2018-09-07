<?php

/**
 * Class CES_CustomerSurvey_Model_Observer
 */
class CES_CustomerSurvey_Model_Observer
{
    public $customersurveyHelper;

    public function __construct() {
        $this->customersurveyHelper = Mage::helper('customersurvey/data');
    }

    public function sendSurvey() {
        try {
            $items = $this->prepareOrderToSendSurvey();
            /* @var $translate Mage_Core_Model_Translate */
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $this->customersurveyHelper->sendSurvey($item);
                }
                $translate->setTranslateInline(true);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $translate->setTranslateInline(true);
        }
    }

    private function prepareOrderToSendSurvey() {
        $timeToSend = Mage::getModel('core/date')->timestamp() - Mage::helper('customersurvey/data')->getTimeToSendSurvey();
        $scheduleModel = Mage::getModel('customersurvey/schedule');
        // get order haven't sent
        $collection = $scheduleModel->getCollection()
            ->addFieldToFilter('created_time', array('to' => date('Y-m-d H:i:s', $timeToSend)))
            ->addFieldToFilter('sent', 0);
        return $collection;
    }

    public function saveSchedule(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        if ($this->canSaveSchedule($order)) {
            try {
                $this->customersurveyHelper->saveSchedule($order);
            } catch (Exception $e) {
                Mage::logException($e);
            }

        }

    }

    private function canSaveSchedule($order) {
        if($order->getState() != Mage_Sales_Model_Order::STATE_COMPLETE) {
            return false;
        } else {
            $scheduleModel = Mage::getModel('customersurvey/schedule');
            $orderNumber = $order->getIncrementId();
            $collection = $scheduleModel->getCollection()->addFieldToFilter('order_number', $orderNumber);
            if (count($collection) > 0) {
                return false;
            }
        }
        return true;
    }

    public function insertSurveyBlock(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if (($block->getNameInLayout() == 'order_info') && ($child = $block->getChild('customersurvey.order.status'))) {
            $transport = $observer->getTransport();
            if ($transport) {
                $html = $transport->getHtml();
                $transport->setHtml($child->toHtml().$html);
            }
        }
    }
}
