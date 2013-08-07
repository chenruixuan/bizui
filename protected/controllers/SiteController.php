<?php

class SiteController extends Controller
{
	public $pageTitle='';
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
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$model = new Posts;
		$model->order = 'bp_score DESC, bp_create_time DESC';

		$this->render('index', array(
				'model'=>$model,
			));
	}

	/**
	 * 最新排序
	 */
	public function actionNew()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$model = new Posts;
		$model->order = 'bp_create_time DESC';

		$this->render('index', array(
				'model'=>$model,
			));
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
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm('login');

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

	//注册
 	public function actionSignup()
	{   
        $model = new LoginForm('signup');
        // 开启Ajax验证
        if(isset($_POST['ajax']) && $_POST['ajax']==='signup-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (isset($_POST['LoginForm'])) {
            $model->attributes=$_POST['LoginForm'];
            if($model->validate()){
            	if($model->signup()){ 
				   $this->redirect(array('login'));
				}
            }
        }
        
        $this->pageTitle = t('site_signup');
        
        $this->render('signup', array('model'=>$model));
    }

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}


	//我的文章
	public function actionMyposts()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$model = new Posts;
		$model->bu_id = Yii::app()->user->id;

		$this->render('index', array(
				'model'=>$model,
			));
	}

	//我的文章
	public function actionLikeposts()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$model = new Posts;
		$model->bu_id = Yii::app()->user->id;

		$this->render('index', array(
				'model'=>$model,
			));
	}

	//发邮件
	public function actionSendEmail($address)
	{
		$encdata = Yii::app()->securityManager->encrypt($address);//加密
		$mailer = Yii::app()->phpMailer->_mailer;
        $mailer->Subject = '找回密码';
        $mailer->Body = 'Hi,</br>
我们的系统在'.date('Y-m-d H:i:m',time()).'收到一个请求，说你希望通过电子邮件重新设置你在 你丫闭嘴 的密码。你可以点击下面的链接开始重设密码：</br>
<a href="'.Yii::app()->homeUrl.$this->createUrl('site/reset', array('mail'=>$encdata)).'">'.Yii::app()->homeUrl.$this->createUrl('site/reset', array('mail'=>$encdata)).'</a></br>
如果这个请求不是由你发起的，那没问题，你不用担心，你可以安全地忽略这封邮件。</br>
如果你有任何疑问，可以回复这封邮件向我们提问。</br>
--你丫闭嘴';
        $mailer->AddAddress($address);
        $mailer->send();
	}
}