<?php

    namespace AppBundle\Command;

    use AppBundle\Entity\MovieStatus;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Output\OutputInterface;

    class ParseKickAssCommand extends Command
    {
        //base url to parse
        //the ##page## will be replaced by the page number
        protected $baseUrl = "http://kickass.to/movies/##page##/?field=seeders&sorder=desc";
        protected $oi; //output interface
        protected $doctrine; //doctrine service
        protected $validator;   //validator service
        protected $kickass_parser;  //parser for kickass torrents (service)
        protected $imdb_parser;     //parser for imdb_parser (service)
        protected $logger;  //log service

        //see services.yml for injection
        public function __construct($doctrine, $validator, $kickass_parser, $imdb_parser, $logger)
        {
            parent::__construct(); //parent's contructor, required
            $this->doctrine = $doctrine;
            $this->validator = $validator;
            $this->kickass_parser = $kickass_parser;
            $this->imdb_parser = $imdb_parser;
            $this->logger = $logger;
        }

        //set the name, description, arguments and option
        public function configure()
        {
            $this
                ->setName("em:parse:kickass")
                ->setDescription("Extracts movies from KickAss Torrents")
                //we can provide an optionnal number of "results" pages to parse from
                ->addArgument(
                    'pages',
                    InputArgument::OPTIONAL,
                    'How many pages to parse in kickass ?'
                );
        }

        //called when em:parse:kickass in console
        /**
         * @param InputInterface $input
         * @param OutputInterface $output
         * @return int|null|void
         */
        protected function execute(InputInterface $input, OutputInterface $output)
        {
            //logs are in app/logs/
            $this->logger->info("Parsing kickass !");

            //referencable by other methods
            $this->oi = $output;

            //get the optionnal argument, default to 3 pages
            $pages = $input->getArgument('pages');
            $pages = ($pages) ? $pages : 3;

            //loop through all first i pages
            for($i=1;$i<=$pages;$i++){
                $baseUrl = str_replace("##page##", $i, $this->baseUrl);

                //some output
                $output->writeln("<question>Parsing list page number $i...</question>");

                //get all links to detailled page from the service
                $detailLinks = $this->kickass_parser->parseListPage($baseUrl);

                $output->writeln("Looking for new torrents...");
                $this->getAndSaveNewData($detailLinks);
            }
        }

        /**
         * @param array array of detail page links
         */
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
                    $movieStatus = new MovieStatus();
                    $movieStatus->setStatus("new");
                    $movieStatus->setMovie($movie);
                    $movie->setStatus($movieStatus);
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
                //saving a new torrent, then mark movie in "waiting" status as "new"
                if ($movie->getStatus()->getStatus() == "waiting"){
                    $movieStatus = $movie->getStatus();
                    $movieStatus->setStatus("new");
                    $this->saveEntity($movieStatus, "MovieStatus");
                }
                $this->saveEntity($torrent, "Torrent");
            }
            return;
        }

        //just to avoir typing that tag again
        /**
         * @param $message
         */
        protected function showInfo($message)
        {
            $this->oi->writeln("<info>" . $message . "</info>");
        }

        //save any kind of entity
        protected function saveEntity($entity, $type)
        {
            $em = $this->doctrine->getManager();
            $em->persist($entity);
            $em->flush(); //flush right away to avoid data lost on error
            $this->oi->writeln("<info>$type saved !</info>");
        }

        /**
         * @param $errors array of errors
         * @param null $message global optionnal message
         */
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