# DaqAuto – Automotive Parts E‑commerce Platform

A Vue 3 + Vite automotive parts e‑commerce platform with VIN decoding, product management, orders, and more.

## Features

- **VIN decode** – Decode 17‑character VIN; fill vehicle info automatically with optional vehicle selection modal
- **Product management** – Add, edit, delete products
- **Shopping cart** – Multi‑item cart
- **Analytics** – Product and order stats
- **Chat** – WebSocket‑based support chat
- **Translation** – Auto‑translate (e.g. Chinese → English)
- **Responsive** – Mobile and desktop

## Quick start

### Requirements

- Node.js >= 16
- npm >= 7
- PHP >= 7.4 (for backend)
- MySQL >= 5.7

### Install and run

1. **Clone and enter project**
   ```bash
   git clone <repository-url>
   cd daqauto-main
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Database config**
   ```bash
   cp config.example.php config.php
   # Edit config.php with your DB host, dbname, username, password
   ```

4. **Database schema**
   - Run `database/create_cart_table.sql` for cart table
   - Run `add_model_list_column.sql` to add `model_list` to VIN table if needed

5. **Start dev server**
   ```bash
   npm run dev
   ```
   Open **http://localhost:5173**.

### Local development and API proxy

To avoid “Error server connexion” and CORS when the frontend calls the backend:

- **`.env.development`** sets `VITE_API_BASE_URL=/api_adjame` so requests go to the same origin in dev.
- **Vite proxy** in `vite.config.js` forwards `/api_adjame` to your backend (e.g. `https://sastock.com`).

So in development, all API calls (categories, products, VIN decode, etc.) go to `http://localhost:5173/api_adjame/...` and Vite proxies them to the real backend. Restart the dev server after changing `vite.config.js` or `.env.development`.

## VIN decode (decodeVIN2)

### Flow

1. User enters a 17‑character VIN and clicks **Decode** in the Add Product modal.
2. Frontend calls **decodeVIN2** (POST to `products.php?action=decodeVIN2` with `{ vin }`).
3. Backend **decodeVIN2()** in `products.php`:
   - Looks up the VIN in table `vin_decoded_data`.
   - If found: returns cached data (English) with `source: 'database'`.
   - If not found: calls external Tanshu VIN API, maps the response to a standard format, and returns it with `source: 'api'` (no DB write).
4. Frontend fills the form and, when applicable, shows a **vehicle selection modal**:
   - If the response has `model_list` with one or more models → show selection dialog (one or multiple options).
   - If there is no `model_list` but vehicle info exists → show a single‑option selection so the user can confirm.
5. After the user selects a vehicle (or confirms the only option), the form is filled and the modal closes.

### Backend integration (products.php)

- Add the **decodeVIN2()** method to your `ProductsAPI` class. Full PHP code is in **`decodeVIN2_class_method.php`** (copy the whole `private function decodeVIN2() { ... }` into your class).
- In **handlePost()**, add:
  ```php
  case 'decodeVIN2':
      $this->decodeVIN2();
      break;
  ```
- Ensure DB config (in `decodeVIN2()` or via `config.php`) matches your environment. See **`ADD_DECODE_VIN2_TO_PRODUCTS_PHP.md`** for step‑by‑step instructions.

### Frontend

- **`src/services/api.js`** – `productsApi.decodeVIN2(vin)` sends POST to `products.php` with `action=decodeVIN2`.
- **`src/components/boutiques/AddProductModal.vue`** – `decodeVIN2(vin)` uses that API, handles `model_list`, shows the vehicle selection modal when there is at least one model or when there is vehicle info but no `model_list`, then fills the form (with translation for API‑sourced data).

### External VIN API

- **`src/services/vinDecodeApi.js`** – Calls Tanshu API (used when backend calls the external API). Proxy in Vite: `/api/vin` → `https://api.tanshuapi.com`.

## Project structure

```
daqauto-main/
├── src/
│   ├── components/
│   │   ├── boutiques/       # e.g. AddProductModal.vue (VIN decode UI)
│   │   ├── product/
│   │   └── ...
│   ├── composables/
│   ├── services/
│   │   ├── api.js          # productsApi.decodeVIN2, categories, etc.
│   │   └── vinDecodeApi.js # External Tanshu VIN API client
│   ├── stores/
│   └── router/
├── public/
├── database/
├── save_vin_data.php       # VIN save/retrieve endpoint (optional cache)
├── config.example.php      # Config template
├── decodeVIN2_class_method.php  # PHP decodeVIN2() to add to products.php
├── ADD_DECODE_VIN2_TO_PRODUCTS_PHP.md  # Integration steps
├── vite.config.js          # Dev server + proxy (/api_adjame, /api/vin)
├── .env.development        # VITE_API_BASE_URL=/api_adjame for local proxy
└── package.json
```

## Configuration

### Database

In `config.php` (see `config.example.php`):

```php
return [
    'database' => [
        'host'     => 'localhost',
        'dbname'   => 'your_database',
        'username' => 'your_user',
        'password' => 'your_password',
        'charset'  => 'utf8mb4'
    ],
    // ...
];
```

### VIN API

- Backend: `decodeVIN2()` uses Tanshu (URL and key in the PHP method; can be moved to config).
- Frontend external call: `vinDecodeApi.js`; proxy in `vite.config.js` for `/api/vin`.

## Production build and deploy

1. **Build**
   ```bash
   npm run build
   ```
2. Deploy the `dist/` output and your PHP backend (e.g. `products.php`, `save_vin_data.php`).
3. Ensure the production API base URL is correct (no `.env.development`; default in `api.js` is `https://sastock.com/api_adjame` or set `VITE_API_BASE_URL` in your build env).

## API overview

### VIN (decodeVIN2)

- **POST** `products.php?action=decodeVIN2`  
- Body: `{ "vin": "17-character-vin" }`  
- Response: `{ success, data, source: 'database'|'api' }` (and optional `raw_data` for API source).

### VIN save/retrieve (save_vin_data.php)

- **GET** `save_vin_data.php?action=get&vin=...` – get cached VIN data.
- **POST** `save_vin_data.php` – save VIN data (JSON body).

## Troubleshooting

- **“Error server connexion” / “Impossible to load categories”**  
  Use the dev proxy: `VITE_API_BASE_URL=/api_adjame` in `.env.development` and the `/api_adjame` proxy in `vite.config.js`; restart `npm run dev`.

- **VIN decode fails**  
  Check network, 17‑char VIN, and that `products.php` has `decodeVIN2()` and the `decodeVIN2` case in `handlePost()`. Check browser Network tab and backend logs.

- **Vehicle selection modal does not appear after decode**  
  The modal is shown when (a) `model_list` has at least one model, or (b) there is no `model_list` but vehicle info exists (single option). Check API response shape and console logs (e.g. “Showing vehicle selection dialog”).

- **CORS**  
  Backend must send appropriate CORS headers; using the proxy in dev avoids CORS for `/api_adjame`.

## License and contact

[Add your license and contact details.]

---

Before production:

- [ ] `config.php` created and correct
- [ ] DB migrations run (cart table, `model_list` if needed)
- [ ] `decodeVIN2()` and case added to `products.php` if using VIN decode
- [ ] Environment / proxy and API base URL verified
- [ ] Full flow tested (categories, add product, VIN decode, vehicle selection)
