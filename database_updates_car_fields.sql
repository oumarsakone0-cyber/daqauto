-- ==========================================
-- SQL Script pour ajouter les champs CAR
-- ==========================================

-- Ajout des champs spécifiques aux voitures (Cars)
ALTER TABLE `adjame_products`
-- Basic Car Info
ADD COLUMN `car_make` VARCHAR(100) NULL COMMENT 'Car manufacturer (e.g., Tesla, BMW)',
ADD COLUMN `car_model` VARCHAR(100) NULL COMMENT 'Car model name',
ADD COLUMN `car_year` INT(4) NULL COMMENT 'Manufacturing year',
ADD COLUMN `car_condition` ENUM('New', 'Used', 'Certified Pre-Owned') NULL COMMENT 'Vehicle condition',
ADD COLUMN `car_vin` VARCHAR(17) NULL COMMENT 'Vehicle Identification Number',
ADD COLUMN `car_mileage` INT(11) NULL COMMENT 'Current mileage in miles or km',

-- Battery & Electric (for EVs)
ADD COLUMN `car_battery_range` INT(11) NULL COMMENT 'Battery range in miles',
ADD COLUMN `car_charge_time_full` VARCHAR(50) NULL COMMENT 'Full charge time (0-100%)',
ADD COLUMN `car_quick_charge_time` VARCHAR(50) NULL COMMENT 'Quick charge time (0-80%)',
ADD COLUMN `car_battery_capacity` DECIMAL(10,2) NULL COMMENT 'Battery capacity in kWh',

-- Dimensions
ADD COLUMN `car_height` INT(11) NULL COMMENT 'Vehicle height in mm',
ADD COLUMN `car_length` INT(11) NULL COMMENT 'Vehicle length in mm',
ADD COLUMN `car_width` INT(11) NULL COMMENT 'Vehicle width in mm',
ADD COLUMN `car_kerb_weight` INT(11) NULL COMMENT 'Minimum kerb weight in kg',
ADD COLUMN `car_wheelbase` INT(11) NULL COMMENT 'Wheelbase in mm',

-- Engine & Drivetrain
ADD COLUMN `car_fuel_type` ENUM('Electric', 'Petrol', 'Diesel', 'Hybrid', 'Plug-in Hybrid', 'Hydrogen') NULL COMMENT 'Type of fuel',
ADD COLUMN `car_transmission` ENUM('Automatic', 'Manual', 'CVT', 'Semi-Automatic') NULL COMMENT 'Transmission type',
ADD COLUMN `car_engine_size` DECIMAL(10,2) NULL COMMENT 'Engine size in liters',
ADD COLUMN `car_engine_cylinders` INT(2) NULL COMMENT 'Number of cylinders',
ADD COLUMN `car_drivetrain` ENUM('FWD', 'RWD', 'AWD', '4WD') NULL COMMENT 'Drive type',

-- General Info
ADD COLUMN `car_doors` INT(1) NULL COMMENT 'Number of doors',
ADD COLUMN `car_seats` INT(2) NULL COMMENT 'Number of seats',
ADD COLUMN `car_warranty_miles` INT(11) NULL COMMENT 'Warranty coverage in miles',
ADD COLUMN `car_warranty_years` INT(2) NULL COMMENT 'Warranty coverage in years',
ADD COLUMN `car_insurance_group` VARCHAR(10) NULL COMMENT 'Insurance group rating',

-- Performance
ADD COLUMN `car_top_speed` INT(11) NULL COMMENT 'Top speed in mph',
ADD COLUMN `car_engine_power_bhp` INT(11) NULL COMMENT 'Engine power in bhp',
ADD COLUMN `car_engine_power_kw` INT(11) NULL COMMENT 'Engine power in kW',
ADD COLUMN `car_engine_torque` DECIMAL(10,2) NULL COMMENT 'Engine torque in lbs/ft or Nm',
ADD COLUMN `car_acceleration` DECIMAL(10,2) NULL COMMENT '0-60mph time in seconds',

-- Colors & Interior
ADD COLUMN `car_exterior_color` VARCHAR(50) NULL COMMENT 'Exterior color name',
ADD COLUMN `car_interior_color` VARCHAR(50) NULL COMMENT 'Interior color name',
ADD COLUMN `car_interior_material` VARCHAR(100) NULL COMMENT 'Interior material (leather, fabric, etc.)',

-- Efficiency
ADD COLUMN `car_mpg_city` DECIMAL(10,2) NULL COMMENT 'MPG in city',
ADD COLUMN `car_mpg_highway` DECIMAL(10,2) NULL COMMENT 'MPG on highway',
ADD COLUMN `car_mpg_combined` DECIMAL(10,2) NULL COMMENT 'Combined MPG',
ADD COLUMN `car_co2_emissions` INT(11) NULL COMMENT 'CO2 emissions in g/km',

-- Additional Features
ADD COLUMN `car_body_type` VARCHAR(50) NULL COMMENT 'Body type (Sedan, SUV, Coupe, etc.)',
ADD COLUMN `car_trim_level` VARCHAR(100) NULL COMMENT 'Trim level or variant',
ADD COLUMN `car_previous_owners` INT(2) NULL COMMENT 'Number of previous owners',
ADD COLUMN `car_service_history` TEXT NULL COMMENT 'Service history details',

-- Data Source
ADD COLUMN `car_data_source` ENUM('manual', 'api', 'vin_decoder') NULL DEFAULT 'manual' COMMENT 'How the data was entered',
ADD COLUMN `car_vin_decoded_at` DATETIME NULL COMMENT 'When VIN was decoded',
ADD COLUMN `car_api_provider` VARCHAR(50) NULL COMMENT 'API provider used (NHTSA, Edmunds, etc.)';

-- ==========================================
-- Index pour améliorer les performances
-- ==========================================

-- Index sur les champs fréquemment recherchés
CREATE INDEX idx_car_make ON adjame_products(car_make);
CREATE INDEX idx_car_model ON adjame_products(car_model);
CREATE INDEX idx_car_year ON adjame_products(car_year);
CREATE INDEX idx_car_vin ON adjame_products(car_vin);
CREATE INDEX idx_car_condition ON adjame_products(car_condition);
CREATE INDEX idx_car_fuel_type ON adjame_products(car_fuel_type);
CREATE INDEX idx_car_body_type ON adjame_products(car_body_type);

-- ==========================================
-- Fin du script
-- ==========================================
