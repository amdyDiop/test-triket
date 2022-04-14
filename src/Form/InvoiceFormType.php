<?php

namespace App\Form;

use App\Entity\Invoicelines;
use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InvoiceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceDate', DateType::class, ['attr' => ['class' => 'form-control']])
            ->add('customerId', NumberType::class, ['attr' => ['class' => 'form-control']])
            ->add('invoicelines', CollectionType::class, [
                'entry_type' => InvoicelinesFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
            ])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-primary'], 'label' => 'Create invoce']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
