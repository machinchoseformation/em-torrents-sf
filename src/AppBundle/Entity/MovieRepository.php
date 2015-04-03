<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * MovieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MovieRepository extends EntityRepository
{

    public function getMovies($offset = 0)
    {
        $dql = "SELECT g,m,t,s
                FROM AppBundle:Movie m
                JOIN m.torrents t
                JOIN m.status s
                LEFT JOIN m.genres g
                WHERE s.status = 'new'
                ORDER BY m.imdbRating DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setFirstResult($offset);
        $query->setMaxResults(70);
        $paginator = new Paginator($query);
        return $paginator;
    }


    public function getMovieWithTorrents($id)
    {
        $dql = "SELECT g,m,t,s
                FROM AppBundle:Movie m
                LEFT JOIN m.torrents t
                LEFT JOIN m.genres g
                LEFT JOIN m.status s
                WHERE m.id = :id";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("id", $id);
        return $query->getSingleResult();
    }

}
