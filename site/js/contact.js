const form = document.querySelector('#inquiry-form');
if (form) {
  const role = form.querySelector('#role');
  const commodity = form.querySelector('#commodity');
  const tradeFields = form.querySelector('#trade-fields');
  const portField = form.querySelector('#port-field');
  const status = form.querySelector('#form-status');
  const submitBtn = form.querySelector('#submit-btn');
  const fileInput = form.querySelector('#document');

  const loadedAt = Date.now();

  // Pre-fill from links such as contact.html?role=Buyer&commodity=Fertilizers
  const params = new URLSearchParams(location.search);
  const pick = (select, value) => {
    if (!value) return;
    const match = [...select.options].find((o) => o.value === value || o.text === value);
    if (match) select.value = match.value;
  };
  pick(role, params.get('role'));
  pick(commodity, params.get('commodity'));

  const syncTradeFields = () => {
    const isBuyer = role.value === 'Buyer';
    tradeFields.hidden = !(isBuyer || role.value === 'Seller / Supplier');
    portField.hidden = !isBuyer;
  };
  role.addEventListener('change', syncTradeFields);
  syncTradeFields();

  const show = (ok, text) => {
    status.hidden = false;
    status.className = 'form-status ' + (ok ? 'form-status--ok' : 'form-status--err');
    status.textContent = text;
    status.scrollIntoView({ behavior: 'smooth', block: 'center' });
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    status.hidden = true;

    const file = fileInput.files[0];
    if (file && (file.size > 5 * 1024 * 1024 || !/\.pdf$/i.test(file.name))) {
      show(false, 'The supporting document must be a PDF of 5 MB or less.');
      return;
    }
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending…';
    try {
      const body = new FormData(form);
      body.set('elapsed', String(Date.now() - loadedAt));
      const res = await fetch(form.action, { method: 'POST', body, headers: { Accept: 'application/json' } });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || data.ok === false) throw new Error(data.error || data.message || 'Something went wrong.');
      form.reset();
      syncTradeFields();
      show(true, 'Thank you. Your inquiry has been sent and our team will respond within 48 hours.');
    } catch (err) {
      show(false, err.message + ' You can also reach us on WhatsApp or by phone.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit Inquiry';
    }
  });
}
