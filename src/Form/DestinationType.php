<?php

namespace App\Form;

use App\Entity\Destination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\All;

class DestinationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la destination',
                'attr'  => ['placeholder' => 'Ex: Toulouse, Dubai...'],
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays',
                'attr'  => ['placeholder' => 'Ex: France, Tunisie...'],
            ])
            ->add('codeIata', TextType::class, [
                'label'    => 'Code IATA (3 lettres)',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'TUN, CDG...',
                    'maxlength'   => 3,
                    'style'       => 'text-transform:uppercase',
                ],
            ])

            // ✅ FIX : 'multiple' => true au niveau Symfony (pas seulement dans attr)
            // Sans cette option, getData() ne retourne qu'un seul fichier !
            ->add('imageFiles', FileType::class, [
                'label'    => '📷 Images (sélectionnez plusieurs)',
                'mapped'   => false,
                'required' => false,
                'multiple' => true,   // ← LA CLÉ DU FIX
                'attr'     => [
                    'accept' => 'image/*',
                ],
                // Contrainte appliquée à chaque fichier du tableau
                'constraints' => [
                    new All([
                        'constraints' => [
                            new Image(['maxSize' => '3M']),
                        ],
                    ]),
                ],
            ])

            ->add('youtubeSearch', TextType::class, [
                'label'    => '🎬 Rechercher une vidéo YouTube',
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Ex: Toulouse city tour, Dubai travel...',
                    'id'          => 'youtubeSearch',
                ],
            ])
            ->add('videoUrl', TextType::class, [
                'label'    => 'ID Vidéo YouTube sélectionnée',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'dQw4w9WgXcQ',
                    'id'          => 'videoUrlField',
                    'readonly'    => true,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => [
                    'rows'        => 4,
                    'placeholder' => 'Décrivez la destination...',
                ],
            ])
            ->add('order', IntegerType::class, [
                'label'    => 'Ordre d\'affichage',
                'required' => false,
                'attr'     => ['placeholder' => '1, 2, 3...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Destination::class]);
    }
}