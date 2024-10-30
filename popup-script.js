
    const minus = document.getElementById('minus');
    const plus = document.getElementById('plus');
    const quantity = document.getElementById('quantity');
    const minusVip = document.getElementById('minus-vip');
    const plusVip = document.getElementById('plus-vip');
    const quantityVip = document.getElementById('quantity-vip');
    const total = document.getElementById('total');
    const standardQuantity = document.getElementById('standard-quantity');
    const vipQuantity = document.getElementById('vip-quantity');

    minus.addEventListener('click', () => {
      if (quantity.value > 1) {
        quantity.value--;
        updateTotal();
      }
    });

    plus.addEventListener('click', () => {
      if (quantity.value < 10) {
        quantity.value++;
        updateTotal();
      }
    });

    minusVip.addEventListener('click', () => {
      if (quantityVip.value > 1) {
        quantityVip.value--;
        updateTotal();
      }
    });

    plusVip.addEventListener('click', () => {
      if (quantityVip.value < 10) {
        quantityVip.value++;
        updateTotal();
      }
    });

    function updateTotal() {
      const standardPrice = 10;
      const vipPrice = 20;
      const standardQuantityValue = parseInt(quantity.value);
      const vipQuantityValue = parseInt(quantityVip.value);
      const totalValue = standardPrice * standardQuantityValue + vipPrice * vipQuantityValue;
      total.textContent = totalValue;
      standardQuantity.textContent = standardQuantityValue;
      vipQuantity.textContent = vipQuantityValue;
    }

    updateTotal();