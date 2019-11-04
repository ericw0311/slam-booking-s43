<?php
// src/Form/PlanificationPeriodCreateDateType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\PlanificationPeriodCreateDate;

class PlanificationPeriodCreateDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$builder->add('date', DateType::class, array('label' => 'planificationPeriod.beginning.date', 'translation_domain' => 'messages', 'widget' => 'single_text', 'html5' => false, 'format' => 'dd/MM/yyyy', 'attr' => ['class' => 'datepicker']));
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => PlanificationPeriodCreateDate::class));
	}
}
