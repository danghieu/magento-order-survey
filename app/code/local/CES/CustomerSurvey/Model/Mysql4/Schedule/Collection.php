<?php

/**
 * Class CES_CustomerSurvey_Model_Mysql4_Schedule_Collection
 *
 * @author Hieu Nguyen <hieu.nguyen@codeenginestudio.com>
 */
class CES_CustomerSurvey_Model_Mysql4_Schedule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('customersurvey/schedule');
    }

}
