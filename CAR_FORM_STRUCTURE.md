# Structure du formulaire Car - AddProductModal.vue

## Vue d'ensemble

Le formulaire Car sera organisé en **3 étapes** après l'étape 0 (informations de base):

1. **Étape 0**: Informations de base + Choix de la méthode de saisie
2. **Étape 1**: Informations véhicule & Spécifications
3. **Étape 2**: Performance & Caractéristiques
4. **Étape 3**: Prix, Stock & Images

---

## ÉTAPE 0: Informations de base

### Section 1: Choix du type de produit
- Catégorie (sélection)
- Sous-catégorie (sélection)

### Section 2: **NOUVEAU - Choix de la méthode de saisie (uniquement si catégorie = Car)**

```
┌─────────────────────────────────────────────────┐
│ How would you like to enter the vehicle data?  │
│                                                 │
│  ○ Retrieve data via VIN (Automatic)          │
│     → Enter VIN and auto-fill fields          │
│                                                 │
│  ○ Enter data manually                         │
│     → Fill all fields manually                 │
└─────────────────────────────────────────────────┘
```

### Section 3A: Si "VIN Automatic" sélectionné

```
┌─────────────────────────────────────────────────┐
│ Vehicle Identification Number (VIN)            │
│ ┌─────────────────────────────────────────┐   │
│ │ Enter 17-character VIN  [🔍 Decode]     │   │
│ └─────────────────────────────────────────┘   │
│                                                 │
│ [Status: Decoding VIN... / Success / Error]    │
└─────────────────────────────────────────────────┘
```

### Section 3B: Champs communs (tous modes)
- Nom du produit
- Description
- Tags
- Vidéo (URL YouTube)

---

## ÉTAPE 1: Informations Véhicule & Spécifications

### Section 1: Basic Information

**Si VIN décodé**: Champs pré-remplis et en lecture seule avec option "Edit"
**Si manuel**: Champs éditables

```
┌── Basic Vehicle Information ──────────────────┐
│                                                │
│ Make *          [Tesla          ]             │
│ Model *         [Model Y        ]             │
│ Year *          [2024           ]             │
│ Condition *     [⌄ New          ]             │
│ VIN             [XXXXXXXXXXXXX  ]             │
│ Mileage         [12,500 miles   ]             │
│ Body Type       [⌄ SUV          ]             │
│ Trim Level      [Long Range AWD ]             │
└────────────────────────────────────────────────┘
```

### Section 2: Dimensions

```
┌── Dimensions ──────────────────────────────────┐
│                                                │
│ Height          [1626 mm        ]             │
│ Length          [4634 mm        ]             │
│ Width           [2063 mm        ]             │
│ Wheelbase       [2767 mm        ]             │
│ Kerb Weight     [2098 kg        ]             │
└────────────────────────────────────────────────┘
```

### Section 3: Engine & Drivetrain

```
┌── Engine & Drivetrain ─────────────────────────┐
│                                                │
│ Fuel Type *     [⌄ Electric     ]             │
│ Transmission *  [⌄ Automatic    ]             │
│ Engine Size     [2.0 L          ]             │
│ Cylinders       [4              ]             │
│ Drivetrain      [⌄ AWD          ]             │
└────────────────────────────────────────────────┘
```

### Section 4: Battery & Electric (si Fuel Type = Electric)

```
┌── Battery & Electric ──────────────────────────┐
│                                                │
│ Range           [389 miles      ]             │
│ Battery Capacity[75 kWh         ]             │
│ Charge Time     [11h 21m (0-100%)]           │
│ Quick Charge    [1h 17m (0-80%) ]             │
└────────────────────────────────────────────────┘
```

---

## ÉTAPE 2: Performance & Caractéristiques

### Section 1: Performance

```
┌── Performance ─────────────────────────────────┐
│                                                │
│ Top Speed       [111 mph        ]             │
│ Engine Power    [282 bhp        ]             │
│                 [210 kW         ]             │
│ Torque          [402 lbs/ft     ]             │
│ 0-60 mph        [4.8 seconds    ]             │
└────────────────────────────────────────────────┘
```

### Section 2: Efficiency

```
┌── Fuel Efficiency ─────────────────────────────┐
│                                                │
│ MPG City        [28 mpg         ]             │
│ MPG Highway     [35 mpg         ]             │
│ MPG Combined    [31 mpg         ]             │
│ CO2 Emissions   [120 g/km       ]             │
└────────────────────────────────────────────────┘
```

### Section 3: Interior & Exterior

```
┌── Colors & Interior ───────────────────────────┐
│                                                │
│ Exterior Color  [Pearl White    ] [⚫]        │
│ Interior Color  [Black          ] [⚫]        │
│ Interior Mat.   [Premium Leather]             │
└────────────────────────────────────────────────┘
```

### Section 4: General Information

```
┌── General Information ─────────────────────────┐
│                                                │
│ Doors           [5              ]             │
│ Seats           [5              ]             │
│ Warranty (years)[3 years        ]             │
│ Warranty (miles)[60,000 miles   ]             │
│ Insurance Group [29E            ]             │
│ Previous Owners [0              ]             │
└────────────────────────────────────────────────┘
```

### Section 5: Service History (Optional)

```
┌── Service History ─────────────────────────────┐
│                                                │
│ ┌────────────────────────────────────────────┐│
│ │ Full service history available.           ││
│ │ Last service: January 2024                ││
│ │ All maintenance up to date.               ││
│ └────────────────────────────────────────────┘│
└────────────────────────────────────────────────┘
```

---

## ÉTAPE 3: Prix, Stock & Images

### Section 1: Pricing & Stock

```
┌── Pricing & Stock ─────────────────────────────┐
│                                                │
│ Unit Price *    [$ 52,990       ]             │
│ Wholesale Price [$ 48,000       ]             │
│ Min Qty         [1              ]             │
│ Stock Quantity *[5              ]             │
│ Availability    [⌄ Available    ]             │
└────────────────────────────────────────────────┘
```

### Section 2: Images

```
┌── Product Images ──────────────────────────────┐
│                                                │
│ [📷 Upload Images]  [📷 From URL]             │
│                                                │
│ ┌─────┐ ┌─────┐ ┌─────┐                      │
│ │Img1 │ │Img2 │ │ Img3│  + Add more          │
│ │ [×] │ │ [×] │ │ [×] │                      │
│ └─────┘ └─────┘ └─────┘                      │
└────────────────────────────────────────────────┘
```

---

## Validation des champs

### Champs obligatoires (*):
- **Étape 0**: Catégorie, Sous-catégorie, Nom
- **Étape 1**: Make, Model, Year, Condition, Fuel Type, Transmission
- **Étape 3**: Unit Price, Stock

### Champs optionnels:
- Tous les autres champs

### Validation conditionnelle:
- Si Fuel Type = "Electric" → Afficher section Battery
- Si Manual mode → Tous champs éditables
- Si VIN mode → Champs décodés en lecture seule (avec option Edit)

---

## Logique de navigation

```
Step 0 → Check if Car category selected
         ↓
      Yes → Show "Data entry method" choice
         ↓
   Choice = VIN?
         ↓
      Yes → Show VIN input field
            → Decode VIN via API
            → Pre-fill fields in Step 1
         ↓
      No  → Show regular form (all editable)
         ↓
Step 1 → Validate required fields
       → Next to Step 2
         ↓
Step 2 → Optional fields
       → Next to Step 3
         ↓
Step 3 → Price & Images
       → Submit
```

---

## API Data Flow

### VIN Decode Flow:
```
User enters VIN
    ↓
Frontend: Call GET /api/products/decode-vin?vin=XXXXX
    ↓
Backend: Query NHTSA API
    ↓
Backend: Map data to car_* fields
    ↓
Backend: Return JSON with car data
    ↓
Frontend: Pre-fill form fields
    ↓
User: Review, edit if needed, continue
```

### Product Creation Flow:
```
User fills form across 3 steps
    ↓
Frontend: Collect all data
    ↓
Frontend: POST /api/products with car_* fields
    ↓
Backend: Detect isCar = true
    ↓
Backend: Extract all car_* fields from input
    ↓
Backend: INSERT with 122 parameters (81 existing + 41 car fields)
    ↓
Backend: Return created product
    ↓
Frontend: Show success, close modal
```

---

## Champs à retirer / ignorer

Les champs suivants de ta liste initiale ne seront PAS inclus car redondants ou déjà couverts:

- ~~MPG~~ → Remplacé par MPG City/Highway/Combined
- ~~Engine~~ → Remplacé par Engine Size + Cylinders
- ~~Mileage~~ → Déjà inclus comme car_mileage
- ~~Transmission~~ → Déjà inclus
- ~~Fuel type~~ → Déjà inclus
- ~~Drivetrain~~ → Déjà inclus

Tous les autres champs de ta liste sont inclus et organisés dans les 3 étapes ci-dessus.

---

## Notes techniques

1. **Détection de la catégorie Car**:
   ```javascript
   const isCarCategory = computed(() => {
     if (!selectedCategory.value?.id) return false
     const catName = selectedCategory.value.name.toLowerCase()
     return catName.includes('car') || catName.includes('voiture') ||
            catName.includes('auto') || catName.includes('vehicle')
   })
   ```

2. **État de saisie de données**:
   ```javascript
   const carDataEntryMode = ref('manual') // 'manual' | 'vin'
   const vinDecoding = ref(false)
   const vinDecoded = ref(false)
   const vinError = ref('')
   ```

3. **Fonction de décodage VIN**:
   ```javascript
   const decodeVIN = async (vin) => {
     vinDecoding.value = true
     vinError.value = ''
     try {
       const response = await api.get(`/products/decode-vin?vin=${vin}`)
       // Pre-fill form with response.data
       vinDecoded.value = true
     } catch (error) {
       vinError.value = error.message
     } finally {
       vinDecoding.value = false
     }
   }
   ```

4. **Steps dynamiques**:
   ```javascript
   const steps = computed(() => {
     if (isCarCategory.value) {
       return [
         { title: 'Basic Info', subtitle: 'Category & Method' },
         { title: 'Vehicle Info', subtitle: 'Specs & Dimensions' },
         { title: 'Performance', subtitle: 'Power & Efficiency' },
         { title: 'Finalize', subtitle: 'Price & Images' }
       ]
     }
     // ... existing truck/trailer steps
   })
   ```

---

Fin du document
