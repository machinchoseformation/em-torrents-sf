<?php

    namespace AppBundle\Command;

    use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    class ParseKickAssCommand extends ContainerAwareCommand
    {
        protected $baseUrl = "http://kickass.to/movies/3/?field=seeders&sorder=desc";
        protected $oi;

        public function configure()
        {
            $this
                ->setName("em:parse:kickass")
                ->setDescription("Extracts movies from KickAss Torrents");
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $this->oi = $output;
            $container = $this->getContainer();
            $kickass_parser = $container->get('kickass_parser');
            $imdb_parser = $container->get('imdb_parser');
            $validator = $container->get('validator');

            $output->writeln("Parsing list page...");

            //get all links to detailled page
            $linksCrawler = $kickass_parser->parseListPage($this->baseUrl);

            //only 4 for faster testing
           /*$start = 8;
            $end = $start+5;
            $linksCrawler = $linksCrawler->reduce(function($node, $i) use($start,$end){
                return $i >= $start && $i <= $end;
            });*/

            //add all detail links in an array
            $detailLinks = array();
            $linksCrawler->each(function($linkCrawler) use (&$detailLinks) {
                $detailLinks[] = $linkCrawler->link()->getUri();
            });

            $doctrine = $container->get('doctrine');
            $movieRepo = $doctrine->getRepository("AppBundle\Entity\Movie");
            $torrentRepo = $doctrine->getRepository("AppBundle\Entity\Torrent");

            //loop through all links in array
            foreach($detailLinks as $detailUri){
                $output->writeln( "\r\nGetting " . $detailUri . "...");

                //get detailled torrent info
                $torrent = $kickass_parser->parseDetailPage( $detailUri );

                //torrent already in db ?
                //maybe update here instead...
                if ($torrentRepo->findOneByInfoHash($torrent->getInfoHash())){
                    $output->writeln("Torrent already there !");
                    continue;
                }

                //movie already in db ?
                $movie = $movieRepo->findOneByImdbId( $torrent->getImdbId() );
                if (!$movie){
                    //get movie info from imdb
                    $movie = $imdb_parser->parseMoviePage( $torrent->getImdbId() );

                    //check if valid
                    $movieValidationErrors = $validator->validate($movie);
                    if (count($movieValidationErrors) > 0){
                        $this->showValidationErrors($movieValidationErrors, "Movie not good enough !");
                        continue;
                    }

                    //save it
                    $this->saveEntity($movie, "Movie");
                }
                else {
                    $output->writeln("Movie already there !");
                }

                //assign movie to torrent
                $torrent->setMovie($movie);

                //validate torrent
                $torrentValidationErrors = $validator->validate($torrent);
                if (count($torrentValidationErrors) > 0){
                    $this->showValidationErrors($torrentValidationErrors, "Torrent not good enough !");
                    continue;
                }

                //save if valid
                $this->saveEntity($torrent, "Torrent");
            }
        }

        protected function showInfo($message)
        {
            $this->oi->writeln("<info>" . $message . "</info>");
        }

        protected function saveEntity($entity, $type)
        {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $em->persist($entity);
            $em->flush(); //flush right away to avoid data lost on error
            $this->oi->writeln("<info>$type saved !</info>");
        }

        protected function showValidationErrors($errors, $message = null)
        {
            if ($message){
                $this->oi->writeln("<comment>".$message."</comment>");
            }

            foreach($errors as $e){
                $invalidValue = (empty($e->getInvalidValue())) ? "null" : $e->getInvalidValue();
                $this->oi->writeln("" . $e->getMessage() . "(" . $invalidValue . ")");
            }
        }

    }