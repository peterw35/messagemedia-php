<?php
/**
 * Copyright 2014 MessageMedia
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require('Autoload.php');

class MMSoap {
    public function __construct($userId, $password, $options) {
        // SOAP Services
        $this->serviceCheck = new ServiceCheck($options);
        $this->serviceGet   = new ServiceGet($options);
        $this->serviceSend  = new ServiceSend($options);

        // Authentication object
        $this->authentication = new StructAuthenticationType($userId, $password);
    }

    /**
     * Check user information
     *
     * @return StructCheckUserResponseType
     */
    public function getUserInfo() {
        return $this->serviceCheck->checkUser(new StructCheckUserRequestType($this->authentication));
    }

    /**
     * Send a single message to one recipient
     *
     * @param $to
     * @param $message
     * @return StructSendMessagesResponseType
     */
    public function sendMessage($to, $message) {
        if (is_array($to)) {
            return $this->sendMessages($to, $message);
        }
        return $this->sendMessages(array($to), $message);
    }

    /**
     * Send a single message to multiple recipients
     *
     * @param $recipients
     * @param $message
     * @return StructSendMessagesResponseType
     */
    public function sendMessages($recipients, $message) {
        $recipientsStruct = array();

        foreach ($recipients as $recipient) {
            $recipientsStruct[] = new StructRecipientType($recipient);
        }

        $msgList = array(new StructMessageType(
            null,
            new StructRecipientsType($recipientsStruct),
            $message
        ));

        $messages    = new StructMessageListType($msgList);
        $requestBody = new StructSendMessagesBodyType($messages);
        $sendRequest = new StructSendMessagesRequestType($this->authentication, $requestBody);

        return $this->serviceSend->sendMessages($sendRequest);
    }

    public function getBlockedNumbers() {
        $requestBody = new StructGetBlockedNumbersBodyType(5);
        $getRequest  = new StructGetBlockedNumbersRequestType($this->authentication, $requestBody);
        return $this->serviceGet->getBlockedNumbers($getRequest);
    }
}