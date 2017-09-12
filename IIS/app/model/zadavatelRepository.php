<?php

namespace IIS;
use Nette;
/**
 * Tabulka zadavatel
 */
class zadavatelRepository extends Repository{
    /**
     * Vraci vycet zadavatelu
     * @return Nette\Database\Table\Selection
     */
    public function vycetZadavatelu()
    {
        return $this->getTable()
		->select('ic, nazev')
		->order('nazev ASC')
		->fetchPairs('ic','nazev');
    }
	
	/**
	 * Vytvori noveho zadavatele v databazi
	 * @param type $value
	 */
	public function pridejZadavatele($value) {
	  try {
		$this->getTable()->insert(array(
	  'ic' => $value->ic,
	  'dic' => $value->dic,
	  'nazev' => $value->nazev,
	  'zkratka' => $value->zkratka,
	  'mesto' => $value->mesto,
	  'ulice' => $value->ulice,
	  'cp' => $value->cp,
	  'psc' => $value->psc,
	  'ucet' => $value->ucet,
	  'kontakt' => $value->kontakt,
	  ));
		
	  } catch (\PDOException $e) {
		
		  if ($e->getCode() == 23000) {
			  return false;
		  } else {
			throw $e;  
		  }
	  }
	  return true;
	}
	public function infoZadavatel(array $by) {
        return $this->getTable()
				->select('*')
				->where($by);
    }
	
	public function aktualizujInformace ($id,$form) {
		Nette\Diagnostics\Debugger::dump($form);
		echo "cokoliv";
		$values = $form->values;
		$this->getTable()->where(array('ic' => $id))->update(array(
			'nazev' => $values->nazev,
			'zkratka' => $values->zkratka,
			'mesto' => $values->mesto,
			'ulice' => $values->ulice,
			'cp' => $values->cp,
			'psc' => $values->psc,
			'ucet' => $values->ucet,
			'kontakt' => $values->kontakt,
		));	
	}
}

