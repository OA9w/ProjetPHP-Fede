<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'contact_field')]
#[ORM\Index(columns: ['name'], name: 'idx_contact_field_name')]
class ContactField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Contact $contact = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le nom du champ est obligatoire.')]
    #[Assert\Length(max: 120)]
    private string $name = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La valeur du champ est obligatoire.')]
    private string $value = '';

    // =======================
    // Getters / Setters
    // =======================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
}
