<?php
namespace App\Utils;

/**
 * Classe utilitaire pour gérer les uploads de fichiers
 */
class FileUploader {
    /**
     * Dossier cible pour les uploads
     * @var string
     */
    private $targetDir;
    
    /**
     * Extensions autorisées
     * @var array
     */
    private $allowedExtensions;
    
    /**
     * Taille maximale du fichier (en octets)
     * @var int
     */
    private $maxFileSize;
    
    /**
     * Messages d'erreurs
     * @var array
     */
    private $errors = [];
    
    /**
     * Nom du fichier uploadé
     * @var string
     */
    private $uploadedFileName = '';
    
    /**
     * Constructeur
     * 
     * @param string $targetDir Dossier cible pour les uploads
     * @param array $allowedExtensions Extensions autorisées (par défaut images)
     * @param int $maxFileSize Taille maximale en octets (par défaut 2MB)
     */
    public function __construct(string $targetDir, array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'], int $maxFileSize = 2097152) {
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
            $this->errors[] = "Le dossier d'upload n'existe pas et n'a pas pu être créé.";
        }
        
        if (!is_writable($targetDir)) {
            $this->errors[] = "Le dossier d'upload n'est pas accessible en écriture.";
        }
        
        $this->targetDir = rtrim($targetDir, '/') . '/';
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);
        $this->maxFileSize = $maxFileSize;
    }
    
    /**
     * Upload un fichier
     * 
     * @param array $file Élément de $_FILES
     * @param string|null $newFileName Nouveau nom de fichier (null = garder le nom original)
     * @param bool $overwrite Écraser le fichier existant
     * @return bool Succès de l'upload
     */
    public function upload(array $file, ?string $newFileName = null, bool $overwrite = false): bool {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->errors[] = "Aucun fichier n'a été uploadé.";
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $this->errors[] = "Le fichier est trop volumineux. La taille maximale est de " . round($this->maxFileSize / 1048576, 2) . " MB.";
            return false;
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $this->allowedExtensions)) {
            $this->errors[] = "L'extension du fichier n'est pas autorisée. Extensions autorisées: " . implode(', ', $this->allowedExtensions);
            return false;
        }

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']) && !getimagesize($file['tmp_name'])) {
            $this->errors[] = "Le fichier n'est pas une image valide.";
            return false;
        }

        if ($newFileName === null) {
            $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
            $fileName = $baseName . '_' . uniqid() . '.' . $fileExtension;
        } else {
            $fileName = $newFileName . '.' . $fileExtension;
        }
        
        $targetFile = $this->targetDir . $fileName;

        if (file_exists($targetFile) && !$overwrite) {
            $this->errors[] = "Un fichier avec ce nom existe déjà.";
            return false;
        }

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $this->uploadedFileName = $fileName;
            return true;
        } else {
            $this->errors[] = "Une erreur s'est produite lors de l'upload du fichier.";
            return false;
        }
    }
    
    /**
     * Récupère les erreurs
     * 
     * @return array Erreurs
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Récupère le nom du fichier uploadé
     * 
     * @return string Nom du fichier
     */
    public function getUploadedFileName(): string {
        return $this->uploadedFileName;
    }
    
    /**
     * Traduit le code d'erreur d'upload en message
     * 
     * @param int $errorCode Code d'erreur
     * @return string Message d'erreur
     */
    private function getUploadErrorMessage(int $errorCode): string {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return "Le fichier dépasse la taille maximale autorisée par PHP.";
            case UPLOAD_ERR_FORM_SIZE:
                return "Le fichier dépasse la taille maximale autorisée par le formulaire.";
            case UPLOAD_ERR_PARTIAL:
                return "Le fichier n'a été que partiellement uploadé.";
            case UPLOAD_ERR_NO_FILE:
                return "Aucun fichier n'a été uploadé.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Le dossier temporaire est manquant.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Échec d'écriture du fichier sur le disque.";
            case UPLOAD_ERR_EXTENSION:
                return "L'upload a été arrêté par une extension PHP.";
            default:
                return "Erreur d'upload inconnue.";
        }
    }
}
