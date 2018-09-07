<?php

/**
 * Class CES_CustomerSurvey_Model_Mysql4_Schedule
 *
 * @author Hieu Nguyen <hieu.nguyen@codeenginestudio.com>
 */
class CES_CustomerSurvey_Model_Mysql4_Schedule extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('customersurvey/schedule', 'id');
    }
}
