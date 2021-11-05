<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CallApiService
{
    private string $apiHost;
    private string $apiKey;
    private $client;
    private $cachepool;

    public function __construct(string $apiHost, string $apiKey,HttpClientInterface $client)
    {
        $this->apiHost   = $apiHost;
        $this->apiKey    = $apiKey;
        $this->client    = $client;
        $this->cachePool = new FilesystemAdapter('', 0, "cache");        
    }
    
    public function clearCache() {
        $this->cachePool->clear(); 
    }

    public function callApi(string $endpoint, array $data = [], string $method = 'GET'): array
    {

        // Check if the result already exists in the cache. If so return the value stored into the cache otherwize call the API.
        $key = sha1($endpoint . serialize($data) . $method);

        if ( $this->cachePool->hasItem($key) ) {
            
            $item = $this->cachePool->getItem($key);
            if ($item->isHit()) {
                $res = unserialize( $item->get() );
                return $res;
            } 
          
        }

        $response = $this->client->request($method, sprintf('https://%s/%s', $this->apiHost, $endpoint), [
            'query' => $data,
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key'  => $this->apiKey,
            ],
        ]); 

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
             // update the cache 
            $item = $this->cachePool->getItem( $key );
            $item->set( serialize($response->toArray()) );
            $this->cachePool->save($item); 
            
            return $response->toArray();
        }
        throw new ApiException($response);     

    }
}

