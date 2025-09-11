export function formatPrice(value) {
    if (typeof value !== 'number') {
      value = parseFloat(value);
      if (isNaN(value)) return '0 FCFA';
    }
  
    return value.toLocaleString('fr-FR', {
      style: 'currency',
      currency: 'XOF',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).replace('XOF', 'FCFA');
  }