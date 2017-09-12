<?php

use Nette\Application\UI,
 Nette\Utils\Strings;


/**
 * Sign in/out presenters.
 */

class signPresenter extends BasePresenter
{
	

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new UI\Form;
		$form->addText('login', 'Login:')
			->setRequired('Nebyl zadán login.');

		$form->addPassword('heslo', 'Heslo:')
			->setRequired('Nebylo zadáno heslo.');

		$form->addCheckbox('remember', 'Zapamatovat heslo');

		$form->addSubmit('send', 'Přihlásit');

		// call method signInFormSubmitted() on success
		$form->onSuccess[] = $this->signInFormSubmitted;
		return $form;
	}



	public function signInFormSubmitted($form)
	{
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('+ 14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('+ 20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->login, md5($values->heslo));
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			$this->getPresenter()->flashMessage($e->getMessage(),'error');
			return;
		}
		$navstevnik = $this->getUser()->getIdentity();
		switch ($navstevnik->getId()) {
			case ("zamestnanec") :
				$this->redirect('Homepage:zakazky');
				break;
			case ("zakaznik") :
				$this->redirect('homepage:infoZad',$navstevnik->login);
				break;
			default :
				$this->redirect('homepage:lide');
				break;
		}
	}

}
