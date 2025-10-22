<?php
include "partials/header.php";
session_start();

$nomeFile = "utenti.json";

// se il file non esiste o è vuoto crealo con array vuoto
if (!is_file($nomeFile) || filesize($nomeFile) == 0) {
    file_put_contents($nomeFile, json_encode([]));
}

// se non ci sono gia carica i file nella sessione
if (!isset($_SESSION["utenti"])) {
    $utentiDaFile = json_decode(file_get_contents($nomeFile), true);
    if (!is_array($utentiDaFile)) {
        $utentiDaFile = []; // protezione contro file corrotti
    }
    $_SESSION["utenti"] = $utentiDaFile;
}

?>

<p>Scegli cosa fare:</p>
<form action="" method="POST">
    <input type="hidden" name="action" value="menu">
    <select name="selezione" required>
        <option value="" disabled selected> Seleziona un'opzione </option>
        <option value="add">Aggiungi un utente</option>
        <option value="list">Lista utenti</option>
        <option value="delete">Elimina un utente</option>
        <option value="update">Aggiorna un utente</option>
    </select>
    <input type="submit" value="Esegui">
</form>

<?php

// menu
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "menu") {
    $selezione = $_POST["selezione"];

    switch ($selezione) {
        case "add":
            include "aggiungi.php";
            break;
        case "delete":
            include "rimuovi.php";
            break;
        case "update":
            include "modifica.php";
            break;
        case "list":
            if (empty($_SESSION["utenti"])) {
                echo "<p>Lista vuota</p>";
            } else {
                foreach ($_SESSION["utenti"] as $utente) {
                    echo "<p>{$utente['nome']} {$utente['cognome']} ({$utente['email']})</p>";
                }
            }
            break;
    }

    // add

} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "toadd") {
    
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $email = $_POST["email"];

    $nuovoUtente = [
        "nome" => $nome,
        "cognome" => $cognome,
        "email" => $email
    ];

    // Carica utenti dal file
    $utenti = json_decode(file_get_contents($nomeFile), true);
    if (!is_array($utenti)) {
        $utenti = [];
    }

    // Aggiungi il nuovo utente
    $utenti[] = $nuovoUtente;

    // Aggiorna la sessione
    $_SESSION["utenti"] = $utenti;

    // Salva tutto nel file JSON
    file_put_contents($nomeFile, json_encode($utenti, JSON_PRETTY_PRINT));

    echo "<p>Utente aggiunto con successo!</p>";
}
?>