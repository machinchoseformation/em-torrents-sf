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
     * @Assert\NotBlank(message="The torrent must have a title")
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="The torrent must have a magnet link")
     * @ORM\Column(name="magnetLink", type="text")
     */
    private $magnetLink;

    /**
     * @var string
     * @Assert\NotBlank(message="The torrent must have an info hash")
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
     */
    private $imdbId;

    /**
     * @var string
     *
     * @Assert\Choice(callback = "getOkQualities")
     * @ORM\Column(name="quality", type="string", length=30, nullable=true)
     */
    private $quality;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Movie", inversedBy="torrents", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
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

    public function setQualityFromTitle($title = null)
    {
        $title = ($title) ? $title : $this->getTitle();
        $foundQualities = [];
        $allQualities = array_merge(self::getBadQualities(), self::getOkQualities());
        foreach($allQualities as $quality){
            if (strstr(strtoupper($title), $quality) > -1){
                $foundQualities[] = $quality;
            }
        }
        if (count($foundQualities) >= 1){
            $this->setQuality($foundQualities[0]);
        }
        return $this->getQuality();
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


    public static function getOkQualities()
    {
        return array("DVDRIP", "DVDSCR", "BRSCR", "BRRIP", "WEBRIP", "HDRIP", "HDTV");
    }

    public static function getBadQualities()
    {
        return array("CAM", "TS", "HDTS", "HDCAM");
    }

    /**
     * Set quality
     *
     * @param string $quality
     * @return Torrent
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return string 
     */
    public function getQuality()
    {
        return $this->quality;
    }

}
