<?php
/**
 * ==========================================
 * RÉFÉRENCE: Modifications à apporter dans products.php
 * pour supporter la catégorie CAR
 * ==========================================
 *
 * INSTRUCTIONS:
 * 1. Ajouter la détection de catégorie "Car"
 * 2. Ajouter les champs Car dans createProduct()
 * 3. Ajouter les champs Car dans updateProduct()
 * 4. Créer l'endpoint de décodage VIN
 */

// ========================================
// ÉTAPE 1: Détection de la catégorie Car
// ========================================
// À ajouter après la détection isTrailer dans createProduct()

$isCar = false;
if (!empty($input['category_id'])) {
    $catSql = "SELECT name FROM adjame_categories WHERE id = ?";
    $catResult = $this->db->query($catSql, [$input['category_id']]);
    if (!empty($catResult)) {
        $catName = strtolower($catResult[0]['name']);
        $isCar = strpos($catName, 'car') !== false ||
                 strpos($catName, 'voiture') !== false ||
                 strpos($catName, 'auto') !== false ||
                 strpos($catName, 'vehicle') !== false;
    }
}

// ========================================
// ÉTAPE 2: Récupération des champs Car
// ========================================
// À ajouter après les champs trailers

// Champs Car - Basic Info
$car_make = $input['car_make'] ?? null;
$car_model = $input['car_model'] ?? null;
$car_year = $input['car_year'] ?? null;
$car_condition = $input['car_condition'] ?? null;
$car_vin = $input['car_vin'] ?? null;
$car_mileage = $input['car_mileage'] ?? null;

// Champs Car - Battery & Electric
$car_battery_range = $input['car_battery_range'] ?? null;
$car_charge_time_full = $input['car_charge_time_full'] ?? null;
$car_quick_charge_time = $input['car_quick_charge_time'] ?? null;
$car_battery_capacity = $input['car_battery_capacity'] ?? null;

// Champs Car - Dimensions
$car_height = $input['car_height'] ?? null;
$car_length = $input['car_length'] ?? null;
$car_width = $input['car_width'] ?? null;
$car_kerb_weight = $input['car_kerb_weight'] ?? null;
$car_wheelbase = $input['car_wheelbase'] ?? null;

// Champs Car - Engine & Drivetrain
$car_fuel_type = $input['car_fuel_type'] ?? null;
$car_transmission = $input['car_transmission'] ?? null;
$car_engine_size = $input['car_engine_size'] ?? null;
$car_engine_cylinders = $input['car_engine_cylinders'] ?? null;
$car_drivetrain = $input['car_drivetrain'] ?? null;

// Champs Car - General
$car_doors = $input['car_doors'] ?? null;
$car_seats = $input['car_seats'] ?? null;
$car_warranty_miles = $input['car_warranty_miles'] ?? null;
$car_warranty_years = $input['car_warranty_years'] ?? null;
$car_insurance_group = $input['car_insurance_group'] ?? null;

// Champs Car - Performance
$car_top_speed = $input['car_top_speed'] ?? null;
$car_engine_power_bhp = $input['car_engine_power_bhp'] ?? null;
$car_engine_power_kw = $input['car_engine_power_kw'] ?? null;
$car_engine_torque = $input['car_engine_torque'] ?? null;
$car_acceleration = $input['car_acceleration'] ?? null;

// Champs Car - Colors & Interior
$car_exterior_color = $input['car_exterior_color'] ?? null;
$car_interior_color = $input['car_interior_color'] ?? null;
$car_interior_material = $input['car_interior_material'] ?? null;

// Champs Car - Efficiency
$car_mpg_city = $input['car_mpg_city'] ?? null;
$car_mpg_highway = $input['car_mpg_highway'] ?? null;
$car_mpg_combined = $input['car_mpg_combined'] ?? null;
$car_co2_emissions = $input['car_co2_emissions'] ?? null;

// Champs Car - Additional
$car_body_type = $input['car_body_type'] ?? null;
$car_trim_level = $input['car_trim_level'] ?? null;
$car_previous_owners = $input['car_previous_owners'] ?? null;
$car_service_history = $input['car_service_history'] ?? null;

// Champs Car - Data Source
$car_data_source = $input['car_data_source'] ?? 'manual';
$car_vin_decoded_at = $input['car_vin_decoded_at'] ?? null;
$car_api_provider = $input['car_api_provider'] ?? null;

// ========================================
// ÉTAPE 3: Génération automatique du nom pour Cars
// ========================================
// À ajouter après la génération du nom pour trailers

if ($isCar && $car_year && $car_make && $car_model && $car_trim_level) {
    $input['name'] = $car_year . ' ' . $car_make . ' ' . $car_model . ' ' . $car_trim_level;
} else if ($isCar && $car_year && $car_make && $car_model) {
    $input['name'] = $car_year . ' ' . $car_make . ' ' . $car_model;
}

// ========================================
// ÉTAPE 4: Mise à jour de la requête INSERT
// ========================================
// Modifier la requête $productSql pour inclure les champs Car

$productSql = "
    INSERT INTO adjame_products (
        name, slug, sku, description, category_id, subcategory_id,
        unit_price, wholesale_price, wholesale_min_qty, stock,
        status, icon, tags, description_plus, video,
        is_active, unit_type, boutique_id, created_by,
        sub_subcategory_id, sub_sub_subcategory_id,

        -- Champs Trucks
        vehicle_condition, vehicle_make, vehicle_model, drive_type, vehicle_year,
        fuel_type, transmission_type, engine_brand, vehicle_mileage,

        -- Champs Trailers
        trailer_type, trailer_brand, trailer_use, trailer_size, trailer_axle,
        trailer_suspension, trailer_tire, trailer_king_pin, trailer_main_beam,
        trailer_max_payload, trailer_place_of_origin, trailer_material,
        trailer_function, trailer_landing_gear, trailer_color, trailer_axle_brand,
        trailer_condition,

        -- Champs Trucks techniques
        brake_system, cabin_type, country_of_origin,
        curb_weight, dimensions, power, car_availability, engine_emissions,
        fuel_tank_capacity, gvw, other_description, payload_capacity,
        production_date, size_type, suspension_type, tyre_size, wheelbase,
        engine_number, stock_number, disponibility, speed, gearbox_brand,
        gearbox_model, chassis_dimensions, frame_rear_suspension,
        suspension_front, suspension_rear, axle_brand, axle_front,
        axle_rear, axle_speed_ratio, air_filter, electrics, cab,

        -- NOUVEAUX CHAMPS CAR
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
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        -- 41 nouveaux paramètres Car
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
";

// ========================================
// ÉTAPE 5: Mise à jour des paramètres
// ========================================

$productParams = [
    // Champs de base (15)
    $input['name'], $slug, $sku, $input['description'] ?? '',
    $input['category_id'], $input['subcategory_id'],
    $input['unit_price'], $input['wholesale_price'] ?? null, $input['wholesale_min_qty'] ?? null,
    $input['stock'], $status, $input['icon'] ?? '', $input['tags'] ?? '',
    $other_description, $video,

    // Champs système (4)
    $input['is_active'] ?? true, $input['unit_type'] ?? 'unité',
    $boutiqueId, $userId,

    // Hiérarchie catégorie (2)
    $subSubcategoryId, $subSubSubcategoryId,

    // Champs véhicule trucks (9)
    $vehicle_condition, $vehicle_make, $vehicle_model, $drive_type, $vehicle_year,
    $fuel_type, $transmission_type, $engine_brand, $vehicle_mileage,

    // Champs trailers (17)
    $trailer_type, $trailer_brand, $trailer_use, $trailer_size, $trailer_axle,
    $trailer_suspension, $trailer_tire, $trailer_king_pin, $trailer_main_beam,
    $trailer_max_payload, $trailer_place_of_origin, $trailer_material,
    $trailer_function, $trailer_landing_gear, $trailer_color, $trailer_axle_brand,
    $trailer_condition,

    // Champs techniques trucks (34)
    $brake_system, $cabin_type, $country_of_origin,
    $curb_weight, $dimensions, $input['power'] ?? null, $car_availability, $input['engine_emissions'] ?? null,
    $fuel_tank_capacity, $gvw, $other_description, $payload_capacity,
    $production_date, $size_type, $suspension_type, $tyre_size, $wheelbase,
    $engine_n, $stockNumber, $disponibility, $input['speed'] ?? null, $input['gearbox_brand'] ?? null,
    $input['gearbox_model'] ?? null, $input['chassis_dimensions'] ?? null,
    $input['frame_rear_suspension'] ?? null, $input['suspension_front'] ?? null,
    $input['suspension_rear'] ?? null, $input['axle_brand'] ?? null,
    $input['axle_front'] ?? null, $input['axle_rear'] ?? null,
    $input['axle_speed_ratio'] ?? null, $input['air_filter'] ?? null,
    $input['electrics'] ?? null, $input['cab'] ?? null,

    // NOUVEAUX CHAMPS CAR (41)
    $car_make, $car_model, $car_year, $car_condition, $car_vin, $car_mileage,
    $car_battery_range, $car_charge_time_full, $car_quick_charge_time, $car_battery_capacity,
    $car_height, $car_length, $car_width, $car_kerb_weight, $car_wheelbase,
    $car_fuel_type, $car_transmission, $car_engine_size, $car_engine_cylinders, $car_drivetrain,
    $car_doors, $car_seats, $car_warranty_miles, $car_warranty_years, $car_insurance_group,
    $car_top_speed, $car_engine_power_bhp, $car_engine_power_kw, $car_engine_torque, $car_acceleration,
    $car_exterior_color, $car_interior_color, $car_interior_material,
    $car_mpg_city, $car_mpg_highway, $car_mpg_combined, $car_co2_emissions,
    $car_body_type, $car_trim_level, $car_previous_owners, $car_service_history,
    $car_data_source, $car_vin_decoded_at, $car_api_provider
];

// Total: 81 + 41 = 122 paramètres

// ========================================
// ÉTAPE 6: Nouveau endpoint pour VIN Decoder
// ========================================

/**
 * Décoder un VIN via l'API NHTSA (gratuite)
 * Endpoint: GET /api/products/decode-vin?vin=XXXXX
 */
private function decodeVIN() {
    $vin = $_GET['vin'] ?? '';

    if (empty($vin) || strlen($vin) !== 17) {
        $this->sendResponse(400, ['error' => 'VIN invalide. Le VIN doit contenir 17 caractères.']);
        return;
    }

    try {
        // Utiliser l'API NHTSA (gratuite, pas de clé API requise)
        $url = "https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/{$vin}?format=json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Erreur lors de la récupération des données VIN');
        }

        $data = json_decode($response, true);

        if (empty($data['Results'])) {
            throw new Exception('Aucune donnée trouvée pour ce VIN');
        }

        $result = $data['Results'][0];

        // Mapper les données de l'API aux champs de notre base
        $carData = [
            'car_make' => $result['Make'] ?? null,
            'car_model' => $result['Model'] ?? null,
            'car_year' => $result['ModelYear'] ?? null,
            'car_body_type' => $result['BodyClass'] ?? null,
            'car_trim_level' => $result['Trim'] ?? null,
            'car_fuel_type' => $this->mapFuelType($result['FuelTypePrimary'] ?? ''),
            'car_transmission' => $this->mapTransmission($result['TransmissionStyle'] ?? ''),
            'car_engine_size' => $this->parseEngineSize($result['DisplacementL'] ?? ''),
            'car_engine_cylinders' => $result['EngineCylinders'] ?? null,
            'car_drivetrain' => $this->mapDrivetrain($result['DriveType'] ?? ''),
            'car_doors' => $result['Doors'] ?? null,
            'car_seats' => $result['Seats'] ?? null,
            'car_vin' => $vin,
            'car_data_source' => 'api',
            'car_vin_decoded_at' => date('Y-m-d H:i:s'),
            'car_api_provider' => 'NHTSA',

            // Données supplémentaires
            'vehicle_make' => $result['Make'] ?? null,
            'vehicle_model' => $result['Model'] ?? null,
            'vehicle_year' => $result['ModelYear'] ?? null,
        ];

        // Générer un nom automatique
        if ($carData['car_year'] && $carData['car_make'] && $carData['car_model']) {
            $carData['suggested_name'] = $carData['car_year'] . ' ' .
                                        $carData['car_make'] . ' ' .
                                        $carData['car_model'];
            if ($carData['car_trim_level']) {
                $carData['suggested_name'] .= ' ' . $carData['car_trim_level'];
            }
        }

        $this->sendResponse(200, [
            'success' => true,
            'message' => 'VIN décodé avec succès',
            'data' => $carData,
            'raw_data' => $result // Pour debug
        ]);

    } catch (Exception $e) {
        $this->sendResponse(500, [
            'error' => 'Erreur lors du décodage du VIN: ' . $e->getMessage()
        ]);
    }
}

// Fonctions helper pour mapper les données
private function mapFuelType($fuel) {
    $map = [
        'Gasoline' => 'Petrol',
        'Diesel' => 'Diesel',
        'Electric' => 'Electric',
        'Flex Fuel' => 'Hybrid',
        'Hydrogen' => 'Hydrogen'
    ];
    return $map[$fuel] ?? null;
}

private function mapTransmission($trans) {
    if (stripos($trans, 'Auto') !== false || stripos($trans, 'CVT') !== false) {
        return 'Automatic';
    } else if (stripos($trans, 'Manual') !== false) {
        return 'Manual';
    }
    return null;
}

private function mapDrivetrain($drive) {
    $map = [
        'FWD' => 'FWD',
        'RWD' => 'RWD',
        'AWD' => 'AWD',
        '4WD' => '4WD',
        'Four Wheel Drive' => '4WD',
        'All Wheel Drive' => 'AWD'
    ];
    return $map[$drive] ?? null;
}

private function parseEngineSize($displacement) {
    return $displacement ? floatval($displacement) : null;
}

// ========================================
// ÉTAPE 7: Ajouter la route dans le routeur
// ========================================

public function handleRequest() {
    // ... existing routes ...

    if ($method === 'GET' && $endpoint === 'decode-vin') {
        $this->decodeVIN();
        return;
    }

    // ... rest of routes ...
}

?>
