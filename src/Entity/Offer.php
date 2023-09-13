<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\Table(name: 'offer')]
class Offer  implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $contractType = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): self
    {
        $this->contractType = $contractType;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function createFromJsonArray(array $offer): self
    {
        $this->setCity($offer['lieuTravail']['libelle'] ?? 'UnkownCity');
        $this->setCompany($offer['entreprise']['nom'] ?? 'CompanyNameUnknown');
        $this->setContractType($offer['typeContrat'] ?? 'ContractTypeUnknown');
        $this->setUrl($offer['origineOffre']['urlOrigine'] ?? 'UrlUnknown');
        $this->setDescription($offer['description'] ?? 'NoDescription');
        return $this;
    }

    public function jsonSerialize(): array
    {
        return array(
            'city' => $this->getCity(),
            'company'=> $this->getCompany(),
            'contractType'=> $this->getContractType(),
            'url'=> $this->getUrl(),
            //'description'=> $this->getDescription(),
        );
    }
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
}
