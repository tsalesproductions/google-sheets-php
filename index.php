<?php
# RESOLVER ERRO SSL
    # https://stackoverflow.com/questions/35638497/curl-error-60-ssl-certificate-prblm-unable-to-get-local-issuer-certificate

#GOOGLE SHEET PHP
    # https://developers.google.com/sheets/api/guides/values

#DELETAR ITEM
    # https://stackoverflow.com/questions/41000763/how-can-i-delete-rows-from-google-sheet-with-php

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
// header('Content-Type: application/json; charset=utf-8');
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;

require 'vendor/autoload.php';

# ID DA PLANILHA
define("sheet_id", '1ruzFijAtYDaY3HWO45lDA7kYUBkph_g9IIkqL9-ntDc');

# LOCAL ONDE ESTÁ SALVO AS CREDÊNCIAIS
define('auth_config_file', __DIR__.'/auth.json');

# STATUS DA APLICAÇÃO: offline=local;online=produção;
define("application_access_type", "offline");

# NOME DA APLICAÇÃO
define("application_name", 'Google Sheets API PHP Quickstart');

define("vtex_host", "https://tfcvvp.vtexcommercestable.com.br");
define("vtex_app_id", "vtexappkey-tfcvvp-IVYQAD");
define("vtex_app_token", "PRLFPBJOCWWAWGRZHGVSFNYPHNTBQBLFGXQCXOCVFXJNWYAPNHYYHMIBTWXBIIYBLRTZQOBSDZNIEKSJNENICMXUOOJBQANWYHCFEBZZFFVHYAWIDNODVQYYTZLBEACC");

# QUANTIDADE DE COLUNAS QUE A TABELA IRÁ BUSCAR: 
# A2:D999 = QUERO QUE ME TRAGA AS LINHAS DE A2(PARA IGNORAR OS TÍTULOS) ATÉ O D(MINHA ÚLTIMA COLUNA). 
# ALÉM DISSO QUERO QUE ELE ME TRAGA OS PRIMEIROS 999 RESULTADOS DESSE DOCUMENTO
define("sheet_range_find", 'A6:F999');

function appendValues($spreadsheetId, $range, $valueInputOption, $data)
{
    /* Load pre-authorized user credentials from the environment.
       TODO(developer) - See https://developers.google.com/identity for
        guides on implementing OAuth2 for your application. */
    $client = new Google\Client();
    $client->setApplicationName(application_name);
    $client->setScopes('https://www.googleapis.com/auth/spreadsheets');
    $client->setAuthConfig(auth_config_file);
    $client->setAccessType(application_access_type);
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
        var_dump($e->getMessage());
        return json_encode([
            'status'=> false,
            'data' => $e->getMessage()
        ]);
    }
    // [END sheets_append_values]
}

function batchGetValues($spreadsheetId)
{
    /* Load pre-authorized user credentials from the environment.
    TODO(developer) - See https://developers.google.com/identity for
    guides on implementing OAuth2 for your application. */       
    $client = new Google\Client();
    $client->setApplicationName(application_name);
    $client->setScopes('https://www.googleapis.com/auth/spreadsheets');
    $client->setAuthConfig(auth_config_file);
    $client->setAccessType(application_access_type);
    $client->addScope(Google\Service\Drive::DRIVE);
    $service = new Google_Service_Sheets($client);
    try{
        $ranges = sheet_range_find;
        $params = array(
            'ranges' => $ranges,
        );
        //execute the request
        $result = $service->spreadsheets_values->batchGet($spreadsheetId, $params);
        // printf("%d ranges retrieved.", count($result->getValueRanges()));
        return $result;
    }
    catch(Exception $e) {
        // TODO(developer) - handle error appropriately
        echo 'Message: ' .$e->getMessage();
    }
}

# ASSOCIA E ORGANIZA AS CATEGORIAS E SUB CATEGORIAS
function getListCategoryes(){
    $values = batchGetValues(sheet_id)->valueRanges[0]->values;

    $categorieswithChild = [];

    $last = '';

    foreach ($values as $key => $value) {
        //$has = array_search($needle, $value);
        if($value){
            $found_key = array_search($value[2], array_column($categorieswithChild, 'name'));
            if($found_key == false && $value[2] !== ''){
                array_push($categorieswithChild, [
                    'name' => $value[2],
                    'title' => $value[3],
                    'meta_title' => $value[4],
                    'meta_description' => $value[5],
                    'childs' => []
                ]);
            }
        }
    }

    // var_dump($categorieswithChild);

    foreach ($values as $key => $value) {
        if($value){
            if($value[2] !== ''){
                $last = $value[2];
            }else{
                // echo $last.'->'.$value[3].'<br>  ';
                $seachCategory = array_search($last, array_column($categorieswithChild, 'name'));
                if($seachCategory !== false){
                    $childrens = $categorieswithChild[$seachCategory]['childs'];
                    $searchChildren = array_search($value[3], array_column($childrens, 'name'));
                    if($searchChildren == false &&  isset($value[5])){
                        array_push($categorieswithChild[$seachCategory]['childs'], [
                            'name' => $value[3],
                            'title' => $value[3],
                            'meta_title' => $value[4],
                            'meta_description' => $value[5],
                        ]);
                    }
                }
            } 
        }
    }

    return $categorieswithChild;
    
}

# FAZ A REQUISIÇÃO GET
# https://packagist.org/packages/guzzlehttp/guzzle
function getdata($url,$headers=[]) 
{ 
    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', $url, [
        'headers' => [
            "Content-Type: application/json",
            "Accept: application/json",
            "X-VTEX-API-AppKey: ".vtex_app_id,
            "X-VTEX-API-AppToken: ".vtex_app_token
        ]        
    ]);

    return json_decode($response->getBody(), true);
}

# CRIA A CATEGORIA PRIMÁRIA; RETORNA O ID APÓS CRIADO;
# TAMBÉM É REAPROVEITADA PARA CRIAR A SUBCATEGORIA FILHO
function createFirstStageCategory($data, $fatherCategoryId = null){
    $body = '{
        "Name":"'.strtoupper($data['name']).'",
        "FatherCategoryId":"'.$fatherCategoryId.'",
        "Title":"'.$data['title'].'",
        "Description":"'.$data['meta_title'].'",
        "Keywords":"'.substr(implode(",", explode(" ", $data['title'])),0, -1).'",
        "IsActive":true,
        "LomadeeCampaignCode":null,
        "AdWordsRemarketingCode":null,
        "ShowInStoreFront":true,
        "ShowBrandFilter":false,
        "ActiveStoreFrontLink":true,
        "GlobalCategoryId":166,
        "StockKeepingUnitSelectionMode":"SPECIFICATION",
        "Score":null
    }';

    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST', vtex_host."/api/catalog/pvt/category", [
        'body'  => $body,
        'headers' => [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "X-VTEX-API-AppKey" => vtex_app_id,
            "X-VTEX-API-AppToken" => vtex_app_token
        ],
    ]);

    $res = json_decode($response->getBody(), true);

    if(isset($res['Id'])){
        echo (!$fatherCategoryId ? "- Categoria" : "--Subcategoria")." criada com sucesso!<br>";
        return $res['Id'];
    }else{
        return false;
    }
}

function createSecondStageCategory($value, $vtexCList, $seachCategory){
    $fatherCategoryId = $vtexCList[$seachCategory]['id'];
    $childsVtex = $vtexCList[$seachCategory]['children'];
    $childsPlan = $value['childs'];

    foreach ($childsPlan as $key => $chd) {
        $searchChild = array_search(strtoupper($chd['name']), array_column($childsVtex, 'name'));
        if($searchChild === false){ # CASO NÃO TENHA A SUBCATEGORIA, ELE CRIA
            createFirstStageCategory($chd, $fatherCategoryId);
        }
    }
}

# FAZ A CONFERÊNCIA NA API DA VTEX E TAMBÉM NA PLANILHA
# VERIFICA SE EXISTE OU NÃO A CATEGORIA, SE NÃO EXISTIR, ELE CRIA O PRIMEIRO ESTÁGIO DA CATEGORIA E DEPOIS CRIA OS PRÓXIMOS
# CASO EXISTA, VERIFICA O SEGUNDO ESTÁGIO E DEPOIS CRIA
# OBS: A API DE CATEGORIAS TEM CACHE DE 5~10M, NÃO RODE NOVAMENTE ANTES DESSE TEMPO OU IRÁ DUPLICAR
function createOrUpdateCategory(){
    $vtexCList = getdata(vtex_host."/api/catalog_system/pub/category/tree/1"); # BUSCA A API DA VTEX
    
    // var_dump($vtexCList);

    foreach (getListCategoryes() as $key => $value) {
        if($key >= 1) break;
        $seachCategory = array_search(strtoupper($value['name']), array_column($vtexCList, 'name'));

        if($seachCategory === false){ # caso não tenha a categoria primária
            $fId = createFirstStageCategory($value); # CRIA A CATEGORIA CRIADA

            if($fId){ # SE CRIAR A PRIMÁRIA CORRETAMENTE, AGORA CRIA AS SUBS
                foreach ($value['childs'] as $key => $chd) {
                    createFirstStageCategory($chd, $fId);
                }
            }
        }else{ #caso tenha, verifica e cria o segundo estágio das categorias
            createSecondStageCategory($value, $vtexCList, $seachCategory);
        }
        
    }
}

createOrUpdateCategory();
