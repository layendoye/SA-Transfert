<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert; //pour la validation des données
/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity(fields= {"username"},message="Login déja utilisé")
 * @UniqueEntity(fields= {"email"},message="Email déja utilisé")
 * @UniqueEntity(fields= {"telephone"},message="téléphone déja utilisé")
 * @UniqueEntity(fields= {"nci"},message="NCI déja utilisé")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le login est trop court !!")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le mot de passe est trop court !!")
     */
    private $password;

    /**
    *@Assert\EqualTo(propertyPath="password",message="Les mots de passes ne correspondent pas !")
    */
    private $confirmPassword; //créé le getter et setter!

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le nom est trop court !!")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez mettre un email valide !!")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nci;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="caissier")
     */
    private $depots;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="userPartenaireEmetteur")
     */
    private $envois;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="userPartenaireRecepteur")
     */
    private $retraits;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="utilisateurs")
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="utilisateurs")
     */
    private $entreprise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image()
     */
    private $image;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->envois = new ArrayCollection();
        $this->retraits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): string
    {
        return (string) $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNci(): ?string
    {
        return $this->nci;
    }

    public function setNci(string $nci): self
    {
        $this->nci = $nci;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCaissier($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->contains($depot)) {
            $this->depots->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCaissier() === $this) {
                $depot->setCaissier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getEnvois(): Collection
    {
        return $this->envois;
    }

    public function addEnvois(Transaction $envois): self
    {
        if (!$this->envois->contains($envois)) {
            $this->envois[] = $envois;
            $envois->setUserEmetteur($this);
        }

        return $this;
    }

    public function removeEnvois(Transaction $envois): self
    {
        if ($this->envois->contains($envois)) {
            $this->envois->removeElement($envois);
            // set the owning side to null (unless already changed)
            if ($envois->getUserEmetteur() === $this) {
                $envois->setUserEmetteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getRetraits(): Collection
    {
        return $this->retraits;
    }

    public function addRetrait(Transaction $retrait): self
    {
        if (!$this->retraits->contains($retrait)) {
            $this->retraits[] = $retrait;
            $retrait->setUserPartenaireRecepteur($this);
        }

        return $this;
    }

    public function removeRetrait(Transaction $retrait): self
    {
        if ($this->retraits->contains($retrait)) {
            $this->retraits->removeElement($retrait);
            // set the owning side to null (unless already changed)
            if ($retrait->getUserPartenaireRecepteur() === $this) {
                $retrait->setUserPartenaireRecepteur(null);
            }
        }

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

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getImage() //enlever le string ici
    {
        return $this->image;
    }

    public function setImage($image)//enlever le string ici aussi
    {
        $this->image = $image;

        return $this;
    }
}
