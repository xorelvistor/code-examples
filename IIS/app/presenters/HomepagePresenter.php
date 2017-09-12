<?php
use Nette\Application\UI\Form;
use Nette\Utils\Strings;

/**
 * Description of HomepagePresenter
 *
 * @author oem
 */
class homepagePresenter extends BasePresenter {

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    /** @var Todo\TaskRepository */
	private $seznam;
	private $pocet;
	
	public function actionOut() {
		$this->getUser()->logout();
		$this->flashMessage("Byli jste odhlášeni. Pěkný den.");
		$this->redirect('sign:in');
	}
		
	public function handleDelete($id) {
		if ($this->identita->role == "administrator") {
			$this->zamestnanecRepository->delIt(array('osobniCislo' => $id));
			$this->flashMessage("Zaměstnec byl odstraněn z databáze.", 'success');
			$this->redirect('this');
		}
	}
	
	public function handleDeleteOdd($kod) {
		if ($this->identita->role == "administrator") {
			if ($kod !== "nez") {
			  $this->oddeleniRepository->delIt(array('kodOddeleni' => $kod));
			} else {
			  $this->flashMessage('Toto oddělení nemůže být odstraněno.','info');
			  $this->redirect('this');
			}
			$this->redirect('homepage:oddeleni');
		}
	}
	
	/** RENDERERY **/
	
	
	/**
	 * Prehled zamestnancu a jejich zarazeni, serazene dle prijmeni
	 */
	public function renderLide() {
	  $this->template->lide= $this->zamestnanecRepository->prehledZamestnancu();
	}
	
	/**
	 * Blizsi informace o zamestnanci a seznam zakazek, ktere resi
	 * @param type $id
	 */
	public function renderInfoZam($id) {
	  $this->template->info = $this->zamestnanecRepository->infoZamestnanec(array('osobniCislo' => $id));
	  $this->template->co = $this->resiRepository->coResi($id);
	  $this->template->pocet = $this->resiRepository->howMuch(array('zamestnanec_id' => $id));
	}
	/**
	 * Blizsi informace o oddeleni
	 * @param type $id
	 */
	public function renderInfoOdd($odd) {
	  $this->template->pocet = $this->zamestnanecRepository->howMuch(array('oddeleni_id' => $odd));
	  $this->template->info= $this->oddeleniRepository->findBy(array('kodOddeleni' => $odd));
	  $this->template->vedouci = $this->vedouciRepository->findBy(array('oddeleni_id' => $odd));
	}
	/**
	 * Blizsi informace o zakazce
	 * @param type $id
	 */
	public function renderInfoZak($id) {
	  $this->template->info = $this->zakazkaRepository->findBy(array('cisloZakazky'=> $id));
	  $this->template->kdo= $this->resiRepository->findBy(array('zakazka_id' => $id));
	  $this->template->kolik=$this->vydajRepository->prehledVydaju(array('zakazka_id'=>$id));
	  $this->template->suma = $this->vydajRepository->sumaVydaju(array('zakazka_id'=>$id));
	 // $this->template->zakazky=$this->
	}
	/**
	 * Blizsi informace o zadavateli
	 * @param type $id
	 */
	public function renderInfoZad($id) {
	  $this->template->info= $this->zadavatelRepository->findBy(array('ic' => $id));
	  $this->template->co = $this->zakazkaRepository->findBy(array('zadavatel_id' => $id));
	}
	/**
	 * Prehled oddeleni a informaci o nich
	 */
	public function renderOddeleni() {
	  $this->template->oddeleni= $this->oddeleniRepository->prehledOddeleni();
	}
	/**
	 * Prehled zakazek
	 */
	public function renderZakazky() {
	  $this->template->progres= $this->zakazkaRepository->prehledZakazek(array('stav'=>'resi se'));
	  $this->template->done= $this->zakazkaRepository->prehledZakazek(array('stav'=>'hotovo'));
	  $this->template->none= $this->zakazkaRepository->prehledZakazek(array('stav'=>'zadano'));
	  $this->template->storno= $this->zakazkaRepository->prehledZakazek(array('stav'=>'storno'));
	  
	}
	
	/*
	 * Prehled zadavatelu
	 */
	public function actionZadavatele() {
		$zadavatele = $this->zadavatelRepository->vycetZadavatelu();
		$seznam = array();
		$zakazek = array();
		
		foreach ($zadavatele as $key => $value) {
			$info = $this->zadavatelRepository->infoZadavatel(array('ic' => $key));
			$pocet = $this->zakazkaRepository->howMuch(array ('zadavatel_id' => $key));
			$zakazek[$key] = $pocet;
			array_push($seznam, $info);
		}
		$this->seznam = $seznam;
		$this->pocet = $zakazek;

		
	}

	public function renderZadavatele() {
		$this->template->zakaznici = $this->seznam;
		$this->template->pocet = $this->pocet;
	}

	public function renderDefault() {	
	  
    }
	
	
	
	
	/** VYTVORENI FORMULARE A VALIDACE **/
	
	
	/**
	 * Formular pro prirazeni zamestnancu zakazce a pro zmenu stavu zakazky
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentPriradForm() {
	  $zamestnanci =  $this->zamestnanecRepository->vycetZamestnancu();
	  
	 // dump($zamestnanci);
	  $cokoliv=$this->getParameter();
	  $vse=  $this->zakazkaRepository->findBy(array('cisloZakazky' => $cokoliv['id'])); 
	  foreach ($vse as $v) {
		$stav = $v->stav;
	  }
	  $resi = $this->resiRepository->findBy(array('zakazka_id' => $cokoliv['id']))->fetchPairs('zamestnanec_id','zakazka_id');
	
	  $mozne_stavy = array('zadano'=> 'Zadáno','resi se'=> 'Řeší se','hotovo'=> 'Hotová','storno'=> 'Zrušena');
	  
	  $form= new Form();
	  $form->addSelect('stav','Stav zakázky: ',$mozne_stavy)
			->setDefaultValue($stav);
	  
	  $form->addSubmit('zmenit','Změnit stav zakázky')
	  ->onClick[]=callback($this,'changeZakSubmitted');
		  
	  $form->addCheckboxList('option',NULL,$zamestnanci)
			->setDefaultValue(array_keys($resi));
	  $form->addSubmit('priradit','Přiřadit zakázce')
	  ->onClick[]=callback($this,'priradZakSubmitted');
	  
	  if($this->identita->role == "administrator") {
		  if ($stav == 'storno' || $stav == 'hotovo') {
			  $form['stav']->setDisabled();
			  $form['option']->setDisabled();
			  $form['zmenit']->setDisabled();
			  $form['priradit']->setDisabled();
		  }
	  } else {
		   $form['stav']->setDisabled();
	  }
		  
	  return $form;
	}
	
	/**
	 * Pridani resitelu k zakazce
	 * @param type $form
	 */
    public function priradZakSubmitted($button) {
		if ($this->identita->role == "administrator") {
		  $values= $button->form->getValues();
		  $zakazka=$this->getParameter();

		  $this->resiRepository->pridatResitele($values['option'],$zakazka);
		  $this->flashMessage('Řešitelé byli přiřazeni.', 'success');
		  $this->redirect('this');
		}
    }
	
	/**
	 * Aktualizace stavu zakazky
	 * @param type $form
	 */
	
    public function changeZakSubmitted($button) {
		if ($this->identita->role == "administrator") {
			$value= $button->form->getValues();
			$zakazka=  $this->getParameter();

			$this->zakazkaRepository->zmenStav($value,$zakazka['id']);
			$this->flashMessage('Stav zakázky byl změněn.', 'success');
			$this->redirect('this');
		}
    }
	
	/**
	 * Formular pro zmenu vedouciho
	 * @return \form
	 */
	public function createComponentZmenVedForm() {
	  $zam =$this->zamestnanecRepository->vycetZamestnancu();
	  $form= new form();
	  $form->addSelect('zam',NULL,$zam)
			->setPrompt('- - - Nový vedoucí - - -');
	  $form->addSubmit('change','Změnit vedoucího');
		  $form->onSuccess[]=callback($this,'changeVedouciSubmitted');
	
	  return $form;
	}
	/**
	 * Zmeni vedouciho oddeleni
	 * @param type $form
	 */
	public function changeVedouciSubmitted($form) {
		$value= $form->getValues();
		$odd = $this->getParameter();
		if ($value['zam'] != NULL && $odd['odd'] != NULL) {
			$neexistuje=$this->vedouciRepository->pridatVedouciho($value['zam'],$odd['odd']);
			if ($neexistuje) {
				$this->zamestnanecRepository->updateOddeleni($value['zam'],$odd['odd']);
				$this->flashMessage('Vedouci byl změněn.', 'success');
				$this->redirect('this');
			} else {
				$this->flashMessage('Tento zaměstnanec již vede jiné oddělení!', 'error');
			}
		}
	}
	
	
}