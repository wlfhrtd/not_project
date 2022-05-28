<?php

namespace App\Form;

use App\Entity\Cart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', CollectionType::class, [
                'entry_type' => OrderCartItemType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'd-flex justify-content-start',
                    ],
                ],
                'attr' => [
                    'class' => 'items',
                    'data-prototype' => 'form_widget(orderForm.cart.items.vars.prototype)|e',
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}
