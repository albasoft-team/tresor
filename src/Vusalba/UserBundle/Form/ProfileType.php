<?php
/**
 * Created by PhpStorm.
 * User: Ibrahima
 * Date: 14/08/2017
 * Time: 18:13
 */

namespace Vusalba\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vusalba\UserBundle\Entity\Profile;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role')
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'userbundle_profil';
    }

}