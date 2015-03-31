<?php

namespace AppBundle\Parser;


class BaseParser {

    protected $client;

    /**
     * @param \Goutte\Client $client
     */
    public function __construct(\Goutte\Client $client)
    {
        $client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYPEER, false);
        $this->client = $client;
    }
} 