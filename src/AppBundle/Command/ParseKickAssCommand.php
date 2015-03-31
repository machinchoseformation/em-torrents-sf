<?php

    namespace AppBundle\Command;

    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Output\OutputInterface;

    class ParseKickAssCommand extends Command
    {
        protected $baseUrl = "http://kickass.to/movies/##page##/?field=seeders&sorder=desc";
        protected $oi; //output interface
        protected $doctrine;
        protected $validator;
        protected $kickass_parser;
        protected $imdb_parser;

        public function __construct($doctrine, $validator, $kickass_parser, $imdb_parser)
        {
            parent::__construct();
            $this->doctrine = $doctrine;
            $this->validator = $validator;
            $this->kickass_parser = $kickass_parser;
            $this->imdb_parser = $imdb_parser;
        }

        public function configure()
        {
            $this
                ->setName("em:parse:kickass")
                ->setDescription("Extracts movies from KickAss Torrents")
                ->addArgument(
                    'page',
                    InputArgument::OPTIONAL,
                    'Which page to parse in kickass ?'
                );
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $this->oi = $output;

            $page = $input->getArgument('page');
            $page = ($page) ? $page : 1;
            $baseUrl = str_replace("##page##", $page, $this->baseUrl);

            $output->writeln("Parsing list page number $page...");

            //get all links to detailled page
            $detailLinks = $this->kickass_parser->parseListPage($baseUrl);

            $output->writeln("Looking for new torrents...");
            $this->getAndSaveNewData($detailLinks);
        }


        protected function getAndSaveNewData(array $detailLinks)
        {
            $movieRepo = $this->doctrine->getRepository("AppBundle\Entity\Movie");
            $torrentRepo = $this->doctrine->getRepository("AppBundle\Entity\Torrent");

            //loop through all links in array
            foreach($detailLinks as $detailUri){
                $this->oi->writeln( "\r\nGetting " . $detailUri . "...");

                //get detailled torrent info
                $torrent = $this->kickass_parser->parseDetailPage( $detailUri );

                //torrent already in db ?
                //maybe update here instead...
                if ($torrentRepo->findOneByInfoHash($torrent->getInfoHash())){
                    $this->oi->writeln("Torrent already there !");
                    continue;
                }

                //movie already in db ?
                $movie = $movieRepo->findOneByImdbId( $torrent->getImdbId() );
                if (!$movie){

                    //get movie info from imdb
                    $movie = $this->imdb_parser->parseMoviePage( $torrent->getImdbId() );

                    //check if valid
                    $movieValidationErrors = $this->validator->validate($movie);
                    if (count($movieValidationErrors) > 0){
                        $this->showValidationErrors($movieValidationErrors, "Movie not good enough !");
                        continue;
                    }

                    //save it, even if we do not save the torrent later on
                    $this->saveEntity($movie, "Movie");
                }
                else {
                    $this->oi->writeln("Movie already there !");
                }

                //assign movie to torrent
                $torrent->setMovie($movie);

                //validate torrent
                $torrentValidationErrors = $this->validator->validate($torrent);
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
            $em = $this->doctrine->getManager();
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
                $invalidValue = (!$e->getInvalidValue()) ? "null" : $e->getInvalidValue();
                $this->oi->writeln("" . $e->getMessage() . " (<error>" . $invalidValue . "</error>)");
            }
        }

    }