<?php
// src/Form/UserFileGroupType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\UserFileGroup;

class UserFileGroupType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('name', TextType::class, array('label' => 'userFileGroup.name', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-yellow']));
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => UserFileGroup::class));
	}
}
