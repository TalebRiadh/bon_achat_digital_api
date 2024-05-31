<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ApiResource()]
#[Get(
    normalizationContext: ['groups' => ['read:transaction']],
    denormalizationContext: ['groups' => ['read:transaction']]
)]
#[GetCollection(
    normalizationContext: ['groups' => ['read:transaction']],
    denormalizationContext: ['groups' => ['read:transaction']]
)]
#[ORM\HasLifecycleCallbacks()]
#[Post(
    normalizationContext: ['groups' => ['post:transaction']],
    denormalizationContext: ['groups' => ['post:transaction']]
)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    #[ORM\SequenceGenerator(sequenceName:"transaction_id_seq", allocationSize:1, initialValue:1)]
    #[Groups(['read:transaction'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:transaction'])]
    private ?\DateTimeInterface $date_transaction = null;

    #[ORM\Column]
    #[Groups(['post:transaction', 'read:transaction'])]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[Groups(['post:transaction', 'read:transaction'])]
    private ?BonAchat $bon = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[Groups(['post:transaction', 'read:transaction'])]
    private ?Magasin $magasin = null;


    public function getId(): ?int
    {
        return $this->id;
    }
    public function __construct()
    {
        $this->date_transaction = new \DateTime();
    }

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->date_transaction;
    }

    public function setDateTransaction(\DateTimeInterface $date_transaction): static
    {
        $this->date_transaction = $date_transaction;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }


    public function getBon(): ?BonAchat
    {
        return $this->bon;
    }

    public function setBon(?BonAchat $bon): static
    {
        $this->bon = $bon;

        return $this;
    }


    #[ORM\PrePersist()]
    public function updateMontantRestant()
    {
        $bonAchat = $this->getBon();
        $montantRestant = $bonAchat->getMontantRestant() - $this->getMontant();
        $bonAchat->setMontantRestant($montantRestant);
    }
    #[Assert\Callback]
    public function validateSoldeBonAchat(ExecutionContextInterface $context)
    {
        if ($this->montant > $this->bon->getMontantRestant()) {
            $context->buildViolation('Le montant de la transaction dépasse le solde restant du bon d\'achat.')
                ->atPath('montant')
                ->addViolation();
        }
    }
    #[Assert\Callback]
    public function validateMagasinBonAchat(ExecutionContextInterface $context)
    {
        if (!in_array($this->magasin,$this->bon->getMagasins()->toArray())) {
            $context->buildViolation('Vous ne pouvez pas utliser ce bon dans ce magasin.')
                ->atPath('Bon d\'achat')
                ->addViolation();
        }
    }

    #[Assert\Callback]
    public function checkExpireBonAchat(ExecutionContextInterface $context)
    {
        if ($this->bon->getDateExpire() < new \DateTime()) {
            $context->buildViolation('Votre bon d\'achat est expiré.')
                ->atPath('Bon d\'achat')
                ->addViolation();
        }
    }

    public function getMagasin(): ?Magasin
    {
        return $this->magasin;
    }

    public function setMagasin(?Magasin $magasin): static
    {
        $this->magasin = $magasin;

        return $this;
    }
}
