<?php

/**
 * 
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SignupForm extends CFormModel
{
	public $first_name;
	public $last_name;
	public $username;
	public $password;
	public $email;
	//public $body;
	//public $verifyCode;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('first_name, last_name', 'required'),
			
			array('username, password, email', 'required'),
				
			array('email', 'email'),
			
			// email has to be a valid email address
			//array('email', 'email'),
			
			// verifyCode needs to be entered correctly
			//array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	/*
	public function attributeLabels()
	{
		return array(
			'verifyCode'=>'Verification Code',
		);
	}
	*/
	public function attributeLabels()
	{
		return array(
				'first_name' => 'First_name',
				'last_name' => 'Last_name',
				'username' => 'Username',
		);
	}
	
}