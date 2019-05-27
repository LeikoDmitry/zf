<?php


namespace Application\Controller;


use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;
use DateTime;


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
     * UserController constructor.
     *
     * @param  AuthenticationService  $authenticationService
     * @param  SessionManager  $session_manager
     * @param  Container  $sessionContainer
     * @param  EntityManager  $entityManager
     */
    public function __construct(
        AuthenticationService $authenticationService,
        SessionManager $session_manager,
        Container $sessionContainer,
        EntityManager $entityManager
    ) {
        $this->authenticationService = $authenticationService;
        $this->sessionManager = $session_manager;
        $this->builder = new AnnotationBuilder();
        $this->sessionContainer = $sessionContainer;
        $this->entityManager = $entityManager;
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
        $data = $form->getData();
        $new_user = new User();
        $new_user->setUsername($data['username']);
        $new_user->setEmail($data['email']);
        $new_user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        try {
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
     * @param  User  $user
     * @param  string  $inputPassword
     *
     * @return bool
     */
    public static function verifyCredential(User $user, string $inputPassword)
    {
        return password_verify($inputPassword, $user->getPassword());
    }
}