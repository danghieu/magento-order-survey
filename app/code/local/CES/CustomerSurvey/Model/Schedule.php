<?php

/**
 * Class CES_CustomerSurvey_Model_Schedule
 *
 * @author Hieu Nguyen <hieu.nguyen@codeenginestudio.com>
 */
class CES_CustomerSurvey_Model_Schedule extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customersurvey/schedule');
    }

    public function _beforeSave()
    {
        if ($this->isObjectNew()) {
            $this->setCreatedTime(Mage::getModel('core/date')->timestamp());
        }
        $this->setUpdatedTime(Mage::getModel('core/date')->timestamp());
        if ($this->getSent()) {
            $this->setSentTime(Mage::getModel('core/date')->timestamp());
        }
        return parent::_beforeSave();
    }

    public function willBeSend() {
        return date('Y-M-d', strtotime($this->getCreatedTime()) + Mage::helper('customersurvey/data')->getTimeToSendSurvey());
    }
}
