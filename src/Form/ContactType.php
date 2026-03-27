<?php

namespace App\Form;  // ← doit être exactement ça

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'constraints' => [new NotBlank()],
                'attr'        => ['placeholder' => 'Mohamed'],
            ])
            ->add('nom', TextType::class, [
                'constraints' => [new NotBlank()],
                'attr'        => ['placeholder' => 'Ben Ali'],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email()],
                'attr'        => ['placeholder' => 'email@example.com'],
            ])
            ->add('telephone', TextType::class, [
                'required' => false,
                'attr'     => ['placeholder' => '+216 ...'],
            ])
            ->add('destinationSouhaitee', TextType::class, [
                'required' => false,
                'attr'     => ['placeholder' => 'Paris, Bali...'],
            ])
            ->add('budget', ChoiceType::class, [
                'required' => false,
                'choices'  => [
                    '- 500 TND'          => '- 500 TND',
                    '500 – 1 500 TND'    => '500 – 1 500 TND',
                    '1 500 – 3 000 TND'  => '1 500 – 3 000 TND',
                    '+ 3 000 TND'        => '+ 3 000 TND',
                ],
                'placeholder' => 'Choisir...',
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [new NotBlank()],
                'attr'        => ['placeholder' => 'Décrivez votre voyage idéal, dates, nombre de voyageurs...', 'rows' => 4],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}