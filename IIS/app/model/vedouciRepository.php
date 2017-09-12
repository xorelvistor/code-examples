<?php

namespace IIS;
use Nette;

/**
 * Tabulka vedouci
 */
class vedouciRepository extends Repository
{
    /**
	 * Zmeni vedouciho oddeleni
	 * @param type $value
	 */
	public function pridatVedouciho($zam_id,$odd_id) {
		try {
		 $this->getTable()->where(array('oddeleni_id' => $odd_id))->delete();
		 $this->getTable()->insert(array('zamestnanec_id'=>$zam_id,'oddeleni_id'=>$odd_id));
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
	 * Vrati vedouciho oddeleni
	 * @param type $odd_id
	 * @return type
	 * 
	 */
	public function kdoTuVeli($odd_id) {
		return $this->getTable()
				->select('zamestnanec_id')
				->where(array('oddeleni_id' => $odd_id));
	}
}