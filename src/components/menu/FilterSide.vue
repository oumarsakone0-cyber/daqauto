<template>
  <div class="modern-filter-sidebar">
    <!-- Tabs -->
    <div class="tabs-container">
      <button
        @click="switchTab('truck')"
        :class="['tab-button', { active: activeTab === 'truck' }]"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="1" y="3" width="15" height="13"></rect>
          <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
          <circle cx="5.5" cy="18.5" r="2.5"></circle>
          <circle cx="18.5" cy="18.5" r="2.5"></circle>
        </svg>
        <span>Trucks</span>
        <div v-if="activeTab === 'truck'" class="tab-indicator" />
      </button>
      <button
        @click="switchTab('car')"
        :class="['tab-button', { active: activeTab === 'car' }]"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 17h14v-5l-1.5-4.5h-11L5 12v5z"/>
          <circle cx="7" cy="17" r="2"/>
          <circle cx="17" cy="17" r="2"/>
        </svg>
        <span>Cars</span>
        <div v-if="activeTab === 'car'" class="tab-indicator" />
      </button>
    </div>

    <!-- Header -->
    <div class="filter-header">
      <div class="header-content">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
        </svg>
        <h3 class="filter-title">Filters</h3>
      </div>
      <button v-if="hasActiveFilters" @click="resetFilters" class="reset-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
        </svg>
        <span>Reset</span>
      </button>
    </div>

    <!-- Active Filters Count -->
    <div v-if="activeFiltersCount > 0" class="active-filters-badge">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>{{ activeFiltersCount }} filter{{ activeFiltersCount > 1 ? 's' : '' }} applied</span>
    </div>

    <!-- Filters Content -->
    <div class="filters-content">
      <!-- Truck Filters -->
      <div v-if="activeTab === 'truck'">
        <!-- Category -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('category')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
              </div>
              <h4 class="filter-section-title">Category</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.categories.length > 0" class="count-badge">{{ currentFilters.categories.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.category }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.category" class="filter-section-content">
              <label 
                v-for="category in currentData.categories" 
                :key="category" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="category" 
                  v-model="currentFilters.categories"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ category }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Brand (Make) -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('brand')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                  <path d="M2 12h20"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Brand</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.makes.length > 0" class="count-badge">{{ currentFilters.makes.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.brand }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.brand" class="filter-section-content">
              <div class="search-box">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input 
                  type="text" 
                  v-model="searchMake" 
                  placeholder="Search brand..."
                  class="search-input"
                />
              </div>
              <div class="scrollable-list">
                <label 
                  v-for="make in filteredMakes" 
                  :key="make" 
                  class="filter-checkbox-label"
                >
                  <input 
                    type="checkbox" 
                    :value="make" 
                    v-model="currentFilters.makes"
                    @change="emitFilters"
                    class="custom-checkbox"
                  />
                  <span class="checkbox-custom"></span>
                  <span class="checkbox-text">{{ make }}</span>
                </label>
              </div>
            </div>
          </transition>
        </div>

        <!-- Vehicle Condition -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('condition')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Vehicle Condition</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.conditions.length > 0" class="count-badge">{{ currentFilters.conditions.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.condition }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.condition" class="filter-section-content">
              <label 
                v-for="condition in currentData.conditions" 
                :key="condition" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="condition" 
                  v-model="currentFilters.conditions"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ condition }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Drivetrain -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('drivetrain')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <circle cx="12" cy="12" r="6"></circle>
                  <circle cx="12" cy="12" r="2"></circle>
                </svg>
              </div>
              <h4 class="filter-section-title">Drivetrain</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.drivetrains.length > 0" class="count-badge">{{ currentFilters.drivetrains.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.drivetrain }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.drivetrain" class="filter-section-content">
              <label 
                v-for="drive in currentData.drivetrains" 
                :key="drive" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="drive" 
                  v-model="currentFilters.drivetrains"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ drive }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Price -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('price')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23"></line>
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Price (FCFA)</h4>
            </div>
            <svg 
              class="chevron-icon" 
              :class="{ rotated: expandedSections.price }"
              width="16" 
              height="16" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.price" class="filter-section-content">
              <div class="range-inputs">
                <div class="input-group">
                  <label class="input-label">Minimum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.priceMin" 
                      @input="emitFilters"
                      placeholder="0"
                      class="range-input"
                    />
                  </div>
                </div>
                <div class="input-separator">—</div>
                <div class="input-group">
                  <label class="input-label">Maximum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.priceMax" 
                      @input="emitFilters"
                      placeholder="∞"
                      class="range-input"
                    />
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Fuel Type -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('fuelType')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 22V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v18"></path>
                  <path d="M13 10h4a2 2 0 0 1 2 2v10"></path>
                  <path d="M15 10V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"></path>
                  <circle cx="18" cy="18" r="2"></circle>
                </svg>
              </div>
              <h4 class="filter-section-title">Fuel Type</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.fuelTypes.length > 0" class="count-badge">{{ currentFilters.fuelTypes.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.fuelType }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.fuelType" class="filter-section-content">
              <label 
                v-for="fuel in currentData.fuelTypes" 
                :key="fuel" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="fuel" 
                  v-model="currentFilters.fuelTypes"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ fuel }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Transmission -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('transmission')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="5" r="1"></circle>
                  <circle cx="12" cy="12" r="1"></circle>
                  <circle cx="12" cy="19" r="1"></circle>
                  <path d="M3 12h9M12 5h9M12 19h9"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Transmission</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.transmissions.length > 0" class="count-badge">{{ currentFilters.transmissions.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.transmission }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.transmission" class="filter-section-content">
              <label 
                v-for="trans in currentData.transmissions" 
                :key="trans" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="trans" 
                  v-model="currentFilters.transmissions"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ trans }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Mileage -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('mileage')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <h4 class="filter-section-title">Mileage (km)</h4>
            </div>
            <svg 
              class="chevron-icon" 
              :class="{ rotated: expandedSections.mileage }"
              width="16" 
              height="16" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.mileage" class="filter-section-content">
              <div class="range-inputs">
                <div class="input-group">
                  <label class="input-label">Minimum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.mileageMin" 
                      @input="emitFilters"
                      placeholder="0"
                      class="range-input"
                    />
                  </div>
                </div>
                <div class="input-separator">—</div>
                <div class="input-group">
                  <label class="input-label">Maximum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.mileageMax" 
                      @input="emitFilters"
                      placeholder="∞"
                      class="range-input"
                    />
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Year -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('year')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <h4 class="filter-section-title">Year</h4>
            </div>
            <svg 
              class="chevron-icon" 
              :class="{ rotated: expandedSections.year }"
              width="16" 
              height="16" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.year" class="filter-section-content">
              <div class="range-inputs">
                <div class="input-group">
                  <label class="input-label">Minimum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.yearMin" 
                      @input="emitFilters"
                      :min="1990"
                      :max="currentYear"
                      placeholder="1990"
                      class="range-input"
                    />
                  </div>
                </div>
                <div class="input-separator">—</div>
                <div class="input-group">
                  <label class="input-label">Maximum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.yearMax" 
                      @input="emitFilters"
                      :min="1990"
                      :max="currentYear"
                      :placeholder="currentYear.toString()"
                      class="range-input"
                    />
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>

      <!-- Car Specific Filters -->
      <div v-if="activeTab === 'car'">
        <!-- Brand (Make) for Cars -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('brand')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                  <path d="M2 12h20"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Brand</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.makes.length > 0" class="count-badge">{{ currentFilters.makes.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.brand }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.brand" class="filter-section-content">
              <div class="search-box">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input 
                  type="text" 
                  v-model="searchMake" 
                  placeholder="Search brand..."
                  class="search-input"
                />
              </div>
              <div class="scrollable-list">
                <label 
                  v-for="make in filteredCarMakes" 
                  :key="make" 
                  class="filter-checkbox-label"
                >
                  <input 
                    type="checkbox" 
                    :value="make" 
                    v-model="currentFilters.makes"
                    @change="emitFilters"
                    class="custom-checkbox"
                  />
                  <span class="checkbox-custom"></span>
                  <span class="checkbox-text">{{ make }}</span>
                </label>
              </div>
            </div>
          </transition>
        </div>

        <!-- Vehicle Condition -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('condition')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Condition</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.conditions.length > 0" class="count-badge">{{ currentFilters.conditions.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.condition }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.condition" class="filter-section-content">
              <label 
                v-for="condition in currentData.conditions" 
                :key="condition" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="condition" 
                  v-model="currentFilters.conditions"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ condition }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Price -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('price')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23"></line>
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Price (FCFA)</h4>
            </div>
            <svg 
              class="chevron-icon" 
              :class="{ rotated: expandedSections.price }"
              width="16" 
              height="16" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.price" class="filter-section-content">
              <div class="range-inputs">
                <div class="input-group">
                  <label class="input-label">Minimum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.priceMin" 
                      @input="emitFilters"
                      placeholder="0"
                      class="range-input"
                    />
                  </div>
                </div>
                <div class="input-separator">—</div>
                <div class="input-group">
                  <label class="input-label">Maximum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.priceMax" 
                      @input="emitFilters"
                      placeholder="∞"
                      class="range-input"
                    />
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Mileage -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('mileage')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <h4 class="filter-section-title">Mileage (km)</h4>
            </div>
            <svg 
              class="chevron-icon" 
              :class="{ rotated: expandedSections.mileage }"
              width="16" 
              height="16" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.mileage" class="filter-section-content">
              <div class="range-inputs">
                <div class="input-group">
                  <label class="input-label">Minimum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.mileageMin" 
                      @input="emitFilters"
                      placeholder="0"
                      class="range-input"
                    />
                  </div>
                </div>
                <div class="input-separator">—</div>
                <div class="input-group">
                  <label class="input-label">Maximum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.mileageMax" 
                      @input="emitFilters"
                      placeholder="∞"
                      class="range-input"
                    />
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Year -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('year')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <h4 class="filter-section-title">Year</h4>
            </div>
            <svg 
              class="chevron-icon" 
              :class="{ rotated: expandedSections.year }"
              width="16" 
              height="16" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.year" class="filter-section-content">
              <div class="range-inputs">
                <div class="input-group">
                  <label class="input-label">Minimum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.yearMin" 
                      @input="emitFilters"
                      :min="1990"
                      :max="currentYear"
                      placeholder="1990"
                      class="range-input"
                    />
                  </div>
                </div>
                <div class="input-separator">—</div>
                <div class="input-group">
                  <label class="input-label">Maximum</label>
                  <div class="input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="currentFilters.yearMax" 
                      @input="emitFilters"
                      :min="1990"
                      :max="currentYear"
                      :placeholder="currentYear.toString()"
                      class="range-input"
                    />
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Exterior Color -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('exteriorColor')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Exterior Color</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.exteriorColors.length > 0" class="count-badge">{{ currentFilters.exteriorColors.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.exteriorColor }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.exteriorColor" class="filter-section-content">
              <label 
                v-for="color in currentData.colors" 
                :key="color" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="color" 
                  v-model="currentFilters.exteriorColors"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ color }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Interior Color -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('interiorColor')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="8" width="18" height="12" rx="1"></rect>
                  <path d="M7 8V6a2 2 0 012-2h6a2 2 0 012 2v2"></path>
                  <line x1="3" y1="14" x2="21" y2="14"></line>
                </svg>
              </div>
              <h4 class="filter-section-title">Interior Color</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.interiorColors.length > 0" class="count-badge">{{ currentFilters.interiorColors.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.interiorColor }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.interiorColor" class="filter-section-content">
              <label 
                v-for="color in currentData.colors" 
                :key="color" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="color" 
                  v-model="currentFilters.interiorColors"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ color }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Body Type -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('bodyType')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 17h14v-5l-1.5-4.5h-11L5 12v5z"/>
                  <circle cx="7" cy="17" r="2"/>
                  <circle cx="17" cy="17" r="2"/>
                </svg>
              </div>
              <h4 class="filter-section-title">Body Type</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.bodyTypes.length > 0" class="count-badge">{{ currentFilters.bodyTypes.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.bodyType }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.bodyType" class="filter-section-content">
              <label 
                v-for="type in currentData.bodyTypes" 
                :key="type" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="type" 
                  v-model="currentFilters.bodyTypes"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ type }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Fuel Type -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('fuelType')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 22V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v18"></path>
                  <path d="M13 10h4a2 2 0 0 1 2 2v10"></path>
                  <path d="M15 10V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"></path>
                  <circle cx="18" cy="18" r="2"></circle>
                </svg>
              </div>
              <h4 class="filter-section-title">Fuel Type</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.fuelTypes.length > 0" class="count-badge">{{ currentFilters.fuelTypes.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.fuelType }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.fuelType" class="filter-section-content">
              <label 
                v-for="fuel in currentData.carFuelTypes" 
                :key="fuel" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="fuel" 
                  v-model="currentFilters.fuelTypes"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ fuel }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Transmission -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('transmission')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="5" r="1"></circle>
                  <circle cx="12" cy="12" r="1"></circle>
                  <circle cx="12" cy="19" r="1"></circle>
                  <path d="M3 12h9M12 5h9M12 19h9"></path>
                </svg>
              </div>
              <h4 class="filter-section-title">Transmission</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.transmissions.length > 0" class="count-badge">{{ currentFilters.transmissions.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.transmission }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.transmission" class="filter-section-content">
              <label 
                v-for="trans in currentData.transmissions" 
                :key="trans" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="trans" 
                  v-model="currentFilters.transmissions"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ trans }}</span>
              </label>
            </div>
          </transition>
        </div>

        <!-- Drivetrain -->
        <div class="filter-section">
          <button class="filter-section-header" @click="toggleSection('drivetrain')">
            <div class="header-left">
              <div class="icon-wrapper">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <circle cx="12" cy="12" r="6"></circle>
                  <circle cx="12" cy="12" r="2"></circle>
                </svg>
              </div>
              <h4 class="filter-section-title">Drivetrain</h4>
            </div>
            <div class="header-right">
              <span v-if="currentFilters.drivetrains.length > 0" class="count-badge">{{ currentFilters.drivetrains.length }}</span>
              <svg 
                class="chevron-icon" 
                :class="{ rotated: expandedSections.drivetrain }"
                width="16" 
                height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </button>
          <transition name="slide-fade">
            <div v-show="expandedSections.drivetrain" class="filter-section-content">
              <label 
                v-for="drive in currentData.carDrivetrains" 
                :key="drive" 
                class="filter-checkbox-label"
              >
                <input 
                  type="checkbox" 
                  :value="drive" 
                  v-model="currentFilters.drivetrains"
                  @change="emitFilters"
                  class="custom-checkbox"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">{{ drive }}</span>
              </label>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export default {
  name: 'FilterSide',
  emits: ['filter-change'],
  setup(props, { emit }) {
    const route = useRoute()
    const router = useRouter()
    
    // Constants - CATEGORY IDs
    const TRUCK_CATEGORY_ID = '13'
    const CAR_CATEGORY_ID = '20'
    
    const currentYear = new Date().getFullYear()
    const activeTab = ref('truck')
    const searchMake = ref('')

    const expandedSections = ref({
      category: true,
      brand: true,
      condition: true,
      drivetrain: true,
      price: true,
      fuelType: false,
      transmission: false,
      mileage: false,
      year: false,
      exteriorColor: false,
      interiorColor: false,
      bodyType: false
    })

    const filters = ref({
      truck: {
        categories: [],
        makes: [],
        conditions: [],
        drivetrains: [],
        priceMin: null,
        priceMax: null,
        fuelTypes: [],
        transmissions: [],
        mileageMin: null,
        mileageMax: null,
        yearMin: null,
        yearMax: null
      },
      car: {
        makes: [],
        conditions: [],
        priceMin: null,
        priceMax: null,
        mileageMin: null,
        mileageMax: null,
        yearMin: null,
        yearMax: null,
        exteriorColors: [],
        interiorColors: [],
        bodyTypes: [],
        fuelTypes: [],
        transmissions: [],
        drivetrains: []
      }
    })

    const data = {
      truck: {
        categories: ['Cargo Truck', 'Dump Truck', 'Tractor Truck', 'Concrete Mixer', 'Tanker Truck', 'Refrigerated Truck', 'Flatbed Truck', 'Box Truck'],
        makes: ['Shacman', 'Sinotruk', 'FAW', 'Dongfeng', 'Foton', 'JAC', 'Howo', 'Beiben', 'Isuzu', 'Mercedes-Benz', 'Volvo', 'Scania', 'MAN', 'DAF', 'Iveco'],
        conditions: ['New', 'Used', 'Refurbished'],
        drivetrains: ['4x2', '4x4', '6x2', '6x4', '6x6', '8x4', '8x8'],
        fuelTypes: ['Diesel', 'Electric', 'Hybrid', 'CNG', 'LPG'],
        transmissions: ['Manual', 'Automatic', 'Semi-Automatic']
      },
      car: {
        makes: ['Toyota', 'Honda', 'Nissan', 'Hyundai', 'Kia', 'Mercedes-Benz', 'BMW', 'Audi', 'Volkswagen', 'Ford', 'Chevrolet', 'Peugeot', 'Renault', 'Mazda', 'Suzuki'],
        conditions: ['New', 'Used', 'Certified Pre-Owned', 'Refurbished'],
        colors: ['Black', 'White', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Brown', 'Beige', 'Gold', 'Orange', 'Yellow'],
        bodyTypes: ['Sedan', 'SUV', 'Hatchback', 'Coupe', 'Convertible', 'Wagon', 'Van', 'Pickup Truck', 'Minivan', 'Crossover'],
        carFuelTypes: ['Gasoline', 'Diesel', 'Electric', 'Hybrid', 'Plug-in Hybrid', 'CNG', 'LPG'],
        transmissions: ['Manual', 'Automatic', 'CVT', 'Semi-Automatic', 'Dual-Clutch'],
        carDrivetrains: ['FWD', 'RWD', 'AWD', '4WD']
      }
    }

    const currentFilters = computed(() => filters.value[activeTab.value])
    const currentData = computed(() => data[activeTab.value])

    const filteredMakes = computed(() => {
      if (!searchMake.value) {
        return currentData.value.makes || []
      }
      const search = searchMake.value.toLowerCase()
      return (currentData.value.makes || []).filter(make => 
        make.toLowerCase().includes(search)
      )
    })

    const filteredCarMakes = computed(() => {
      if (!searchMake.value) {
        return data.car.makes || []
      }
      const search = searchMake.value.toLowerCase()
      return (data.car.makes || []).filter(make => 
        make.toLowerCase().includes(search)
      )
    })

    const activeFiltersCount = computed(() => {
      const f = currentFilters.value
      let count = 0
      
      if (activeTab.value === 'truck') {
        count += f.categories?.length || 0
        count += f.makes?.length || 0
        count += f.conditions?.length || 0
        count += f.drivetrains?.length || 0
        count += f.fuelTypes?.length || 0
        count += f.transmissions?.length || 0
        if (f.priceMin !== null && f.priceMin !== '') count++
        if (f.priceMax !== null && f.priceMax !== '') count++
        if (f.mileageMin !== null && f.mileageMin !== '') count++
        if (f.mileageMax !== null && f.mileageMax !== '') count++
        if (f.yearMin !== null && f.yearMin !== '') count++
        if (f.yearMax !== null && f.yearMax !== '') count++
      } else {
        count += f.makes?.length || 0
        count += f.conditions?.length || 0
        count += f.exteriorColors?.length || 0
        count += f.interiorColors?.length || 0
        count += f.bodyTypes?.length || 0
        count += f.fuelTypes?.length || 0
        count += f.transmissions?.length || 0
        count += f.drivetrains?.length || 0
        if (f.priceMin !== null && f.priceMin !== '') count++
        if (f.priceMax !== null && f.priceMax !== '') count++
        if (f.mileageMin !== null && f.mileageMin !== '') count++
        if (f.mileageMax !== null && f.mileageMax !== '') count++
        if (f.yearMin !== null && f.yearMin !== '') count++
        if (f.yearMax !== null && f.yearMax !== '') count++
      }
      
      return count
    })

    const hasActiveFilters = computed(() => activeFiltersCount.value > 0)

    const toggleSection = (section) => {
      expandedSections.value[section] = !expandedSections.value[section]
    }

    // Détecter le tab actif depuis la catégorie dans l'URL
    const detectTabFromCategory = () => {
      const categoryId = route.query.category
      
      if (categoryId === TRUCK_CATEGORY_ID) {
        activeTab.value = 'truck'
      } else if (categoryId === CAR_CATEGORY_ID) {
        activeTab.value = 'car'
      }
    }

    // Initialiser les filtres depuis les paramètres URL
    const initializeFiltersFromURL = () => {
      const query = route.query
      
      // Déterminer le tab basé sur la catégorie
      if (query.category === TRUCK_CATEGORY_ID) {
        activeTab.value = 'truck'
      } else if (query.category === CAR_CATEGORY_ID) {
        activeTab.value = 'car'
      }

      // Récupérer les filtres communs depuis l'URL
      const f = currentFilters.value
      
      if (query.min_price) f.priceMin = parseFloat(query.min_price)
      if (query.max_price) f.priceMax = parseFloat(query.max_price)
      if (query.vehicle_year_min) f.yearMin = parseInt(query.vehicle_year_min)
      if (query.vehicle_year_max) f.yearMax = parseInt(query.vehicle_year_max)
      if (query.vehicle_mileage_min) f.mileageMin = parseInt(query.vehicle_mileage_min)
      if (query.vehicle_mileage_max) f.mileageMax = parseInt(query.vehicle_mileage_max)
      
      // Filtres spécifiques au véhicule
      if (query.vehicle_make) f.makes = query.vehicle_make.split(',')
      if (query.vehicle_condition) f.conditions = query.vehicle_condition.split(',')
      if (query.fuel_type) f.fuelTypes = query.fuel_type.split(',')
      if (query.transmission_type) f.transmissions = query.transmission_type.split(',')
      if (query.drive_type) f.drivetrains = query.drive_type.split(',')
      
      // Filtres spécifiques aux camions
      if (activeTab.value === 'truck') {
        if (query.subcategories) f.categories = query.subcategories.split(',')
      }
      
      // Filtres spécifiques aux voitures
      if (activeTab.value === 'car') {
        if (query.car_exterior_color) f.exteriorColors = query.car_exterior_color.split(',')
        if (query.car_interior_color) f.interiorColors = query.car_interior_color.split(',')
        if (query.car_body_type) f.bodyTypes = query.car_body_type.split(',')
      }
    }

    // Émettre les filtres vers le parent et mettre à jour l'URL
    const emitFilters = () => {
      const f = currentFilters.value
      
      // Objet de filtres avec la catégorie appropriée (13 pour truck, 20 pour car)
      const filterObj = {
        vehicleType: activeTab.value,
        category: activeTab.value === 'truck' ? TRUCK_CATEGORY_ID : CAR_CATEGORY_ID
      }

      // Filtres communs
      if (f.priceMin !== null && f.priceMin !== '') {
        filterObj.minPrice = f.priceMin
      }
      if (f.priceMax !== null && f.priceMax !== '') {
        filterObj.maxPrice = f.priceMax
      }
      if (f.mileageMin !== null && f.mileageMin !== '') {
        filterObj.vehicleMileageMin = f.mileageMin
      }
      if (f.mileageMax !== null && f.mileageMax !== '') {
        filterObj.vehicleMileageMax = f.mileageMax
      }
      if (f.yearMin !== null && f.yearMin !== '') {
        filterObj.vehicleYearMin = f.yearMin
      }
      if (f.yearMax !== null && f.yearMax !== '') {
        filterObj.vehicleYearMax = f.yearMax
      }
      if (f.makes && f.makes.length > 0) {
        filterObj.vehicleMake = f.makes.join(',')
      }
      if (f.conditions && f.conditions.length > 0) {
        filterObj.vehicleCondition = f.conditions.join(',')
      }
      if (f.fuelTypes && f.fuelTypes.length > 0) {
        filterObj.fuelType = f.fuelTypes.join(',')
      }
      if (f.transmissions && f.transmissions.length > 0) {
        filterObj.transmissionType = f.transmissions.join(',')
      }
      if (f.drivetrains && f.drivetrains.length > 0) {
        filterObj.driveType = f.drivetrains.join(',')
      }

      // Filtres spécifiques aux camions
      if (activeTab.value === 'truck') {
        if (f.categories && f.categories.length > 0) {
          filterObj.subcategories = f.categories.join(',')
        }
      }
      
      // Filtres spécifiques aux voitures
      if (activeTab.value === 'car') {
        if (f.exteriorColors && f.exteriorColors.length > 0) {
          filterObj.carExteriorColor = f.exteriorColors.join(',')
        }
        if (f.interiorColors && f.interiorColors.length > 0) {
          filterObj.carInteriorColor = f.interiorColors.join(',')
        }
        if (f.bodyTypes && f.bodyTypes.length > 0) {
          filterObj.carBodyType = f.bodyTypes.join(',')
        }
      }

      console.log('[FilterSide] Émission des filtres:', filterObj)
      
      // Mettre à jour l'URL avec les paramètres de filtrage
      updateURLParams(filterObj)
      
      // Émettre l'événement vers le parent
      emit('filter-change', filterObj)
    }

    // Mettre à jour les paramètres URL
    const updateURLParams = (filterObj) => {
      const query = { ...route.query }
      
      // Mettre à jour la catégorie
      query.category = filterObj.category
      
      // Mettre à jour les autres paramètres
      if (filterObj.minPrice) query.min_price = filterObj.minPrice
      else delete query.min_price
      
      if (filterObj.maxPrice) query.max_price = filterObj.maxPrice
      else delete query.max_price
      
      if (filterObj.vehicleMileageMin) query.vehicle_mileage_min = filterObj.vehicleMileageMin
      else delete query.vehicle_mileage_min
      
      if (filterObj.vehicleMileageMax) query.vehicle_mileage_max = filterObj.vehicleMileageMax
      else delete query.vehicle_mileage_max
      
      if (filterObj.vehicleYearMin) query.vehicle_year_min = filterObj.vehicleYearMin
      else delete query.vehicle_year_min
      
      if (filterObj.vehicleYearMax) query.vehicle_year_max = filterObj.vehicleYearMax
      else delete query.vehicle_year_max
      
      if (filterObj.vehicleMake) query.vehicle_make = filterObj.vehicleMake
      else delete query.vehicle_make
      
      if (filterObj.vehicleCondition) query.vehicle_condition = filterObj.vehicleCondition
      else delete query.vehicle_condition
      
      if (filterObj.fuelType) query.fuel_type = filterObj.fuelType
      else delete query.fuel_type
      
      if (filterObj.transmissionType) query.transmission_type = filterObj.transmissionType
      else delete query.transmission_type
      
      if (filterObj.driveType) query.drive_type = filterObj.driveType
      else delete query.drive_type
      
      if (filterObj.subcategories) query.subcategories = filterObj.subcategories
      else delete query.subcategories
      
      if (filterObj.carExteriorColor) query.car_exterior_color = filterObj.carExteriorColor
      else delete query.car_exterior_color
      
      if (filterObj.carInteriorColor) query.car_interior_color = filterObj.carInteriorColor
      else delete query.car_interior_color
      
      if (filterObj.carBodyType) query.car_body_type = filterObj.carBodyType
      else delete query.car_body_type
      
      // Réinitialiser la page à 1 lors du changement de filtres
      query.page = '1'
      
      router.replace({ path: route.path, query })
    }

    const resetFilters = () => {
      if (activeTab.value === 'truck') {
        filters.value.truck = {
          categories: [],
          makes: [],
          conditions: [],
          drivetrains: [],
          priceMin: null,
          priceMax: null,
          fuelTypes: [],
          transmissions: [],
          mileageMin: null,
          mileageMax: null,
          yearMin: null,
          yearMax: null
        }
      } else {
        filters.value.car = {
          makes: [],
          conditions: [],
          priceMin: null,
          priceMax: null,
          mileageMin: null,
          mileageMax: null,
          yearMin: null,
          yearMax: null,
          exteriorColors: [],
          interiorColors: [],
          bodyTypes: [],
          fuelTypes: [],
          transmissions: [],
          drivetrains: []
        }
      }
      searchMake.value = ''
      emitFilters()
    }

    const switchTab = (tab) => {
      // Ne pas émettre si c'est le même tab
      if (activeTab.value === tab) return
      
      activeTab.value = tab
      searchMake.value = ''
      
      // Réinitialiser les filtres du nouveau tab
      resetFilters()
    }

    // Surveiller les changements de catégorie dans l'URL
    watch(() => route.query.category, (newCategory) => {
      if (newCategory === TRUCK_CATEGORY_ID && activeTab.value !== 'truck') {
        activeTab.value = 'truck'
      } else if (newCategory === CAR_CATEGORY_ID && activeTab.value !== 'car') {
        activeTab.value = 'car'
      }
    })

    onMounted(() => {
      detectTabFromCategory()
      initializeFiltersFromURL()
      emitFilters()
    })

    return {
      currentYear,
      activeTab,
      searchMake,
      expandedSections,
      filters,
      currentFilters,
      currentData,
      filteredMakes,
      filteredCarMakes,
      hasActiveFilters,
      activeFiltersCount,
      toggleSection,
      emitFilters,
      resetFilters,
      switchTab
    }
  }
}
</script>

<style scoped>
/* Modern Filter Sidebar */
.modern-filter-sidebar {
  background: #ffffff;
  border-radius: 16px;
  width: 320px;
  height: fit-content;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 2px solid transparent;
  position: relative;
}

.modern-filter-sidebar::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 16px;
  padding: 2px;
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  opacity: 0.3;
  pointer-events: none;
}

/* Tabs */
.tabs-container {
  display: flex;
  background: linear-gradient(135deg, #f8f9fa, #e5e7eb);
  border-bottom: 2px solid #e5e7eb;
  padding: 6px;
  gap: 6px;
}

.tab-button {
  flex: 1;
  padding: 12px 16px;
  background: transparent;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.tab-button:hover {
  background: rgba(254, 121, 0, 0.1);
  color: #fe7900;
}

.tab-button.active {
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  color: white;
  box-shadow: 0 4px 12px rgba(254, 121, 0, 0.3);
}

.tab-button svg {
  transition: transform 0.3s ease;
}

.tab-button.active svg {
  transform: scale(1.1);
}

/* Header */
.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 20px;
  border-bottom: 2px solid #f3f4f6;
  background: linear-gradient(135deg, #ffffff, #f9fafb);
}

.header-content {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-content svg {
  color: #fe7900;
}

.filter-title {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.reset-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  border: none;
  color: #dc2626;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  padding: 8px 14px;
  border-radius: 8px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.reset-btn:hover {
  background: linear-gradient(135deg, #dc2626, #ef4444);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

/* Active Filters Badge */
.active-filters-badge {
  margin: 16px 20px 0;
  padding: 12px 16px;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

/* Filters Content */
.filters-content {
  padding: 12px 20px 20px;
}

.filters-content::-webkit-scrollbar {
  width: 6px;
}

.filters-content::-webkit-scrollbar-track {
  background: transparent;
}

.filters-content::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #d1d5db, #9ca3af);
  border-radius: 3px;
}

/* Filter Sections */
.filter-section {
  margin-bottom: 8px;
  border-radius: 12px;
  overflow: hidden;
  background: linear-gradient(135deg, #f9fafb, #f3f4f6);
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.filter-section:hover {
  border-color: #fe7900;
  box-shadow: 0 2px 8px rgba(254, 121, 0, 0.1);
}

.filter-section-header {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  border: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: all 0.3s ease;
}

.filter-section-header:hover {
  background: linear-gradient(135deg, #fff7ed, #ffedd5);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.icon-wrapper {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  border-radius: 8px;
  color: #fe7900;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.filter-section-header:hover .icon-wrapper {
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  color: white;
  transform: scale(1.1);
}

.filter-section-title {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin: 0;
  text-align: left;
}

.count-badge {
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  color: white;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
}

.chevron-icon {
  transition: transform 0.3s ease;
  color: #9ca3af;
  flex-shrink: 0;
}

.chevron-icon.rotated {
  transform: rotate(180deg);
  color: #fe7900;
}

.filter-section-content {
  padding: 12px 16px 16px;
  background: white;
}

/* Transitions */
.slide-fade-enter-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-leave-active {
  transition: all 0.2s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Custom Checkboxes */
.filter-checkbox-label {
  display: flex;
  align-items: center;
  padding: 10px 8px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 4px;
}

.filter-checkbox-label:hover {
  background: linear-gradient(135deg, #fff7ed, #ffedd5);
}

.custom-checkbox {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.checkbox-custom {
  width: 20px;
  height: 20px;
  border: 2px solid #d1d5db;
  border-radius: 6px;
  margin-right: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.custom-checkbox:checked + .checkbox-custom {
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  border-color: #fe7900;
}

.custom-checkbox:checked + .checkbox-custom::after {
  content: '✓';
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.checkbox-text {
  font-size: 13px;
  color: #374151;
  font-weight: 500;
}

/* Search Box */
.search-box {
  position: relative;
  margin-bottom: 12px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

.search-input {
  width: 100%;
  padding: 10px 12px 10px 40px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 13px;
  transition: all 0.3s ease;
  background: #f9fafb;
}

.search-input:focus {
  outline: none;
  border-color: #fe7900;
  background: white;
  box-shadow: 0 0 0 3px rgba(254, 121, 0, 0.1);
}

.scrollable-list {
  max-height: 200px;
  overflow-y: auto;
}

.scrollable-list::-webkit-scrollbar {
  width: 4px;
}

.scrollable-list::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}

/* Range Inputs */
.range-inputs {
  display: flex;
  align-items: flex-end;
  gap: 12px;
}

.input-group {
  flex: 1;
}

.input-label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.input-wrapper {
  position: relative;
}

.range-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
  background: #f9fafb;
}

.range-input:focus {
  outline: none;
  border-color: #fe7900;
  background: white;
  box-shadow: 0 0 0 3px rgba(254, 121, 0, 0.1);
}

.range-input::placeholder {
  color: #9ca3af;
}

.input-separator {
  color: #9ca3af;
  font-weight: 600;
  padding-bottom: 12px;
}

/* Hide number input spinners */
.range-input::-webkit-outer-spin-button,
.range-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.range-input[type=number] {
  -moz-appearance: textfield;
}
</style>
