<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:14px 0;font-size:14px">
<?php foreach ($lines as $l): ?>
<tr>
  <td style="padding:8px 0;border-bottom:1px solid #eceef3"><?= e($l['product_name']) ?><?= $l['variant_name'] ? '<br><span style="color:#6b7280;font-size:12px">' . e($l['variant_name']) . '</span>' : '' ?></td>
  <td style="padding:8px 0;border-bottom:1px solid #eceef3;text-align:center;color:#6b7280">&times; <?= (int)$l['qty'] ?></td>
  <td style="padding:8px 0;border-bottom:1px solid #eceef3;text-align:right"><?= e(money($l['line_total'])) ?></td>
</tr>
<?php endforeach; ?>
<tr><td colspan="2" style="padding:8px 0;color:#6b7280">Subtotal</td><td style="text-align:right"><?= e(money($order['subtotal'])) ?></td></tr>
<tr><td colspan="2" style="padding:4px 0;color:#6b7280">Shipping</td><td style="text-align:right"><?= $order['shipping'] > 0 ? e(money($order['shipping'])) : 'Free' ?></td></tr>
<?php if ($order['tax'] > 0): ?><tr><td colspan="2" style="padding:4px 0;color:#6b7280">Tax</td><td style="text-align:right"><?= e(money($order['tax'])) ?></td></tr><?php endif; ?>
<tr><td colspan="2" style="padding:10px 0;font-weight:bold;font-size:16px">Total</td><td style="text-align:right;font-weight:bold;font-size:16px"><?= e(money($order['total'])) ?></td></tr>
</table>
