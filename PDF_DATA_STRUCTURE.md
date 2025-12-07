# Structure de données pour les PDF (Proforma & Contract)

## Vue d'ensemble

Les fonctions `downloadProforma()` et `downloadContract()` dans les composables reçoivent maintenant des objets complets avec toutes les informations de la commande.

## 1. Structure pour Proforma Invoice

```javascript
{
  // Numéro et dates
  number: "CMD-1765014900032-AB",
  date: "2025-12-06 10:55:01",
  dueDate: "2025-01-05",

  // Informations client
  client: {
    name: "ADAM ODG",
    email: "daqauto08@gmail.com",
    phone: "daqauto08@gmail.com",
    address: "asdfg",
    city: "Abidjan",
    company: "Company Name (if any)"
  },

  // Informations fournisseur/boutique
  supplier: {
    name: "MALINGO",
    email: "ousmanek@outlook.com",
    phone: "+2250777988987",
    address: "Abidjan"
  },

  // Articles/Produits
  items: [{
    productId: 87,
    product_name: "Used Shacman M3000s Tractor Head 6x4 2020",
    quantity: 1,
    unit_price: 12000,  // Prix ajusté (ajust_price)
    total: 12000
  }],

  // Informations d'expédition
  shipping: {
    method: "CIF",
    cost: 0,  // shipping_cost
    loading_port: "asdfd",
    destination_port: "asdfg",
    incoterm: "CIF"
  },

  // Termes de paiement
  payment_terms: {
    deposit_percent: 30,
    deposit_amount: 3600.00,
    before_shipping_percent: 40,
    before_shipping_amount: 4800.00,
    against_bl_percent: 30,
    against_bl_amount: 3600.00
  },

  // Informations bancaires
  bank_info: {
    beneficiary_name: "John Doe",
    bank_name: "Bank of Africa",
    account_number: "CI00 1234 5678 9012",
    swift_code: "BOABCIAB",
    bank_address: "123 Avenue Street, Abidjan"
  },

  // Calculs
  subtotal: 12000,
  shipping_cost: 0,
  total: 12000,

  notes: "Payment is due according to the payment terms specified above."
}
```

## 2. Structure pour Contract

```javascript
{
  // Numéro et date
  number: "CMD-1765014900032-AB",
  date: "2025-12-06 10:55:01",

  // Acheteur (Buyer)
  buyer: {
    name: "ADAM ODG",
    email: "daqauto08@gmail.com",
    phone: "daqauto08@gmail.com",
    address: "asdfg",
    city: "Abidjan",
    company: "Company Name (if any)"
  },

  // Vendeur (Seller)
  seller: {
    name: "MALINGO",
    email: "ousmanek@outlook.com",
    phone: "+2250777988987",
    address: "Abidjan"
  },

  // Articles/Produits
  items: [{
    productId: 87,
    product_name: "Used Shacman M3000s Tractor Head 6x4 2020",
    quantity: 1,
    unit_price: 12000,  // Prix ajusté
    total: 12000
  }],

  // Informations d'expédition
  shipping: {
    method: "CIF",
    cost: 0,
    loading_port: "asdfd",
    destination_port: "asdfg",
    incoterm: "CIF",
    eta_destination: null,  // Date d'arrivée estimée
    etd_date: null  // Date de départ estimée
  },

  // Termes de paiement détaillés
  payment_terms: {
    // Dépôt initial
    deposit_percent: 30,
    deposit_amount: 3600.00,
    deposit_paid: false,
    deposit_paid_date: null,

    // Avant expédition
    before_shipping_percent: 40,
    before_shipping_amount: 4800.00,
    before_shipping_paid: false,
    before_shipping_paid_date: null,

    // Contre BL
    against_bl_percent: 30,
    against_bl_amount: 3600.00,
    against_bl_paid: false,
    against_bl_paid_date: null
  },

  // Informations bancaires
  bank_info: {
    beneficiary_name: "John Doe",
    bank_name: "Bank of Africa",
    account_number: "CI00 1234 5678 9012",
    swift_code: "BOABCIAB",
    bank_address: "123 Avenue Street, Abidjan"
  },

  // Documents
  documents: {
    bl_number: "",
    bl_date: null,
    bl_url: null
  },

  // Calculs
  subtotal: 12000,
  shipping_cost: 0,
  total: 12000,

  // Signatures
  signature_method: "online",
  contract_signed: false,
  contract_signed_date: null,

  notes: "This contract is legally binding. Please read all terms carefully before signing."
}
```

## 3. Modifications nécessaires dans les Composables

### ProformaInvoice.js

```javascript
export function downloadProforma(invoice) {
  // invoice contient maintenant toutes les informations

  // Exemple d'utilisation:
  const {
    number,
    date,
    client,
    supplier,
    items,
    shipping,
    payment_terms,
    bank_info,
    subtotal,
    shipping_cost,
    total,
    notes
  } = invoice

  // Générer le PDF avec toutes les sections:
  // 1. Header avec numéro et date
  // 2. Supplier info (From)
  // 3. Client info (To)
  // 4. Table des articles avec prix ajustés
  // 5. Section expédition (si shipping.cost > 0)
  // 6. Calculs (Subtotal, Shipping, Total)
  // 7. Payment Terms avec les 3 paiements
  // 8. Bank Information pour le paiement
  // 9. Notes
}
```

### contract.js

```javascript
export function downloadContract(contract) {
  // contract contient maintenant toutes les informations

  const {
    number,
    date,
    buyer,
    seller,
    items,
    shipping,
    payment_terms,
    bank_info,
    documents,
    subtotal,
    shipping_cost,
    total,
    signature_method,
    contract_signed,
    notes
  } = contract

  // Générer le contrat avec toutes les sections:
  // 1. Title "SALES CONTRACT"
  // 2. Contract number et date
  // 3. Parties (Seller & Buyer)
  // 4. Article 1: Products
  // 5. Article 2: Price and Payment Terms
  //    - Total amount
  //    - Deposit (30%)
  //    - Before shipping (40%)
  //    - Against BL (30%)
  // 6. Article 3: Shipping Terms
  //    - Incoterm (CIF)
  //    - Loading port
  //    - Destination port
  //    - Dates (ETD, ETA)
  // 7. Article 4: Bank Information
  // 8. Article 5: Documents (BL info)
  // 9. Signature section
}
```

## 4. Sections à afficher dans les PDF

### Proforma Invoice

1. **Header**
   - PROFORMA INVOICE
   - Invoice #: CMD-XXX
   - Date: YYYY-MM-DD

2. **From/To**
   - Supplier info (left)
   - Client info (right)

3. **Items Table**
   | Description | Qty | Unit Price | Total |
   |-------------|-----|------------|-------|
   | Product     | 1   | $12,000    | $12,000 |

4. **Shipping** (si applicable)
   - Method: CIF
   - Loading Port: xxx
   - Destination: xxx
   - Cost: $0

5. **Totals**
   - Subtotal: $12,000
   - Shipping: $0
   - **Total: $12,000**

6. **Payment Terms**
   - Deposit (30%): $3,600
   - Before Shipping (40%): $4,800
   - Against BL (30%): $3,600

7. **Bank Information**
   - Beneficiary: xxx
   - Bank: xxx
   - Account: xxx
   - SWIFT: xxx
   - Address: xxx

8. **Notes**

### Contract

1. **Header**
   - SALES CONTRACT
   - Contract #: CMD-XXX
   - Date: YYYY-MM-DD

2. **Parties**
   - SELLER: [info]
   - BUYER: [info]

3. **Article 1: Subject Matter**
   - Product details
   - Quantity and price

4. **Article 2: Price and Payment**
   - Total Contract Value: $12,000
   - Payment Schedule:
     * Deposit (30%): $3,600 - Status: [Paid/Unpaid]
     * Before Shipping (40%): $4,800 - Status: [Paid/Unpaid]
     * Against BL (30%): $3,600 - Status: [Paid/Unpaid]

5. **Article 3: Delivery Terms**
   - Incoterm: CIF
   - Port of Loading: xxx
   - Port of Destination: xxx
   - ETD: [date]
   - ETA: [date]

6. **Article 4: Payment Method**
   - Bank Information (table format)

7. **Article 5: Documents**
   - Bill of Lading #: xxx
   - BL Date: xxx

8. **Signatures**
   - Seller signature space
   - Buyer signature space
   - Method: [Online/Physical]

## 5. Points importants

### Prix
- **Toujours utiliser `ajust_price`** au lieu de `produit_prix`
- Si `ajust_price` n'est pas défini, utiliser `produit_prix` comme fallback

### Frais de transport
- Afficher uniquement si `shipping_cost > 0`
- Inclure dans le total

### Informations bancaires
- Vérifier que tous les champs sont remplis
- Afficher "To be provided" si manquant

### Payment Terms
- Afficher les 3 paiements avec pourcentages et montants
- Dans le contrat, montrer aussi le statut (Paid/Unpaid)

## 6. Exemple de mapping depuis la commande

```javascript
// Dans commandes-management.vue
const order = {
  ajust_price: "12000",  // PRIX À UTILISER
  produit_prix: 10000,   // Prix de base (ne pas utiliser)

  shipping_cost: "",     // Peut être vide

  banque_beneficiaire: "John Doe",  // Ou order.banque.beneficiaire
  banque_nom: "BOA",
  // etc...
}

// Devient dans l'invoice:
const invoice = {
  items: [{
    unit_price: parseFloat(order.ajust_price || order.produit_prix)
  }],

  shipping: {
    cost: parseFloat(order.shipping_cost || 0)
  },

  bank_info: {
    beneficiary_name: order.banque_beneficiaire || order.banque?.beneficiaire || ''
  }
}
```

## 7. Checklist pour les Composables

- [ ] Utiliser `invoice.supplier` au lieu de `invoice.seller` pour Proforma
- [ ] Utiliser `contract.buyer` et `contract.seller` pour Contract
- [ ] Afficher les payment terms en 3 lignes avec pourcentages
- [ ] Ajouter section Bank Information avec tous les champs
- [ ] Afficher shipping info (ports, incoterm)
- [ ] Utiliser `items[0].unit_price` (déjà ajusté)
- [ ] Calculer: Subtotal + Shipping = Total
- [ ] Pour le contrat: afficher statut des paiements (Paid/Unpaid)
