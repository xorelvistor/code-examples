<?php
use Nette\Application\UI\Form,
    Nette\Application\AppForm,
    Nette\Database,
    Nette\Database\Table\Selection,
	Nette\Utils\Strings;


	
/**
 * Description of newPresenter
 *
 * @author oem
 */
class newPresenter extends BasePresenter {

	
    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
   	
	public function renderVyd($zak_id) {
	}
	/**
	 * Vytvori formular pro vlozeni zamestnance
	 * @return \Nette\Application\AppForm
	 */
    public function createComponentZamestnanecForm() {
	
	$oddeleni=  $this->oddeleniRepository->vycetOddeleni();
	
	$form= new Form();
	$form->addText('jmeno','* Jméno: ')
		->setRequired('Zadejte prosim jméno');
	$form->addText('prijmeni','* Příjmení: ')
		->setRequired('Zadejte prosim Příjmení');
	$form->addText('tituly','Tituly: ');
	$form->addText('rc','* RČ: ')
		->setRequired('Zadejte prosím rodné číslo zaměstnance.');
	$form->addText('tel','Telefon: ');
	$form->addRadioList('uvazek','* Pracovní úvazek: ',array('plny'=>'plný','polovicni'=>'poloviční'))
		->setRequired('Zadejte pracovní úvázek')
		->getSeparatorPrototype()->setName(NULL);
	$form->addSelect('odd','* Oddělení: ',$oddeleni)
		->setRequired('Zvolte zařazení zaměstnance')
		->setPrompt('Zvolte oddělení');
	$form->addSubmit('create','Přidat zaměstnance');
		$form->onSuccess[]=callback($this,'newZamSubmitted');
		return $form;
	}
	/**
	 * Zvaliduje formular a zajisti vlozeni do databaze
	 * @param type $form
	 */
    public function newZamSubmitted($form) {
	$values= $form->getValues();
	$vlozene_id = $this->zamestnanecRepository->pridejZamestnance($values);
	if ($vlozene_id) {
		$existuje=$this->pristupRepository->insertAccess($vlozene_id,$values->jmeno,"zamestnanec");
		if (!$existuje)
			$this->flashMessage('Tento zaměstnanec již je v databázi', 'error');
		else
			$this->flashMessage('Zaměstanenec byl přidán do databáze', 'success');
	} else {
		$this->flashMessage('Tento zaměstnanec již je v databázi', 'error');
	}
	$this->redirect('this');
    }
    
	/**
	 * Vytvori formular pro zalozeni oddeleni
	 * @return \Nette\Application\AppForm
	 */
	public function createComponentOddeleniForm() {
	
	$form= new Form();
	$form->addText('nazev','* Název oddělení: ')
		->setRequired('Uveďte prosím název nového oddělení.')
			->addRule(Form::MAX_LENGTH, "Název oddělení může mít nejvýše %d znaků",80)
		->setAttribute('size', 40);
	$form->addText('kodOddeleni','* Zkratka: ')
		->addRule(Form::MAX_LENGTH, '* Zkratka oddělení musí mít délku %d znaky.', 3)
		->setRequired('Zadejte prosím zkratku nového oddělení.');
	$form->addTextArea('popis','Zaměření oddělení ')
		 ->addRule(Form::MAX_LENGTH, 'Zaměření oddělení může mít nejvýše %d znaků', 80);
	$form->addSubmit('create','Založit');
		$form->onSuccess[]=callback($this,'newOddSubmitted');
		return $form;
	}
	/**
	 * Zvaliduje formular a zajisti vlozeni do databaze
	 * @param type $form
	 */
    public function newOddSubmitted($form) {
		$values= $form->getValues();
		$existuje = $this->oddeleniRepository->pridejOddeleni($values);
		if (!$existuje) {
			$this->flashMessage('Oddělení s touto zkratkou již existuje.','error');
		} else {
			$this->flashMessage('Oddělení bylo založeno.', 'success');
		}
		$this->redirect('this');
	}
	/**
	 * Vytvori formular pro zalozeni zakazky
	 * @return \Nette\Application\AppForm
	 */
	public function createComponentZakazkaForm() {
	  
	  $zadavatele= $this->zadavatelRepository->vycetZadavatelu();
	  $dnes = date("Y-m-d");
	  
	  $form= new Form();
	  $form->addText('prijato','Přijato dne *')
		  ->setRequired('Není uvedeno, kdy byla zakázka přijata.')
		  ->setValue($dnes)
		  ->setType('date');
	  $form->addText('termin','Termín vyhotovení *') 
		  ->setRequired('Nebyl zadán termín do kdy má být zakázka vyhotovena.')
			  ->setDefaultValue('yyyy-mm-dd')
		  ->setType('date')
		  ->setAttribute('min', $dnes);
	  $form->addText('zaloha','Záloha')
		  ->addCondition(Form::FILLED)
			->addRule(Form::RANGE, 'Částka musí být kladná!', array(0,NULL));
	  $form->addText('rozpocet','Rozpočet *')
		  ->setRequired('Nebyl zadán rozpočet zakázky.')
		  ->addRule(Form::RANGE, 'Částka musí být kladná!', array(0,NULL));
	  $form->addSelect('zadal','Zakázku zadal *',$zadavatele)
		  ->setRequired('Kdo zadal zakázku?')
		  ->setPrompt('Zvolte zadavatele');
	  $form->addTextArea('popis','Popis zakázky *')
		   ->setRequired('Co byste si přál?')
		   ->addRule(Form::MAX_LENGTH, 'Popis zakázky může obsahovat nejvýše %d znaků', 150);
	  $form->addSubmit('create','Vytvořit');
	  $form->onSuccess[]=callback($this,'newZakSubmitted');
	  
	  $identita = $this->getUser()->identity;
	  if ($identita->role == "zakaznik") {
		$form->setDefaults(array(
			'zadal' => $identita->login
		));
	  }
	
		  return $form;
	}
	/**
	 * Zvaliduje formular a zajisti vlozeni do databaze
	 * @param type $form
	 */
    public function newZakSubmitted($form) {
	  $values= $form->getValues();
	  $this->zakazkaRepository->pridejZakazku($values);
	  $this->flashMessage('Zakázka byla vytvořena.', 'success');
	  $this->redirect('this');
    }
	
	/**
	 * Vytvori formular pro pridani zadavatele
	 * @return \Nette\Application\AppForm
	 */
	public function createComponentZadavatelForm() { 
	  $form= new Form();
	  $form->addGroup('Nový zadavatel');
	  $form->addText('ic','* IČ: ')
		  ->addRule(Form::INTEGER, 'IČ musí být číslo')
		  ->addRule(Form::LENGTH, 'IČ musí obsahovat %d čísel', 8)
		  ->setRequired('Není uveden IČ.');
	  $form->addText('dic','DIČ: ')
		  ->setAttribute('maxlength',10)
		  ->addCondition(Form::FILLED)
			->addRule(Form::LENGTH, 'DIČ musÍ obsahovat %d znaků', 10);
	  $form->addText('nazev','* Název firmy: ')
		  ->setRequired('Není uveden název firmy.')
		  ->addRule(Form::MAX_LENGTH, 'Název firmy smí obsahovat nejvýše %d znaků', 80)
		  ->setAttribute('size', 40);
	  $form->addText('zkratka','* Zkratka firmy: ')
		   ->addRule(Form::MAX_LENGTH, 'Zkratka firmy smí obsahovat nejvýše %d znaků', 5);
	  $form->addText('mesto','Město: ');
	  $form->addText('ulice','Ulice: ');
	  $form->addText('cp','Č. p.: ')
		  ->addCondition(Form::FILLED)
			->addRule(Form::INTEGER, 'Č.p musí být číslo');
	  $form->addText('psc','PSČ: ')
		  ->addCondition(Form::FILLED)
			->addRule(Form::INTEGER, 'PSČ musí být číslo')
			->addRule(Form::LENGTH, 'PSČ musi obsahovat %d čísel', 5);
	  $form->addText('ucet','Bankovní spojení: ')
		  ->addCondition(Form::FILLED)
			->addRule(Form::REGEXP,'Bankovní spojení nemá správný tvar',"/^[0-9]+\/[0-9]{4}$/");
	  $form->addText('kontakt','* Kontakt: ')
			  ->setRequired('Není uveden kontakt');
	  $form->addSubmit('create','Přidat');
	  $form->onSuccess[]=callback($this,'newZadSubmitted');
	  
	  
	  return $form;
	}
	/**
	 * Zvaliduje formular a zajisti vlozeni do databaze
	 * @param type $form
	 */
    public function newZadSubmitted($form) {
	  $values= $form->getValues();
	  if($this->zadavatelRepository->pridejZadavatele($values)){		  
		$existuje=$this->pristupRepository->insertAccess($values->ic,$values->zkratka,"zakaznik");
		if (!$existuje)
			$this->flashMessage('Tento zadavatel již je v databázi', 'error');
	    $this->flashMessage('Zadavatel byl přidán do databáze.', 'success');
	  } else {
		$this->flashMessage('Tento zadavatel již je v databázi', 'error');
	  }
	  /*dump($values);*/
	  $this->redirect('this');
    }
	
	/**
	 * Vytvori formular pro zalozeni zakazky
	 * @return \Nette\Application\AppForm
	 */
	public function createComponentVydajForm() {
	 
	  $form= new Form();
	  $form->addText('vystaveno','* Vystaveno dne: ')
			->setType('date')
			->setValue(date("Y-m-d"))
		  ->setRequired('Není uvedeno datum vystavení dokladu.');
	  $form->addText('castka','* Částka: ')
		->setRequired('Není uvedena částka výdaje.')
		->addRule(Form::RANGE, 'Částka musí být kladná!', array(0,NULL));
	  $form->addTextArea('popis','* Účel: ')
		  ->setRequired('Nebyl zadán účel výdaje.')
		   ->addRule(Form::MAX_LENGTH, 'Účel výdaje může obsahovat nejvýše %d znaků', 50);
	  $form->addSubmit('create','Přidat');
		  $form->onSuccess[]=callback($this,'newVydSubmitted');
		  return $form;
	}
	
	/**
	 * Zvaliduje formular a zajisti vlozeni do databaze
	 * @param type $form
	 */
    public function newVydSubmitted($form) {
	  $vse = $this->getParameter();
	  $values= $form->getValues();
	  $this->vydajRepository->pridejVydaj($values,  $vse);
	  $this->flashMessage('Vydaj byl přidán do databáze.', 'success');
	  $this->redirect('homepage:infoZak',$vse['zak_id']);
    }
    public function actionDefault() {
		
    }

    public function renderDefault() {
		
    }
    

}