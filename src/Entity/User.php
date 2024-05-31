<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    normalizationContext: ['groups' => ['read:user']],
    denormalizationContext: ['groups' => ['write:user']]
)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    #[ORM\SequenceGenerator(sequenceName:"user_id_seq", allocationSize:1, initialValue:1)]
    #[Groups(['read:bon_achat', 'write:bon_achat', 'read:user'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['read:bon_achat', 'write:user'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, BonAchat>
     */
    #[ORM\OneToMany(targetEntity: BonAchat::class, mappedBy: 'user')]
    private Collection $bonAchats;

    public function __construct()
    {
        $this->bonAchats = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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
            $bonAchat->setIdUser($this);
        }

        return $this;
    }

    public function removeBonAchat(BonAchat $bonAchat): static
    {
        if ($this->bonAchats->removeElement($bonAchat)) {
            // set the owning side to null (unless already changed)
            if ($bonAchat->getIdUser() === $this) {
                $bonAchat->setIdUser(null);
            }
        }

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
}
