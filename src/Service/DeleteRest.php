<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class DeleteRest
{
    /*
     * Constucting an HttpClient to get Json
     */
    public function delete(string $url, int $id)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('DELETE', $url.'/'.$id);
        return true;
    }

}