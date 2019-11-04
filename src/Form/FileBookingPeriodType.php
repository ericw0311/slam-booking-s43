<?php
// src/Form/FileBookingPeriodType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\FileBookingPeriod;

class FileBookingPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('before', CheckboxType::class, array('label' => 'file.booking.period.before.1', 'translation_domain' => 'messages', 'required' => false))
			->add('beforeType', ChoiceType::class, array(
			'label' => 'file.booking.period.type',
			'translation_domain' => 'messages',
			'choices' => array('DAY' => 'DAY', 'WEEK' => 'WEEK', 'MONTH' => 'MONTH', 'YEAR' => 'YEAR'),
			'choice_label' => function ($value, $key, $index) { return 'file.booking.period.type.'.$key; },
			'attr' => ['class' => 'w3-input w3-border w3-pale-green']
        ))
		->add('beforeNumber', ChoiceType::class,
			array('label' => 'file.booking.period.number', 'translation_domain' => 'messages',
	'choices'  => array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
			'10' => 10, '11' => 11, '12' => 12, '13' => 13, '14' => 14, '15' => 15, '16' => 16, '17' => 17, '18' => 18, '19' => 19,
			'20' => 20, '21' => 21, '22' => 22, '23' => 23, '24' => 24, '25' => 25, '26' => 26, '27' => 27, '28' => 28, '29' => 29,
			'30' => 30),
			'attr' => ['class' => 'w3-input w3-border w3-pale-green']			
		))
		->add('after', CheckboxType::class, array('label' => 'file.booking.period.after.1', 'translation_domain' => 'messages', 'required' => false))
			->add('afterType', ChoiceType::class, array(
			'label' => 'file.booking.period.type',
			'translation_domain' => 'messages',
			'choices' => array('DAY' => 'DAY', 'WEEK' => 'WEEK', 'MONTH' => 'MONTH', 'YEAR' => 'YEAR'),
			'choice_label' => function ($value, $key, $index) { return 'file.booking.period.type.'.$key; },
			'attr' => ['class' => 'w3-input w3-border w3-pale-green']
        ))
		->add('afterNumber', ChoiceType::class,
			array('label' => 'file.booking.period.number', 'translation_domain' => 'messages',
	'choices'  => array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
			'10' => 10, '11' => 11, '12' => 12, '13' => 13, '14' => 14, '15' => 15, '16' => 16, '17' => 17, '18' => 18, '19' => 19,
			'20' => 20, '21' => 21, '22' => 22, '23' => 23, '24' => 24, '25' => 25, '26' => 26, '27' => 27, '28' => 28, '29' => 29,
			'30' => 30),
			'attr' => ['class' => 'w3-input w3-border w3-pale-green']
		));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => FileBookingPeriod::class));
	}
}
