<?php
/**
 * PHP MMSoap Example code
 *
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

// Uncomment the following line for debugging information
//ini_set('display_errors', true);

require('mmsoap/MMSoap.php');

// Set up account details
$username = 'YourUserName001';
$password = 'y0urpassw0rd';

// Set up sendMessage parameters
// http://www.acma.gov.au/Citizen/Consumer-info/All-about-numbers/Special-numbers/fictitious-numbers-for-radio-film-and-television-i-acma
$recipients = array('+61491570156');
$origin     =       "+61491570157";
$message    = 'Hello from messagemedia-php!';

// Set up SOAP Options
$options = array( // Put options here to override defaults
    // Example Proxy Options

    //WsdlClass::WSDL_PROXY_HOST => '127.0.0.1',
    //WsdlClass::WSDL_PROXY_PORT => '8888'

);

// Create new MMSoap class
$soap = new MMSoap($username, $password, $options);

// Check user info
echo "\n** User Info\n";
$response       = $soap->getUserInfo();
if ($response instanceof SoapFault) {
    exit('Error: ' . $response->getMessage());
}
$result         = $response->getResult();
$accountDetails = $result->accountDetails;
echo 'Account type: ' . $accountDetails->type . "\n";
echo $result->accountDetails->creditRemaining . " credits remaining\n";

// Send messages
echo "\n** Send Messages\n";
echo "Sending '$message' to " . implode(', ', $recipients) . "\n";

// Example of sending a message
$response = $soap->sendMessages($recipients, $message, null, $origin);
$result   = $response->getResult();
echo $result->sent . ' sent / ' . $result->scheduled . ' scheduled / ' . $result->failed . " failed\n";

// Example of sending a message at a scheduled date and time
echo "\n** Schedule A Message\n";
echo "Scheduling to send '$message' on the 28th July 2016 5:10pm to " . implode(', ', $recipients) . "\n";
$scheduled = "2016-07-28T17:10:00";
$response = $soap->sendMessages($recipients, $message, $scheduled, $origin);
$result   = $response->getResult();
echo $result->sent . ' sent / ' . $result->scheduled . ' scheduled / ' . $result->failed . " failed\n";

// Get blocked numbers
echo "\n** Get Blocked Numbers\n";
$response   = $soap->getBlockedNumbers();
$result     = $response->getResult();
$recipients = $result->recipients->recipient;
if (isset($recipients)) {
    foreach ($recipients as $recipient) {
        echo 'The number ' . $recipient . " is blocked\n";
    }
} else {
    echo "No numbers in your blocked list\n";
}
