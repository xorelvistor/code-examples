<?php

namespace IIS;
use Nette;

/**
 * Tabulka resi
 */
class resiRepository extends Repository
{
	/**
	 * Prida resitele k zakazce
	 * @param type $value
	 */
	public function pridatResitele($zam,$zakazka) {

	  if(isset($zam)) {
		$this->getTable()->where(array('zakazka_id'=>$zakazka['id']))->delete();
		foreach ($zam as $id_zam) {
		   $this->getTable()->insert(array('zamestnanec_id'=>$id_zam,'zakazka_id'=>$zakazka['id']));
		}
	  }
	}
	
	
	public function coResi($zam_id) {
		return $this->getTable()->where(array('zamestnanec_id' => $zam_id));
	}
}