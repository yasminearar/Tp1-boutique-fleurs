<?php
namespace App\Controllers;

use App\Models\User;

/**
 * Contrôleur pour l'authentification
 */
class AuthController extends Controller {
    
    /**
     * Affiche le formulaire de connexion
     */
    public function login() {

        if ($this->isAuthenticated(false)) {
            $this->addFlashMessage('Vous êtes déjà connecté', 'info');
            $this->redirect('/');
            return;
        }
        
        $this->display('auth/login', [
            'pageTitle' => 'Connexion',
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Traite la connexion
     */
    public function authenticate() {
        if (!$this->isPost()) {
            $this->redirect('/login');
            return;
        }
        
        $username = $this->postParam('username', FILTER_SANITIZE_STRING);
        $password = $this->postParam('password');

        if (empty($username) || empty($password)) {
            $this->addFlashMessage('Veuillez remplir tous les champs', 'error');
            $this->redirect('/login');
            return;
        }

        $user = new User();
        $authenticatedUser = $user->authenticate($username, $password);
        
        if ($authenticatedUser) {
            session_regenerate_id(delete_old_session: true);

            $_SESSION['user'] = $authenticatedUser;
            $_SESSION['user_id'] = $authenticatedUser['id'];
            $_SESSION['user_name'] = $authenticatedUser['name'];
            $_SESSION['username'] = $authenticatedUser['username'];
            $_SESSION['privilege_id'] = $authenticatedUser['privilege_id'];
            $_SESSION['privilege'] = $authenticatedUser['privilege'];
            $_SESSION['fingerPrint'] = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
            $_SESSION['LAST_ACTIVITY'] = time();
            
            $this->addFlashMessage('Connexion réussie! Bienvenue ' . $authenticatedUser['name'], 'success');

            if (isset($_SESSION['requested_page'])) {
                $redirectTo = $_SESSION['requested_page'];
                unset($_SESSION['requested_page']);
                $this->redirect($redirectTo);
            } else {
                $this->redirect('/');
            }
        } else {
            $this->addFlashMessage('Nom d\'utilisateur ou mot de passe incorrect', 'error');
            $this->redirect('/login');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        \App\Providers\Auth::logout();

        $this->addFlashMessage('Vous avez été déconnecté avec succès', 'success');
        $this->redirect('/login');
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function register() {
        if ($this->isAuthenticated(false)) {
            $this->redirect('/');
            return;
        }
        
        $this->display('auth/register', [
            'pageTitle' => 'Inscription',
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Traite l'inscription
     */
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/register');
            return;
        }
        
        $name = $this->postParam('name', FILTER_SANITIZE_STRING);
        $username = $this->postParam('username', FILTER_SANITIZE_STRING);
        $email = $this->postParam('email', FILTER_SANITIZE_EMAIL);
        $password = $this->postParam('password');
        $passwordConfirm = $this->postParam('password_confirm');

        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Le nom est requis';
        }
        
        if (empty($username)) {
            $errors[] = 'Le nom d\'utilisateur est requis';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Un email valide est requis';
        }
        
        if (empty($password)) {
            $errors[] = 'Le mot de passe est requis';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        $user = new User();
        
        if ($user->usernameExists($username)) {
            $errors[] = 'Ce nom d\'utilisateur est déjà utilisé';
        }
        
        if ($user->emailExists($email)) {
            $errors[] = 'Cet email est déjà utilisé';
        }
        
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlashMessage($error, 'error');
            }
            $this->redirect('/register');
            return;
        }

        $userData = [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'privilege_id' => 2
        ];
        
        $userId = $user->createUser($userData);
        
        if ($userId) {
            $this->addFlashMessage('Inscription réussie! Vous pouvez maintenant vous connecter', 'success');
            $this->redirect('/login');
        } else {
            $this->addFlashMessage('Erreur lors de l\'inscription', 'error');
            $this->redirect('/register');
        }
    }
    
    /**
     * Vérifie si un utilisateur est authentifié
     * 
     * @param bool $redirect Rediriger vers la page de login si non authentifié
     * @return bool True si authentifié
     */
    protected function isAuthenticated(bool $redirect = false): bool {
        return \App\Providers\Auth::session($redirect);
    }
}
