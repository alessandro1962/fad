# Fix per Errore 500 nel Salvataggio Quiz

## Problema Identificato
Quando si tenta di salvare un quiz tramite l'interfaccia admin, si verifica un errore 500 Internal Server Error con il messaggio "Errore nel salvataggio della lezione".

## Causa del Problema
Nel file `app/Http/Controllers/Admin/LessonController.php`, nei metodi `createQuizForLesson` e `updateQuizForLesson`, il codice stava tentando di salvare un campo `time_limit` nelle `settings` del quiz, ma questo campo non esiste nel database.

La tabella `quizzes` ha il campo `time_limit_minutes`, non `time_limit`.

## Fix Applicato
Sostituito in entrambi i metodi:
```php
// PRIMA (ERRATO)
'settings' => [
    'time_limit' => $payload['time_limit'] ?? 0,
    // ...
],

// DOPO (CORRETTO)
'settings' => [
    'time_limit_minutes' => $payload['time_limit'] ?? 0,
    // ...
],
```

## File Modificati
- `app/Http/Controllers/Admin/LessonController.php` (righe 196 e 242)

## Test Eseguiti
- ✅ Creazione quiz tramite Tinker: funziona
- ✅ Sintassi PHP: nessun errore
- ⚠️ Test tramite interfaccia admin: da verificare

## Note
Il fix corregge la duplicazione dei dati nelle settings, mantenendo solo il campo corretto `time_limit_minutes` che corrisponde alla struttura del database.

## Prossimi Passi
1. Testare il salvataggio quiz tramite interfaccia admin
2. Verificare che non ci siano altri errori correlati
3. Controllare i log per eventuali errori aggiuntivi
