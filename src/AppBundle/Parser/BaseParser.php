<?php

namespace AppBundle\Parser;


class BaseParser {

    protected $client;
    protected $doctrine;

    /**
     * @param \Goutte\Client $client
     */
    public function __construct(\Goutte\Client $client, $doctrine)
    {
        $client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYPEER, false);
        $this->client = $client;
        $this->doctrine = $doctrine;
    }
} 