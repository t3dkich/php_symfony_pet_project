<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('dateAdded', DateType::class)
            ->add('category', EntityType::class, [
                'class' => 'AppBundle\Entity\Category'
            ])
            ->add('animalType', EntityType::class, [
                'class' => 'AppBundle\Entity\AnimalCategory'
            ])
            ->add('user', EntityType::class, [
                'class' => 'AppBundle\Entity\User'
            ])
            ->add('animal', EntityType::class, [
                'class' => 'AppBundle\Entity\Animal'
            ])
            ->add('price', MoneyType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Offer'
        ));
    }


}
