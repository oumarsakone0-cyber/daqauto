# 🚗 Guide: Activer le Décodeur VIN

## ⚠️ Problème actuel
Le bouton "Decode" VIN affiche l'erreur: **"VIN decode failed. Please enter details manually"**

**Raison**: L'endpoint API `/api/products?action=decode-vin` n'existe pas encore dans ton backend.

---

## ✅ Solution: Ajouter l'endpoint dans products.php

### Étape 1: Ouvrir le fichier products.php
Sur ton serveur, ouvre le fichier:
```
api_adjame/products.php
```

### Étape 2: Ajouter les fonctions à la fin de la classe

Trouve la fin de la classe (cherche la dernière accolade fermante `}` avant `?>`).

Juste AVANT cette dernière accolade, ajoute tout le code du fichier `VIN_DECODER_TO_ADD.php` (que je viens de créer).

**Exemple de structure**:
```php
class Products {
    // ... tes fonctions existantes ...

    // ✅ AJOUTE ICI les nouvelles fonctions (decodeVIN, mapFuelType, etc.)
    private function decodeVIN() {
        // ... code ...
    }

    private function mapFuelType($fuel) {
        // ... code ...
    }

    // ... autres helper functions ...

} // ← Fermeture de la classe
?>
```

### Étape 3: Ajouter la route dans le routeur

**Trouve où sont gérés les endpoints dans products.php**. Ça ressemble probablement à ça:

```php
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && $action === 'get_products') {
    // ...
} else if ($method === 'POST' && $action === 'create') {
    // ...
}
```

**Ajoute cette condition AVANT les autres**:

```php
// ✅ NOUVEAU: Route pour décoder le VIN
if ($method === 'GET' && $action === 'decode-vin') {
    $products = new Products($pdo);
    $products->decodeVIN();
    exit;
}
```

### Étape 4: Vérifier que cURL est activé

L'API NHTSA nécessite cURL. Vérifie que c'est activé sur ton serveur:

```php
// Ajoute temporairement ce code pour tester:
<?php
if (function_exists('curl_version')) {
    echo "cURL est activé ✅";
} else {
    echo "cURL est DÉSACTIVÉ ❌ - Contacte ton hébergeur";
}
?>
```

---

## 🧪 Test de l'endpoint

Une fois le code ajouté, teste directement l'endpoint dans ton navigateur:

```
https://ton-domaine.com/api_adjame/products.php?action=decode-vin&vin=1HGBH41JXMN109186
```

**Réponse attendue** (JSON):
```json
{
  "success": true,
  "message": "VIN décodé avec succès",
  "data": {
    "car_make": "Honda",
    "car_model": "Accord",
    "car_year": "1991",
    "car_body_type": "Sedan",
    "car_fuel_type": "Gasoline",
    "car_transmission": "Automatic",
    "car_vin": "1HGBH41JXMN109186",
    "suggested_name": "1991 Honda Accord"
  }
}
```

---

## 🎯 VINs de Test

Utilise ces VINs pour tester:

| VIN | Véhicule |
|-----|----------|
| `1HGBH41JXMN109186` | Honda Accord 1991 |
| `5YJSA1E26HF219886` | Tesla Model S 2017 |
| `WVWZZZ3CZBE041663` | Volkswagen Passat 2011 |
| `1G1YY22G965108782` | Chevrolet Corvette 2006 |

---

## ❓ En cas d'erreur

### Erreur: "VIN invalide"
- Le VIN doit avoir exactement **17 caractères**
- Pas d'espaces, pas de caractères spéciaux

### Erreur: "cURL error"
- cURL n'est pas activé sur ton serveur
- Contacte ton hébergeur pour l'activer

### Erreur: "Aucune donnée trouvée"
- Le VIN n'existe pas dans la base NHTSA
- Utilise un VIN de test valide

### Erreur 404 sur l'endpoint
- La route n'a pas été ajoutée correctement
- Vérifie que le code de la route est bien avant les autres routes

---

## 📝 Checklist

- [ ] Fichier `products.php` ouvert
- [ ] Fonctions `decodeVIN()`, `mapFuelType()`, etc. ajoutées à la classe
- [ ] Route `decode-vin` ajoutée au routeur
- [ ] cURL vérifié et activé
- [ ] Test de l'endpoint dans le navigateur
- [ ] Test avec un VIN valide depuis le frontend

---

## 🎉 Après activation

Une fois l'endpoint fonctionnel:

1. Va dans l'interface d'ajout de produit
2. Sélectionne une catégorie "Car"
3. Choisis "Retrieve data via VIN (Automatic)"
4. Entre un VIN valide (17 caractères)
5. Clique sur "Decode"
6. ✅ Les champs devraient se remplir automatiquement!

---

**Besoin d'aide?**
- Vérifie les logs d'erreur PHP sur ton serveur
- Teste l'endpoint directement dans le navigateur
- Utilise les VINs de test fournis
