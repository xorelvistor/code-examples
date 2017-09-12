<?php

namespace IIS;
use Nette;

/**
 * Tabulka oddeleni
 */
class oddeleniRepository extends Repository
{
    /**
     * Vraci vycet oddeleni
     * @return Nette\Database\Table\Selection
     */
    public function vycetOddeleni()
    {
        return $this->getTable()
		->select('kodOddeleni, nazev')
		->order('kodOddeleni ASC')
		->fetchPairs('kodOddeleni','nazev');
    } 
	/**
	 * Vlozi nove oddeleni do databaze
	 * @param type $value
	 */
	public function pridejOddeleni($value) {
		try {
			$this->getTable()->insert(array(
			'kodOddeleni' => $value->kodOddeleni,
			'nazev' => $value->nazev,
			'zamereni' => $value->popis,
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
	
	/**
	 * Vytvori seznam oddeleni
	 * @return type
	 */
	public function prehledOddeleni () {
	  return 
	  $this->getTable()
			  ->select('*')
			  ->order('nazev ASC');
	}
	
	/**
	 * Aktualizuje informace o oddeleni
	 */
	public function aktualizujInformace ($id,$form) {
		$this->getTable()->where(array('osobniCislo' => $id))->update(array(
			'nazev' => $form->values->nazev,
			'zamereni' => $form->values->popis,
		));
	}
}