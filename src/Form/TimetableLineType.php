<?php
// src/Form/TimetableLineType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\TimetableLine;

class TimetableLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$builder->add('beginningTime', TimeType::class, array('label' => false,
		'widget' => 'single_text', 'html5' => false, 'attr' => ['class' => 'timepicker w3-input w3-border']))
	->add('endTime', TimeType::class, array('label' => false,
		'widget' => 'single_text', 'html5' => false, 'attr' => ['class' => 'timepicker w3-input w3-border']));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	$resolver->setDefaults(array('data_class' => TimetableLine::class));
	}
}
