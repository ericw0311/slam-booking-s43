<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\PlanificationLinesNDB;
use App\Entity\Timetable;

use App\Repository\TimetableRepository;

class PlanificationLinesNDBType extends AbstractType
{
	private $currentFile;

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	$this->currentFile = $options['current_file'];

	$builder->add('timetable_MON', EntityType::class, array(
		'label' => false,
		'class' => 'App\Entity\Timetable',
		'query_builder' => function(TimetableRepository $tr)
						{
						return $tr->getTimetablesQB($this->currentFile);
						},
		'choice_label' => 'name',
		'attr' => ['class' => 'w3-input w3-pale-green']))
			->add('timetable_TUE', EntityType::class, array(
		'label' => false,
		'class' => 'App\Entity\Timetable',
		'query_builder' => function(TimetableRepository $tr)
						{
						return $tr->getTimetablesQB($this->currentFile);
						},
		'choice_label' => 'name',
		'attr' => ['class' => 'w3-input w3-pale-green']))
			->add('timetable_WED', EntityType::class, array(
		'label' => false,
		'class' => 'App\Entity\Timetable',
		'query_builder' => function(TimetableRepository $tr)
						{
						return $tr->getTimetablesQB($this->currentFile);
						},
		'choice_label' => 'name',
		'attr' => ['class' => 'w3-input w3-pale-green']))
			->add('timetable_THU', EntityType::class, array(
		'label' => false,
		'class' => 'App\Entity\Timetable',
		'query_builder' => function(TimetableRepository $tr)
						{
						return $tr->getTimetablesQB($this->currentFile);
						},
		'choice_label' => 'name',
		'attr' => ['class' => 'w3-input w3-pale-green']))
			->add('timetable_FRI', EntityType::class, array(
		'label' => false,
		'class' => 'App\Entity\Timetable',
		'query_builder' => function(TimetableRepository $tr)
						{
						return $tr->getTimetablesQB($this->currentFile);
						},
		'choice_label' => 'name',
		'attr' => ['class' => 'w3-input w3-pale-green']))
			->add('timetable_SAT', EntityType::class, array(
		'label' => false,
		'class' => 'App\Entity\Timetable',
		'query_builder' => function(TimetableRepository $tr)
						{
						return $tr->getTimetablesQB($this->currentFile);
						},
		'choice_label' => 'name',
		'attr' => ['class' => 'w3-input w3-pale-green']))
			->add('timetable_SUN', EntityType::class, array(
		'label' => false,
		'class' => 'App\Entity\Timetable',
		'query_builder' => function(TimetableRepository $tr)
						{
						return $tr->getTimetablesQB($this->currentFile);
						},
		'choice_label' => 'name',
		'attr' => ['class' => 'w3-input w3-pale-green']))
			->add('activate_MON', CheckboxType::class, array(
		'label' => false,
		'required' => false))
			->add('activate_TUE', CheckboxType::class, array(
		'label' => false,
		'required' => false))
			->add('activate_WED', CheckboxType::class, array(
		'label' => false,
		'required' => false))
			->add('activate_THU', CheckboxType::class, array(
		'label' => false,
		'required' => false))
			->add('activate_FRI', CheckboxType::class, array(
		'label' => false,
		'required' => false))
			->add('activate_SAT', CheckboxType::class, array(
		'label' => false,
		'required' => false))
			->add('activate_SUN', CheckboxType::class, array(
		'label' => false,
		'required' => false));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	$resolver->setDefaults(array('data_class' => PlanificationLinesNDB::class));
	$resolver->setRequired('current_file');
	}
}
