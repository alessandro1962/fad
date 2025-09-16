# Debug Admin Authentication - Campus Digitale Forma

## 🐛 Problema Iniziale
L'admin entrava ma veniva trattato come utente normale invece che come admin. Il frontend non riconosceva il ruolo admin e reindirizzava alla dashboard utente normale.

## 🔍 Analisi del Problema

### 1. Campo `is_admin` Mancante nel Modello User
- **Problema**: Il campo `is_admin` non era presente nell'array `$fillable` del modello User
- **Problema**: Il campo `is_admin` non era presente nell'array `$casts` (dovrebbe essere boolean)
- **Problema**: Mancava un metodo helper per verificare se l'utente è admin

### 2. Middleware di Autorizzazione Admin Mancante
- **Problema**: Non esisteva un middleware per verificare se l'utente è admin
- **Problema**: Le rotte admin non erano protette con il middleware admin

### 3. Frontend Non Gestiva il Campo `is_admin`
- **Problema**: Il frontend non aveva un computed `isAdmin` nello store di autenticazione
- **Problema**: Il redirect dopo il login era hardcoded a `/dashboard` invece di controllare il ruolo

### 4. Errore Fatale nel AnalyticsController
- **Problema**: Metodo `getAverageOrderValue()` duplicato (riga 258 e 358)
- **Errore**: `Cannot redeclare App\Http\Controllers\Admin\AnalyticsController::getAverageOrderValue()`

## 🛠️ Soluzioni Implementate

### 1. Correzione Modello User
```php
// app/Models/User.php
protected $fillable = [
    // ... altri campi
    'is_admin',  // ✅ Aggiunto
];

protected function casts(): array
{
    return [
        // ... altri cast
        'is_admin' => 'boolean',  // ✅ Aggiunto
    ];
}

// ✅ Aggiunto metodo helper
public function isAdmin(): bool
{
    return $this->is_admin === true;
}

// ✅ Aggiunto scope
public function scopeAdmin($query)
{
    return $query->where('is_admin', true);
}
```

### 2. Creazione Middleware Admin
```php
// app/Http/Middleware/EnsureUserIsAdmin.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Accesso negato. Solo gli amministratori possono accedere a questa risorsa.',
            ], 403);
        }

        return $next($request);
    }
}
```

### 3. Registrazione Middleware
```php
// bootstrap/app.php
$middleware->alias([
    'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,  // ✅ Aggiunto
]);
```

### 4. Protezione Rotte Admin
```php
// routes/api.php
// Admin routes (protected)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {  // ✅ Aggiunto 'admin'
    // ... rotte admin
});
```

### 5. Correzione Frontend Store
```javascript
// resources/js/stores/auth.js
const isAdmin = computed(() => user.value?.is_admin === true)  // ✅ Aggiunto

return {
    user,
    token,
    isAuthenticated,
    isAdmin,  // ✅ Aggiunto
    setUser,
    setToken,
    logout,
    loadUser
}
```

### 6. Correzione Redirect Login
```javascript
// resources/js/views/auth/Login.vue
// Update auth store
authStore.setUser(user)
authStore.setToken(token)

// ✅ Redirect basato sul ruolo utente
if (user.is_admin) {
    router.push('/admin')
} else {
    router.push('/dashboard')
}
```

### 7. Correzione Errore AnalyticsController
```php
// app/Http/Controllers/Admin/AnalyticsController.php
// ✅ Rimosso metodo duplicato getAverageOrderValue() (righe 358-361)
```

## 🧪 Test di Verifica

### 1. Test Login Admin API
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "admin@campusdigitale.it", "password": "admin123"}'
```

**Risposta Attesa**:
```json
{
    "message": "Login effettuato con successo.",
    "data": {
        "user": {
            "id": 1,
            "email": "admin@campusdigitale.it",
            "is_admin": true,  // ✅ Campo presente
            // ... altri campi
        },
        "token": "..."
    }
}
```

### 2. Test Login Utente Normale
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "giulia.bianchi@example.com", "password": "giulia123"}'
```

**Risposta Attesa**:
```json
{
    "data": {
        "user": {
            "is_admin": false,  // ✅ Campo presente
            // ... altri campi
        }
    }
}
```

### 3. Test Endpoint Admin
```bash
# Con admin (dovrebbe funzionare)
curl -X GET http://127.0.0.1:8000/api/v1/admin/users \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Accept: application/json"

# Con utente normale (dovrebbe dare errore 403)
curl -X GET http://127.0.0.1:8000/api/v1/admin/users \
  -H "Authorization: Bearer USER_TOKEN" \
  -H "Accept: application/json"
```

### 4. Test Frontend
1. Vai su `http://127.0.0.1:8000/login`
2. Login con admin@campusdigitale.it / admin123
3. **Dovrebbe reindirizzare a `/admin`** (dashboard admin)
4. **Dovrebbe mostrare**: "Pannello Amministratore", statistiche globali, azioni rapide

## 🚨 Problemi Riscontrati Durante il Debug

### 1. Errore Fatal AnalyticsController
- **Sintomo**: `Cannot redeclare App\Http\Controllers\Admin\AnalyticsController::getAverageOrderValue()`
- **Causa**: Metodo duplicato nel controller
- **Soluzione**: Rimozione del metodo duplicato

### 2. Redirect HTML invece di JSON
- **Sintomo**: API restituiva HTML invece di JSON
- **Causa**: Mancava header `Accept: application/json`
- **Soluzione**: Aggiunta header nelle richieste API

### 3. Frontend Non Si Caricava
- **Sintomo**: Pagina bianca, JavaScript non si eseguiva
- **Causa**: Errori fatali nel backend impedivano il caricamento
- **Soluzione**: Correzione errori backend e ricompilazione frontend

## 📋 Checklist per Futuri Debug

### Backend
- [ ] Verificare che il campo `is_admin` sia presente nel modello User
- [ ] Verificare che il middleware admin sia registrato e applicato
- [ ] Verificare che le rotte admin siano protette
- [ ] Controllare errori fatali nei controller
- [ ] Testare API con header `Accept: application/json`

### Frontend
- [ ] Verificare che lo store auth abbia il computed `isAdmin`
- [ ] Verificare che il redirect dopo login controlli il ruolo
- [ ] Controllare console browser per errori JavaScript
- [ ] Verificare che il build sia aggiornato

### Database
- [ ] Verificare che la migrazione `is_admin` sia stata eseguita
- [ ] Verificare che il seeder crei l'admin con `is_admin: true`

## 🎯 Risultato Finale

✅ **Admin login funzionante**: L'admin viene riconosciuto correttamente
✅ **Redirect corretto**: Admin → Dashboard admin, Utente → Dashboard utente
✅ **Autorizzazione funzionante**: Solo admin può accedere alle rotte admin
✅ **Frontend funzionante**: Login e redirect funzionano correttamente

## 📝 Note Aggiuntive

- Il problema principale era la mancanza del campo `is_admin` nel modello User
- Il middleware admin è essenziale per proteggere le rotte
- Il frontend deve gestire correttamente il ruolo utente per il redirect
- Sempre testare con header `Accept: application/json` per le API
- In caso di errori fatali, controllare i log Laravel e i controller per metodi duplicati

---
**Data**: 16 Settembre 2025  
**Problema**: Admin non riconosciuto come admin  
**Soluzione**: Implementazione completa sistema ruoli admin  
**Status**: ✅ RISOLTO
