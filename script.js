// Maximale Anzahl an Tickets pro Bestellung
const maxTickets = 20;

// Alle Mengenfelder holen
const qtyInputs = document.querySelectorAll('.ticket-qty');

// Event: Eingabe per Tastatur
qtyInputs.forEach(input => {
    input.addEventListener('input', updateCart);
});

// Plus-Buttons
document.querySelectorAll('.qty-plus').forEach(button => {
    button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('.ticket-qty');

        if (getTotalQty() < maxTickets) {
            input.value = parseInt(input.value || 0) + 1;
            updateCart();
        } else {
            Swal.fire(
                'Limit erreicht',
                'Maximal 20 Tickets pro Bestellung erlaubt',
                'warning'
            );
        }
    });
});

// Minus-Buttons
document.querySelectorAll('.qty-minus').forEach(button => {
    button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('.ticket-qty');
        input.value = Math.max(0, parseInt(input.value || 0) - 1);
        updateCart();
    });
});

// Gesamtanzahl aller Tickets berechnen
function getTotalQty() {
    let total = 0;
    qtyInputs.forEach(input => {
        total += parseInt(input.value || 0);
    });
    return total;
}

// Warenkorb & Gesamtsumme aktualisieren
function updateCart() {
    const cart = document.getElementById('cart');
    const totalField = document.getElementById('total');

    let totalPrice = 0;
    cart.innerHTML = '';

    // Sicherheitsprüfung für Tastatureingabe
    if (getTotalQty() > maxTickets) {
        Swal.fire(
            'Limit erreicht',
            'Maximal 20 Tickets pro Bestellung erlaubt',
            'warning'
        );
        return;
    }

    document.querySelectorAll('.ticket-row').forEach(row => {
        const qty = parseInt(row.querySelector('.ticket-qty').value || 0);
        const price = parseFloat(row.dataset.price);
        const label = row.children[1].innerText;

        if (qty > 0) {
            const sum = qty * price;
            totalPrice += sum;

            cart.innerHTML += `
                <div>
                    ${label}: ${qty} Stück = ${sum} €
                </div>
            `;
        }
    });

    totalField.innerText = totalPrice;
}

// E-Mail Validierung beim Verlassen des Feldes
const emailInput = document.getElementById('email');
emailInput.addEventListener('blur', () => {
    if (!emailInput.checkValidity()) {
        Swal.fire(
            'E-Mail Prüfung Hinweis',
            'Die E-Mail-Adresse scheint nicht korrekt zu sein, bitte geben sie eine gültige Mailadresse an!',
            'warning'
        );
    }
});

document.getElementById('ticketForm').addEventListener('submit', function (e) {
    // Prüfen ob Tickets gewählt wurden
    if (getTotalQty() === 0) {
        e.preventDefault();

        Swal.fire(
            'Keine Tickets ausgewählt',
            'Bitte wählen Sie mindestens ein Ticket aus, um fortzufahren.',
            'info'
        );
        return;
    }
    
    const agb = document.getElementById('agb');

    if (!agb.checked) {
        e.preventDefault();

        Swal.fire(
            'AGB zustimmen',
            'Bitte akzeptieren Sie die AGB, um fortzufahren.',
            'warning'
        );
    }
});
