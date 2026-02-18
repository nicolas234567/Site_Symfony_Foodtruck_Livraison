<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr'  => ['class' => 'form-control', 'placeholder' => 'Ex: Burger classique'],
            ])
            ->add('prix', MoneyType::class, [
                'label'    => 'Prix (€)',
                'currency' => 'EUR',
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('imageFile', FileType::class, [
                'label'    => 'Image du produit',
                'mapped'   => false,
                'required' => false,
                'attr'     => ['class' => 'form-control', 'accept' => 'image/*'],
            ])
            ->add('disponible', CheckboxType::class, [
                'label'    => 'Disponible à la vente',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Produit::class]);
    }
}
