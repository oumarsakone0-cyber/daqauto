<?php
/**
 * ==========================================
 * FONCTIONS COMPLÈTES avec support CAR
 * createProduct() et updateProduct()
 * ==========================================
 *
 * À copier dans products.php
 */

// ==========================================
// 1. CREATE PRODUCT avec support Car
// ==========================================

private function createProduct() {
    $input = json_decode(file_get_contents('php://input'), true);

    // Récupérer la boutique et l'utilisateur
    $boutiqueId = $_GET['boutique_id'] ?? $_POST['boutique_id'] ?? $this->currentBoutique['id'];
    $userId = $this->currentUser['id'];

    if (!$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID de boutique requis']);
        return;
    }

    // Vérifier l'accès à la boutique
    if (!$this->hasAccessToBoutique($userId, $boutiqueId)) {
        $this->sendResponse(403, ['error' => 'Accès non autorisé à cette boutique']);
        return;
    }

    // Validation des données requises
    $required = ['name', 'category_id', 'subcategory_id', 'unit_price', 'stock'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            $this->sendResponse(400, ['error' => "Le champ $field est requis"]);
            return;
        }
    }

    // ✅ Détecter la catégorie (Trailer, Car, ou Truck)
    $isTrailer = false;
    $isCar = false;

    if (!empty($input['category_id'])) {
        $catSql = "SELECT name FROM adjame_categories WHERE id = ?";
        $catResult = $this->db->query($catSql, [$input['category_id']]);
        if (!empty($catResult)) {
            $catName = strtolower($catResult[0]['name']);

            $isTrailer = strpos($catName, 'trailer') !== false ||
                        strpos($catName, 'semi') !== false ||
                        strpos($catName, 'remorque') !== false;

            $isCar = strpos($catName, 'car') !== false ||
                     strpos($catName, 'voiture') !== false ||
                     strpos($catName, 'auto') !== false ||
                     (strpos($catName, 'vehicle') !== false && !$isTrailer);
        }
    }

    // ========================================
    // Champs TRUCKS (véhicules lourds)
    // ========================================
    $vehicle_condition = $input['vehicle_condition'] ?? null;
    $vehicle_make = $input['vehicle_make'] ?? null;
    $vehicle_model = $input['vehicle_model'] ?? null;
    $drive_type = $input['drive_type'] ?? null;
    $vehicle_year = $input['vehicle_year'] ?? null;
    $fuel_type = $input['fuel_type'] ?? null;
    $transmission_type = $input['transmission_type'] ?? null;
    $engine_brand = $input['engine_brand'] ?? null;
    $vehicle_mileage = $input['vehicle_mileage'] ?? null;

    // Champs techniques trucks
    $brake_system = $input['brake_system'] ?? null;
    $cabin_type = $input['cabin_type'] ?? null;
    $country_of_origin = $input['country_of_origin'] ?? null;
    $curb_weight = $input['curb_weight'] ?? null;
    $dimensions = $input['dimensions'] ?? null;
    $fuel_tank_capacity = $input['fuel_tank_capacity'] ?? null;
    $gvw = $input['gvw'] ?? null;
    $other_description = $input['description_plus'] ?? null;
    $payload_capacity = $input['payload_capacity'] ?? null;
    $car_availability = $input['car_availability'] ?? 'Disponible';
    $production_date = $input['production_date'] ?? null;
    $size_type = $input['size_type'] ?? null;
    $suspension_type = $input['suspension_type'] ?? null;
    $tyre_size = $input['tyre_size'] ?? null;
    $wheelbase = $input['wheelbase'] ?? null;
    $engine_n = $input['engine_number'] ?? null;
    $disponibility = $input['disponibility'] ?? null;
    $stockNumber = $this->generateStockNumber();
    $video = $input['video'] ?? '';

    // ========================================
    // Champs TRAILERS
    // ========================================
    $trailer_type = $input['trailer_type'] ?? null;
    $trailer_brand = $input['trailer_brand'] ?? null;
    $trailer_use = $input['trailer_use'] ?? null;
    $trailer_size = $input['trailer_size'] ?? null;
    $trailer_axle = $input['trailer_axle'] ?? null;
    $trailer_suspension = $input['trailer_suspension'] ?? null;
    $trailer_tire = $input['trailer_tire'] ?? null;
    $trailer_king_pin = $input['trailer_king_pin'] ?? null;
    $trailer_main_beam = $input['trailer_main_beam'] ?? null;
    $trailer_max_payload = $input['trailer_max_payload'] ?? null;
    $trailer_place_of_origin = $input['trailer_place_of_origin'] ?? null;
    $trailer_material = $input['trailer_material'] ?? null;
    $trailer_function = $input['trailer_function'] ?? null;
    $trailer_landing_gear = $input['trailer_landing_gear'] ?? null;
    $trailer_color = $input['trailer_color'] ?? null;
    $trailer_condition = $input['trailer_condition'] ?? null;
    $trailer_axle_brand = $input['trailer_axle_brand'] ?? null;

    // ========================================
    // NOUVEAUX CHAMPS CAR (41 champs)
    // ========================================

    // Basic Info (6)
    $car_make = $input['car_make'] ?? null;
    $car_model = $input['car_model'] ?? null;
    $car_year = $input['car_year'] ?? null;
    $car_condition = $input['car_condition'] ?? null;
    $car_vin = $input['car_vin'] ?? null;
    $car_mileage = $input['car_mileage'] ?? null;

    // Battery & Electric (4)
    $car_battery_range = $input['car_battery_range'] ?? null;
    $car_charge_time_full = $input['car_charge_time_full'] ?? null;
    $car_quick_charge_time = $input['car_quick_charge_time'] ?? null;
    $car_battery_capacity = $input['car_battery_capacity'] ?? null;

    // Dimensions (5)
    $car_height = $input['car_height'] ?? null;
    $car_length = $input['car_length'] ?? null;
    $car_width = $input['car_width'] ?? null;
    $car_kerb_weight = $input['car_kerb_weight'] ?? null;
    $car_wheelbase = $input['car_wheelbase'] ?? null;

    // Engine & Drivetrain (5)
    $car_fuel_type = $input['car_fuel_type'] ?? null;
    $car_transmission = $input['car_transmission'] ?? null;
    $car_engine_size = $input['car_engine_size'] ?? null;
    $car_engine_cylinders = $input['car_engine_cylinders'] ?? null;
    $car_drivetrain = $input['car_drivetrain'] ?? null;

    // General (5)
    $car_doors = $input['car_doors'] ?? null;
    $car_seats = $input['car_seats'] ?? null;
    $car_warranty_miles = $input['car_warranty_miles'] ?? null;
    $car_warranty_years = $input['car_warranty_years'] ?? null;
    $car_insurance_group = $input['car_insurance_group'] ?? null;

    // Performance (5)
    $car_top_speed = $input['car_top_speed'] ?? null;
    $car_engine_power_bhp = $input['car_engine_power_bhp'] ?? null;
    $car_engine_power_kw = $input['car_engine_power_kw'] ?? null;
    $car_engine_torque = $input['car_engine_torque'] ?? null;
    $car_acceleration = $input['car_acceleration'] ?? null;

    // Colors & Interior (3)
    $car_exterior_color = $input['car_exterior_color'] ?? null;
    $car_interior_color = $input['car_interior_color'] ?? null;
    $car_interior_material = $input['car_interior_material'] ?? null;

    // Efficiency (4)
    $car_mpg_city = $input['car_mpg_city'] ?? null;
    $car_mpg_highway = $input['car_mpg_highway'] ?? null;
    $car_mpg_combined = $input['car_mpg_combined'] ?? null;
    $car_co2_emissions = $input['car_co2_emissions'] ?? null;

    // Additional (4)
    $car_body_type = $input['car_body_type'] ?? null;
    $car_trim_level = $input['car_trim_level'] ?? null;
    $car_previous_owners = $input['car_previous_owners'] ?? null;
    $car_service_history = $input['car_service_history'] ?? null;

    // Data Source (3)
    $car_data_source = $input['car_data_source'] ?? 'manual';
    $car_vin_decoded_at = $input['car_vin_decoded_at'] ?? null;
    $car_api_provider = $input['car_api_provider'] ?? null;

    // ========================================
    // Génération automatique du nom
    // ========================================

    if ($isTrailer && $trailer_condition && $trailer_type && $trailer_axle && $trailer_max_payload) {
        $conditionFormatted = ucfirst(strtolower($trailer_condition));
        $input['name'] = $conditionFormatted . ' - ' . $trailer_type . ' - ' . $trailer_axle . ' Axles - ' . $trailer_max_payload . 'T';
    }

    if ($isCar && $car_year && $car_make && $car_model) {
        $input['name'] = $car_year . ' ' . $car_make . ' ' . $car_model;
        if ($car_trim_level) {
            $input['name'] .= ' ' . $car_trim_level;
        }
    }

    try {
        $this->db->beginTransaction();

        $slug = $this->generateSlug($input['name']);

        // Validation hiérarchie catégorie
        $subSubcategoryId = null;
        if (!empty($input['subsubcategory_id'])) {
            $check = $this->db->query("SELECT id FROM adjame_sub_subcategories WHERE id = ?", [$input['subsubcategory_id']]);
            if (!empty($check)) {
                $subSubcategoryId = $input['subsubcategory_id'];
            }
        }

        $subSubSubcategoryId = 0;
        if (!empty($input['subsubsubcategory_id'])) {
            $check = $this->db->query("SELECT id FROM adjame_sub_sub_subcategories WHERE id = ?", [$input['subsubsubcategory_id']]);
            if (!empty($check)) {
                $subSubSubcategoryId = $input['subsubsubcategory_id'];
            }
        }

        // ========================================
        // REQUÊTE INSERT avec TOUS les champs (122 paramètres)
        // ========================================
        $productSql = "
            INSERT INTO adjame_products (
                name, slug, sku, description, category_id, subcategory_id,
                unit_price, wholesale_price, wholesale_min_qty, stock,
                status, icon, tags, description_plus, video,
                is_active, unit_type, boutique_id, created_by,
                sub_subcategory_id, sub_sub_subcategory_id,

                -- Champs Trucks (9)
                vehicle_condition, vehicle_make, vehicle_model, drive_type, vehicle_year,
                fuel_type, transmission_type, engine_brand, vehicle_mileage,

                -- Champs Trailers (17)
                trailer_type, trailer_brand, trailer_use, trailer_size, trailer_axle,
                trailer_suspension, trailer_tire, trailer_king_pin, trailer_main_beam,
                trailer_max_payload, trailer_place_of_origin, trailer_material,
                trailer_function, trailer_landing_gear, trailer_color, trailer_axle_brand,
                trailer_condition,

                -- Champs Techniques Trucks (34)
                brake_system, cabin_type, country_of_origin,
                curb_weight, dimensions, power, car_availability, engine_emissions,
                fuel_tank_capacity, gvw, other_description, payload_capacity,
                production_date, size_type, suspension_type, tyre_size, wheelbase,
                engine_number, stock_number, disponibility, speed, gearbox_brand,
                gearbox_model, chassis_dimensions, frame_rear_suspension,
                suspension_front, suspension_rear, axle_brand, axle_front,
                axle_rear, axle_speed_ratio, air_filter, electrics, cab,

                -- NOUVEAUX CHAMPS CAR (41)
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
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";

        $sku = $input['sku'] ?? $this->generateSKU();
        $status = ($input['is_active'] ?? true) ? 'Actif' : 'Brouillon';

        // ========================================
        // PARAMÈTRES (122 total)
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

            // Champs Trucks (9)
            $vehicle_condition, $vehicle_make, $vehicle_model, $drive_type, $vehicle_year,
            $fuel_type, $transmission_type, $engine_brand, $vehicle_mileage,

            // Champs Trailers (17)
            $trailer_type, $trailer_brand, $trailer_use, $trailer_size, $trailer_axle,
            $trailer_suspension, $trailer_tire, $trailer_king_pin, $trailer_main_beam,
            $trailer_max_payload, $trailer_place_of_origin, $trailer_material,
            $trailer_function, $trailer_landing_gear, $trailer_color, $trailer_axle_brand,
            $trailer_condition,

            // Champs Techniques Trucks (34)
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

        $this->db->query($productSql, $productParams);
        $productId = $this->db->lastInsertId();

        // Ajout trim_numbers
        if (!empty($input['trim_numbers'])) {
            foreach ($input['trim_numbers'] as $trimNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_trim_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$productId, $trimNumber]
                );
            }
        }

        // Ajout VIN numbers
        if (!empty($input['vin'])) {
            foreach ($input['vin'] as $vinNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_vin_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$productId, $vinNumber]
                );
            }
        }

        // Couleurs
        if (!empty($input['colors'])) {
            foreach ($input['colors'] as $hexValue) {
                $existingColor = $this->db->query("SELECT id FROM adjame_colors WHERE hex_value = ?", [$hexValue]);
                if (!empty($existingColor)) {
                    $colorId = $existingColor[0]['id'];
                } else {
                    $colorName = 'Couleur ' . strtoupper(substr($hexValue, 1));
                    $this->db->query("INSERT INTO adjame_colors (name, hex_value) VALUES (?, ?)", [$colorName, $hexValue]);
                    $colorId = $this->db->lastInsertId();
                }
                $this->db->query("INSERT INTO adjame_product_colors (product_id, color_id) VALUES (?, ?)", [$productId, $colorId]);
            }
        }

        // Images
        if (!empty($input['images'])) {
            foreach ($input['images'] as $index => $image) {
                $this->db->query(
                    "INSERT INTO adjame_product_images (product_id, image_url, alt_text, is_primary, sort_order)
                    VALUES (?, ?, ?, ?, ?)",
                    [
                        $productId,
                        $image['url'],
                        $image['alt_text'] ?? $input['name'],
                        $index === 0 ? 1 : 0,
                        $index
                    ]
                );
            }
        }

        $this->db->commit();

        $createdProduct = $this->getProductByIdWithDetails($productId);
        $this->sendProductCreationEmail($createdProduct, $this->currentUser, $this->currentBoutique);

        $this->sendResponse(201, [
            'success' => true,
            'message' => 'Produit créé avec succès',
            'data' => $createdProduct
        ]);

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("❌ Erreur dans createProduct: " . $e->getMessage());
        $this->sendResponse(500, ['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
    }
}

// ==========================================
// 2. UPDATE PRODUCT avec support Car
// ==========================================

private function updateProduct() {
    $id = $_GET['id'] ?? null;
    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];

    if (!$id || !$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID produit et ID boutique requis']);
        return;
    }

    // Vérifier que le produit appartient à la boutique
    if (!$this->productBelongsToBoutique($id, $boutiqueId)) {
        $this->sendResponse(403, ['error' => 'Produit non trouvé dans cette boutique']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    error_log("🔄 UpdateProduct - ID: $id, Boutique: $boutiqueId");
    error_log("📤 UpdateProduct - Input reçu: " . json_encode($input));

    // ========================================
    // Champs TRUCKS
    // ========================================
    $vehicle_condition = $input['vehicle_condition'] ?? null;
    $vehicle_make = $input['vehicle_make'] ?? null;
    $vehicle_model = $input['vehicle_model'] ?? null;
    $drive_type = $input['drive_type'] ?? null;
    $vehicle_year = $input['vehicle_year'] ?? null;
    $fuel_type = $input['fuel_type'] ?? null;
    $transmission_type = $input['transmission_type'] ?? null;
    $engine_brand = $input['engine_brand'] ?? null;
    $vehicle_mileage = $input['vehicle_mileage'] ?? null;

    // Champs techniques trucks
    $brake_system = $input['brake_system'] ?? null;
    $cabin_type = $input['cabin_type'] ?? null;
    $country_of_origin = $input['country_of_origin'] ?? null;
    $curb_weight = $input['curb_weight'] ?? null;
    $dimensions = $input['dimensions'] ?? null;
    $fuel_tank_capacity = $input['fuel_tank_capacity'] ?? null;
    $gvw = $input['gvw'] ?? null;
    $other_description = $input['other_description'] ?? null;
    $payload_capacity = $input['payload_capacity'] ?? null;
    $production_date = $input['production_date'] ?? null;
    $size_type = $input['size_type'] ?? null;
    $suspension_type = $input['suspension_type'] ?? null;
    $tyre_size = $input['tyre_size'] ?? null;
    $wheelbase = $input['wheelbase'] ?? null;

    // ========================================
    // Champs TRAILERS
    // ========================================
    $trailer_type = $input['trailer_type'] ?? null;
    $trailer_brand = $input['trailer_brand'] ?? null;
    $trailer_use = $input['trailer_use'] ?? null;
    $trailer_size = $input['trailer_size'] ?? null;
    $trailer_axle = $input['trailer_axle'] ?? null;
    $trailer_suspension = $input['trailer_suspension'] ?? null;
    $trailer_tire = $input['trailer_tire'] ?? null;
    $trailer_king_pin = $input['trailer_king_pin'] ?? null;
    $trailer_main_beam = $input['trailer_main_beam'] ?? null;
    $trailer_max_payload = $input['trailer_max_payload'] ?? null;
    $trailer_place_of_origin = $input['trailer_place_of_origin'] ?? null;
    $trailer_material = $input['trailer_material'] ?? null;
    $trailer_function = $input['trailer_function'] ?? null;
    $trailer_landing_gear = $input['trailer_landing_gear'] ?? null;
    $trailer_color = $input['trailer_color'] ?? null;
    $trailer_condition = $input['trailer_condition'] ?? null;
    $trailer_axle_brand = $input['trailer_axle_brand'] ?? null;

    // ========================================
    // NOUVEAUX CHAMPS CAR (41)
    // ========================================

    // Basic Info
    $car_make = $input['car_make'] ?? null;
    $car_model = $input['car_model'] ?? null;
    $car_year = $input['car_year'] ?? null;
    $car_condition = $input['car_condition'] ?? null;
    $car_vin = $input['car_vin'] ?? null;
    $car_mileage = $input['car_mileage'] ?? null;

    // Battery & Electric
    $car_battery_range = $input['car_battery_range'] ?? null;
    $car_charge_time_full = $input['car_charge_time_full'] ?? null;
    $car_quick_charge_time = $input['car_quick_charge_time'] ?? null;
    $car_battery_capacity = $input['car_battery_capacity'] ?? null;

    // Dimensions
    $car_height = $input['car_height'] ?? null;
    $car_length = $input['car_length'] ?? null;
    $car_width = $input['car_width'] ?? null;
    $car_kerb_weight = $input['car_kerb_weight'] ?? null;
    $car_wheelbase = $input['car_wheelbase'] ?? null;

    // Engine & Drivetrain
    $car_fuel_type = $input['car_fuel_type'] ?? null;
    $car_transmission = $input['car_transmission'] ?? null;
    $car_engine_size = $input['car_engine_size'] ?? null;
    $car_engine_cylinders = $input['car_engine_cylinders'] ?? null;
    $car_drivetrain = $input['car_drivetrain'] ?? null;

    // General
    $car_doors = $input['car_doors'] ?? null;
    $car_seats = $input['car_seats'] ?? null;
    $car_warranty_miles = $input['car_warranty_miles'] ?? null;
    $car_warranty_years = $input['car_warranty_years'] ?? null;
    $car_insurance_group = $input['car_insurance_group'] ?? null;

    // Performance
    $car_top_speed = $input['car_top_speed'] ?? null;
    $car_engine_power_bhp = $input['car_engine_power_bhp'] ?? null;
    $car_engine_power_kw = $input['car_engine_power_kw'] ?? null;
    $car_engine_torque = $input['car_engine_torque'] ?? null;
    $car_acceleration = $input['car_acceleration'] ?? null;

    // Colors & Interior
    $car_exterior_color = $input['car_exterior_color'] ?? null;
    $car_interior_color = $input['car_interior_color'] ?? null;
    $car_interior_material = $input['car_interior_material'] ?? null;

    // Efficiency
    $car_mpg_city = $input['car_mpg_city'] ?? null;
    $car_mpg_highway = $input['car_mpg_highway'] ?? null;
    $car_mpg_combined = $input['car_mpg_combined'] ?? null;
    $car_co2_emissions = $input['car_co2_emissions'] ?? null;

    // Additional
    $car_body_type = $input['car_body_type'] ?? null;
    $car_trim_level = $input['car_trim_level'] ?? null;
    $car_previous_owners = $input['car_previous_owners'] ?? null;
    $car_service_history = $input['car_service_history'] ?? null;

    // Data Source
    $car_data_source = $input['car_data_source'] ?? null;
    $car_vin_decoded_at = $input['car_vin_decoded_at'] ?? null;
    $car_api_provider = $input['car_api_provider'] ?? null;

    try {
        $this->db->beginTransaction();

        // Vérifier la sous-sous-catégorie
        $subSubcategoryId = null;
        if (!empty($input['subsubcategory_id'])) {
            $checkSubSubSql = "SELECT id FROM adjame_sub_subcategories WHERE id = ?";
            $subSubExists = $this->db->query($checkSubSubSql, [$input['subsubcategory_id']]);
            if (!empty($subSubExists)) {
                $subSubcategoryId = $input['subsubcategory_id'];
            }
        }

        // Vérifier la sous-sous-sous-catégorie
        $subSubSubcategoryId = 0;
        if (!empty($input['subsubsubcategory_id'])) {
            $checkSubSubSubSql = "SELECT id FROM adjame_sub_sub_subcategories WHERE id = ?";
            $subSubSubExists = $this->db->query($checkSubSubSubSql, [$input['subsubsubcategory_id']]);
            if (!empty($subSubSubExists)) {
                $subSubSubcategoryId = $input['subsubsubcategory_id'];
            }
        }

        // Gestion des images à supprimer
        if (!empty($input['image_remove'])) {
            foreach ($input['image_remove'] as $imageUrl) {
                $this->db->query("DELETE FROM adjame_product_images WHERE product_id = ? AND image_url = ?", [$id, $imageUrl]);
                if (strpos($imageUrl, '/uploads/') === 0) {
                    $filePath = '../' . ltrim($imageUrl, '/');
                    if (file_exists($filePath)) unlink($filePath);
                }
            }
        }

        // Gestion des images à ajouter
        if (!empty($input['image_add'])) {
            $maxSortSql = "SELECT COALESCE(MAX(sort_order), -1) as max_sort FROM adjame_product_images WHERE product_id = ?";
            $maxSortResult = $this->db->query($maxSortSql, [$id]);
            $currentMaxSort = $maxSortResult[0]['max_sort'];

            foreach ($input['image_add'] as $index => $imageUrl) {
                $isPrimary = ($currentMaxSort == -1 && $index == 0) ? 1 : 0;
                $this->db->query("
                    INSERT INTO adjame_product_images (product_id, image_url, alt_text, is_primary, sort_order)
                    VALUES (?, ?, ?, ?, ?)
                ", [
                    $id,
                    $imageUrl,
                    $input['name'] ?? 'Image produit',
                    $isPrimary,
                    $currentMaxSort + $index + 1
                ]);
            }
        }

        // Mise à jour des couleurs
        if (isset($input['colors'])) {
            $this->db->query("DELETE FROM adjame_product_colors WHERE product_id = ?", [$id]);
            foreach ($input['colors'] as $hexValue) {
                $existingColor = $this->db->query("SELECT id FROM adjame_colors WHERE hex_value = ?", [$hexValue]);
                if (!empty($existingColor)) {
                    $colorId = $existingColor[0]['id'];
                } else {
                    $colorName = 'Couleur ' . strtoupper(substr($hexValue, 1));
                    $this->db->query("INSERT INTO adjame_colors (name, hex_value) VALUES (?, ?)", [$colorName, $hexValue]);
                    $colorId = $this->db->lastInsertId();
                }
                $this->db->query("INSERT INTO adjame_product_colors (product_id, color_id) VALUES (?, ?)", [$id, $colorId]);
            }
        }

        // Mise à jour des tailles
        if (isset($input['sizes'])) {
            $this->db->query("DELETE FROM adjame_product_sizes WHERE product_id = ?", [$id]);
            foreach ($input['sizes'] as $sizeName) {
                $existingSize = $this->db->query("SELECT id FROM adjame_sizes WHERE name = ?", [$sizeName]);
                if (!empty($existingSize)) {
                    $sizeId = $existingSize[0]['id'];
                } else {
                    $this->db->query("INSERT INTO adjame_sizes (name) VALUES (?)", [$sizeName]);
                    $sizeId = $this->db->lastInsertId();
                }
                $this->db->query("INSERT INTO adjame_product_sizes (product_id, size_id) VALUES (?, ?)", [$id, $sizeId]);
            }
        }

        // Mise à jour des trim_numbers
        if (isset($input['trim_numbers'])) {
            $this->db->query("DELETE FROM adjame_product_trim_numbers WHERE product_id = ?", [$id]);
            foreach ($input['trim_numbers'] as $engineNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_trim_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$id, $engineNumber]
                );
            }
        }

        // Mise à jour des vin_numbers
        if (isset($input['vin'])) {
            $this->db->query("DELETE FROM adjame_product_vin_numbers WHERE product_id = ?", [$id]);
            foreach ($input['vin'] as $vinNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_vin_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$id, $vinNumber]
                );
            }
        }

        // ========================================
        // REQUÊTE UPDATE avec TOUS les champs
        // ========================================
        $updateSql = "
            UPDATE adjame_products SET
                name = ?, description = ?, category_id = ?, subcategory_id = ?,
                sub_subcategory_id = ?, sub_sub_subcategory_id = ?, unit_price = ?, stock = ?,
                unit_type = ?, wholesale_price = ?, wholesale_min_qty = ?, description_plus = ?,
                status = ?, icon = ?, tags = ?, is_active = ?,

                -- Champs Trucks
                vehicle_condition = ?, vehicle_make = ?, vehicle_model = ?, drive_type = ?,
                vehicle_year = ?, fuel_type = ?, transmission_type = ?, engine_brand = ?, vehicle_mileage = ?,

                -- Champs Techniques
                brake_system = ?, cabin_type = ?, country_of_origin = ?, curb_weight = ?, dimensions = ?,
                fuel_tank_capacity = ?, gvw = ?, car_availability = ?, other_description = ?, payload_capacity = ?,
                production_date = ?, size_type = ?, suspension_type = ?, tyre_size = ?, power = ?,
                engine_emissions = ?, wheelbase = ?,

                -- Champs Trailers
                trailer_type = ?, trailer_brand = ?, trailer_use = ?, trailer_size = ?, trailer_axle = ?,
                trailer_suspension = ?, trailer_tire = ?, trailer_king_pin = ?, trailer_main_beam = ?,
                trailer_max_payload = ?, trailer_place_of_origin = ?, trailer_material = ?,
                trailer_function = ?, trailer_landing_gear = ?, trailer_color = ?, trailer_axle_brand = ?,
                trailer_condition = ?,

                -- NOUVEAUX CHAMPS CAR
                car_make = ?, car_model = ?, car_year = ?, car_condition = ?, car_vin = ?, car_mileage = ?,
                car_battery_range = ?, car_charge_time_full = ?, car_quick_charge_time = ?, car_battery_capacity = ?,
                car_height = ?, car_length = ?, car_width = ?, car_kerb_weight = ?, car_wheelbase = ?,
                car_fuel_type = ?, car_transmission = ?, car_engine_size = ?, car_engine_cylinders = ?, car_drivetrain = ?,
                car_doors = ?, car_seats = ?, car_warranty_miles = ?, car_warranty_years = ?, car_insurance_group = ?,
                car_top_speed = ?, car_engine_power_bhp = ?, car_engine_power_kw = ?, car_engine_torque = ?, car_acceleration = ?,
                car_exterior_color = ?, car_interior_color = ?, car_interior_material = ?,
                car_mpg_city = ?, car_mpg_highway = ?, car_mpg_combined = ?, car_co2_emissions = ?,
                car_body_type = ?, car_trim_level = ?, car_previous_owners = ?, car_service_history = ?,
                car_data_source = ?, car_vin_decoded_at = ?, car_api_provider = ?,

                updated_at = CURRENT_TIMESTAMP, updated_by = ?
            WHERE id = ? AND boutique_id = ?
        ";

        $params = [
            // Base (16)
            $input['name'] ?? '',
            $input['description'] ?? '',
            $input['category_id'] ?? null,
            $input['subcategory_id'] ?? null,
            $subSubcategoryId,
            $subSubSubcategoryId,
            $input['unit_price'] ?? 0,
            $input['stock'] ?? 0,
            $input['unit_type'] ?? 'pièce',
            $input['wholesale_price'] ?? null,
            $input['wholesale_min_qty'] ?? null,
            $input['description_plus'] ?? '',
            $input['status'] ?? 'Actif',
            $input['icon'] ?? '',
            $input['tags'] ?? '',
            $input['is_active'] ?? true,

            // Trucks (9)
            $vehicle_condition,
            $vehicle_make,
            $vehicle_model,
            $drive_type,
            $vehicle_year,
            $fuel_type,
            $transmission_type,
            $engine_brand,
            $vehicle_mileage,

            // Techniques (21)
            $brake_system,
            $cabin_type,
            $country_of_origin,
            $curb_weight,
            $dimensions,
            $fuel_tank_capacity,
            $gvw,
            $input['car_availability'] ?? '',
            $other_description,
            $payload_capacity,
            $production_date,
            $size_type,
            $suspension_type,
            $tyre_size,
            $input['power'] ?? '',
            $input['engine_emissions'] ?? '',
            $wheelbase,

            // Trailers (17)
            $trailer_type,
            $trailer_brand,
            $trailer_use,
            $trailer_size,
            $trailer_axle,
            $trailer_suspension,
            $trailer_tire,
            $trailer_king_pin,
            $trailer_main_beam,
            $trailer_max_payload,
            $trailer_place_of_origin,
            $trailer_material,
            $trailer_function,
            $trailer_landing_gear,
            $trailer_color,
            $trailer_axle_brand,
            $trailer_condition,

            // CAR (41)
            $car_make, $car_model, $car_year, $car_condition, $car_vin, $car_mileage,
            $car_battery_range, $car_charge_time_full, $car_quick_charge_time, $car_battery_capacity,
            $car_height, $car_length, $car_width, $car_kerb_weight, $car_wheelbase,
            $car_fuel_type, $car_transmission, $car_engine_size, $car_engine_cylinders, $car_drivetrain,
            $car_doors, $car_seats, $car_warranty_miles, $car_warranty_years, $car_insurance_group,
            $car_top_speed, $car_engine_power_bhp, $car_engine_power_kw, $car_engine_torque, $car_acceleration,
            $car_exterior_color, $car_interior_color, $car_interior_material,
            $car_mpg_city, $car_mpg_highway, $car_mpg_combined, $car_co2_emissions,
            $car_body_type, $car_trim_level, $car_previous_owners, $car_service_history,
            $car_data_source, $car_vin_decoded_at, $car_api_provider,

            // System
            $this->currentUser['id'],
            $id,
            $boutiqueId
        ];

        $result = $this->db->query($updateSql, $params);

        $this->db->commit();

        $updatedProduct = $this->getProductByIdWithDetails($id);

        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Produit mis à jour avec succès',
            'data' => $updatedProduct
        ]);

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("❌ Erreur dans updateProduct: " . $e->getMessage());
        $this->sendResponse(500, ['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
    }
}

?>
