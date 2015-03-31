<?php

    namespace AppBundle\Parser;

    use AppBundle\Entity\Movie;
    use AppBundle\Entity\Torrent;

    use Symfony\Component\DomCrawler\Crawler;

    class KickAssParser extends BaseParser {

        public function __construct(\Goutte\Client $client)
        {
            parent::__construct($client);
        }

        /**
         * @param string $listUrl
         * @return \Symfony\Component\DomCrawler\Crawler
         */
        public function parseListPage($listUrl)
        {
            $crawler = $this->client->request('GET', $listUrl);
            $linksCrawler = $crawler->filter("tr a.cellMainLink");

            return $linksCrawler;
        }

        /**
         * @param string $detailUri
         * @return Torrent
         */
        public function parseDetailPage($detailUri)
        {
            $torrent = new Torrent();

            $crawler = $this->client->request('GET', $detailUri);

            //extract torrent title
            $torrent->setTitle( $this->extractTitle($crawler) );
            $torrent->setSeeders( $this->extractSeeders($crawler) );
            $torrent->setLeechers( $this->extractLeechers($crawler) );

            //extract magnet link and info hash
            $torrent->setMagnetLink( $this->extractMagnetLink($crawler) );
            $torrent->setInfoHash( $this->extractInfoHashFromMagnetLink($torrent->getMagnetLink()) );

            //movie infos
            $torrent->setImdbId( $this->extractImdbId($crawler) );

            return $torrent;
        }

        /**
         * @param Crawler $crawler
         * @return null|string
         */
        protected function extractMagnetLink(Crawler $crawler)
        {
            $magnetLinkCrawler = $crawler->filter('a.magnetlinkButton');
            if (count($magnetLinkCrawler) > 0){
                return $magnetLinkCrawler->attr("href");
            }

            return null;
        }

        /**
         * @param Crawler $crawler
         * @return null|string
         */
        protected function extractTitle(Crawler $crawler)
        {
            $titleCrawler = $crawler->filter('span[itemprop="name"]');
            if (count($titleCrawler) > 0){
                return $titleCrawler->text();
            }

            return null;
        }

        /**
         * @param Crawler $crawler
         * @return null|string
         */
        protected function extractSeeders(Crawler $crawler)
        {
            $seedersCrawler = $crawler->filter('strong[itemprop="seeders"]');
            if (count($seedersCrawler) > 0){
                return (int) $seedersCrawler->text();
            }

            return null;
        }

        /**
         * @param Crawler $crawler
         * @return null|string
         */
        protected function extractLeechers(Crawler $crawler)
        {
            $leechersCrawler = $crawler->filter('strong[itemprop="leechers"]');
            if (count($leechersCrawler) > 0){
                return (int) $leechersCrawler->text();
            }

            return null;
        }

        protected function extractImdbId(Crawler $crawler)
        {
            $imdbId = null;
            $imdbIdCrawler = $crawler->filter('.dataList a')->reduce(function(Crawler $node, $i) {
                return strstr($node->link()->getUri(), "http://www.imdb.com/title/tt");
            });

            if (count($imdbIdCrawler) > 0){
                return $imdbIdCrawler->text();
            }

            return $imdbId;
        }

        /**
         * @param string $magnetLink
         * @return mixed InfoHash or null if not found
         */
        protected function extractInfoHashFromMagnetLink($magnetLink)
        {
            if (preg_match("#btih:([a-f0-9]{40})#i", $magnetLink, $matches)){
                return $matches[1];
            }

            return null;
        }

    }