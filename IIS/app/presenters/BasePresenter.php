<?php
use Nette\Utils\Strings;
/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	public $uzivatel;
	public $identita;
	public $resiRepository;
    public $zamestnanecRepository;
    public $oddeleniRepository;
	public $zakazkaRepository;
	public $vedouciRepository;
	public $zadavatelRepository;
	public $vydajRepository;
	public $pristupRepository;
	public $zaznam;
	
	
    public function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->getName() != 'SignPresenter' && $this->getAction() != 'in')
			$this->redirect ('sign:in');
		}
		$this->zamestnanecRepository = $this->context->zamestnanecRepository;
		$this->resiRepository= $this->context->resiRepository;
		$this->oddeleniRepository= $this->context->oddeleniRepository;
		$this->zakazkaRepository= $this->context->zakazkaRepository;
		$this->vedouciRepository= $this->context->vedouciRepository;
		$this->zadavatelRepository= $this->context->zadavatelRepository;
		$this->vydajRepository= $this->context->vydajRepository;
		$this->pristupRepository= $this->context->pristupRepository;
		if ($this->getUser()->isLoggedIn()) {
			  $this->identita=$this->getUser()->identity;
			  $login = $this->identita->login;
				  switch($this->who($login)) {
					  case "zamestnanec" :
						  $uzivatel = $this->zamestnanecRepository->jmenoUzivatele($login);
						  foreach ($uzivatel as $u) {
							  $this->template->uzivatel = ($u->jmeno . " " . $u->prijmeni);
						  }
						  break;
					  case "zakaznik" :
						  $uzivatel = $this->zadavatelRepository->findBy(array('ic' => $login))->fetch();
						  $this->template->uzivatel = $uzivatel->nazev;
						  break;
					  case "administrator" :
						  $this->template->uzivatel = $this->getUser()->identity->login;
					  default :
						  break;
				  }
	  }
	}
	
	public function who($login) {
		if (is_numeric($login) && Strings::length($login) < 8) { //zamestnanec
			return "zamestnanec";
		} elseif (is_numeric($login) ) {
			return "zakaznik"; //zakaznik
		}else { //admin
			return "administrator";
		}
	}
	
}
