<?php

class SiteController extends Controller
{
	public $layout='column1';

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
		//$this->render('signup',array('model'=>$model));
	}
	
	/**
	 * Displays the signup page
	 */
	/*
	public function actionSignup()
	{
		$model=new SignupForm;
		if(isset($_POST['SignupForm']))
		{
			echo 'test1';
			$model->attributes=$_POST['SignupForm'];
			if($model->validate())
			{
				echo 'test1';
				//$firstname='=?UTF-8?B?'.base64_encode($model->firstname).'?=';
				//$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->firstname,$model->lastname,$model->username,$headers);
				Yii::app()->user->setFlash('signup','Thank you for signing. Your account will be actived as soon as possible.');
				$this->refresh();
			}
		}
		//$this->render('contact',array('model'=>$model));
		$this->render('signup',array('model'=>$model));
	}
	*/
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionSignup()
	{		
		$model=new User;
		
		if(isset($_POST['User']))
		{			
			$model->attributes=$_POST['User'];
			if($model->save())
			{
				Yii::app()->user->setFlash('signup','Your account has been activated, please use the username and password to login.');
				$this->refresh();				
			}
		}
	    
		$this->render('signup',array('model'=>$model,));
		
		echo 'action';		
	}
	
	

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (!defined('CRYPT_BLOWFISH')||!CRYPT_BLOWFISH)
			throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}
