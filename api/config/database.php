<?php
    class Db{
        private $pdo;
        public function connect() {
            if ($this->pdo == null) {
                $this->pdo = new PDO("sqlite:".realpath("db/nn.sqlite3"));
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return $this->pdo;
        }
    }
?>