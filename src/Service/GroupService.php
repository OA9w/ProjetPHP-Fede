<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Applique des noms de groupes au contact:
     * - crée le groupe s’il n’existe pas
     * - enlève les groupes non listés
     * - supprime les groupes devenus vides
     */
    public function applyGroupNames(Contact $contact, array $names): void
    {
        $names = array_values(array_unique(array_filter(array_map(
            fn($n) => trim((string)$n),
            $names
        ), fn($n) => $n !== '')));

        $repo = $this->em->getRepository(Group::class);

        // Index de ce qu'on veut
        $wanted = [];
        foreach ($names as $name) {
            $wanted[$name] = true;
        }

        // Retirer groupes non souhaités
        foreach ($contact->getGroups()->toArray() as $group) {
            if (!isset($wanted[$group->getName()])) {
                $contact->removeGroup($group);
            }
        }

        // Ajouter / créer les groupes souhaités
        foreach ($names as $name) {
            $group = $repo->findOneBy(['name' => $name]);
            if (!$group) {
                $group = (new Group())->setName($name);
                $this->em->persist($group);
            }
            $contact->addGroup($group);
        }

        // Supprimer les groupes vides
        $this->removeEmptyGroups();
    }

    public function removeEmptyGroups(): void
    {
        $groups = $this->em->getRepository(Group::class)->findAll();
        foreach ($groups as $group) {
            if ($group->getContacts()->count() === 0) {
                $this->em->remove($group);
            }
        }
    }
}
