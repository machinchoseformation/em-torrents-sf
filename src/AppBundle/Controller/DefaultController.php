<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use AppBundle\Entity\MovieStatus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/movie/get/{id}", name="get_movie")
     */
    public function getMovieAction($id)
    {
        $movieRepo = $this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie = $movieRepo->getMovieWithTorrents($id);

        return $this->render('default/details.html.twig', compact("movie"));
    }



    /**
     * @Route("/movie/remove/{movieId}", name="remove_movie")
     */
    public function removeMovieAction($movieId)
    {
        $movieRepo = $this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie = $movieRepo->find($movieId);
        $status = $movie->getStatus();
        $status->setStatus("removed");

        $em = $this->getDoctrine()->getManager();
        $em->persist($status);
        $em->flush();

        return new JsonResponse(array("status" => "ok"));
    }




    /**
     * @Route("/movie/wait-for-better-torrent/{movieId}", name="wait_for_better_torrent")
     */
    public function waitForBetterTorrentAction($movieId)
    {
        $movieRepo = $this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie = $movieRepo->find($movieId);
        $status = $movie->getStatus();
        $status->setStatus("waiting ");

        $em = $this->getDoctrine()->getManager();
        $em->persist($status);
        $em->flush();

        return new JsonResponse(array("status" => "ok"));
    }



    /**
     * @Route("/torrent/download/{torrentId}", name="download_torrent")
     */
    public function downloadTorrentAction(Request $request, $torrentId)
    {

        $torrentRepo = $this->getDoctrine()->getRepository("AppBundle:Torrent");
        $torrent = $torrentRepo->find($torrentId);

        $em = $this->getDoctrine()->getManager();

        $movie = $torrent->getMovie();
        $movieStatus = ($movie->getStatus()) ? $movie->getStatus() : new MovieStatus();
        $movieStatus->setStatus("downloading");
        $movieStatus->setMovie($movie);
        $movie->setStatus($movieStatus);

        $em->persist($movieStatus);
        $em->persist($movie);
        $em->flush();

        if ($request->isXmlHttpRequest()){
            $r = [
                "status" => "ok",
                "magnet" => $torrent->getMagnetLink()
            ];
            return new JsonResponse($r);
        }
        else {
            return $this->redirect($torrent->getMagnetLink());
        }
    }
}
