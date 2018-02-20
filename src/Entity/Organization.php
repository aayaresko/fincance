<?php

namespace App\Entity;

use App\Service\OrganizationService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 */
class Organization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id = 0;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title = '';
    /**
     * @ORM\Column(type="smallint")
     */
    private $type = 0;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address = '';
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url = '';
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $externalIdentifier = '';
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $branch;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rate", mappedBy="organization")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rates;

    /**
     * Organization constructor.
     */
    public function __construct()
    {
        $this->rates = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        if ($type === OrganizationService::TYPE_BANK || $type === OrganizationService::TYPE_EXCHANGER) {
            $this->type = $type;
        }
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getExternalIdentifier(): string
    {
        return $this->externalIdentifier;
    }

    /**
     * @param string $externalIdentifier
     */
    public function setExternalIdentifier(string $externalIdentifier)
    {
        $this->externalIdentifier = $externalIdentifier;
    }

    /**
     * @return mixed
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param mixed $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return mixed
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * @param array $rates
     */
    public function setRates(array $rates)
    {
        $this->rates = $rates;
    }
}
