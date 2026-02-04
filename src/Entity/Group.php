<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupRepository;




#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '"group"')]
#[ORM\UniqueConstraint(name: 'uniq_group_name', columns: ['name'])]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name = '';

    /** @var Collection<int, Contact> */
    #[ORM\ManyToMany(targetEntity: Contact::class, mappedBy: 'groups')]
    private Collection $contacts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    /** @return Collection<int, Contact> */
    public function getContacts(): Collection { return $this->contacts; }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
        }
        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        $this->contacts->removeElement($contact);
        return $this;
    }
}
