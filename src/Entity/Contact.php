<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: 'contact')]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(max: 120)]
    private string $firstName = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 120)]
    private string $lastName = '';

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank(message: 'Le téléphone est obligatoire.')]
    #[Assert\Length(max: 40)]
    #[Assert\Regex(
        pattern: '/^[0-9+\s().-]{6,40}$/',
        message: 'Format de téléphone invalide.'
    )]
    private string $phone = '';

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email(message: 'Email invalide.')]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $photoPath = null;

    /** @var Collection<int, ContactField> */
    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: ContactField::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid] // valide aussi les ContactField
    private Collection $fields;

    /** @var Collection<int, Group> */
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'contacts', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'contact_group')]
    private Collection $groups;

    #[ORM\Column(options: ['default' => false])]
    private bool $isFavorite = false;

    public function isFavorite(): bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): self
    {
        $this->isFavorite = $isFavorite;
        return $this;
    }


    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function setGroups(Collection $groups): void
    {
        $this->groups = $groups;
    }

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    // … tes getters/setters

    #[Assert\Callback]
    public function validateUniqueFieldNames(ExecutionContextInterface $context): void
    {
        $seen = [];
        foreach ($this->fields as $i => $field) {
            $name = mb_strtolower(trim($field->getName()));
            if ($name === '') {
                // NotBlank le gérera aussi, mais on renforce
                continue;
            }
            if (isset($seen[$name])) {
                $context->buildViolation('Le champ "{{ name }}" est dupliqué.')
                    ->setParameter('{{ name }}', $field->getName())
                    ->atPath('fields') // message global sur la collection
                    ->addViolation();
                return;
            }
            $seen[$name] = true;
        }
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(?string $photoPath): void
    {
        $this->photoPath = $photoPath;
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function setFields(Collection $fields): void
    {
        $this->fields = $fields;
    }
}
