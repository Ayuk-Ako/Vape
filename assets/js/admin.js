(function () {
  'use strict';
  var add = document.getElementById('addVariant');
  var rows = document.getElementById('variantRows');
  var tpl = document.getElementById('variantTpl');
  if (add && rows && tpl) {
    add.addEventListener('click', function () {
      rows.appendChild(tpl.content.cloneNode(true));
      var last = rows.querySelector('.variant-row:last-child input[name="variant_name[]"]');
      if (last) last.focus();
    });
    rows.addEventListener('click', function (e) {
      var b = e.target.closest('.rm-variant');
      if (b) b.closest('.variant-row').remove();
    });
  }
  // Close mobile sidebar when clicking outside
  document.addEventListener('click', function (e) {
    var side = document.getElementById('adminSide');
    if (side && side.classList.contains('open') && !side.contains(e.target) && !e.target.closest('.admin-top')) side.classList.remove('open');
  });
})();
