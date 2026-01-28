<?php
// ========================================
// À AJOUTER DANS products.php
// ========================================

// ÉTAPE 1: Ajouter cette fonction dans la classe (vers la fin, avant la dernière accolade)
// Placer cette fonction après toutes les autres fonctions de la classe

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
            'data' => $carData
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
        'Gasoline' => 'Gasoline',
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


// ÉTAPE 2: Ajouter cette route dans la fonction handleRequest() ou au début du fichier
// Cherche où sont gérés les endpoints (probablement une structure if/else ou switch)
// Ajoute cette condition AVANT les autres routes:

/*
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'decode-vin') {
    $this->decodeVIN();
    exit;
}
*/

// OU si tu utilises un routeur différent, utilise cette structure:
/*
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET' && $action === 'decode-vin') {
    $this->decodeVIN();
    exit;
}
*/

?>
