<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Shortener
 *
 * @ORM\Table(name="shortener")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShortenerRepository")
 */
class Shortener
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="origin_url", type="string", length=255)
     *
     * @Assert\Url(
     *   message = "The url '{{ value }}' is not a valid url",
     *   protocols = {"http", "https"},
     *  )
     */


    private $originUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="short_url", type="string", length=255, nullable=true, unique=true)
     */
    private $shortUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set originUrl
     *
     * @param string $originUrl
     *
     * @return Shortener
     */
    public function setOriginUrl($originUrl)
    {
        $this->originUrl = $originUrl;

        return $this;
    }

    /**
     * Get originUrl
     *
     * @return string
     */
    public function getOriginUrl()
    {
        return $this->originUrl;
    }

    /**
     * Set shortUrl
     *
     * @param string $shortUrl
     *
     * @return Shortener
     */
    public function setShortUrl($shortUrl)
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    /**
     * Get shortUrl
     *
     * @return string
     */
    public function getShortUrl()
    {
        return $this->shortUrl;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Shortener
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
}

