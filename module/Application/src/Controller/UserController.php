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
    private $session_manager;

    /**
     * @var AnnotationBuilder
     */
    private $form_builder;

    /**
     * UserController constructor.
     *
     * @param  AuthenticationService  $authenticationService
     * @param  SessionManager  $session_manager
     */
    public function __construct(AuthenticationService $authenticationService, SessionManager $session_manager)
    {
        $this->authenticationService = $authenticationService;
        $this->session_manager = $session_manager;
        $this->form_builder = new AnnotationBuilder();
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
        $form = $this->form_builder->createForm(User::class);
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $form->setData($data);
            $form->setValidationGroup(['email', 'password', 'csrf']);
            if ($form->isValid()) {
                $adapter = $this->authenticationService->getAdapter();
                $adapter->setIdentity($data['email']);
                $adapter->setCredential($data['password']);
                $authResult = $this->authenticationService->authenticate();
                if ($authResult->isValid()) {
                    return $this->redirect()->toRoute('home');
                }
            }
            return $this->redirect()->toRoute('auth');
        }
        return new ViewModel(compact('form'));
    }

    /**
     * @return Response
     */
    public function logout()
    {
        if ($this->authenticationService->hasIdentity()) {
            $this->authenticationService->clearIdentity();
        }
        $this->session_manager->forgetMe();
        $this->session_manager->regenerateId();
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