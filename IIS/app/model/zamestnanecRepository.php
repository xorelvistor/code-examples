<?php

namespace IIS;
use Nette;

/**
 * Tabulka zamestnanec
 */
class zamestnanecRepository extends Repository
{
	/**
	 * Vytvori novy zaznam v tabulce zamestnancu
	 * @param type $value
	 */
	public function pridejZamestnance($value) {
	  try {
		$vlozene = $this->getTable()->insert(array(
		'osobniCislo' => NULL,
		'jmeno' => $value->jmeno,
		'prijmeni' => $value->prijmeni,
		'tituly' => $value->tituly,
		'rc' => $value->rc,
		'telefon' => $value->tel,
		'pracUvazek' => $value->uvazek,
		'oddeleni_id' => $value->odd,
		));
		return $vlozene->osobniCislo;
	  } catch (\PDOException $e) {
		
		  if ($e->getCode() == 23000) {
			return false;
		  } else {
			throw $e;  
		  }
	  }
	}
	/**
	 * Ziska seznam zamestancu
	 * @return type
	 */
	public function prehledZamestnancu () {
	  return 
	  $this->getTable()
			  ->select('osobniCislo,jmeno, prijmeni, oddeleni_id')
			  ->order('prijmeni ASC');
	}
	
	/**
	 * Vytvori vycet zamestancu
	 * @return type
	 */
	public function vycetZamestnancu () {
	  return 
	  $this->getTable()
		->select('osobniCislo,CONCAT(jmeno," ",prijmeni," (",oddeleni_id,")") AS name')
		->order('prijmeni ASC')
		->fetchPairs('osobniCislo','name');
	}
	
	/**
	 * Ziska informace o danem zamestnanci
	 * @param array $by
	 * @return type
	 */
    public function infoZamestnanec(array $by)
    {
        return $this->getTable()
				->select('*')
				->where($by);
    }
	/**
	 * Aktualizuje oddeleni daneho zamestnance
	 * 
	 * @return type
	 */
	public function updateOddeleni($zam_id,$odd_id) {
		$this->getTable()->where(array('osobniCislo' => $zam_id))
			  ->update(array('oddeleni_id' =>$odd_id));
	}
	
	public function updateUvazku($zam_id,$uvazek) {
		$this->getTable()->where(array('osobniCislo' => $zam_id))
			  ->update(array('pracUvazek' => $uvazek));
	}
	
	public function jmenoUzivatele($zam_id) {
		return $this->getTable()->where(array('osobniCislo' => $zam_id));
	}
	
public function aktualizujInformace ($id,$form) {
	$tabulka=$this->getTable()->where(array('osobniCislo' => $id));
	$tabulka->update(array(
		'prijmeni' => $form->values->prijmeni,
		'tituly' => $form->values->tituly,
		'telefon' => $form->values->tel,
	));	
}
	
}