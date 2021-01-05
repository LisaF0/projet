<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Domain;
use App\Entity\Product;
use App\Entity\Appellation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('unitPrice', MoneyType::class, [
                'label' => 'Prix à l\'unité',
            ])
            ->add('unitStock', NumberType::class, [
                'label' => 'Quantité en stock'
            ])
            ->add('photo')
            ->add('year', NumberType::class, [
                'label' => 'Année',
            ])
            ->add('appellation', EntityType::class, [
                'class' => Appellation::class,
                'choice_label' => 'name'
            ])
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name'
            ])
            ->add('domain', EntityType::class, [
                'class' => Domain::class,
                'choice_label' => 'name'
            ])
            ->add('ajouter', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
