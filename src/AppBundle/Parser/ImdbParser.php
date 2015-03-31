<?php

namespace AppBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use AppBundle\Entity\Movie;


class ImdbParser extends BaseParser {

    public function parseMoviePage($imdbId)
    {
        $movie = new Movie();
        $movie->setImdbId($imdbId);

        $crawler = $this->client->request("GET", "http://www.imdb.com/title/tt" . $imdbId);

        $movie->setTitle( $this->extractTitle($crawler) );
        $movie->setYear( $this->extractYear($crawler) );
        $movie->setDirector( $this->extractDirector($crawler) );
        $movie->setImdbRating( $this->extractRating($crawler) );
        $movie->setNumVotes( $this->extractNumVotes($crawler) );
        $movie->setPosterUrl( $this->extractPosterUrl($crawler) );

        return $movie;

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
    protected function extractYear(Crawler $crawler)
    {
        $yearCrawler = $crawler->filter('h1.header span.nobr a');
        if (count($yearCrawler) > 0){
            return (int) $yearCrawler->text();
        }

        return null;
    }

    /**
     * @param Crawler $crawler
     * @return null|string
     */
    protected function extractPosterUrl(Crawler $crawler)
    {
        $posterCrawler = $crawler->filter('#img_primary img');
        if (count($posterCrawler) > 0){
            return $posterCrawler->attr("src");
        }

        return null;
    }

    /**
     * @param Crawler $crawler
     * @return null|string
     */
    protected function extractDirector(Crawler $crawler)
    {
        $directorCrawler = $crawler->filter('div[itemprop="director"] span[itemprop="name"]');
        if (count($directorCrawler) > 0){
            return $directorCrawler->text();
        }

        return null;
    }

    /**
     * @param Crawler $crawler
     * @return null|string
     */
    protected function extractRating(Crawler $crawler)
    {
        $ratingCrawler = $crawler->filter('div[itemprop="aggregateRating"] span[itemprop="ratingValue"]');
        if (count($ratingCrawler) > 0){
            return (float) $ratingCrawler->text();
        }

        return null;
    }

    /**
     * @param Crawler $crawler
     * @return null|string
     */
    protected function extractNumVotes(Crawler $crawler)
    {
        $ratingCountCrawler = $crawler->filter('div[itemprop="aggregateRating"] span[itemprop="ratingCount"]');
        if (count($ratingCountCrawler) > 0){
            return (int) preg_replace("/[^0-9]/", "", $ratingCountCrawler->text() );
        }

        return null;
    }

} 