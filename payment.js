
document.addEventListener('DOMContentLoaded', function () {
  try{
    // Préférence : récupérer les données depuis sessionStorage (évite d'exposer le montant dans l'URL)
    let usedFromStorage = false;
    const raw = sessionStorage.getItem('checkoutData');
    if(raw){
      try{
        const data = JSON.parse(raw);
        const amountInput = document.getElementById('amount');
        const amountCents = document.getElementById('amount_cents');
        const totalsDisplay = document.getElementById('totalsDisplay');
        const totalHTEl = document.getElementById('totalHT');
        const totalTTCEl = document.getElementById('totalTTC');
        if(data.amount && amountInput){
          // Legacy support: if 'amount' present use it as TTC
          amountInput.value = Number(data.amount).toFixed(2);
          amountInput.readOnly = true;
          amountInput.setAttribute('aria-readonly','true');
        }
        // New format: support total_ht / total_ttc
        if(data.total_ttc && amountInput){
          amountInput.value = Number(data.total_ttc).toFixed(2);
          amountInput.readOnly = true; amountInput.setAttribute('aria-readonly','true');
        }
        if(data.total_ttc && amountCents){
          amountCents.value = Math.round(Number(data.total_ttc) * 100);
        }
        if(totalsDisplay && data.total_ht && data.total_ttc){
          totalsDisplay.style.display = 'block';
          totalHTEl.textContent = Number(data.total_ht).toFixed(2);
          totalTTCEl.textContent = Number(data.total_ttc).toFixed(2);
        }
        if(Array.isArray(data.items) && data.items.length){
          const itemsList = document.getElementById('itemsList');
          const section = document.getElementById('selected-items');
          itemsList.innerHTML = '';
          data.items.forEach(it => {
            const li = document.createElement('li');
            if(it.price_ht !== undefined && it.price_ttc !== undefined){
              li.textContent = `${it.name} — ${it.price_ht}€ HT / ${it.price_ttc}€ TTC / mois`;
            }else if(it.price !== undefined){
              li.textContent = `${it.name} — ${it.price}€/mois`;
            }else{
              li.textContent = it.name;
            }
            itemsList.appendChild(li);
          });
          section.hidden = false;
        }
        usedFromStorage = true;
      }catch(e){/* ignore parse errors */}
    }
    // Fallback : si rien en sessionStorage, utiliser les paramètres d'URL (compatible ancien comportement)
    if(!usedFromStorage){
      const params = new URLSearchParams(location.search);
      const amountParam = params.get('amount');
      if(amountParam){
        const amountInput = document.getElementById('amount');
        const amountCents = document.getElementById('amount_cents');
        if(amountInput){
          amountInput.value = parseFloat(amountParam).toFixed(2);
          amountInput.readOnly = true;
          amountInput.setAttribute('aria-readonly','true');
        }
        if(amountCents){
          amountCents.value = Math.round(Number(amountParam) * 100);
        }
      }
      // Lire et afficher les modules sélectionnés (items=Module%20A|Module%20B)
      const itemsParam = params.get('items');
      if(itemsParam){
        const itemsList = document.getElementById('itemsList');
        const section = document.getElementById('selected-items');
        const parts = itemsParam.split('|').map(s => decodeURIComponent(s));
        itemsList.innerHTML = '';
        parts.forEach(it => {
          const li = document.createElement('li');
          li.textContent = it;
          itemsList.appendChild(li);
        });
        if(parts.length) section.hidden = false;
      }
    }
    // Nettoyer la query string pour ne pas exposer le montant dans l'URL
    try{
      if(location.search){
        // Remplace l'URL affichée sans recharger la page
        history.replaceState(null, '', location.pathname + location.hash);
      }
    }catch(e){/* ignore */}
  }catch(e){/* ignore */}
  const form = document.getElementById('payment-form');
  const payBtn = document.getElementById('payBtn');
  const msg = document.getElementById('message');

  function setMessage(text, isError){
    msg.textContent = text;
    msg.style.color = isError ? '#b00020' : '#117a37';
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    setMessage('', false);

    const fullName = form.fullName.value.trim();
    const email = form.email.value.trim();
    const amount = parseFloat(form.amount.value);
    const card = form.cardNumber.value.replace(/\s+/g,'');
    const exp = form.expDate.value.trim();
    const cvc = form.cvc.value.trim();

    if(!fullName || !email || isNaN(amount) || amount <= 0){
      setMessage('Veuillez renseigner un nom, email et un montant valide.', true);
      return;
    }
    if(!/^[0-9]{12,19}$/.test(card)){
      setMessage('Numéro de carte invalide (simulation).', true);
      return;
    }
    if(!/^[0-9]{3,4}$/.test(cvc)){
      setMessage('CVC invalide.', true);
      return;
    }

    payBtn.disabled = true;
    payBtn.textContent = 'Traitement...';

    // Simulate server call
    setTimeout(function(){
      payBtn.disabled = false;
      payBtn.textContent = 'Payer';
      setMessage('Paiement simulé réussi — merci ! Un reçu a été envoyé à ' + email + '.', false);
      // Nettoyer les données de checkout stockées
      try{ sessionStorage.removeItem('checkoutData'); }catch(e){}
      form.reset();
    }, 900);
  });
});
