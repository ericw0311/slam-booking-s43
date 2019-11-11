<?php
// src/Form/UserFileEmailType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\UserFile;

class UserFileEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('email', EmailType::class, array('label' => 'user.email', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-yellow']))
			->add('lastName', HiddenType::class, array('data' => 'lastName'))
			->add('firstName', HiddenType::class, array('data' => 'firstName'));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => UserFile::class));
	}
}
