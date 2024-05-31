<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\BonAchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BonAchatRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:bon_achat']],
    denormalizationContext: ['groups' => ['write:bon_achat']]
)]
#[Get]
#[GetCollection]
#[Post]
#[Patch(
    normalizationContext: ['groups' => ['patch:bon_achat']],
    denormalizationContext: ['groups' => ['patch:bon_achat']]
)]
#[Delete]
class BonAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    #[ORM\SequenceGenerator(sequenceName:"bon_achat_id_seq", allocationSize:1, initialValue:1)]
    #[Groups(['read:bon_achat'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:bon_achat', 'write:bon_achat'])]
    private ?int $montantInitial = null;

    #[ORM\Column]
    #[Groups(['read:bon_achat', 'patch:bon_achat'])]
    private ?int $montantRestant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:bon_achat', 'write:bon_achat'])]
    private ?\DateTimeInterface $date_expire = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'bonAchats')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:bon_achat', 'write:bon_achat'])]
    private ?User $user = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'bon')]
    private Collection $transactions;

    /**
     * @var Collection<int, Magasin>
     */
    #[ORM\ManyToMany(targetEntity: Magasin::class, inversedBy: 'bonAchats')]
    #[Groups(['read:bon_achat', 'write:bon_achat', 'patch:bon_achat'])]
    private Collection $magasins;

    #[ORM\Column]
    #[Groups(['read:bon_achat', 'patch:bon_achat'])]
    private ?bool $etat = true;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->date_creation = new \DateTime();
        $this->magasins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantInitial(): ?int
    {
        return $this->montantInitial;
    }

    public function setMontantInitial(int $montantInitial): static
    {
        $this->montantInitial = $montantInitial;
        $this->montantRestant = $this->montantInitial;
        return $this;
    }

    public function getMontantRestant(): ?int
    {
        return $this->montantRestant;
    }

    public function setMontantRestant(int $montantRestant): static
    {
        $this->montantRestant = $montantRestant;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getDateExpire(): ?\DateTimeInterface
    {
        return $this->date_expire;
    }

    public function setDateExpire(\DateTimeInterface $date_expire): static
    {
        $this->date_expire = $date_expire;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
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
            $transaction->setIdBon($this);
        }
        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getIdBon() === $this) {
                $transaction->setIdBon(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Magasin>
     */
    public function getMagasins(): Collection
    {
        return $this->magasins;
    }

    public function addMagasin(Magasin $magasin): static
    {
        if (!$this->magasins->contains($magasin)) {
            $this->magasins->add($magasin);
        }

        return $this;
    }

    public function removeMagasin(Magasin $magasin): static
    {
        $this->magasins->removeElement($magasin);

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}