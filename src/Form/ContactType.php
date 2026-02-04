<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('phone', TelType::class, ['label' => 'Téléphone'])
            ->add('email', EmailType::class, ['required' => false])
            ->add('photoFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Photo',
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Formats autorisés : JPG, PNG, WEBP.',
                    ])
                ],
            ])
            ->add('fields', CollectionType::class, [
                'entry_type' => ContactFieldType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, // IMPORTANT pour addField/removeField
                'prototype' => true,
                'label' => 'Champs personnalisés',
            ])

            ->add('groupNames', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Groupes (séparés par virgules)',
                'help' => 'Ex: famille, bureau',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
