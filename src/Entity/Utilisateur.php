<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert; //pour la validation des données
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"list-user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Le login ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le login est trop court !!")
     * @Groups({"list-user"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"list-user"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le mot de passe ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le mot de passe est trop court !!")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password",message="Les mots de passes ne correspondent pas !")
     */
    private $confirmPassword; //créé le getter et setter!

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom du responsable ne doit pas être vide")
     * @Assert\Length(min="2", max="255" ,minMessage="Le nom est trop court !!")
     * @Groups({"list-userCmpt","list-user","liste-affCmpt"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'email du responsable ne doit pas être vide")
     * @Assert\Email(message="Veuillez mettre un email valide !!")
     * @Groups({"list-user"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le téléphone du responsable ne doit pas être vide")
     * @Groups({"list-user"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le NCI du responsable ne doit pas être vide")
     * @Groups({"list-user"})
     */
    private $nci;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list-user"})
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="utilisateurs")
     * @Groups({"list-user"})
     */
    private $entreprise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list-user"})
     */
    private $image;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserCompteActuel", mappedBy="utilisateur")
     */
    private $userCompteActuels;
    private $profil;

    public function __construct()
    {
        $this->imageFile='';
        $this->depots = new ArrayCollection();
        $this->envois = new ArrayCollection();
        $this->retraits = new ArrayCollection();
        $this->userCompteActuels = new ArrayCollection();
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

    /**
     * @return Collection|UserCompteActuel[]
     */
    public function getUserCompteActuels(): Collection
    {
        return $this->userCompteActuels;
    }

    public function addUserCompteActuel(UserCompteActuel $userCompteActuel): self
    {
        if (!$this->userCompteActuels->contains($userCompteActuel)) {
            $this->userCompteActuels[] = $userCompteActuel;
            $userCompteActuel->setUtilisateur($this);
        }

        return $this;
    }

    public function removeUserCompteActuel(UserCompteActuel $userCompteActuel): self
    {
        if ($this->userCompteActuels->contains($userCompteActuel)) {
            $this->userCompteActuels->removeElement($userCompteActuel);
            // set the owning side to null (unless already changed)
            if ($userCompteActuel->getUtilisateur() === $this) {
                $userCompteActuel->setUtilisateur(null);
            }
        }

        return $this;
    }
        /**
     * Get the value of profil
     */ 
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set the value of profil
     *
     * @return  self
     */ 
    public function setProfil($profil)
    {
        $this->profil = $profil;

        return $this;
    }
}
