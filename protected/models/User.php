<?php

class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'tbl_user':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $email
	 * @var string $profile
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, email, first_name, last_name', 'required'),
			array('username, password, email', 'length', 'max'=>128),
			array('profile', 'safe'),
			//array('first_name','last_name', 'required'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'posts' => array(self::HAS_MANY, 'Post', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'profile' => 'Profile',	
			'last_name' => 'Last_name',
			'first_name' => 'First_name',
		);
	}

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{
		//echo $password, $this->password;
		
		//return CPasswordHelper::verifyPassword($password,$this->password);
		return self::verifyPassword($password,$this->password);
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @return string hash
	 */
	public function hashPassword($password)
	{
		return CPasswordHelper::hashPassword($password);
	}
	
	/**
	 * Verify a password against a hash.
	 *
	 * @param string $password The password to verify. If password is empty or not a string, method will return false.
	 * @param string $hash The hash to verify the password against.
	 * @return bool True if the password matches the hash.
	 * @throws CException on bad password or hash parameters or if crypt() with Blowfish hash is not available.
	 */
	public static function verifyPassword($password, $hash)
	{
		//echo $password, $hash;
		self::checkBlowfish();
		if(!is_string($password) || $password==='')
			return false;
	
		/*
		if (!$password || !preg_match('{^\$2[axy]\$(\d\d)\$[\./0-9A-Za-z]{22}}',$hash,$matches) ||
		$matches[1]<4 || $matches[1]>31)
		{
			//echo $matches[1];
			echo 'false1';			
			return false;
		}
	    */
	
		/*
		$test=crypt($password,$hash);
		{
		   if(!is_string($test) || strlen($test)<32)
		   {
		         echo 'false2';
		         return false;
		   }
		}
		*/
	
		//return self::same($test, $hash);
		return self::same($password, $hash);
	}
	
	/**
	 * Check for sameness of two strings using an algorithm with timing
	 * independent of the string values if the subject strings are of equal length.
	 *
	 * The function can be useful to prevent timing attacks. For example, if $a and $b
	 * are both hash values from the same algorithm, then the timing of this function
	 * does not reveal whether or not there is a match.
	 *
	 * NOTE: timing is affected if $a and $b are different lengths or either is not a
	 * string. For the purpose of checking password hash this does not reveal information
	 * useful to an attacker.
	 *
	 * @see http://blog.astrumfutura.com/2010/10/nanosecond-scale-remote-timing-attacks-on-php-applications-time-to-take-them-seriously/
	 * @see http://codereview.stackexchange.com/questions/13512
	 * @see https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
	 *
	 * @param string $a First subject string to compare.
	 * @param string $b Second subject string to compare.
	 * @return bool true if the strings are the same, false if they are different or if
	 * either is not a string.
	 */
	public static function same($a,$b)
	{
		//echo $a, $b;
		if(!is_string($a) || !is_string($b))
			return false;
	
		$mb=function_exists('mb_strlen');
		$length=$mb ? mb_strlen($a,'8bit') : strlen($a);
		if($length!==($mb ? mb_strlen($b,'8bit') : strlen($b)))
			return false;
	
		$check=0;
		for($i=0;$i<$length;$i+=1)
			$check|=(ord($a[$i])^ord($b[$i]));
	
		return $check===0;
	}
	

	/**
	 * Check for availability of PHP crypt() with the Blowfish hash option.
	 * @throws CException if the runtime system does not have PHP crypt() or its Blowfish hash option.
	 */
	protected static function checkBlowfish()
	{
		if(!function_exists('crypt'))
			throw new CException(Yii::t('yii','{class} requires the PHP crypt() function. This system does not have it.',
					array('{class}'=>__CLASS__)));
	
		if(!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH)
			throw new CException(Yii::t('yii',
					'{class} requires the Blowfish option of the PHP crypt() function. This system does not have it.',
					array('{class}'=>__CLASS__)));
	}
	
	/**
	 * Generate a secure hash from a password and a random salt.
	 *
	 * Uses the
	 * PHP {@link http://php.net/manual/en/function.crypt.php crypt()} built-in function
	 * with the Blowfish hash option.
	 *
	 * @param string $password The password to be hashed.
	 * @param int $cost Cost parameter used by the Blowfish hash algorithm.
	 * The higher the value of cost,
	 * the longer it takes to generate the hash and to verify a password against it. Higher cost
	 * therefore slows down a brute-force attack. For best protection against brute for attacks,
	 * set it to the highest value that is tolerable on production servers. The time taken to
	 * compute the hash doubles for every increment by one of $cost. So, for example, if the
	 * hash takes 1 second to compute when $cost is 14 then then the compute time varies as
	 * 2^($cost - 14) seconds.
	 * @return string The password hash string, ASCII and not longer than 64 characters.
	 * @throws CException on bad password parameter or if crypt() with Blowfish hash is not available.
	 */
	
	/*
	public static function hashPassword($password,$cost=13)
	{
		self::checkBlowfish();
		$salt=self::generateSalt($cost);
		$hash=crypt($password,$salt);
	
		if(!is_string($hash) || (function_exists('mb_strlen') ? mb_strlen($hash, '8bit') : strlen($hash))<32)
			throw new CException(Yii::t('yii','Internal error while generating hash.'));
	
		return $hash;
	}
	*/
	
}
