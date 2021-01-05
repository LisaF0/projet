<?php 
namespace App\Form;

use App\Entity\Type;
use App\Entity\Domain;
use App\Entity\Filter;
use App\Entity\Appellation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('appellations', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Appellation::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name'
            ])
            ->add('domains', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Domain::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name'
            ])
            ->add('types', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Type::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
            ])
            ->add('min', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min'
                ]
            ])
            ->add('max', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max'
                ]
            ])
            // ->add('submit', SubmitType::class, [
            //     'label' => 'Filtrer',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
?>