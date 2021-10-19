<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NationaliteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=NationaliteRepository::class)
 * @ApiResource(
 *      attributes = {
 *      "order" = {
 *          "libelle": "ASC"    
 *      },
 *  "pagination_enabled" = false  
 * },
 * 
 * collectionOperations = 
 *          {
 *              "get" = 
 *                  {
 *                      "method" = "GET",
 *                      "normalization_context" = 
 *                          {
 *                              "groups" = {"get_auteur_role_adherent"}
 *                          }
 * 
 *                  },
 * 
 *              "post" = {
 *                       
 *                      "method" = "POST",
 *                      "security" = "is_granted('ROLE_ADMIN')",
 *                      "security_message" = "Vous n'avez pas les droits d'accèder à cet fonction",
 *                      "denormalization_context" = {
 *                                  "groups" = {"put_role_manager"}
 *                         }
 * 
 *                      
 *                 }
 *          
 *          },
 * 
 * 
 *      itemOperations = {
 * 
 *              "get" = {
 *                      "method" = "GET",
 *                      "normalization_context" = {
 *                               "groups" = {"get_auteur_role_adherent"}
 *                          }
 * 
 *                  },
 *              "put" = {
 *                      "method" = "PUT",
 *                      "path" = "/nationalites/{id}",
 *                      "security" = "is_granted('ROLE_ADMIN')",
 *                      "security_message" = "Vous n'avez pas les droits d'accéder !",
 *                      "denormalization_context" = {
 *                              "groups" = {"put_role_manager"}
 *      
 *                          }
 *                  }
 * 
 * }
 * )
 * 
 */
class Nationalite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_auteur_role_adherent"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length( min=4, max=50, minMessage = "Le libellé doit contenir au moins {{ limit }} caractères", 
     * maxMessage="doit contenir au plus {{ limit }} caractères")
     * @Groups({"get_auteur_role_adherent"})
     */
    private $libelle;

    /**
     * 
     * @ORM\OneToMany(targetEntity=Auteur::class, mappedBy="nationalite")
     * @Groups({"listeNationaliteFull"})
     *
     */
    private $auteurs;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Auteur[]
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): self
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs[] = $auteur;
            $auteur->setNationalite($this);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): self
    {
        if ($this->auteurs->removeElement($auteur)) {
            // set the owning side to null (unless already changed)
            if ($auteur->getNationalite() === $this) {
                $auteur->setNationalite(null);
            }
        }

        return $this;
    }
}
