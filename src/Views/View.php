<?php
namespace App\Views;

/**
 * Classe pour gérer l'affichage des templates Twig
 */
class View {
    /**
     * @var \Twig\Environment L'instance de l'environnement Twig
     */
    private static $twig;    /**
     * Initialise l'environnement Twig
     */
    public static function init(): void {
        // Création du loader pour les templates Twig
        $loader = new \Twig\Loader\FilesystemLoader(ROOT_DIR . '/templates');

        // Options de configuration de Twig
        $options = [
            'cache' => ROOT_DIR . '/cache/twig', // Dossier de cache
            'auto_reload' => true,              // Recharger les templates si modifiés
            'debug' => true,                    // Mode debug pour dev
        ];

        // Création de l'environnement Twig
        self::$twig = new \Twig\Environment($loader, $options);

        self::$twig->addGlobal('BASE_URL', BASE_URL);
        self::$twig->addGlobal('ASSETS_URL', ASSETS_URL);

        self::$twig->addGlobal('user', $_SESSION['user'] ?? null);
        self::$twig->addGlobal('user_id', $_SESSION['user_id'] ?? null);
        self::$twig->addGlobal('is_authenticated', isset($_SESSION['user_id']));
        self::$twig->addGlobal('user_privilege', $_SESSION['privilege'] ?? null);
    }

    /**
     * Rendu d'un template Twig
     * 
     * @param string $template Le nom du template à rendre
     * @param array $data Les données à passer au template
     * @return string Le HTML rendu
     */
    public static function render(string $template, array $data = []): string {
        // Vérifier si Twig est initialisé
        if (!isset(self::$twig)) {
            self::init();
        }

        // Ajouter l'extension .twig si non spécifiée
        if (!str_contains($template, '.twig')) {
            $template .= '.twig';
        }

        // Rendu du template
        return self::$twig->render($template, $data);
    }

    /**
     * Affiche un template et termine l'exécution
     * 
     * @param string $template Le nom du template à rendre
     * @param array $data Les données à passer au template
     */
    public static function display(string $template, array $data = []): void {
        echo self::render($template, $data);
        exit;
    }
}
