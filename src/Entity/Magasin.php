<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MagasinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MagasinRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:magasin']],
    denormalizationContext: ['groups' => ['write:magasin']]
)]
class Magasin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:magasin', 'write:magasin'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:magasin', 'write:magasin'])]
    private ?string $adresse = null;

    #[ORM\Column]
    #[Groups(['read:magasin', 'write:magasin'])]
    private ?bool $autorise = null;

    /**
     * @var Collection<int, BonAchat>
     */
    #[ORM\ManyToMany(targetEntity: BonAchat::class, mappedBy: 'magasins')]
    private Collection $bonAchats;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'magasin')]
    private Collection $transactions;

    public function __construct()
    {
        $this->bonAchats = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function isAutorise(): ?bool
    {
        return $this->autorise;
    }

    public function setAutorise(bool $autorise): static
    {
        $this->autorise = $autorise;

        return $this;
    }

    /**
     * @return Collection<int, BonAchat>
     */
    public function getBonAchats(): Collection
    {
        return $this->bonAchats;
    }

    public function addBonAchat(BonAchat $bonAchat): static
    {
        if (!$this->bonAchats->contains($bonAchat)) {
            $this->bonAchats->add($bonAchat);
            $bonAchat->addMagasin($this);
        }

        return $this;
    }

    public function removeBonAchat(BonAchat $bonAchat): static
    {
        if ($this->bonAchats->removeElement($bonAchat)) {
            $bonAchat->removeMagasin($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setMagasin($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getMagasin() === $this) {
                $transaction->setMagasin(null);
            }
        }

        return $this;
    }
}
