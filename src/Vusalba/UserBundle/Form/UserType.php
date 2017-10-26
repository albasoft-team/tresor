<?php
/**
 * Created by PhpStorm.
 * User: Ibrahima
 * Date: 14/08/2017
 * Time: 17:06
 */

namespace Vusalba\UserBundle\Form;


use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('plainPassword')
            ->add('node', EntityType::class, array(
                'class' => 'Vusalba\VueBundle\Entity\Node',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('q')
                        ->where('q.isScopeAnalyse='.true)
                        ->orderBy('q.name');
                },
                'choice_label' => 'name',
                'required' => false,
                'empty_data' => null,
                'preferred_choices' => array()
            ))
            ->add('file', FileType::class, ['required' => false]);
//            ->add('profiles', EntityType::class, array(
//                'class' => 'Vusalba\UserBundle\Entity\Profile',
//                'choice_label' => 'role'
//            ));
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vusalba\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_user';
    }
}