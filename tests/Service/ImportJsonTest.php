<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\ImportJson;
use Symfony\Component\HttpClient\HttpClient;

class ImportJsonTest extends TestCase
{
   
    public $url = "https://jsonplaceholder.typicode.com/users";

    public function testUrlImportJson()
    {   
        $response = new ImportJson();
        $response->importJson("https://jsonplaceholder.typicode.com/users");
        
        $this->assertTrue(is_object($response));
    }
    
    public function testVerifingStatus()
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $this->url);
        $this->assertEquals(
            200, // or Symfony\Component\HttpFoundation\Response::HTTP_OK
            $response->getStatusCode()
        );
    }
    

}



