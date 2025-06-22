<?php
// classes/Connexion.php

class Connexion {
    public static function getPDO() {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=e2496039;charset=utf8", "e2496039", "mYCAKIuYBiykg9CwMp06");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
}

