<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Torrent
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TorrentRepository")
 */
class Torrent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="magnetLink", type="text")
     */
    private $magnetLink;

    /**
     * @var string
     * @Assert\Regex(
     *     pattern="/[a-f0-9]{40}/i"
     * )
     * @ORM\Column(name="infoHash", type="string", length=40, unique=true)
     */
    private $infoHash;

    /**
     * @var integer
     *
     * @Assert\Type(type="numeric")
     * @ORM\Column(name="seeders", type="integer")
     */
    private $seeders;

    /**
     * @var integer
     *
     * @Assert\Type(type="numeric")
     * @ORM\Column(name="leechers", type="integer")
     */
    private $leechers;


    /**
     * @var string
     *
     * @Assert\Regex(
     *     pattern="/\d{7}/"
     * )
     * @ORM\Column(name="imdbId", type="string", length=20)
     */
    private $imdbId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    private $dateModified;

    /**
     * @var Movie
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Movie", inversedBy="torrents")
     */
    private $movie;


    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->dateAdded = new \DateTime();
        $this->dateModified = new \DateTime();
    }
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->dateModified = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Torrent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set magnetLink
     *
     * @param string $magnetLink
     * @return Torrent
     */
    public function setMagnetLink($magnetLink)
    {
        $this->magnetLink = $magnetLink;

        return $this;
    }

    /**
     * Get magnetLink
     *
     * @return string 
     */
    public function getMagnetLink()
    {
        return $this->magnetLink;
    }

    /**
     * Set infoHash
     *
     * @param string $infoHash
     * @return Torrent
     */
    public function setInfoHash($infoHash)
    {
        $this->infoHash = $infoHash;

        return $this;
    }

    /**
     * Get infoHash
     *
     * @return string 
     */
    public function getInfoHash()
    {
        return $this->infoHash;
    }

    /**
     * Set seeders
     *
     * @param integer $seeders
     * @return Torrent
     */
    public function setSeeders($seeders)
    {
        $this->seeders = $seeders;

        return $this;
    }

    /**
     * Get seeders
     *
     * @return integer 
     */
    public function getSeeders()
    {
        return $this->seeders;
    }

    /**
     * Set leechers
     *
     * @param integer $leechers
     * @return Torrent
     */
    public function setLeechers($leechers)
    {
        $this->leechers = $leechers;

        return $this;
    }

    /**
     * Get leechers
     *
     * @return integer 
     */
    public function getLeechers()
    {
        return $this->leechers;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Torrent
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Torrent
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set movie
     *
     * @param \AppBundle\Entity\Movie $movie
     * @return Torrent
     */
    public function setMovie(\AppBundle\Entity\Movie $movie = null)
    {
        $this->movie = $movie;
        $movie->addTorrent($this);

        return $this;
    }

    /**
     * Get movie
     *
     * @return \AppBundle\Entity\Movie 
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * Set imdbId
     *
     * @param string $imdbId
     * @return Torrent
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return string 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }
}
