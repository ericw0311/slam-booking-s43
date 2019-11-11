<?php
// src/Form/UserFileAddType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\UserFile;

class UserFileAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('email', HiddenType::class)
			->add('accountType', ChoiceType::class, array(
				'label' => 'user.account.type',
				'translation_domain' => 'messages',
				'choices' => array('INDIVIDUAL' => 'INDIVIDUAL', 'ORGANISATION' => 'ORGANISATION'),
				'choice_label' => function ($value, $key, $index) { return 'user.account.type.'.$key; },
				'attr' => ['class' => 'w3-input w3-pale-yellow']
			))
			->add('firstName', TextType::class, array('label' => 'user.firstName', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-yellow']))
			->add('lastName', TextType::class, array('label' => 'user.lastName', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-yellow']))
			->add('uniqueName', TextType::class, array('label' => 'user.organisation.name', 'translation_domain' => 'messages', 'required' => false, 'attr' => ['class' => 'w3-input w3-pale-yellow']))
			->add('administrator', HiddenType::class, array('data' => 0));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => UserFile::class));
	}
}
