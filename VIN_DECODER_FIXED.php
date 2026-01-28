<?php
// ========================================
// VERSION CORRIGÉE DU DÉCODEUR VIN
// Remplace la fonction decodeVIN() existante par celle-ci
// ========================================

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
        // L'API NHTSA peut retourner des valeurs vides, on utilise trim() et vérifie
        $carData = [
            'car_make' => !empty(trim($result['Make'])) ? trim($result['Make']) : null,
            'car_model' => !empty(trim($result['Model'])) ? trim($result['Model']) : null,
            'car_year' => !empty($result['ModelYear']) ? intval($result['ModelYear']) : null,
            'car_body_type' => !empty(trim($result['BodyClass'])) ? trim($result['BodyClass']) : null,
            'car_trim_level' => !empty(trim($result['Trim'])) ? trim($result['Trim']) : null,
            'car_fuel_type' => $this->mapFuelType($result['FuelTypePrimary'] ?? ''),
            'car_transmission' => $this->mapTransmission($result['TransmissionStyle'] ?? ''),
            'car_engine_size' => $this->parseEngineSize($result['DisplacementL'] ?? ''),
            'car_engine_cylinders' => !empty($result['EngineCylinders']) ? intval($result['EngineCylinders']) : null,
            'car_drivetrain' => $this->mapDrivetrain($result['DriveType'] ?? ''),
            'car_doors' => !empty($result['Doors']) ? intval($result['Doors']) : null,
            'car_seats' => !empty($result['Seats']) ? intval($result['Seats']) : null,
            'car_vin' => $vin,
            'car_data_source' => 'api',
            'car_vin_decoded_at' => date('Y-m-d H:i:s'),
            'car_api_provider' => 'NHTSA',
        ];

        // Générer un nom automatique
        $suggestedName = '';
        if ($carData['car_year'] && $carData['car_make']) {
            $suggestedName = $carData['car_year'] . ' ' . $carData['car_make'];

            if ($carData['car_model']) {
                $suggestedName .= ' ' . $carData['car_model'];
            }

            if ($carData['car_trim_level']) {
                $suggestedName .= ' ' . $carData['car_trim_level'];
            }
        }

        if (!empty($suggestedName)) {
            $carData['suggested_name'] = $suggestedName;
        }

        $this->sendResponse(200, [
            'success' => true,
            'message' => 'VIN décodé avec succès',
            'data' => $carData,
            // Garde raw_data en mode debug pour voir toutes les données disponibles
            'raw_data' => $result
        ]);

    } catch (Exception $e) {
        $this->sendResponse(500, [
            'error' => 'Erreur lors du décodage du VIN: ' . $e->getMessage()
        ]);
    }
}

// Fonctions helper pour mapper les données (CORRIGÉES)
private function mapFuelType($fuel) {
    if (empty(trim($fuel))) return null;

    $fuel = strtolower(trim($fuel));

    if (strpos($fuel, 'gasoline') !== false || strpos($fuel, 'gas') !== false) {
        return 'Gasoline';
    } else if (strpos($fuel, 'diesel') !== false) {
        return 'Diesel';
    } else if (strpos($fuel, 'electric') !== false) {
        return 'Electric';
    } else if (strpos($fuel, 'flex') !== false || strpos($fuel, 'hybrid') !== false) {
        return 'Hybrid';
    } else if (strpos($fuel, 'hydrogen') !== false) {
        return 'Hydrogen';
    }

    return null;
}

private function mapTransmission($trans) {
    if (empty(trim($trans))) return null;

    $trans = strtolower(trim($trans));

    if (strpos($trans, 'auto') !== false || strpos($trans, 'cvt') !== false) {
        return 'Automatic';
    } else if (strpos($trans, 'manual') !== false) {
        return 'Manual';
    } else if (strpos($trans, 'semi') !== false) {
        return 'Semi-Automatic';
    }

    return null;
}

private function mapDrivetrain($drive) {
    if (empty(trim($drive))) return null;

    $drive = trim($drive);

    $map = [
        'FWD' => 'FWD',
        'RWD' => 'RWD',
        'AWD' => 'AWD',
        '4WD' => '4WD',
        '4X4' => '4WD',
        'Four Wheel Drive' => '4WD',
        'All Wheel Drive' => 'AWD',
        'Front Wheel Drive' => 'FWD',
        'Rear Wheel Drive' => 'RWD'
    ];

    // Recherche exacte
    if (isset($map[$drive])) {
        return $map[$drive];
    }

    // Recherche partielle
    $driveLower = strtolower($drive);
    if (strpos($driveLower, 'front') !== false) return 'FWD';
    if (strpos($driveLower, 'rear') !== false) return 'RWD';
    if (strpos($driveLower, 'all') !== false) return 'AWD';
    if (strpos($driveLower, '4wd') !== false || strpos($driveLower, 'four') !== false) return '4WD';

    return null;
}

private function parseEngineSize($displacement) {
    if (empty($displacement)) return null;

    $value = floatval($displacement);
    return $value > 0 ? $value : null;
}

?>
