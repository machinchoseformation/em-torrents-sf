<?php

namespace AppBundle\Parser;


use AppBundle\Entity\Torrent;

class DataPersister {

    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }


    public function takeDecision(Torrent $torrent, $output)
    {
        $doctrine = $this->doctrine;
        $movieRepo = $doctrine->getRepository("AppBundle\Entity\Movie");
        $torrentRepo = $doctrine->getRepository("AppBundle\Entity\Torrent");
        $em = $doctrine->getManager();


        //torrent already there ?
        //maybe update here instead...
        if ($torrentRepo->findOneByInfoHash($torrent->getInfoHash())){
            return false;
        }

        $movie = $movieRepo->findOneByImdbId( $torrent->getImdbId() );
        if (!$movie){
            $movie = $imdb_parser->parseMoviePage( $torrent->getImdbId() );

            $movieValidationErrors = $validator->validate($movie);
            if (count($movieValidationErrors) > 0){
                $output->writeln("Skipping !!!");
                $output->writeln((string) $movieValidationErrors);
                return false;
            }

            $em->persist($movie);
            $em->flush();
        }

        $torrent->setMovie($movie);
        $em->persist($torrent);
        $em->flush();
    }

} 