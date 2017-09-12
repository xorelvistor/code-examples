<?php
namespace IIS;
use Nette,
  PDOException;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of repository
 *
 * @author oem
 */
abstract class Repository extends Nette\Object
{
    /** @var Nette\Database\Connection */
    protected $connection;

    public function __construct(Nette\Database\Connection $db)
    {
        $this->connection = $db;
    }

    /**
     * Vrací objekt reprezentující databázovou tabulku.
     * @return Nette\Database\Table\Selection
     */
    protected function getTable()
    {
        // název tabulky odvodíme z názvu třídy
        preg_match('#(\w+)Repository$#', get_class($this), $m);
        return $this->connection->table(lcfirst($m[1]));
    }

    /**
     * Vraci vsechny radky z tabulky.
     * @return Nette\Database\Table\Selection
     */
    public function findAll()
    {
        return $this->getTable();
    }
    
    /**
     * Vraci radky podle filtru, napr. array('name' => 'John').
     * @return Nette\Database\Table\Selection
     */
    public function findBy(array $by)
    {
        return $this->getTable()
				->select('*')
				->where($by);
    }
	
	/**
     * Vraci pocet radku tabulky
     * @return Nette\Database\Table\Selection
     */
    public function howMuch(array $by)
    {
        return $this->getTable()->where($by)->count("*");
    }
	/**
	 * Zrusi polozku v tabulce
	 * @param array $by
	 */
	public function delIt(array $by) {
	  $this->getTable()->where($by)->delete();
	}
    
}
