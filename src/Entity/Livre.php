<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LivreRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 * @ApiResource(
 *       attributes = {
 *      "order" = {
 *          "titre": "ASC"   
 *          
 *           }
 *   },
 *       collectionOperations= {
 *      "get_coll_role_adherent" = {
 *           "method" = "GET",
 *            "path" = "/adherent/livres",
 *             "normalization_context" = {
 *                  "groups"={"get_livres_role_adherent"}
 * 
 *              }
 *          },
 *      "get_coll_role_manager" = {
 *           "method" = "GET",
 *            "path" = "/manager/livres",
 *             "security" = "is_granted('ROLE_MANAGER')",
 *              "security_message" = "Vous n'avez pas les droits d'accéder à cette ressource"
 *          },
 *          "post" ={
 *              "method" = "POST",
 *              "security" = "is_granted('ROLE_MANAGER')",
 *              "security_message" = "Vous n'avez pas les droits d'accéder à cette ressource"
 *             },
 *          "meilleurslivres" ={
 *                  "method" = "GET",
 *                  "route_name" = "meilleurslivres",
 *                  "controller" = StatsController::class,
 *                  "normalization_context" = {
 *                  "groups"={"get_adherent"}
 * 
 *                  }
 *              }
 *      },  
 *   itemOperations = {
 *          "get_item_role_adherent" = {
 *           "method" = "GET",
 *            "path" = "/adherent/livres/{id}",
 *             "normalization_context" = {
 *                  "groups"={"get_delivres_role_adherent"}
 * 
 *              }
 *          },
 *      "get_item_role_manager" = {
 *           "method" = "GET",
 *            "path" = "/manager/livres/{id}",
 *             "security" = "is_granted('ROLE_MANAGER')",
 *              "security_message" = "Vous n'avez pas les droits d'accéder à cette ressource"
 *          },
 *      "put_item_role_manager" = {
 *           "method" = "PUT",
 *            "path" = "/manager/livres/{id}",
 *            "security" = "is_granted('ROLE_MANAGER')",
 *            "security_message" = "Vous n'avez pas les droits d'accéder à cette ressource",
 *             "denormalization_context" = {
 *                  "groups"={"put_manager"}
 * 
 *              }
 *          },
 *      "put_item_role_admin" = {
 *           "method" = "PUT",
 *            "path" = "/admin/livres/{id}",
 *            "security" = "is_granted('ROLE_ADMIN')",
 *            "security_message" = "Vous n'avez pas les droits d'accéder à cette ressource"
 *          },
 * 
 *       "delete" = {
 *           "method" = "DELETE",
 *            "path" = "/admin/livres/{id}",
 *            "security" = "is_granted('ROLE_ADMIN')",
 *            "security_message" = "Vous n'avez pas les droits d'accéder à cette ressource"
 *          }
 * 
 * 
 *      }
 * 
 * )
 * @ApiFilter(
 *           
 *         SearchFilter::class,
 *         properties = {
 *              "titre" : "ipartial",
 *               "auteur" : "exact",
 *               "genre" : "exact"
 *      }
 * )
 * 
 * @ApiFilter(
 *           
 *         RangeFilter::class,
 *         properties = {
 *              "prix" 
 *      }
 * )
 * 
 * @ApiFilter(
 *           
 *         OrderFilter::class,
 *         properties = {
 *              "titre",
 *              "prix", 
 *              "auteur.nom"
 *      }
 * )
 * 
 * @ApiFilter(
 *           
 *         PropertyFilter::class,
 *         arguments = {
 *              "parameterName" : "properties",
 *               "overrideDefaultProperties" : false,
 *              "whitelist" = {
 *              "isbn",
 *              "titre",
 *              "prix"
 * 
 *            } 
 *      }
 * )
 * 
 * @UniqueEntity(
 *     fields={"titre"},
 *      message="Le titre {{ value }} est déjâ  prise, veuillez choisir un autre !")
 * 
 */
class Livre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_livres_role_adherent", "get_delivres_role_adherent"})
     * @Groups({"get_adherent"})
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=2, max=100, minMessage = "Le titre doit contenir au moins {{ limit }} caractères", 
     * maxMessage="le titre doit contenir au plus {{ limit }} caractères")
     * @Groups({"get_livres_role_adherent", "put_manager", "get_adherent", "get_delivres_role_adherent"})
     */
    private $titre;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Range(min=5, max=400, notInRangeMessage ="Le prix doit être comprise entre {{ min }} € et {{ max }} €" )
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="livres")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_livres_role_adherent", "put_manager", "get_delivres_role_adherent"})
     * 
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity=Editeur::class, inversedBy="livres")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_livres_role_adherent","put_manager"})
     */
    private $editeur;

    /**
     * @ORM\ManyToOne(targetEntity=Auteur::class, inversedBy="livres")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_livres_role_adherent","put_manager", "get_delivres_role_adherent"})
     */
    private $auteur;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get_livres_role_adherent","put_manager", "get_delivres_role_adherent"})
     */
    private $annee;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_livres_role_adherent", "put_manager"})
     */
    private $langue;

    /**
     * @ORM\OneToMany(targetEntity=Pret::class, mappedBy="livre")
     * @Groups({"put_manager"})
     */
    private $prets;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $dispo;

    public function __construct()
    {
        $this->adherent = new ArrayCollection();
        $this->prets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getEditeur(): ?Editeur
    {
        return $this->editeur;
    }

    public function setEditeur(?Editeur $editeur): self
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * @return Collection|Pret[]
     */
    public function getPrets(): Collection
    {
        return $this->prets;
    }

    public function addPret(Pret $pret): self
    {
        if (!$this->prets->contains($pret)) {
            $this->prets[] = $pret;
            $pret->setLivre($this);
        }

        return $this;
    }

    public function removePret(Pret $pret): self
    {
        if ($this->prets->removeElement($pret)) {
            // set the owning side to null (unless already changed)
            if ($pret->getLivre() === $this) {
                $pret->setLivre(null);
            }
        }

        return $this;
    }

    public function getDispo(): ?bool
    {
        return $this->dispo;
    }

    public function setDispo(?bool $dispo): self
    {
        $this->dispo = $dispo;

        return $this;
    }

  
}
