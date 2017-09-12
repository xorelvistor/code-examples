<?php

namespace IIS;
use Nette;

/**
 * Tabulka zakazka
 */
class zakazkaRepository extends Repository
{
  /**
   * Vlozi novou zakazku do databaze
   * @param type $value
   */
  public function pridejZakazku($value) {
	$this->getTable()->insert(array(
	'cisloZakazky' => NULL,
	'prijato' => $value->prijato,
	'termin' => $value->termin,
	'rozpocet' => $value->rozpocet,
	'zaloha' => $value->zaloha,
	'stav' => 'zadano',
	'zadavatel_id' => $value->zadal,
	'popis' => $value->popis,
	));
  }
  /**
   * Ziska seznam zakazek
   * @return type
   */
  public function prehledZakazek (array $stav) {
	return
	$this->getTable()
			->select('*')->where($stav)->order('termin');
  }
  /**
   * Zmeni stav urcite zakazky
   * @param type $stav
   * @param type $zak_id
   */
  public function zmenStav($stav,$zak_id) {
	if(isset($stav)) {
	  $this->getTable()->where(array('cisloZakazky' => $zak_id))
			  ->update(array('stav' =>$stav['stav']));
	}
	
	 
  }
}

