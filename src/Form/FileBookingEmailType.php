<?php
// src/Form/FileBookingEmailType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\FileBookingEmail;

class FileBookingEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$builder->add('fileAdministrator', CheckboxType::class, array('label' => 'file.booking.email.administrator', 'translation_domain' => 'messages', 'required' => false))
		->add('bookingUser', CheckboxType::class, array('label' => 'file.booking.email.user', 'translation_domain' => 'messages', 'required' => false));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array('data_class' => FileBookingEmail::class));
	}
}
