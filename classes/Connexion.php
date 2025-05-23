<?php
// classes/Connexion.php

class Connexion {
    public static function getPDO() {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=boutique_plantes;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
}

