<?php
/**
 * https://console.cloud.google.com/iam-admin/serviceaccounts
 * Copyright 2022 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// [START sheets_append_values]
header('Content-Type: application/json; charset=utf-8');
use Google\Client;
use Google\Service\Sheets;


function appendValues($spreadsheetId, $range, $valueInputOption, $data)
{
    /* Load pre-authorized user credentials from the environment.
       TODO(developer) - See https://developers.google.com/identity for
        guides on implementing OAuth2 for your application. */
    $client = new Google\Client();
    $client->setAuthConfig(__DIR__.'/auth.json');
    $client->addScope('https://www.googleapis.com/auth/spreadsheets');
    $service = new Google\Service\Sheets($client);
    try {
        $values = [$data]; //add the values to be appended
        //execute the request
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => $valueInputOption
        ];
        $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
        
        return json_encode([
            'status'=> true,
            'data' => $result
        ]);
    } catch (Exception $e) {
        return json_encode([
            'status'=> false,
            'data' => $e->getMessage()
        ]);
    }
    // [END sheets_append_values]
}

require 'vendor/autoload.php';
// appendValues('1-JrsMcc7DpPnoorb0q2PiCNwtIZ_hduBrbQuYjq0ko8', 'A1:B2', "RAW", ["Thiago", "foi", "acampar","la"]);

if($_SERVER['REQUEST_METHOD'] === "POST"){
    if(!isset($_POST['planilha_id']) && !isset($_POST['data'])){
        echo json_encode([
            'status'=> false,
            'msg' => 'campos planilha_id e data são obrigatórios e precisam ter valores'
        ]);
        return;
    }else{
        echo appendValues($_POST['planilha_id'], 'A1:B2', "RAW", $_POST['data']);
    }
}


