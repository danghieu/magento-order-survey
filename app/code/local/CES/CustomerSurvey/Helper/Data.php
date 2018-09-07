<?php

/**
 * Class CES_Catalog_Helper_Data
 */
class CES_CustomerSurvey_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_SURVEY_URL = 'customersurvery/customersurvery/survey_url';
    const XML_PATH_TIME_TO_SEND_SURVEY = 'customersurvery/customersurvery/time_to_send_survey';
    const XML_PATH_SURVEY_FORM = 'customersurvery/customersurvery/survey_form';
    public function createARandomHash() {
        return md5(uniqid(rand(), true));
    }

    public function surveyUrl() {
    	return Mage::getBaseUrl().Mage::getStoreConfig(self::XML_PATH_SURVEY_URL);
    }

    public function getTimeToSendSurvey() {
        Mage::getStoreConfig(self::XML_PATH_SURVEY_URL) *60*60;
    }

    public function sendSurvey($schedule) {
        $emailTempVariables = [];
        $emailTempVariables['first_name'] = $schedule->getFirstName();
        $emailTempVariables['last_name'] = $schedule->getLastName();
        $emailTempVariables['email'] = $schedule->getEmail();
        $order = Mage::getModel('sales/order')->loadByIncrementId($schedule->getOrderNumber());
        $orderCreatedAt = $order->getCreatedAt();
        $emailTempVariables['order_created_at'] = $orderCreatedAt;
        $token = $schedule->getToken();
        $emailTempVariables['survey_url'] = $this->surveyUrl().'?token='.$token;

        $this->sendSurveyEmail($emailTempVariables, $schedule->getEmail(), $schedule->getLastName());
        $schedule->setSent(1)->save();
        $message = $this->__('Survey Email Sent');
        $this->addCommentToOrder($order, $message);
    }

    public function sendSurveyEmail($emailTempVariables, $recipientEmail, $recipientName) {
        $sender = [];
        $sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
        $sender['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        $emailTemplate = Mage::getModel('core/email_template');
        $data = new Varien_Object();
        $emailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->setReplyTo($sender['email'])
            ->sendTransactional(
                Mage::getStoreConfig('customersurvery/customersurvery/template'),
                $sender,
                $recipientEmail,
                $recipientName,
                array('data' => $data->setData($emailTempVariables))
            );

        if (!$emailTemplate->getSentSuccess()) {
            throw new Exception();
        }
        return true;
    }

    public function saveSchedule($order) {
        $scheduleModel = Mage::getModel('customersurvey/schedule');
        $orderNumber = $order->getIncrementId();
        $billingAddress = $order->getBillingAddress();
        $firstName = $billingAddress->getFirstname();
        $lastName = $billingAddress->getLastname();
        $email = $billingAddress->getEmail();
        $token = Mage::helper('customersurvey/data')->createARandomHash();
        $data = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'order_number' => $orderNumber,
            'token' => $token
        );
        return $scheduleModel->setData($data)->save();
    }

    public function getFormClass($title) {
        $class = trim($title);
        $class = str_replace(" ","_",$class);
        $class = strtolower($class);
        return $class;
    }

    public function getSchedulesByToken($token) {
        $scheduleModel = Mage::getModel('customersurvey/schedule');
        $collection = $scheduleModel->getCollection()
                ->addFieldToFilter('token', array('eq' => $token));
        return $collection;
    }

    public function getSurveyFormName() {
        return Mage::getStoreConfig(self::XML_PATH_SURVEY_FORM);
    }

    private function addCommentToOrder($order, $message) {
        $order->addStatusHistoryComment($message);
        $order->save();
    }
}
