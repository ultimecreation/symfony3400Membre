<?php

namespace AppBundle\Form;

use AppBundle\Entity\AppUser;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options)
    {
        $builder->add('username',TextType::class,array('label'=>'Nom d\'utilisateur'))
                ->add('email',EmailType::class,array('label'=>'Email'))
                ->add('plainPassword',RepeatedType::class,array(
                    'type'=>PasswordType::class,
                    'invalid_message' => 'les mots de passe ne correspondent pas',
                    'first_options'=>array('label'=>'Mot de Passe'),
                    'second_options'=> array('label'=>'Répéter Mot de Passe')
                ))
                ->add('Accepter_les_Termes',CheckboxType::class,array(
                    'label' => 'j\'accepte les Conditions d\'utilsation',
                    'mapped'=> false,
                    'constraints'=> new IsTrue(),
                ));
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=> AppUser::class,
        ));
    }
}