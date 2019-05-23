<?php


namespace Application\Controller;


use Application\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;

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
     * UserController constructor.
     *
     * @param  AuthenticationService  $authenticationService
     * @param  SessionManager  $session_manager
     */
    public function __construct(AuthenticationService $authenticationService, SessionManager $session_manager)
    {
        $this->authenticationService = $authenticationService;
        $this->sessionManager = $session_manager;
        $this->builder = new AnnotationBuilder();
    }

    public function registerAction()
    {
        return new ViewModel();
    }

    /**
     * Login user
     */
    public function loginAction()
    {
        $form = $this->builder->createForm(User::class);
        $prg = $this->fileprg($form, 'login', true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            return $prg;
        } elseif ($prg === false) {
            return new ViewModel(compact('form'));
        }
        $form->setValidationGroup('email', 'password', 'csrf');
        $form->setData($prg);
        if (! $form->isValid()) {
            return $this->redirect()->toRoute('login');
        }
        $adapter = $this->authenticationService->getAdapter();
        $adapter->setIdentity($form->getData()['email']);
        $adapter->setCredential($form->getData()['password']);
        $authResult = $this->authenticationService->authenticate();
        if (! $authResult->isValid()) {
            $form->get('email')->setMessages(['wrong' => 'Wrong Password']);
            return $this->redirect()->toRoute('login');
        }
        $this->authenticationService->getStorage()->write([
            'id' => $authResult->getIdentity()->getId(),
            'name' => $authResult->getIdentity()->getUsername(),
            'email' => $authResult->getIdentity()->getEmail()
        ]);
        return $this->redirect()->toRoute('home');
    }

    /**
     * @return Response
     */
    public function logout()
    {
        if ($this->authenticationService->hasIdentity()) {
            $this->authenticationService->clearIdentity();
        }
        $this->sessionManager->forgetMe();
        $this->sessionManager->regenerateId();
        return $this->redirect()->toRoute('auth');
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