<?php

namespace IIS;

use Nette;



class pristupRepository extends Repository
{


	/**
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function findByName($login)
	{
		return $this->findBy(array('login' => $login))->fetch();
	}

	public function updatePassword ($id,$new_password) {
		$this->getTable()->where(array('login' => $id))->update(array(
			'heslo' => $new_password,
		));
	}
	
	public function listOfRoles () {
		return $this->getTable()->select('role')->order('role ASC')->fetchPairs('role', 'role');
	}
	
	public function updateRole ($id,$rules) {
		$this->getTable()->where(array('login' => $id))->update(array(
			'role' => $rules
		));
	}
	
	public function insertAccess ($id,$jmeno,$role) {
		try {
			$this->getTable()->insert(array('login' => $id, 'heslo' => md5($id.$jmeno),'role' => $role));
		} catch (\PDOException $e) {
			if ($e->getCode() == 23000) {
				return false;
			} else {
				throw $e;  
			}
		}
		return true;
	}
}
