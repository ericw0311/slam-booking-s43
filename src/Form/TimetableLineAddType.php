<?php
// src/Form/TimetableLineAddType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\TimetableLine;

class TimetableLineAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$builder->add('validateAndCreate', SubmitType::class, array('label' => 'timetableLine.validate.and.create', 'translation_domain' => 'messages'));


	}

	public function getParent()
	{
	return TimetableLineType::class;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	$resolver->setDefaults(array('data_class' => TimetableLine::class));
	}
}
