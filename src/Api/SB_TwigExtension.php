<?php
namespace App\Api;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SB_TwigExtension extends \Twig_Extension
{
    private $translator;

    public function __construct($translator)
    {
    $this->translator = $translator;
    }
    
    public function getTranslator()
    {
    return $this->translator;
    }
    
	// J'ai gardé le filtre de l'exemple mais je ne l'utilise pas...
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        );
    }
    
    public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
		$price = number_format($number, $decimals, $decPoint, $thousandsSep);
		$price = '$'.$price;
		return $price;
    }
    
	public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('dateCourte', array($this, 'date_courte')),
            new \Twig_SimpleFunction('dateLongue', array($this, 'date_longue')),
            new \Twig_SimpleFunction('timetableLine', array($this, 'timetable_line')),
            new \Twig_SimpleFunction('periode', array($this, 'periode')),
        );
    }
    
	// Retourne une date courte. exemple: Vendredi 12 janvier 2018 --> Ven 12/01/18
    public function date_courte(\Datetime $date)
    {
		return $this->getTranslator()->trans('day.abbr.'.strtoupper($date->format('D'))).' '.$date->format('d/m/y');
    }
    
	// Retourne une date longue. exemple: Vendredi 12 janvier 2018
    public function date_longue(\Datetime $date)
    {
		return $this->getTranslator()->trans('day.'.strtoupper($date->format('D'))).' '.$date->format('d/m/Y');
    }
    
	// Retourne un période début - fin en ne gardant la date de fin que si elle est différente de la date de début.
    public function periode(\Datetime $beginningDate, \Datetime $endDate)
    {
		if (strcmp($endDate->format('d/m/Y'), $beginningDate->format('d/m/Y')) > 0) {
			return $beginningDate->format('d/m/Y H:i').' - '.$endDate->format('d/m/Y H:i');
		} else {
			return $beginningDate->format('d/m/Y H:i').' - '.$endDate->format('H:i');
		}
    }
    
	// Retourne un creneau horaire
    public function timetable_line(\App\Entity\TimetableLine $timetableLine)
    {
		return $timetableLine->getBeginningTime()->format('H:i').' - '.$timetableLine->getEndTime()->format('H:i');
    }
    
    public function getName()
    {
        return 'sb_extension';
    }
}
