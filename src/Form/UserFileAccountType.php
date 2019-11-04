<?php
// src/Form/UserFileAccountType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\UserFile;

class UserFileAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('email', HiddenType::class)
			->add('accountType', HiddenType::class)
			->add('lastName', HiddenType::class)
			->add('firstName', HiddenType::class)
			->add('uniqueName', HiddenType::class)
			->add('administrator', CheckboxType::class, array('label' => 'userFile.administrator.rights', 'translation_domain' => 'messages', 'required' => false));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => UserFile::class));
	}
}
