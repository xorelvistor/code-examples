<?php

namespace IIS;

use Nette,
	Nette\Security,
	Nette\Utils\Strings,
	Nette\Security\User;

/**
 * Users authenticator.
 */
class Authenticator extends Nette\Object implements Security\IAuthenticator
{
	/** @var UserRepository */
	private $pristup;



	public function __construct(pristupRepository $pristup)
	{
		$this->pristup = $pristup;
	}



	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($login, $heslo) = $credentials;
		$row = $this->pristup->findByName($login);

		if (!$row) {
			throw new Security\AuthenticationException('Neplatný login.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->heslo !== $heslo) {
			throw new Security\AuthenticationException('Chybné heslo.', self::INVALID_CREDENTIAL);
		}

		unset($row->heslo);
		return new Security\Identity($row->role, NULL, $row->toArray());
	}



	/**
	 * @param  int $id
	 * @param  string $password
	 */
/*
	public function setPassword($id, $password)
	{
		$this->users->findBy(array('id' => $id))->update(array(
			'password' => $this->calculateHash($password),
		));
	}
*/


	/**
	 * Computes salted password hash.
	 * @param string
	 * @return string
	 */
	/*
	public static function calculateHash($password, $salt = NULL)
	{
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		return crypt($password, $salt ?: '$2a$07$' . Strings::random(22));
	}
*/
}
