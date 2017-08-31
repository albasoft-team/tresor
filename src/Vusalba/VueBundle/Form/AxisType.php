<?php

namespace Vusalba\VueBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AxisType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('description')
            ->add('groupe', EntityType::class, array(
                'class' => 'Vusalba\VueBundle\Entity\AxeGroupe',
                'choice_label' => 'name',
                'required' => false,
                'empty_data' => null,
                'preferred_choices' => array()
            ))
            ->add('iscalculated')
            ->add('formula');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vusalba\VueBundle\Entity\Axis'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vusalba_vuebundle_axis';
    }


}
