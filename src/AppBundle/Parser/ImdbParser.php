<?php

namespace AppBundle\Parser;

use AppBundle\AppBundle;
use Symfony\Component\DomCrawler\Crawler;
use AppBundle\Entity\Movie;


class ImdbParser extends BaseParser {

    /**
     * @param $imdbId
     * @return Movie
     */
    public function parseMoviePage($imdbId)
    {
        $movie = new Movie();
        $movie->setImdbId($imdbId);

        //try to get data in english
        $this->client->getClient()->setDefaultOption("headers/Accept-Language", "en-us,en;q=0.5");

        //build the url from imdbid
        $crawler = $this->client->request("GET", "http://www.imdb.com/title/tt" . $imdbId);

        $movie->setTitle( $this->extractTitle($crawler) );
        $movie->setYear( $this->extractYear($crawler) );
        $movie->setDirector( $this->extractDirector($crawler) );
        $movie->setCast( $this->extractCast($crawler) );
        $movie->setPlot( $this->extractPlot($crawler) );
        $movie->setImdbRating( $this->extractRating($crawler) );
        $movie->setNumVotes( $this->extractNumVotes($crawler) );
        $movie->setPosterUrl( $this->extractPosterUrl($crawler) );

        $genres = $this->extractGenres($crawler);
        $genreRepo = $this->doctrine->getRepository("AppBundle\Entity\Genre");
        foreach($genres as $name){
            $genre = $genreRepo->findOneByName($name);
            if (!$genre){
                $genre = new \AppBundle\Entity\Genre($name);
                $em = $this->doctrine->getManager();

                $em->persist($genre);
                $em->flush();
            }
            $movie->addGenre($genre);
        }

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
    protected function extractCast(Crawler $crawler)
    {

        $castCrawler = $crawler->filter('div[itemprop="actors"] span[itemprop="name"]');
        $actors = [];
        if (count($castCrawler) > 0){
            $castCrawler->each(function($nodeCrawler, $i) use(&$actors){
                $actors[] = $nodeCrawler->text();
            });
            $actorsString = implode(", ", $actors);
            return $actorsString;
        }

        return null;
    }


    /**
     * @param Crawler $crawler
     * @return null|string
     */
    protected function extractPlot(Crawler $crawler)
    {
        $plotCrawler = $crawler->filter('p[itemprop="description"]');
        if (count($plotCrawler) > 0){
            return trim($plotCrawler->text());
        }

        return null;
    }

    /**
     * @param Crawler $crawler
     * @return null|string
     */
    protected function extractGenres(Crawler $crawler)
    {
        $genreCrawler = $crawler->filter('.infobar span[itemprop="genre"]');
        if (count($genreCrawler) > 0){
            $genres = array();
            $genreCrawler->each(function($nodeCrawler, $i) use(&$genres){
                $genres[] = $nodeCrawler->text();
            });
            return $genres;
        }

        return array();
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