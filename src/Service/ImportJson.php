<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class ImportJson
{
    /*
     * Constucting an HttpClient to get Json
     */
    public function ImportJson($url):Array
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url);
        $this->verifingStatus($response);
        return $this->getJsonDecode($response);
    }

    /*
    * Verification of status code and throw Exception if not 200 or 302
    */
    private function verifingStatus($response)
    {
        $statusCode = $response->getStatusCode();
        if(!in_array($statusCode , [200,302]))
        {
            throw new \Exception('Error with api. Status code is :'.$statusCode);
        }        
    }

    /*
    * Transform Json into object
    */
    public function getJsonDecode($response)
    {
        $content = json_decode($response->getContent());
        $response->cancel();
        return $content;
    }

}