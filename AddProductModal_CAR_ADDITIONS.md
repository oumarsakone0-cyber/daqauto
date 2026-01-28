# Additions nécessaires pour AddProductModal.vue - Support Car

## ✅ Déjà fait

1. ✅ Ajout des 41+ champs Car dans `productData` (lignes 2054-2140)
2. ✅ Ajout du computed `isCarCategory` (lignes 1961-1973)
3. ✅ Modification du computed `steps` pour inclure les étapes Car (lignes 2148-2176)

---

## 📝 À ajouter maintenant

### 1. Fonction `decodeVIN()` - À ajouter dans la section des fonctions

```javascript
// ========================================
// Fonction pour décoder le VIN
// ========================================
const decodeVIN = async () => {
  if (!productData.car_vin_input || productData.car_vin_input.length !== 17) {
    productData.car_vin_error = 'VIN must be exactly 17 characters'
    return
  }

  productData.car_vin_decoding = true
  productData.car_vin_error = ''

  try {
    const response = await api.get(`/products/decode-vin?vin=${productData.car_vin_input}`)

    if (response.data.success) {
      const data = response.data.data

      // Pre-fill form fields
      productData.car_make = data.car_make || ''
      productData.car_model = data.car_model || ''
      productData.car_year = data.car_year || null
      productData.car_fuel_type = data.car_fuel_type || ''
      productData.car_transmission = data.car_transmission || ''
      productData.car_engine_size = data.car_engine_size || null
      productData.car_engine_cylinders = data.car_engine_cylinders || null
      productData.car_drivetrain = data.car_drivetrain || ''
      productData.car_body_type = data.car_body_type || ''
      productData.car_trim_level = data.car_trim_level || ''
      productData.car_doors = data.car_doors || null
      productData.car_seats = data.car_seats || null
      productData.car_vin = productData.car_vin_input

      // Auto-fill product name if suggested
      if (data.suggested_name) {
        productData.name = data.suggested_name
      }

      productData.car_vin_decoded = true
      productData.car_data_source = 'api'
      productData.car_vin_decoded_at = new Date().toISOString()
      productData.car_api_provider = 'NHTSA'

      showNotificationMessage('success', 'Success', 'VIN decoded successfully!')
    } else {
      productData.car_vin_error = 'Unable to decode VIN'
    }
  } catch (error) {
    productData.car_vin_error = error.response?.data?.error || 'Error decoding VIN'
    console.error('VIN decode error:', error)
  } finally {
    productData.car_vin_decoding = false
  }
}
```

---

### 2. Mise à jour de la fonction `handleSubmit()` - Ajouter les champs Car

Trouver cette section dans handleSubmit():

```javascript
const formData = {
  name: productData.name,
  description: productData.description,
  // ... autres champs ...
}
```

Et ajouter avant l'envoi:

```javascript
// ✅ Ajouter les champs Car si catégorie Car
if (isCarCategory.value) {
  Object.assign(formData, {
    car_make: productData.car_make,
    car_model: productData.car_model,
    car_year: productData.car_year,
    car_condition: productData.car_condition,
    car_vin: productData.car_vin,
    car_mileage: productData.car_mileage,
    car_battery_range: productData.car_battery_range,
    car_charge_time_full: productData.car_charge_time_full,
    car_quick_charge_time: productData.car_quick_charge_time,
    car_battery_capacity: productData.car_battery_capacity,
    car_height: productData.car_height,
    car_length: productData.car_length,
    car_width: productData.car_width,
    car_kerb_weight: productData.car_kerb_weight,
    car_wheelbase: productData.car_wheelbase,
    car_fuel_type: productData.car_fuel_type,
    car_transmission: productData.car_transmission,
    car_engine_size: productData.car_engine_size,
    car_engine_cylinders: productData.car_engine_cylinders,
    car_drivetrain: productData.car_drivetrain,
    car_doors: productData.car_doors,
    car_seats: productData.car_seats,
    car_warranty_miles: productData.car_warranty_miles,
    car_warranty_years: productData.car_warranty_years,
    car_insurance_group: productData.car_insurance_group,
    car_top_speed: productData.car_top_speed,
    car_engine_power_bhp: productData.car_engine_power_bhp,
    car_engine_power_kw: productData.car_engine_power_kw,
    car_engine_torque: productData.car_engine_torque,
    car_acceleration: productData.car_acceleration,
    car_exterior_color: productData.car_exterior_color,
    car_interior_color: productData.car_interior_color,
    car_interior_material: productData.car_interior_material,
    car_mpg_city: productData.car_mpg_city,
    car_mpg_highway: productData.car_mpg_highway,
    car_mpg_combined: productData.car_mpg_combined,
    car_co2_emissions: productData.car_co2_emissions,
    car_body_type: productData.car_body_type,
    car_trim_level: productData.car_trim_level,
    car_previous_owners: productData.car_previous_owners,
    car_service_history: productData.car_service_history,
    car_data_source: productData.car_data_source,
    car_vin_decoded_at: productData.car_vin_decoded_at,
    car_api_provider: productData.car_api_provider
  })
}
```

---

### 3. Template - ÉTAPE 0: Ajouter après la sélection de sous-catégorie

Trouver la section où se termine la sélection de subcategory, et ajouter:

```vue
<!-- ✅ NOUVEAU: Data Entry Method Selection (Cars only) -->
<div v-if="isCarCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mt-6">
  <h3 class="text-lg font-semibold mb-4 text-gray-800">
    How would you like to enter vehicle data?
  </h3>

  <div class="space-y-3">
    <!-- Option 1: VIN Automatic -->
    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-orange-400 hover:bg-orange-50"
           :class="productData.car_data_entry_mode === 'vin' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
      <input
        type="radio"
        v-model="productData.car_data_entry_mode"
        value="vin"
        class="mt-1 mr-3"
      />
      <div class="flex-1">
        <div class="font-semibold text-gray-900">🔍 Retrieve via VIN (Automatic)</div>
        <div class="text-sm text-gray-600 mt-1">Enter VIN to auto-fill vehicle details from NHTSA database</div>
      </div>
    </label>

    <!-- Option 2: Manual Entry -->
    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-orange-400 hover:bg-orange-50"
           :class="productData.car_data_entry_mode === 'manual' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
      <input
        type="radio"
        v-model="productData.car_data_entry_mode"
        value="manual"
        class="mt-1 mr-3"
      />
      <div class="flex-1">
        <div class="font-semibold text-gray-900">✍️ Enter manually</div>
        <div class="text-sm text-gray-600 mt-1">Fill all fields manually with your vehicle information</div>
      </div>
    </label>
  </div>

  <!-- VIN Input Section (shown when VIN mode is selected) -->
  <div v-if="productData.car_data_entry_mode === 'vin'" class="mt-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
      Vehicle Identification Number (VIN) *
    </label>
    <div class="flex gap-2">
      <input
        type="text"
        v-model="productData.car_vin_input"
        maxlength="17"
        placeholder="Enter 17-character VIN"
        class="flex-1 input-style uppercase"
        @input="productData.car_vin_error = ''"
        :class="{ 'border-red-500': productData.car_vin_error }"
      />
      <button
        @click="decodeVIN"
        :disabled="productData.car_vin_decoding || productData.car_vin_input.length !== 17"
        class="btn-degrade-orange px-6 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="!productData.car_vin_decoding">🔍 Decode VIN</span>
        <span v-else class="flex items-center gap-2">
          <div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></div>
          Decoding...
        </span>
      </button>
    </div>

    <!-- VIN Status Messages -->
    <div v-if="productData.car_vin_error" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
      <div class="flex items-center gap-2 text-red-700 text-sm">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
        </svg>
        {{ productData.car_vin_error }}
      </div>
    </div>

    <div v-if="productData.car_vin_decoded" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
      <div class="flex items-center gap-2 text-green-700 text-sm">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
        </svg>
        ✓ VIN decoded successfully! Fields have been pre-filled below.
      </div>
    </div>

    <div class="mt-2 text-xs text-gray-500">
      The VIN decoder uses the free NHTSA database. Some fields may need manual verification.
    </div>
  </div>
</div>
```

---

### 4. Template - ÉTAPE 1 (currentStep === 1): Section Car Vehicle Info

Ajouter après les sections Truck et Trailer existantes:

```vue
<!-- ✅ CAR: Vehicle Information (Step 1) -->
<div v-if="isCarCategory" class="space-y-6">
  <!-- Basic Vehicle Information -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
      <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Basic Vehicle Information
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Make -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Make *
        </label>
        <input
          type="text"
          v-model="productData.car_make"
          required
          class="input-style"
          placeholder="e.g., Tesla, BMW, Toyota"
          :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
        />
      </div>

      <!-- Model -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Model *
        </label>
        <input
          type="text"
          v-model="productData.car_model"
          required
          class="input-style"
          placeholder="e.g., Model Y, X5, Camry"
          :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
        />
      </div>

      <!-- Year -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Year *
        </label>
        <input
          type="number"
          v-model.number="productData.car_year"
          required
          min="1900"
          :max="new Date().getFullYear() + 1"
          class="input-style"
          placeholder="2024"
          :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
        />
      </div>

      <!-- Condition -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Condition *
        </label>
        <select v-model="productData.car_condition" required class="input-style">
          <option value="New">New</option>
          <option value="Used">Used</option>
          <option value="Certified Pre-Owned">Certified Pre-Owned</option>
        </select>
      </div>

      <!-- VIN (if manual mode) -->
      <div v-if="productData.car_data_entry_mode === 'manual'">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          VIN
        </label>
        <input
          type="text"
          v-model="productData.car_vin"
          maxlength="17"
          class="input-style uppercase"
          placeholder="17-character VIN"
        />
      </div>

      <!-- Mileage -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Mileage (miles)
        </label>
        <input
          type="number"
          v-model.number="productData.car_mileage"
          min="0"
          class="input-style"
          placeholder="12,500"
        />
      </div>

      <!-- Body Type -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Body Type
        </label>
        <select v-model="productData.car_body_type" class="input-style">
          <option value="">Select body type</option>
          <option value="Sedan">Sedan</option>
          <option value="SUV">SUV</option>
          <option value="Truck">Truck</option>
          <option value="Coupe">Coupe</option>
          <option value="Convertible">Convertible</option>
          <option value="Hatchback">Hatchback</option>
          <option value="Wagon">Wagon</option>
          <option value="Van">Van</option>
          <option value="Minivan">Minivan</option>
        </select>
      </div>

      <!-- Trim Level -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Trim Level
        </label>
        <input
          type="text"
          v-model="productData.car_trim_level"
          class="input-style"
          placeholder="e.g., Long Range AWD, Sport, Premium"
          :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
        />
      </div>
    </div>
  </div>

  <!-- Dimensions Section -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Dimensions</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Height (mm)</label>
        <input type="number" v-model.number="productData.car_height" class="input-style" placeholder="1626" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Length (mm)</label>
        <input type="number" v-model.number="productData.car_length" class="input-style" placeholder="4634" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Width (mm)</label>
        <input type="number" v-model.number="productData.car_width" class="input-style" placeholder="2063" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Wheelbase (mm)</label>
        <input type="number" v-model.number="productData.car_wheelbase" class="input-style" placeholder="2767" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Kerb Weight (kg)</label>
        <input type="number" v-model.number="productData.car_kerb_weight" class="input-style" placeholder="2098" />
      </div>
    </div>
  </div>

  <!-- Engine & Drivetrain Section -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Engine & Drivetrain</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Fuel Type -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Fuel Type *</label>
        <select v-model="productData.car_fuel_type" required class="input-style">
          <option value="">Select fuel type</option>
          <option value="Electric">Electric</option>
          <option value="Petrol">Petrol</option>
          <option value="Diesel">Diesel</option>
          <option value="Hybrid">Hybrid</option>
          <option value="Plug-in Hybrid">Plug-in Hybrid</option>
          <option value="Hydrogen">Hydrogen</option>
        </select>
      </div>

      <!-- Transmission -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Transmission *</label>
        <select v-model="productData.car_transmission" required class="input-style">
          <option value="">Select transmission</option>
          <option value="Automatic">Automatic</option>
          <option value="Manual">Manual</option>
          <option value="CVT">CVT</option>
          <option value="Semi-Automatic">Semi-Automatic</option>
        </select>
      </div>

      <!-- Engine Size -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Engine Size (L)</label>
        <input type="number" step="0.1" v-model.number="productData.car_engine_size" class="input-style" placeholder="2.0" />
      </div>

      <!-- Cylinders -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Cylinders</label>
        <input type="number" v-model.number="productData.car_engine_cylinders" class="input-style" placeholder="4" />
      </div>

      <!-- Drivetrain -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Drivetrain</label>
        <select v-model="productData.car_drivetrain" class="input-style">
          <option value="">Select drivetrain</option>
          <option value="FWD">FWD (Front Wheel Drive)</option>
          <option value="RWD">RWD (Rear Wheel Drive)</option>
          <option value="AWD">AWD (All Wheel Drive)</option>
          <option value="4WD">4WD (Four Wheel Drive)</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Battery Section (only for Electric/Hybrid) -->
  <div v-if="productData.car_fuel_type === 'Electric' || productData.car_fuel_type === 'Hybrid' || productData.car_fuel_type === 'Plug-in Hybrid'"
       class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
      <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
      Battery & Electric
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Range (miles)</label>
        <input type="number" v-model.number="productData.car_battery_range" class="input-style" placeholder="389" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Battery Capacity (kWh)</label>
        <input type="number" step="0.1" v-model.number="productData.car_battery_capacity" class="input-style" placeholder="75" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Charge Time (0-100%)</label>
        <input type="text" v-model="productData.car_charge_time_full" class="input-style" placeholder="11h 21m" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Charge (0-80%)</label>
        <input type="text" v-model="productData.car_quick_charge_time" class="input-style" placeholder="1h 17m" />
      </div>
    </div>
  </div>
</div>
```

---

### 5. Template - ÉTAPE 2 (currentStep === 2): Performance & Caractéristiques

Ajouter section Car pour l'étape 2:

```vue
<!-- ✅ CAR: Performance & Features (Step 2) -->
<div v-if="isCarCategory" class="space-y-6">
  <!-- Performance Section -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Performance</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Top Speed (mph)</label>
        <input type="number" v-model.number="productData.car_top_speed" class="input-style" placeholder="111" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Engine Power (bhp)</label>
        <input type="number" v-model.number="productData.car_engine_power_bhp" class="input-style" placeholder="282" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Engine Power (kW)</label>
        <input type="number" v-model.number="productData.car_engine_power_kw" class="input-style" placeholder="210" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Torque (lbs/ft)</label>
        <input type="number" step="0.01" v-model.number="productData.car_engine_torque" class="input-style" placeholder="402" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">0-60 mph (seconds)</label>
        <input type="number" step="0.1" v-model.number="productData.car_acceleration" class="input-style" placeholder="4.8" />
      </div>
    </div>
  </div>

  <!-- Fuel Efficiency Section -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Fuel Efficiency</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">MPG City</label>
        <input type="number" step="0.1" v-model.number="productData.car_mpg_city" class="input-style" placeholder="28" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">MPG Highway</label>
        <input type="number" step="0.1" v-model.number="productData.car_mpg_highway" class="input-style" placeholder="35" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">MPG Combined</label>
        <input type="number" step="0.1" v-model.number="productData.car_mpg_combined" class="input-style" placeholder="31" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">CO2 Emissions (g/km)</label>
        <input type="number" v-model.number="productData.car_co2_emissions" class="input-style" placeholder="120" />
      </div>
    </div>
  </div>

  <!-- Colors & Interior Section -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Colors & Interior</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Exterior Color</label>
        <input type="text" v-model="productData.car_exterior_color" class="input-style" placeholder="Pearl White" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Interior Color</label>
        <input type="text" v-model="productData.car_interior_color" class="input-style" placeholder="Black" />
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">Interior Material</label>
        <input type="text" v-model="productData.car_interior_material" class="input-style" placeholder="Premium Leather" />
      </div>
    </div>
  </div>

  <!-- General Information Section -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">General Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Doors</label>
        <input type="number" v-model.number="productData.car_doors" class="input-style" placeholder="5" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Seats</label>
        <input type="number" v-model.number="productData.car_seats" class="input-style" placeholder="5" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Warranty (years)</label>
        <input type="number" v-model.number="productData.car_warranty_years" class="input-style" placeholder="3" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Warranty (miles)</label>
        <input type="number" v-model.number="productData.car_warranty_miles" class="input-style" placeholder="60000" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Group</label>
        <input type="text" v-model="productData.car_insurance_group" class="input-style" placeholder="29E" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Previous Owners</label>
        <input type="number" v-model.number="productData.car_previous_owners" class="input-style" placeholder="0" />
      </div>
    </div>
  </div>

  <!-- Service History Section -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Service History</h3>
    <textarea
      v-model="productData.car_service_history"
      rows="4"
      class="input-style"
      placeholder="Full service history available. Last service: January 2024. All maintenance up to date."
    ></textarea>
  </div>
</div>
```

---

### 6. Template - ÉTAPE 3 (currentStep === 3): Prix & Images

Pour les Cars, l'étape 3 peut réutiliser les sections Prix et Images existantes. Il suffit de s'assurer que la condition dans le template gère bien les 3 catégories (Truck, Trailer, Car).

---

## 🔄 Résumé des modifications

1. ✅ **Déjà fait**: 41+ champs ajoutés dans productData
2. ✅ **Déjà fait**: Computed `isCarCategory`
3. ✅ **Déjà fait**: Steps modifiés pour Car
4. ⏳ **À faire**: Fonction `decodeVIN()`
5. ⏳ **À faire**: Mise à jour de `handleSubmit()` avec champs Car
6. ⏳ **À faire**: Template Étape 0 - Data Entry Method
7. ⏳ **À faire**: Template Étape 1 - Vehicle Info
8. ⏳ **À faire**: Template Étape 2 - Performance
9. ⏳ **À faire**: Template Étape 3 - Prix & Images (réutiliser existant)

---

## 💡 Prochaines étapes

Dis-moi si tu veux que je:
1. Continue à modifier AddProductModal.vue avec toutes ces sections
2. Ou si tu préfères copier-coller manuellement depuis ce fichier de référence

Le fichier fera probablement plus de 3000 lignes après les ajouts complets!
