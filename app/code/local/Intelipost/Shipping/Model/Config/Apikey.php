<?php 

require_once Mage::getBaseDir('code') . '/local/Intelipost/Shipping/Model/Resource/Quote.php';
require_once Mage::getBaseDir('code') . '/local/Intelipost/Shipping/Model/Resource/Volume.php';

class Intelipost_Shipping_Model_Config_Apikey
  extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{

  public function save() {
        $api_key = $this->getValue();
        $token = $this->getData('groups/intelipost/fields/token/value');
        $password = $this->getData('groups/intelipost/fields/password/value');
        // $username = $this->getData('groups/intelipost/fields/account/value');
        $password = 'mate20mg';
        $username = 'projecta';
        $helper = Mage::helper('intelipost');

        $volume = new Intelipost_Model_Request_Volume();
        $volume->weight = 10;
        $volume->volume_type = 'BOX';
        $volume->cost_of_goods = "10";
        $volume->width = 1;
        $volume->height = 1;
        $volume->length = 1;

        $quote = new Intelipost_Model_Request_Quote();
        $quote->origin_zip_code = "04037-002";
        $quote->destination_zip_code = "04037-002";
        array_push($quote->volumes, $volume);

        $body = json_encode($quote);
        $s = curl_init();
        curl_setopt($s, CURLOPT_TIMEOUT, 5000);
        curl_setopt($s, CURLOPT_URL, "https://api.intelipost.com.br/api/v1/quote");
        if ($username != null && $password != null) {
            curl_setopt($s, CURLOPT_USERPWD, $username . ":" . $password);
        }
        curl_setopt($s, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json", "api_key: $api_key"/*, "token: testtoken"*/));
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_ENCODING , "");
        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($s, CURLOPT_POSTFIELDS, $body);

        $responseBody = curl_exec($s);

        $response = json_decode($responseBody);
        curl_close($s);

        if (!isset($response->status)) {
           // Mage::getSingleton('core/session')->addWarning($helper->__('Could not validate username and password!'));
            Mage::throwException($helper->__('Invalid ApiKey!'));
        }

        parent::save();
    }
}

