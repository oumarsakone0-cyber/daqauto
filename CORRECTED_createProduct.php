<?php
// LIGNE À CORRIGER DANS $productParams
// Cherche cette ligne (vers la ligne 4230):

// AVANT (INCORRECT - other_description 2 fois):
$productParams = [
    // Champs de base (15)
    $input['name'], $slug, $sku, $input['description'] ?? '',
    $input['category_id'], $input['subcategory_id'],
    $input['unit_price'], $input['wholesale_price'] ?? null, $input['wholesale_min_qty'] ?? null,
    $input['stock'], $status, $input['icon'] ?? '', $input['tags'] ?? '',
    $other_description, $video,  // ← Première fois

    // ... autres champs ...

    // Champs Techniques Trucks (34)
    $brake_system, $cabin_type, $country_of_origin,
    $curb_weight, $dimensions, $input['power'] ?? null, $car_availability, $input['engine_emissions'] ?? null,
    $fuel_tank_capacity, $gvw, $other_description, $payload_capacity,  // ← ERREUR: Deuxième fois!
    // ...
];

// APRÈS (CORRECT - remplacé par null):
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
    $fuel_tank_capacity, $gvw, null, $payload_capacity,  // ← CORRIGÉ: null au lieu de $other_description
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

// Maintenant tu devrais avoir exactement 122 paramètres!
?>
