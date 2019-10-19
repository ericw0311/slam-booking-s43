<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
  			->add('accountType', ChoiceType::class, array(
  				'label' => 'user.account.type',
  				'translation_domain' => 'messages',
  				'choices' => array('INDIVIDUAL' => 'INDIVIDUAL', 'ORGANISATION' => 'ORGANISATION'),
  				'choice_label' => function ($value, $key, $index) { return 'user.account.type.'.$key; },
  				'attr' => ['class' => 'w3-input w3-pale-green']
  			))
              ->add('firstName', TextType::class, array('label' => 'user.firstName', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-border w3-pale-green']))
              ->add('lastName', TextType::class, array('label' => 'user.lastName', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-border w3-pale-green']))
              ->add('email', EmailType::class, array('label' => 'user.email', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-border w3-pale-green']))
              ->add('userName', TextType::class, array('label' => 'user.name', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-border w3-pale-green']))
  			->add('uniqueName', TextType::class, array('label' => 'user.organisation.name', 'translation_domain' => 'messages', 'required' => false, 'attr' => ['class' => 'w3-input w3-border w3-pale-green']))
              ->add('password', RepeatedType::class, array(
                  'type' => PasswordType::class,
                  'first_options'  => array('label' => 'user.password', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-border w3-pale-green']),
                  'second_options' => array('label' => 'user.repeat.password', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-border w3-pale-green'])));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
