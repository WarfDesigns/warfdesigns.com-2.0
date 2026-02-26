function Calculators(formId, priceId, quantityId, totalId) {
    document.getElementById(formId).addEventListener('input', function () {
    let price = parseFloat(document.getElementById(priceId).value) || 0;
    const quantity = parseInt(document.getElementById(quantityId).value) || 0;
    let subtotal = price * quantity;
    
    // Add Discounts
    if (formId === 'VHSCalculator' && quantity >= 100) {
      subtotal *= 0.9; // 10% discount for 100 or more VHS tapes
    }

    if (formId === 'ReelCalculator' && quantity >= 100) {
    const discountPerReel = 5;
      subtotal = (price - discountPerReel) * quantity;
    }

    const tax = subtotal * 0.06;
    const total = subtotal + tax;

    document.getElementById(totalId).innerText = total.toFixed(2);
    });
}

// Creating Calculators with matching IDs
Calculators('VHSCalculator', 'VHSprice', 'VHSAmount', 'VHStotal');
Calculators('ReelCalculator', 'ReelPrice', 'ReelAmount', 'ReelTotal');
