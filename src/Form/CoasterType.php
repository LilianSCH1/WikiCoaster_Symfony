<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Coaster;
use App\Entity\Park;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Image;

class CoasterType extends AbstractType
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: [
                'label' => 'Nom',
            ])
            ->add('maxSpeed', options: [
                'label' => 'Vitesse maximale (km/h)',
            ])
            ->add('length', options: [
                'label' => 'Longueur (m)',
            ])
            ->add('maxHeight', options: [
                'label' => 'Hauteur maximale (m)',
            ])
            ->add('operating', options: [
                'label' => 'En fonctionnement',
            ])
            ->add('park', EntityType::class, [
                'class' => Park::class,
                'label' => 'Parc',
                'required' => false,
                'group_by' => 'country' // Range les parcs par pays dans le select
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégories',
                'multiple' => true,
                'expanded' => true, // Cases à cocher
            ])
            ->add('image', FileType::class, options: [
                'label' => 'Image',
                'mapped' => false, // Image n'est pas une propriété de Coaster
                'required' => false,
                'constraints' => [
                    new Image()
                ]
            ])
        ;

        // Affiche le champ "Publier la fiche" seulement pour les Admins
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->add('published', options: [
                'label' => "Publier la fiche",
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coaster::class,
        ]);
    }
}
