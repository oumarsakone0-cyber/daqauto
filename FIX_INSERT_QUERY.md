# 🔧 Fix: Erreur "Column count doesn't match value count"

## Le problème

Ta requête SQL INSERT a un nombre différent de:
- **Colonnes** (dans la partie `INSERT INTO ... (colonnes)`)
- **Valeurs** (les `?` dans la partie `VALUES (?, ?, ...)`)
- **Paramètres** (dans le tableau `$params`)

## Solution: Compter les éléments

### Nombre total attendu: **122 éléments**

- 81 colonnes existantes (trucks/trailers)
- 41 nouvelles colonnes CAR
- **TOTAL = 122**

Tu dois avoir:
- ✅ **122 colonnes** dans `INSERT INTO adjame_products (...)`
- ✅ **122 points d'interrogation** `?` dans `VALUES (?, ?, ...)`
- ✅ **122 éléments** dans ton tableau `$params`

## Comment vérifier dans ton code

### Étape 1: Ouvre products.php ligne ~4211

Cherche cette ligne:
```php
$sql = "INSERT INTO adjame_products (
```

### Étape 2: Compte les colonnes

Va jusqu'à `) VALUES (` et compte toutes les colonnes.

**Méthode rapide**: Copie toute la liste de colonnes dans un éditeur, puis:
- Compte les virgules `,` + 1
- OU utilise un compteur en ligne

### Étape 3: Compte les `?` dans VALUES

```php
VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
    // ... continue ...
)
```

**Méthode rapide**:
- Cherche/remplace tous les `?` par rien
- L'éditeur te dira combien il y en a

### Étape 4: Compte les éléments dans $params

```php
$params = [
    $name,
    $slug,
    $sku,
    // ...
    // Doit avoir 122 éléments au total
];
```

## Fix rapide: Structure correcte

Voici la structure complète que tu DOIS avoir:

### Colonnes (122 au total)

```sql
INSERT INTO adjame_products (
    -- Base (15)
    name, slug, sku, description, category_id, subcategory_id,
    unit_price, wholesale_price, wholesale_min_qty, stock, status,
    icon, tags, other_description, video,

    -- Système (4)
    is_active, unit_type, boutique_id, user_id,

    -- Hiérarchie (2)
    subsubcategory_id, subsubsubcategory_id,

    -- Véhicules trucks (9)
    vehicle_condition, vehicle_make, vehicle_model, drive_type, vehicle_year,
    fuel_type, transmission_type, engine_brand, vehicle_mileage,

    -- Trailers (17)
    trailer_type, trailer_brand, trailer_use, trailer_size, trailer_axle,
    trailer_suspension, trailer_tire, trailer_king_pin, trailer_main_beam,
    trailer_max_payload, trailer_place_of_origin, trailer_material,
    trailer_function, trailer_landing_gear, trailer_color, trailer_axle_brand,
    trailer_condition,

    -- Techniques trucks (34)
    brake_system, cabin_type, country_of_origin, curb_weight, dimensions,
    power, car_availability, engine_emissions, fuel_tank_capacity, gvw,
    other_description, payload_capacity, production_date, size_type,
    suspension_type, tyre_size, wheelbase, engine_number, stock_number,
    disponibility, speed, gearbox_brand, gearbox_model, chassis_dimensions,
    frame_rear_suspension, suspension_front, suspension_rear, axle_brand,
    axle_front, axle_rear, axle_speed_ratio, air_filter, electrics, cab,

    -- CAR (41) ← NOUVEAUX CHAMPS
    car_make, car_model, car_year, car_condition, car_vin, car_mileage,
    car_battery_range, car_charge_time_full, car_quick_charge_time, car_battery_capacity,
    car_height, car_length, car_width, car_kerb_weight, car_wheelbase,
    car_fuel_type, car_transmission, car_engine_size, car_engine_cylinders, car_drivetrain,
    car_doors, car_seats, car_warranty_miles, car_warranty_years, car_insurance_group,
    car_top_speed, car_engine_power_bhp, car_engine_power_kw, car_engine_torque, car_acceleration,
    car_exterior_color, car_interior_color, car_interior_material,
    car_mpg_city, car_mpg_highway, car_mpg_combined, car_co2_emissions,
    car_body_type, car_trim_level, car_previous_owners, car_service_history,
    car_data_source, car_vin_decoded_at, car_api_provider

) VALUES (
    -- 15 base
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
    -- 4 système
    ?, ?, ?, ?,
    -- 2 hiérarchie
    ?, ?,
    -- 9 trucks
    ?, ?, ?, ?, ?, ?, ?, ?, ?,
    -- 17 trailers
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
    -- 34 techniques
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
    -- 41 CAR
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)
```

**Total des `?`**: 15 + 4 + 2 + 9 + 17 + 34 + 41 = **122** ✅

## Vérification avec un compteur

Copie tout le contenu entre `VALUES (` et `)` dans un compteur de caractères en ligne et compte les `?`.

## Si le problème persiste

Envoie-moi (copie-colle):
1. La ligne complète de `INSERT INTO adjame_products (`
2. Jusqu'à la fin du `) VALUES (...)`

Je pourrai alors identifier exactement où est le problème.

## Astuce de debug

Ajoute temporairement ce code avant ta requête:

```php
// Debug: compter les colonnes et les valeurs
$columnsCount = substr_count($sql, ',') + 1; // entre INSERT et VALUES
$valuesCount = substr_count($sql, '?');
$paramsCount = count($params);

error_log("Colonnes: $columnsCount, Values: $valuesCount, Params: $paramsCount");

if ($columnsCount !== $valuesCount || $valuesCount !== $paramsCount) {
    throw new Exception("Mismatch: Cols=$columnsCount, Vals=$valuesCount, Params=$paramsCount");
}
```

Cela te dira exactement le nombre de chaque élément et où est la différence.
