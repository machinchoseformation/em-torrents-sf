<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {

        $movieRepo = $this->getDoctrine()->getRepository("AppBundle\Entity\Movie");

        $movies = $movieRepo->getMovies();

        $params = array(
            "movies" => $movies
        );

        return $this->render('default/index.html.twig', $params);
    }


    /**
     * @Route("/movie/remove/{id}", name="remove_movie")
     */
    public function removeMovieAction($id)
    {

    }

    /**
     * @Route("/movie/get/{id}", name="get_movie")
     */
    public function getMovieAction($id)
    {

    }
}
