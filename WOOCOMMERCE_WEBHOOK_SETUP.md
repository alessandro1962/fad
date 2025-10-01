# 🔗 Configurazione Webhook WooCommerce per Integrazione FAD

## 📋 Panoramica

Questo documento descrive come configurare l'integrazione automatica tra `sicurezzadigitale.shop` (WooCommerce) e la piattaforma FAD per:

- ✅ **Creazione automatica utenti** quando acquistano un corso
- ✅ **Generazione password automatica** 
- ✅ **Iscrizione automatica ai corsi** acquistati
- ✅ **Invio email di benvenuto** con credenziali
- ✅ **Gestione Full Vision** con auto-iscrizione a tutti i corsi

## 🚀 Configurazione WooCommerce

### 1. **Accedere all'Admin WooCommerce**
- Vai su `sicurezzadigitale.shop/wp-admin`
- Naviga a **WooCommerce → Settings → Advanced → Webhooks**

### 2. **Creare Nuovo Webhook**
Clicca su **"Add webhook"** e configura:

#### **General Settings:**
- **Name**: `FAD Order Integration`
- **Status**: `Active`
- **Topic**: `Order updated`
- **Secret**: `[GENERA_UNA_CHIAVE_SECRETA]` (es: `fad_webhook_2024_xyz123`)

#### **Delivery URL:**
```
https://fad.campusdigitale.online/api/v1/webhooks/woocommerce/order
```

#### **API Version**: `WP REST API Integration v3`

### 3. **Configurazione .env FAD**
Aggiungi al file `.env` della FAD:

```env
# WooCommerce Webhook Configuration
WOOCOMMERCE_WEBHOOK_SECRET=fad_webhook_2024_xyz123
```

## 🔧 Mapping Prodotti → Corsi

### **Verifica Mapping Esistente**
I corsi sono già mappati tramite il campo `woocommerce_id` nella tabella `courses`:

```sql
SELECT id, title, woocommerce_id FROM courses WHERE woocommerce_id IS NOT NULL;
```

### **Mapping Full Vision**
- **Corso Full Vision**: ID `11` nella FAD
- **WooCommerce Product ID**: Deve corrispondere al `woocommerce_id` del corso Full Vision

## 📊 Flusso di Integrazione

### **1. Acquisto su sicurezzadigitale.shop**
```
Utente acquista corso → WooCommerce processa pagamento → Webhook inviato
```

### **2. Webhook Ricevuto da FAD**
```
POST /api/v1/webhooks/woocommerce/order
{
  "id": 12345,
  "status": "completed",
  "billing": {
    "first_name": "Mario",
    "last_name": "Rossi", 
    "email": "mario@example.com"
  },
  "line_items": [
    {
      "product_id": 789,
      "quantity": 1
    }
  ]
}
```

### **3. Processamento FAD**
1. **Crea/Trova utente** con email da billing
2. **Genera password** automatica (12 caratteri)
3. **Crea record ordine** con `woocommerce_order_id`
4. **Iscrive ai corsi** acquistati
5. **Gestisce Full Vision** (auto-iscrizione a tutti i corsi)
6. **Invia email** di benvenuto con credenziali

## 🎯 Tipi di Email Inviate

### **Corso Singolo**
```
Oggetto: Sei stato iscritto al corso [Nome Corso]
Contenuto: Credenziali di accesso + link al corso
```

### **Full Vision**
```
Oggetto: 🎊 Congratulazioni! Ti è stato assegnato l'accesso Full Vision
Contenuto: Credenziali + lista di tutti i corsi disponibili
```

## 🔍 Debug e Monitoraggio

### **Log FAD**
I log sono salvati in `storage/logs/laravel.log`:

```bash
# Visualizza log webhook
tail -f storage/logs/laravel.log | grep "WooCommerce Order Webhook"
```

### **Test Webhook**
Puoi testare il webhook con curl:

```bash
curl -X POST https://fad.campusdigitale.online/api/v1/webhooks/woocommerce/order \
  -H "Content-Type: application/json" \
  -H "X-WC-Webhook-Signature: [SIGNATURE]" \
  -d '{
    "id": 99999,
    "status": "completed",
    "billing": {
      "first_name": "Test",
      "last_name": "User",
      "email": "test@example.com"
    },
    "line_items": [
      {
        "product_id": [WOOCOMMERCE_PRODUCT_ID],
        "quantity": 1
      }
    ]
  }'
```

## ⚠️ Sicurezza

### **Validazione Signature**
Il webhook valida la signature usando HMAC-SHA256:

```php
$signature = $request->header('X-WC-Webhook-Signature');
$expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));
```

### **Rate Limiting**
Considera l'implementazione di rate limiting per prevenire abusi.

## 🚨 Troubleshooting

### **Webhook Non Ricevuto**
1. Verifica URL del webhook in WooCommerce
2. Controlla che il webhook sia "Active"
3. Verifica che l'ordine sia in stato "completed"

### **Utente Non Creato**
1. Controlla log per errori di validazione
2. Verifica che l'email sia valida
3. Controlla che non esista già un utente con quella email

### **Corso Non Trovato**
1. Verifica mapping `woocommerce_id` nella tabella `courses`
2. Controlla che il prodotto WooCommerce abbia l'ID corretto

### **Email Non Inviata**
1. Verifica configurazione SMTP
2. Controlla log email in `storage/logs/laravel.log`
3. Verifica che l'utente abbia `email_verified_at` impostato

## 📈 Monitoraggio

### **Dashboard Admin**
- **Utenti**: Filtra per `source = 'woocommerce'`
- **Ordini**: Visualizza ordini con `woocommerce_order_id`
- **Iscrizioni**: Controlla iscrizioni con `source = 'purchase'`

### **Analytics**
```sql
-- Utenti creati da WooCommerce
SELECT COUNT(*) FROM users WHERE source = 'woocommerce';

-- Ordini processati
SELECT COUNT(*) FROM orders WHERE woocommerce_order_id IS NOT NULL;

-- Iscrizioni da acquisti
SELECT COUNT(*) FROM enrollments WHERE source = 'purchase';
```

## ✅ Checklist Implementazione

- [ ] Webhook configurato in WooCommerce
- [ ] `WOOCOMMERCE_WEBHOOK_SECRET` aggiunto al .env
- [ ] Mapping prodotti → corsi verificato
- [ ] Test webhook eseguito con successo
- [ ] Email di benvenuto testata
- [ ] Full Vision auto-iscrizione testata
- [ ] Log monitoring configurato

---

**🎉 Una volta completata la configurazione, ogni acquisto su sicurezzadigitale.shop creerà automaticamente l'utente e lo iscriverà ai corsi acquistati!**
