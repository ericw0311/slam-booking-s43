<?php
// /src/Form/UserPasswordType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
			->add('accountType', HiddenType::class)
            ->add('firstName', HiddenType::class)
            ->add('lastName', HiddenType::class)
            ->add('email', HiddenType::class)
            ->add('userName', HiddenType::class)
			->add('uniqueName', HiddenType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'user.password', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-yellow']),
                'second_options' => array('label' => 'user.repeat.password', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-yellow'])));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => User::class));
    }
}
