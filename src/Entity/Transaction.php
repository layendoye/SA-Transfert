<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; //pour la validation des données
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @UniqueEntity(fields= {"code"},message="Ce code existe déja")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le nom est trop court !!")
     */
    private $nomClientEmetteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le téléphone est trop court !!")
     */
    private $telephoneEmetteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le NCI est trop court !!")
     */
    private $nciEmetteur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnvoi;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le code est trop court !!")
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserCompteActuel", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userComptePartenaireEmetteur;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Range(min=500 ,minMessage="Il faut transferer 500 fr au moins!!")
     */
    private $montant;

    /**
     * @ORM\Column(type="integer")
     */
    private $frais;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le nom est trop court !!")
     */
    private $nomClientRecepteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le téléphone est trop court !!")
     */
    private $telephoneRecepteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le NCI est trop court !!")
     */
    private $nciRecepteur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateReception;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserCompteActuel", inversedBy="retraits")
     */
    private $userComptePartenaireRecepteur;

    /**
     * @ORM\Column(type="integer")
     */
    private $commissionEmetteur;

    /**
     * @ORM\Column(type="integer")
     */
    private $commissionRecepteur;

    /**
     * @ORM\Column(type="integer")
     */
    private $commissionWari;

    /**
     * @ORM\Column(type="integer")
     */
    private $taxesEtat;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClientEmetteur(): ?string
    {
        return $this->nomClientEmetteur;
    }

    public function setNomClientEmetteur(string $nomClientEmetteur): self
    {
        $this->nomClientEmetteur = $nomClientEmetteur;

        return $this;
    }

    public function getTelephoneEmetteur(): ?string
    {
        return $this->telephoneEmetteur;
    }

    public function setTelephoneEmetteur(string $telephoneEmetteur): self
    {
        $this->telephoneEmetteur = $telephoneEmetteur;

        return $this;
    }

    public function getNciEmetteur(): ?string
    {
        return $this->nciEmetteur;
    }

    public function setNciEmetteur(string $nciEmetteur): self
    {
        $this->nciEmetteur = $nciEmetteur;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUserComptePartenaireEmetteur(): ?UserCompteActuel
    {
        return $this->userComptePartenaireEmetteur;
    }

    public function setUserComptePartenaireEmetteur(?UserCompteActuel $userComptePartenaireEmetteur): self
    {
        $this->userComptePartenaireEmetteur = $userComptePartenaireEmetteur;

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

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getNomClientRecepteur(): ?string
    {
        return $this->nomClientRecepteur;
    }

    public function setNomClientRecepteur(string $nomClientRecepteur): self
    {
        $this->nomClientRecepteur = $nomClientRecepteur;

        return $this;
    }

    public function getTelephoneRecepteur(): ?string
    {
        return $this->telephoneRecepteur;
    }

    public function setTelephoneRecepteur(string $telephoneRecepteur): self
    {
        $this->telephoneRecepteur = $telephoneRecepteur;

        return $this;
    }

    public function getNciRecepteur(): ?string
    {
        return $this->nciRecepteur;
    }

    public function setNciRecepteur(string $nciRecepteur): self
    {
        $this->nciRecepteur = $nciRecepteur;

        return $this;
    }

    public function getDateReception(): ?\DateTimeInterface
    {
        return $this->dateReception;
    }

    public function setDateReception(\DateTimeInterface $dateReception): self
    {
        $this->dateReception = $dateReception;

        return $this;
    }

    public function getUserComptePartenaireRecepteur(): ?UserCompteActuel
    {
        return $this->userComptePartenaireRecepteur;
    }

    public function setUserComptePartenaireRecepteur(?UserCompteActuel $userComptePartenaireRecepteur): self
    {
        $this->userComptePartenaireRecepteur = $userComptePartenaireRecepteur;

        return $this;
    }

    public function getCommissionEmetteur(): ?int
    {
        return $this->commissionEmetteur;
    }

    public function setCommissionEmetteur(int $commissionEmetteur): self
    {
        $this->commissionEmetteur = $commissionEmetteur;

        return $this;
    }

    public function getCommissionRecepteur(): ?int
    {
        return $this->commissionRecepteur;
    }

    public function setCommissionRecepteur(int $commissionRecepteur): self
    {
        $this->commissionRecepteur = $commissionRecepteur;

        return $this;
    }

    public function getCommissionWari(): ?int
    {
        return $this->commissionWari;
    }

    public function setCommissionWari(int $commissionWari): self
    {
        $this->commissionWari = $commissionWari;

        return $this;
    }

    public function getTaxesEtat(): ?int
    {
        return $this->taxesEtat;
    }

    public function setTaxesEtat(int $taxesEtat): self
    {
        $this->taxesEtat = $taxesEtat;

        return $this;
    }

    
}
