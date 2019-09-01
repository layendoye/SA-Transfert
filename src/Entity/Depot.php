<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; //pour la validation des données
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\DepotRepository")
 */
class Depot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"list-depot"})
     */
    private $date;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\Range(min=75000 ,minMessage="Le depot doit être superieur à 75 000 !!")
     * @Groups({"list-depot"})
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="depots")
     */
    private $caissier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="depots")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCaissier(): ?Utilisateur
    {
        return $this->caissier;
    }

    public function setCaissier(?Utilisateur $caissier): self
    {
        $this->caissier = $caissier;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}
