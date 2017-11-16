<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        HttpService.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        25.10.17
 */

namespace AffiliconApiClient\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class HttpService
{
    /** @var Client */
    protected $httpClient;
    protected $endpoint;
    /** @var  Response $response */
    protected $response;
    protected $headers;
    protected $body;
    protected $data;


    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
        $this->httpClient = new Client();
        return $this;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return object
     */
    public function body()
    {
        $responseBody = json_decode($this->response->getBody(), true);

        if (array_exists('data', $responseBody)) {
            $responseBody['data'] = (object)$responseBody['data'];
        }

        return (object) $responseBody;
    }

    public function data()
    {
        return $this->body()->data;
    }

    private function request($method, $route, $body = [])
    {
        $url = $this->endpoint . $route;

        $this->response = $this->httpClient->request($method, $url, [
            'headers' => $this->getHeaders(),
            'json' => $body
        ]);

        return $this;
    }

    /**
     * @param string $route
     * @param array $body
     * @return $this
     */
    public function post($route, $body = [])
    {
        return $this->request('POST', $route, $body);
    }

    /**
     * @param string $route
     * @return $this
     */
    public function get($route)
    {
        return $this->request('POST', $route);
    }

    /**
     * @param $route
     * @param array $body
     * @return HttpService
     */
    public function put($route, $body = [])
    {
        return $this->request('PUT', $route, $body);
    }

    /**
     * @param $route
     * @param array $body
     * @return HttpService
     */
    public function patch($route, $body = [])
    {
        return $this->request('PATCH', $route, $body);
    }

    /**
     * @param $route
     * @param array $body
     * @return HttpService
     */
    public function delete($route, $body = [])
    {
        return $this->request('DELETE', $route, $body);
    }

}