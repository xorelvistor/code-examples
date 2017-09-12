<?php
use Nette\Application\UI\Form;
use Nette\Utils\Strings;

/**
 * Description of HomepagePresenter
 *
 * @author oem
 */
class editPresenter extends BasePresenter {

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    /** @var Todo\TaskRepository */

	public function actionEditZam() {
					$zaznam=$this->zamestnanecRepository->infoZamestnanec(array('osobniCislo' => $this->identita->login))->fetch();
					if (!$zaznam || !$this->identita) {
						throw new \Nette\Application\BadRequestException;
					}
					$this["editZamForm"]->setDefaults(array(
						'osobniCislo' => $zaznam->osobniCislo,
						'jmeno' => $zaznam->jmeno,
						'prijmeni' => $zaznam->prijmeni,
						'tituly' => $zaznam->tituly,
						'rc' => $zaznam->rc,
						'tel' => $zaznam->telefon,
						'uvazek' => $zaznam->pracUvazek,
						'odd' => $zaznam->oddeleni_id,
						'rules' => $this->identita->role));
	}
	public function actionEditZad() {
		$zaznam=$this->zadavatelRepository->infoZadavatel(array('ic' => $this->identita->login))->fetch();
		if (!$zaznam || !$this->identita) {
			throw new \Nette\Application\BadRequestException;
		}
		if ($zaznam->psc == 0)
				$zaznam->psc="";
		$this["editZadForm"]->setDefaults(array(
			'ic' => $zaznam->ic,
			'dic' => $zaznam->dic,
			'nazev' => $zaznam->nazev,
			'zkratka' => $zaznam->zkratka,
			'mesto' => $zaznam->mesto,
			'ulice' => $zaznam->ulice,
			'cp' => $zaznam->cp,
			'psc' => $zaznam->psc,
			'ucet' => $zaznam->ucet,
			'kontakt' => $zaznam->kontakt,
			'rules' => $this->identita->role
		));
	}
	
	public function actionAdminEditZam($id) {
		if ($this->identita->role == "administrator") {
			$pristup = $this->pristupRepository->findByName($id);
				$zaznam=$this->zamestnanecRepository->infoZamestnanec(array('osobniCislo' => $id))->fetch();
				if (!$zaznam || !$pristup) {
					print("Nebylo možné najít identitu upravovaneho zamestnance");
					throw new \Nette\Application\BadRequestException;
				}
				$this["adminEditZamForm"]->setDefaults(array(
					'osobniCislo' => $zaznam->osobniCislo,
					'jmeno' => $zaznam->jmeno,
					'prijmeni' => $zaznam->prijmeni,
					'tituly' => $zaznam->tituly,
					'rc' => $zaznam->rc,
					'tel' => $zaznam->telefon,
					'uvazek' => $zaznam->pracUvazek,
					'odd' => $zaznam->oddeleni_id,
					'rules' => $pristup->role
				)); 
		}
	}
	public function actionAdminEditZad($id) {
		if ($this->identita->role == "administrator") {
			$zaznam=$this->zadavatelRepository->infoZadavatel(array('ic' => $id))->fetch();
			$pristup = $this->pristupRepository->findByName($id);
			if (!$zaznam || !$pristup) {
					throw new \Nette\Application\BadRequestException;
			}
			if ($zaznam->psc == 0)
				$zaznam->psc="";
			$this["adminEditZadForm"]->setDefaults(array(
				'ic' => $zaznam->ic,
				'dic' => $zaznam->dic,
				'nazev' => $zaznam->nazev,
				'zkratka' => $zaznam->zkratka,
				'mesto' => $zaznam->mesto,
				'ulice' => $zaznam->ulice,
				'cp' => $zaznam->cp,
				'psc' => $zaznam->psc,
				'ucet' => $zaznam->ucet,
				'kontakt' => $zaznam->kontakt,
				'rules' => $pristup->role
			));
			
		}
	}
	public function actionAdminEditOdd($id) {
		if ($this->identita->role == "administrator") {
			$zaznam=$this->oddeleniRepository->findBy(array('kodOddeleni' => $id))->fetch();
			if (!$zaznam) {
				throw new \Nette\Application\BadRequestException;
			}
			$this["adminEditOddForm"]->setDefaults(array(
				'nazev' => $zaznam->nazev,
				'kodOddeleni' => $zaznam->kodOddeleni,
				'popis' => $zaznam->zamereni
			));
		}
	}
	
	/**
	 * Formular pro editaci uzivatele-zamestnance
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentEditZamForm() {
		$form = new Form();
		$oddeleni = $this->oddeleniRepository->vycetOddeleni();
				
		$form->addText('osobniCislo','Osobní číslo: ')->setDisabled();
		$form->addText('jmeno','Jméno: ')->setDisabled();
		$form->addText('prijmeni','Příjmení: ');
		$form->addText('tituly','Tituly: ');
		$form->addText('rc','RČ: ')->setDisabled();
		$form->addText('tel','Telefon: ');
		$form->addRadioList('uvazek','Pracovní úvazek: ',array('plny'=>'plný','polovicni'=>'poloviční'))
			->setDisabled()
			->getSeparatorPrototype()->setName(NULL);
		$form->addSelect('odd','Oddělení: ',$oddeleni)->setDisabled();
		
		$form->addPassword('newPswd', 'Nové heslo: ');
		$form->addPassword('ctrlPswd', 'Kontrola hesla: ');
		$form->addSubmit('edit','Uložit změny');
		$form->onSuccess[]=callback($this,'UserEditSubmitted');
		return $form;
	}
	/**
	 * Formular pro editaci uzivatele-zadavatele
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentEditZadForm(){
		$form = new Form();
		$form->addText('ic','IČ: ')->setDisabled();
		$form->addText('dic','DIČ: ')->setDisabled();
		$form->addText('nazev','Název firmy: ')
			->setRequired('Není uveden název firmy.')
			->addRule(Form::MAX_LENGTH, 'Název firmy smí obsahovat nejvýše %d znaků', 80);
		$form->addText('zkratka','Zkratka firmy: ')
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
		$form->addText('kontakt','Kontakt: ')
				->setAttribute('size',30);
		
		$form->addPassword('newPswd', 'Nové heslo: ');
		$form->addPassword('ctrlPswd', 'Kontrola hesla: ');
		$form->addSubmit('edit','Uložit změny');
		$form->onSuccess[]=callback($this,'UserEditSubmitted');
		
		return $form;
	}
	/**
	 * Formular pro editaci uzivatele-administrator
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentEditAdminForm() {
		$form = new Form();
		$form->addPassword('newPswd', 'Nové heslo: ');
		$form->addPassword('ctrlPswd', 'Kontrola hesla: ');
		$form->addSubmit('edit','Uložit změny');
		$form->onSuccess[]=callback($this,'UserEditSubmitted');
		return $form;
	}
	/**
	 * Umoznuje editaci uzivatele
	 * @param \Nette\Application\UI\Form $form
	 * @return boolean
	 */
	public function UserEditSubmitted(Form $form) {
		$i = $form->getValues();
		
		if (!empty($i->newPswd) || !empty($i->ctrlPswd)) {
			if($this->checkPassword($i->newPswd, $i->ctrlPswd))
				$this->pristupRepository->updatePassword($this->identita->login,md5($i->newPswd));
			else
				switch ($this->identita->role) {
					case "zamestnanec":
						$this->redirect('edit:editZam');
						break;
					case "zakaznik":
						$this->redirect('edit:editZad');
						break;
					case "administrator":
						$this->redirect('edit:editAdmin');
						break;
					default :
				}

		}
		
		switch ($this->identita->role) {
			case "zamestnanec":
				$this->zamestnanecRepository->aktualizujInformace($this->identita->login,$form);
				break;
			case "zakaznik":
				dump($i);
				$this->zadavatelRepository->aktualizujInformace($this->identita->login,$form);
				break;
			default :	
		}
		 
		$this->flashMessage('Změny byly uloženy.');
		
		switch ($this->identita->role) {
			case "zamestnanec":
				$this->redirect('edit:editZam');
				break;
			case "zakaznik":
				$this->redirect('edit:editZad');
				break;
			case "administrator":
				$this->redirect('edit:editAdmin');
				break;
			default :
		}
		
	}
	
	public function createComponentAdminEditZamForm() {
		$form = new Form();
		$oddeleni = $this->oddeleniRepository->vycetOddeleni();
				
		$form->addText('osobniCislo','Osobní číslo: ')->setDisabled();
		$form->addText('jmeno','Jméno: ')->setDisabled();
		$form->addText('prijmeni','Příjmení: ');
		$form->addText('tituly','Tituly: ');
		$form->addText('rc','RČ: ')->setDisabled();
		$form->addText('tel','Telefon: ');
		$form->addRadioList('uvazek','Pracovní úvazek: ',array('plny'=>'plný','polovicni'=>'poloviční'))
			->getSeparatorPrototype()->setName(NULL);
		$form->addSelect('odd','Oddělení: ',$oddeleni);
		
		$role = $this->pristupRepository->listOfRoles();
		$form->addPassword('newPswd', 'Nové heslo: ');
		$form->addPassword('ctrlPswd', 'Kontrola hesla: ');
		$form->addSelect('rules','Oprávnění: ',$role);
		
		$form->addSubmit('edit','Uložit změny');
		$form->onSuccess[]=callback($this,'AdminEditSubmitted');
		
		return $form;

	}
	public function createComponentAdminEditZadForm () {
		$form = new Form();
		$form->addText('ic','IČ: ')->setDisabled();
		$form->addText('dic','DIČ: ')->setDisabled()
			->setAttribute('maxlength',10)
			->setAttribute('size',10);
		$form->addText('nazev','Název firmy: ')
			->setRequired('Není uveden název firmy.')
			->addRule(Form::MAX_LENGTH, 'Název firmy smí obsahovat nejvýše %d znaků', 80)
			->setAttribute('size',40);
		$form->addText('zkratka','Zkratka firmy: ')
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
		$form->addText('kontakt','Kontakt: ');
		
		$role = $this->pristupRepository->listOfRoles();
		$form->addPassword('newPswd', 'Nové heslo: ');
		$form->addPassword('ctrlPswd', 'Kontrola hesla: ');
		$form->addSelect('rules','Oprávnění: ',$role);
		
		$form->addSubmit('edit','Uložit změny');
		$form->onSuccess[]=callback($this,'AdminEditSubmitted');
		
		return $form;
	}
	
	public function createComponentAdminEditOddForm () {
		$form = new Form();
		$form->addText('nazev','* Název oddělení: ')
			->setRequired('Uveďte prosím název nového oddělení.')
			->addRule(Form::MAX_LENGTH, "Název oddělení může mít nejvýše %d znaků",80)
			->setAttribute('size', 40);
		$form->addText('kodOddeleni','Zkratka: ')->setDisabled();
		$form->addTextArea('popis','Zaměření oddělení ')
			->addRule(Form::MAX_LENGTH, 'Zaměření oddělení může mít nejvýše %d znaků', 80);
		
		$form->addSubmit('edit','Uložit změny');
		$form->onSuccess[]=callback($this,'AdminEditSubmitted');
				
		return $form;
	}
	
	public function AdminEditSubmitted(Form $form) {
		$i = $form->getValues();
		$id=$this->getParameter('id');
		
		if (!empty($i->newPswd) || !empty($i->ctrlPswd)) {
			if($this->checkPassword($i->newPswd, $i->ctrlPswd))
				$this->pristupRepository->updatePassword($id,md5($i->newPswd));
		}

		switch ($this->who($id)) {
			case "zamestnanec":
				$this->zamestnanecRepository->aktualizujInformace($id,$form);
				$this->zamestnanecRepository->updateUvazku($id,$i->uvazek);
				$this->zamestnanecRepository->updateOddeleni($id,$i->odd);
				$this->pristupRepository->updateRole($id,$i->rules);
				$this->flashMessage('Změny byly uloženy.');
				$this->redirect('edit:AdminEditZam',$id);
				break;
			case "zakaznik":
				$this->zadavatelRepository->aktualizujInformace($id,$form);
				$this->pristupRepository->updateRole($id,$i->rules);
				$this->flashMessage('Změny byly uloženy.');
				$this->redirect('edit:AdminEditZad',$id);
				break;
			default :
				$this->oddeleniRepository->aktualizujInformace($id,$form);
				$this->flashMessage('Změny byly uloženy.');
				$this->redirect('edit:AdminEditOdd',$id);
				break;
		}
			
		
		
	}
	/** Pomocne funkce **/
	public function checkPassword($newPswd,$ctrlPswd) {
		if ($newPswd == $ctrlPswd) {
			$this->flashMessage('Heslo bylo změněno.','success');
			return TRUE;
		} else {
			$this->flashMessage('Kontrolní heslo nesouhlasí.','error');
			return FALSE;
		}
	}
	
	public function renderDefault() {
		
	}
}
?>
