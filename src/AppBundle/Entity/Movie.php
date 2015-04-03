<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Movie
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MovieRepository")
 */
class Movie
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
     * @Assert\NotBlank(message="The movie must have a title")
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="director", type="string", length=255)
     */
    private $director;

    /**
     * @var string
     *
     * @ORM\Column(name="cast", type="string", length=255)
     */
    private $cast;

    /**
     * @var string
     *
     * @ORM\Column(name="plot", type="text")
     */
    private $plot;


    /**
     * @var string
     *
     * @Assert\NotBlank(message="The movie must have an imdb ID")
     * @Assert\Regex(
     *     pattern="/\d{7}/"
     * )
     * @ORM\Column(name="imdbId", type="string", length=7, unique=true)
     */
    private $imdbId;

    /**
     * @var string
     * @Assert\NotBlank(message="The movie must have a rating")
     * @Assert\Range(
     *      min = 6,
     *      max = 10,
     *      minMessage = "The movie must have a rating of minimum {{ limit }}",
     *      maxMessage = "The movie must have a rating of maximum {{ limit }}"
     * )
     * @ORM\Column(name="imdbRating", type="decimal", precision=2, scale=1)
     */
    private $imdbRating;

    /**
     * @var integer
     * @Assert\NotBlank(message="The movie must have a number of votes")
     * @Assert\GreaterThan(
     *     value = 5000, message="Minimum 1000 votes !"
     * )
     * @ORM\Column(name="numVotes", type="integer")
     */
    private $numVotes;

    /**
     * @var integer
     *
     * @Assert\NotBlank(message="The movie must have a release year")
     * @Assert\Regex(
     *     pattern="/(19|20)\d{2}/"
     * )
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="The movie must have a poster")
     * @Assert\Url(message="The poster is not a valid URL")
     * @ORM\Column(name="posterUrl", type="text")
     */
    private $posterUrl;

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
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Torrent", mappedBy="movie", cascade={"persist"})
     */
    private $torrents;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Genre", inversedBy="movies", cascade={"remove", "persist"})
     */
    private $genres;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\MovieStatus", mappedBy="movie", cascade={"remove", "persist"})
     */
    private $status;


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
     * @return Movie
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
     * Set imdbId
     *
     * @param string $imdbId
     * @return Movie
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

    /**
     * Set imdbRating
     *
     * @param string $imdbRating
     * @return Movie
     */
    public function setImdbRating($imdbRating)
    {
        $this->imdbRating = $imdbRating;

        return $this;
    }

    /**
     * Get imdbRating
     *
     * @return string 
     */
    public function getImdbRating()
    {
        return $this->imdbRating;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set posterUrl
     *
     * @param string $posterUrl
     * @return Movie
     */
    public function setPosterUrl($posterUrl)
    {
        $this->posterUrl = $posterUrl;

        return $this;
    }

    /**
     * Get posterUrl
     *
     * @return string 
     */
    public function getPosterUrl()
    {
        return $this->posterUrl;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Movie
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
     * @return Movie
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
     * Constructor
     */
    public function __construct()
    {
        $this->torrents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add torrents
     *
     * @param \AppBundle\Entity\Torrent $torrents
     * @return Movie
     */
    public function addTorrent(\AppBundle\Entity\Torrent $torrents)
    {
        $this->torrents[] = $torrents;

        return $this;
    }

    /**
     * Remove torrents
     *
     * @param \AppBundle\Entity\Torrent $torrents
     */
    public function removeTorrent(\AppBundle\Entity\Torrent $torrents)
    {
        $this->torrents->removeElement($torrents);
    }

    /**
     * Get torrents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTorrents()
    {
        return $this->torrents;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Movie
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string 
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set numVotes
     *
     * @param integer $numVotes
     * @return Movie
     */
    public function setNumVotes($numVotes)
    {
        $this->numVotes = $numVotes;

        return $this;
    }

    /**
     * Get numVotes
     *
     * @return integer 
     */
    public function getNumVotes()
    {
        return $this->numVotes;
    }

    /**
     * Add genres
     *
     * @param \AppBundle\Entity\Genre $genres
     * @return Movie
     */
    public function addGenre(\AppBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \AppBundle\Entity\Genre $genres
     */
    public function removeGenre(\AppBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set status
     *
     * @param \AppBundle\Entity\MovieStatus $status
     * @return Movie
     */
    public function setStatus(\AppBundle\Entity\MovieStatus $status = null)
    {
        $this->status = $status;
        $status->setMovie($this);

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\MovieStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set cast
     *
     * @param string $cast
     * @return Movie
     */
    public function setCast($cast)
    {
        $this->cast = $cast;

        return $this;
    }

    /**
     * Get cast
     *
     * @return string 
     */
    public function getCast()
    {
        return $this->cast;
    }

    /**
     * Set plot
     *
     * @param string $plot
     * @return Movie
     */
    public function setPlot($plot)
    {
        $this->plot = $plot;

        return $this;
    }

    /**
     * Get plot
     *
     * @return string 
     */
    public function getPlot()
    {
        return $this->plot;
    }
}
