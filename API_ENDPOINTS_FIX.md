# Fix API Endpoints - Campus Digitale Forma

## 🚨 PROBLEMA RISOLTO

**Errore**: `The POST method is not supported for route api/admin/courses. Supported methods: GET, HEAD.`

**Causa**: Le rotte admin sono state spostate nel gruppo `v1` ma il frontend stava ancora chiamando gli endpoint vecchi.

## ✅ SOLUZIONE

### 1. Rotte Backend (routes/api.php)
```php
// ✅ CORRETTO - Rotte admin nel gruppo v1
Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Admin routes (protected)
        Route::prefix('admin')->group(function () {
            Route::apiResource('courses', AdminCourseController::class);
            // ... altre rotte admin
        });
    });
});
```

### 2. Frontend - Endpoint Corretti
```javascript
// ✅ CORRETTO
const response = await api.get('/v1/admin/courses')
const response = await api.post('/v1/admin/courses', data)
const response = await api.put(`/v1/admin/courses/${id}`, data)
const response = await api.delete(`/v1/admin/courses/${id}`)

// ❌ SBAGLIATO - Non funziona più
const response = await fetch('/api/admin/courses', {...})
```

### 3. File Corretti
- ✅ `resources/js/views/admin/Courses.vue` - Aggiornato
- ✅ `resources/js/components/Admin/CourseModal.vue` - Aggiornato
- ✅ `resources/js/components/Admin/ModuleManagementModal.vue` - Già corretto
- ✅ `resources/js/components/Admin/LessonModal.vue` - Già corretto

## 🔧 CHECKLIST PER FUTURI SVILUPPI

### Prima di creare nuove funzionalità admin:
1. **Verificare che le rotte siano nel gruppo v1**
2. **Usare sempre l'istanza `api` invece di `fetch`**
3. **Testare gli endpoint con curl prima del frontend**
4. **Ricompilare il frontend dopo ogni modifica**

### Pattern corretto per API calls:
```javascript
// ✅ SEMPRE USARE QUESTO PATTERN
import api from '@/api'

// GET
const response = await api.get('/v1/admin/courses')

// POST
const response = await api.post('/v1/admin/courses', data)

// PUT
const response = await api.put(`/v1/admin/courses/${id}`, data)

// DELETE
const response = await api.delete(`/v1/admin/courses/${id}`)

// PATCH
const response = await api.patch(`/v1/admin/courses/${id}/toggle-status`)
```

## 🧪 TEST RAPIDO

```bash
# Test login
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "admin@campusdigitale.it", "password": "admin123"}' \
  | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

# Test creazione corso
curl -s -X POST http://127.0.0.1:8000/api/v1/admin/courses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title": "Test", "summary": "Test", "description": "Test", "level": "beginner", "duration_minutes": 60, "price_cents": 0, "currency": "EUR"}'
```

## 📝 NOTE IMPORTANTI

1. **Tutte le rotte admin sono ora in `/api/v1/admin/`**
2. **L'istanza `api` gestisce automaticamente CSRF e auth**
3. **Sempre ricompilare il frontend dopo modifiche API**
4. **Testare con curl prima di testare il frontend**

## 🎯 RISULTATO

- ✅ **Creazione corsi** funzionante
- ✅ **Modifica corsi** funzionante  
- ✅ **Eliminazione corsi** funzionante
- ✅ **Gestione moduli** funzionante
- ✅ **Gestione lezioni** funzionante
- ✅ **Sistema completo** operativo

---

**Data**: 16 Settembre 2025  
**Status**: ✅ RISOLTO  
**Lezione**: Sempre verificare che frontend e backend usino gli stessi endpoint!
