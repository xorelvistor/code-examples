<?php

namespace IIS;
use Nette;

/**
 * Tabulka vydaj
 */
class vydajRepository extends Repository
{
    public function prehledVydaju (array $by) {
	  return $this->getTable()->where($by)->select('*');
	}
	public function sumaVydaju(array $by) {
	  return$this->getTable()->where($by)->sum('castka');
	}
	
	public function pridejVydaj ($value,$id_zak) {
	  $this->getTable()->insert(array(
		 'cisloDokladu' => NULL,
		  'castka' => $value->castka,
		  'datum' => $value->vystaveno,
		  'popis' => $value->popis,
		  'zakazka_id' => $id_zak['zak_id'],
	  ));
	}
}