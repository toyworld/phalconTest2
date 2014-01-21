<?php

	use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
	use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
	
	class Users extends Phalcon\Mvc\Model
	{
		public function validation()
		{
			$this->validate(new EmailValidator(array(
				'field' => 'email'
			)));
			
			$this->validate(new UniquenessValidator(array(
				'field' => 'email',
				'massage' => 'Sorry, The email was registered another user'
			)));
			
			$this->validate(new UniquenessValidator(array(
				'field'	=> 'username',
				'massage' => 'Sorry, That username is already taken'
			)));
			
			if($this->validationHasFailed() == true){
				return false;
			}
		}
		
	}
?>