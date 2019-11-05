<?php
namespace App\Entity;
class ListContext
{
    protected $entityCode; // Code de l'entite
    protected $image; // Image de l'entite
    protected $page; // Numero de la page affichee
    protected $numberRecords; // Nombre d'enregistrements total
    protected $numberLines; // Nombre de lignes pouvant etre affichees sur une page
    protected $numberColumns; // Nombre de colonnes pouvant etre affichees sur une page

    function __construct($em, \App\Entity\User $user, $entityCode, $image, $page, $numberRecords) {
    $this->entityCode = $entityCode;
    $this->image = $image;
    $this->page = $page;
    $this->numberRecords = $numberRecords;
    $userParameterRepository = $em->getRepository(UserParameter::class);
    $userParameter = $userParameterRepository->findOneBy(array('user' => $user, 'parameterGroup' => ($entityCode.'.number.lines.columns'), 'parameter' => ($entityCode.'.number.lines')));
    if ($userParameter != null) { $this->numberLines = $userParameter->getIntegerValue(); } else { $this->numberLines =  constant(Constants::class.'::LIST_DEFAULT_NUMBER_LINES'); }
    $userParameter = $userParameterRepository->findOneBy(array('user' => $user, 'parameterGroup' => ($entityCode.'.number.lines.columns'), 'parameter' => ($entityCode.'.number.columns')));
    if ($userParameter != null) { $this->numberColumns = $userParameter->getIntegerValue(); } else { $this->numberColumns = constant(Constants::class.'::LIST_DEFAULT_NUMBER_COLUMNS'); }
    return $this;
    }

    // Code de l'entite
    public function getEntityCode()
    {
    return $this->entityCode;
    }

    public function getImage()
    {
    return $this->image;
    }

    public function getPage()
    {
    return $this->page;
    }

    public function getNumberRecords()
    {
    return $this->numberRecords;
    }

    public function getNumberLines()
    {
    return $this->numberLines;
    }

    public function getNumberColumns()
    {
    return $this->numberColumns;
    }

    // Nombre maximum d'enregistrements affiches sur une page
    public function getMaxRecords()
    {
    return $this->getNumberLines() * $this->getNumberColumns();
    }

    public function getNumberPages()
    {
    return ceil($this->getNumberRecords() / $this->getMaxRecords());
    }

	// index du premier enregistrement affiche
    public function getFirstRecordIndex()
    {
    return ($this->getPage()-1) * $this->getMaxRecords();
    }

	// Nombre de pages
    public function calculNumberPages()
    {
    return ceil($this->getNumberRecords() / $this->getMaxRecords());
    }

	// Nombre d'enregistrements affiches
    public function getNumberRecordsDisplayed()
    {
    if ($this->getNumberPages() <= 1) { return $this->getNumberRecords(); } // Si une seule page, le nombre d'enregistrements affiches est le nombre complet d'enregistrements
    if ($this->getPage() < $this->getNumberPages()) { return $this->getMaxRecords(); } // Si la page affichee est strictement inferieure au nombre de pages, le nombre d'enregistrements affiches est le nombre maximum d'enregistrements
    // Cas d'une derniere page qui n'est pas la premiere
    if ($this->getNumberPages() * $this->getMaxRecords() <= $this->getNumberRecords()) { return $this->getMaxRecords(); } // Cas ou le nombre d'enregistrement total est un multiple du nombre d'enregistrements affiches.
    return $this->getNumberRecords() % $this->getMaxRecords(); // Cas d'une derniere page qui n'est pas la premiere
    }

	// Nombre de lignes affichees
    public function getNumberLinesDisplayed()
    {
    return min($this->getNumberRecordsDisplayed(), $this->getNumberLines());
    }

	// Nombre de colonnes affichees
    public function getNumberColumnsDisplayed()
    {
    if ($this->getNumberLinesDisplayed() > 0) { return ceil($this->getNumberRecordsDisplayed() / $this->getNumberLinesDisplayed()); }
    return 0;
    }
}
