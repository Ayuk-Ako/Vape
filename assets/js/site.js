(function () {
  'use strict';
  var body = document.body;
  var base = body.getAttribute('data-base') || '';

  /* ---------- Age gate ---------- */
  var gate = document.getElementById('age-gate');
  if (gate) {
    var yes = document.getElementById('age-yes');
    var no = document.getElementById('age-no');
    var denied = document.getElementById('age-denied');
    yes.addEventListener('click', function () {
      var d = new Date(); d.setDate(d.getDate() + 30);
      document.cookie = 'vh_age=1; expires=' + d.toUTCString() + '; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
      gate.remove();
      body.classList.remove('age-locked');
    });
    no.addEventListener('click', function () {
      denied.classList.remove('d-none');
      yes.disabled = true;
    });
    yes.focus();
  }

  /* ---------- Toast ---------- */
  function toast(msg, ok) {
    var el = document.getElementById('site-toast');
    if (!el || !window.bootstrap) return;
    document.getElementById('site-toast-body').textContent = msg;
    el.classList.toggle('text-bg-danger', ok === false);
    el.classList.toggle('text-bg-dark', ok !== false);
    window.bootstrap.Toast.getOrCreateInstance(el, { delay: 3000 }).show();
  }

  /* ---------- Product gallery ---------- */
  var main = document.getElementById('galleryMain');
  document.querySelectorAll('.gallery-thumbs .thumb').forEach(function (t) {
    t.addEventListener('click', function () {
      if (main) main.src = t.getAttribute('data-src');
      document.querySelectorAll('.gallery-thumbs .thumb').forEach(function (x) { x.classList.remove('active'); });
      t.classList.add('active');
    });
  });

  /* ---------- Quantity buttons ---------- */
  document.querySelectorAll('.qty-btn').forEach(function (b) {
    b.addEventListener('click', function () {
      var input = document.getElementById('qty');
      if (!input) return;
      var v = parseInt(input.value || '1', 10) + parseInt(b.getAttribute('data-d'), 10);
      input.value = Math.max(1, Math.min(20, v));
    });
  });

  /* ---------- Variant price swap ---------- */
  var priceNow = document.getElementById('priceNow');
  document.querySelectorAll('.variant input').forEach(function (r) {
    r.addEventListener('change', function () {
      if (priceNow) priceNow.textContent = '$' + parseFloat(r.getAttribute('data-price')).toFixed(2);
    });
  });

  /* ---------- AJAX add to cart (stay on page) ---------- */
  var form = document.getElementById('addForm');
  if (form && window.fetch) {
    form.addEventListener('submit', function (ev) {
      var sub = ev.submitter;
      if (sub && sub.name === 'buy_now') return; // normal submit -> checkout
      if (!form.checkValidity()) { return; }
      ev.preventDefault();
      var btn = document.getElementById('addBtn');
      var orig = btn.innerHTML;
      btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
      fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
        credentials: 'same-origin'
      }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (res) {
          toast(res.j.msg || 'Done', res.ok);
          if (res.ok) {
            var c = document.getElementById('cart-count');
            if (c) { c.textContent = res.j.count; c.hidden = !res.j.count; }
          }
        })
        .catch(function () { form.submit(); })
        .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
    });
  }

  /* ---------- Auto-submit sort ---------- */
  document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
    el.addEventListener('change', function () { el.form.submit(); });
  });
})();
