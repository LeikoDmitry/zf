<?php

namespace Application\Controller;

use Application\Entity\User;
use Application\Service\Smtp;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;
use DateTime;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\View\Renderer\PhpRenderer;


/**
 * Class UserController
 *
 * @package Application\Controller
 */
class UserController extends AbstractActionController
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var AnnotationBuilder
     */
    private $builder;

    /**
     * @var Container
     */
    private $sessionContainer;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Smtp
     */
    private $smtp;

    /**
     * UserController constructor.
     *
     * @param  AuthenticationService  $authenticationService
     * @param  SessionManager  $session_manager
     * @param  Container  $sessionContainer
     * @param  EntityManager  $entityManager
     * @param  Smtp  $smtp
     * @param  PhpRenderer  $phpRenderer
     */
    public function __construct(
        AuthenticationService $authenticationService,
        SessionManager $session_manager,
        Container $sessionContainer,
        EntityManager $entityManager,
        Smtp $smtp,
        PhpRenderer $phpRenderer
    ) {
        $this->authenticationService = $authenticationService;
        $this->sessionManager = $session_manager;
        $this->builder = new AnnotationBuilder();
        $this->sessionContainer = $sessionContainer;
        $this->entityManager = $entityManager;
        $this->smtp = $smtp;
        $this->viewRenderer = $phpRenderer;
    }

    /**
     * @return Response|ViewModel
     * @throws \Exception
     */
    public function registerAction()
    {
        $form = $this->builder->createForm(User::class);
        if (! $this->getRequest()->isPost()) {
            if ($this->sessionContainer->errors) {
                $form->setMessages($this->sessionContainer->errors);
                unset($this->sessionContainer->errors);
            }
            return new ViewModel(compact('form'));
        }
        $form->setValidationGroup('email', 'password', 'confirm_password', 'csrf', 'username');
        $form->setData($this->getRequest()->getPost());
        if (! $form->isValid()) {
            $this->sessionContainer->errors = $form->getMessages();
            return $this->redirect()->toRoute('register');
        }
        $user = $this->entityManager->getRepository(User::class);
        $db_user = $user->findOneBy(['email' => $form->getData()['email']]);
        if ($db_user) {
            $form->get('email')->setMessages(['row_exist' => 'Email Already Exists. Try Another One']);
            $this->sessionContainer->errors = $form->getMessages();
            return $this->redirect()->toRoute('register');
        }
        try {
            $data = $form->getData();
            $new_user = new User();
            $new_user->setUsername($data['username']);
            $new_user->setEmail($data['email']);
            $new_user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
            $this->entityManager->persist($new_user);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            //log errors
        }
        return $this->redirect()->toRoute('home');
    }

    /**
     * Login user
     */
    public function loginAction()
    {
        $form = $this->builder->createForm(User::class);
        if (! $this->getRequest()->isPost()) {
            if ($this->sessionContainer->errors) {
                $form->setMessages($this->sessionContainer->errors);
                unset($this->sessionContainer->errors);
            }
            return new ViewModel(compact('form'));
        }
        $form->setValidationGroup('email', 'password', 'csrf');
        $form->setData($this->getRequest()->getPost());
        if (!$form->isValid()) {
            $this->sessionContainer->errors = $form->getMessages();
            return $this->redirect()->toRoute('login');
        }
        $adapter = $this->authenticationService->getAdapter();
        $adapter->setIdentity($form->getData()['email']);
        $adapter->setCredential($form->getData()['password']);
        $authResult = $this->authenticationService->authenticate();
        if (! $authResult->isValid()) {
            $form->get('email')->setMessages(['wrong' => 'Wrong Password Or Email']);
            $this->sessionContainer->errors = $form->getMessages();
            return $this->redirect()->toRoute('login');
        }
        $this->authenticationService->getStorage()->write($authResult->getIdentity());
        $user = $authResult->getIdentity();
        $user->setLoginDate(new DateTime());
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
           // log
        }
        return $this->redirect()->toRoute('home');
    }

    /**
     * @return Response
     */
    public function logoutAction()
    {
        if ($this->authenticationService->hasIdentity()) {
            $this->authenticationService->clearIdentity();
        }
        $this->sessionManager->forgetMe();
        $this->sessionManager->regenerateId();
        return $this->redirect()->toRoute('login');
    }

    /**
     * Request to Reset Password
     *
     * @return Response|ViewModel
     */
    public function resetAction()
    {
        $form = $this->builder->createForm(User::class);
        if (! $this->getRequest()->isPost()) {
            if ($this->sessionContainer->errors) {
                $form->setMessages($this->sessionContainer->errors);
                unset($this->sessionContainer->errors);
            }
            return new ViewModel(compact('form'));
        }
        $form->setData($this->getRequest()->getPost());
        $form->setValidationGroup('email', 'csrf');
        if (! $form->isValid()) {
            $this->sessionContainer->errors = $form->getMessages();
            return $this->redirect()->toRoute('reset');
        }
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $form->getData()['email']]);
        if (! $user) {
            $form->get('email')->setMessages(['row_not_exist' => 'Email Does Exist. Try Another One']);
            $this->sessionContainer->errors = $form->getMessages();
            return $this->redirect()->toRoute('reset');
        }
        $this->flashMessenger()->addInfoMessage('Check Your Email. We Send Instructions.');
        $username = $user->getUsername();
        $email = $user->getEmail();
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz');
        $bcrypt = new Bcrypt();
        $tokenHash = $bcrypt->create($token);
        $host = $_SERVER['HTTP_HOST'] ?? '0.0.0.0';
        $viewModel = new ViewModel(compact('username', 'email', 'tokenHash', 'host'));
        $viewModel->setTemplate('application/user/email');
        $emailTemplate = $this->getViewRenderer()->render($viewModel);
        $html = new MimePart($emailTemplate);
        $html->type = Mime::TYPE_HTML;
        $html->charset = 'utf-8';
        $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $body = new MimeMessage();
        $body->setParts([$html]);
        $message = new Message();
        $message->setSubject('Reset Password');
        $message->setTo($email);
        $message->addFrom('noreplay@hacker.com');
        $message->setBody($body);
        $this->smtp->getSmtpTransport()->send($message);
        return $this->redirect()->toRoute('reset');
    }

    /**
     * @param  User  $user
     * @param  string  $inputPassword
     *
     * @return bool
     */
    public static function verifyCredential(User $user, string $inputPassword)
    {
        return password_verify($inputPassword, $user->getPassword());
    }

    /**
     * @return PhpRenderer
     */
    private function getViewRenderer()
    {
        return $this->viewRenderer;
    }
}