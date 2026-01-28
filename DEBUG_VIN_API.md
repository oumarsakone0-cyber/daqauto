# 🔍 Debug: API NHTSA - Voir toutes les données disponibles

## Le problème
L'API retourne beaucoup de champs vides. Pour comprendre pourquoi, tu dois voir les données brutes (raw_data).

## Solution rapide

### Étape 1: Vérifier la réponse complète de l'API

Dans ton navigateur, teste directement l'API NHTSA:

```
https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/1HGBH41JXMN109186?format=json
```

Tu verras un gros JSON avec TOUS les champs disponibles pour ce VIN.

### Étape 2: Voir le raw_data dans ta réponse

Ta réponse actuelle devrait inclure `raw_data`. Si ce n'est pas le cas, vérifie que tu as bien cette ligne dans `decodeVIN()`:

```php
$this->sendResponse(200, [
    'success' => true,
    'message' => 'VIN décodé avec succès',
    'data' => $carData,
    'raw_data' => $result  // ← Cette ligne montre TOUTES les données de l'API
]);
```

### Étape 3: Identifier les champs disponibles

Regarde dans `raw_data` quels champs ont des valeurs. Par exemple pour le VIN `1HGBH41JXMN109186`:

**Champs probablement disponibles**:
- `Make` → Honda
- `ModelYear` → 1991
- `BodyClass` → Sedan/Coupe
- `EngineCylinders` → 4
- `DisplacementL` → 2.2
- `FuelTypePrimary` → Gasoline
- `Doors` → 4
- etc.

**Champs souvent vides pour les vieux modèles**:
- `Model` (peut être vide)
- `Trim` (rarement rempli)
- `TransmissionStyle` (pas toujours disponible)
- `DriveType` (pas toujours disponible)

## VINs de test avec plus de données

Essaie des VINs plus récents qui ont plus d'informations:

### Tesla Model S 2017 (beaucoup de données)
```
VIN: 5YJSA1E26HF219886
URL: https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/5YJSA1E26HF219886?format=json
```

**Données attendues**:
- Make: TESLA
- Model: Model S
- ModelYear: 2017
- BodyClass: Sedan/Saloon
- FuelTypePrimary: Electric
- Doors: 4
- Seats: 5
- etc.

### Ford F-150 2020 (très complet)
```
VIN: 1FTEW1EP0LKD12345
URL: https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/1FTEW1EP0LKD12345?format=json
```

### BMW X5 2019
```
VIN: 5UXCR6C0XK0Z12345
URL: https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/5UXCR6C0XK0Z12345?format=json
```

## Amélioration: Utiliser les champs alternatifs

L'API NHTSA a parfois plusieurs champs pour la même info. Modifie ton mapping comme ceci:

```php
// Si 'Model' est vide, essayer 'Series'
'car_model' => !empty(trim($result['Model']))
    ? trim($result['Model'])
    : (!empty(trim($result['Series'])) ? trim($result['Series']) : null),

// Si 'BodyClass' est vide, essayer 'BodyCabType'
'car_body_type' => !empty(trim($result['BodyClass']))
    ? trim($result['BodyClass'])
    : (!empty(trim($result['BodyCabType'])) ? trim($result['BodyCabType']) : null),
```

## Checklist de debug

- [ ] Tester l'URL directe de l'API NHTSA dans le navigateur
- [ ] Vérifier que `raw_data` est bien retourné dans la réponse
- [ ] Identifier quels champs ont des valeurs dans `raw_data`
- [ ] Tester avec un VIN plus récent (2015+)
- [ ] Mettre à jour le mapping avec les bons noms de champs
- [ ] Ajouter des fallbacks pour les champs alternatifs

## Note importante

⚠️ **L'API NHTSA est limitée pour les vieux véhicules**

Pour les véhicules avant 2000, beaucoup de données ne sont pas disponibles. C'est normal. Dans ce cas:
- Les utilisateurs devront remplir manuellement les champs manquants
- Ou choisir directement le mode "Manual" au lieu de "VIN"

## Solution de secours

Si l'API NHTSA ne retourne pas assez de données, tu peux:

1. **Afficher les champs décodés** (même partiels)
2. **Permettre l'édition** des champs pré-remplis
3. **Compléter manuellement** les champs vides

Le frontend gère déjà ça avec:
```javascript
:readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
```

Tu peux rendre les champs éditables même après le décodage VIN si nécessaire.
