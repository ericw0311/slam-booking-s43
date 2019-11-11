<?php
// src/Form/NoteType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Note;

class NoteType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('note', TextareaType::class, array('label' => 'note', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-yellow']));
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => Note::class));
	}
}
