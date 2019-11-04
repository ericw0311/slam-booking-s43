<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\QueryBooking;

class QueryBookingType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$builder->add('name', TextType::class, array('label' => 'queryBooking.name', 'translation_domain' => 'messages', 'attr' => ['class' => 'w3-input w3-pale-green']))
		->add('periodType', ChoiceType::class, array(
			'label' => 'period.type',
			'translation_domain' => 'messages',
			'choices' => array('NO' => 'NO', 'BETWEEN' => 'BETWEEN', 'AFTER' => 'AFTER', 'BEFORE' => 'BEFORE', 'SYSDATE' => 'SYSDATE', 'AFTER.SYSDATE' => 'AFTER.SYSDATE', 'BEFORE.SYSDATE' => 'BEFORE.SYSDATE'),
			'choice_label' => function ($value, $key, $index) { return 'queryBooking.period.type.'.$key; },
			'attr' => ['class' => 'w3-input w3-pale-green']
        ))
		->add('beginningDate', DateType::class, array('label' => 'from', 'translation_domain' => 'messages',
			'widget' => 'single_text', 'html5' => false, 'format' => 'dd/MM/yyyy', 'attr' => ['class' => 'datepicker w3-input w3-border']))
			->add('endDate', DateType::class, array('label' => 'to', 'translation_domain' => 'messages',
			'widget' => 'single_text', 'html5' => false, 'format' => 'dd/MM/yyyy', 'attr' => ['class' => 'datepicker w3-input w3-border']));
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => QueryBooking::class));
	}
}
