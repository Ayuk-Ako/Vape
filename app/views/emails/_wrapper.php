<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;background:#f3f4f8;font-family:Arial,Helvetica,sans-serif;color:#1b1f2a">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f8;padding:24px 12px"><tr><td align="center">
  <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden">
    <tr><td style="background:#0b0d14;color:#ffffff;padding:22px 28px;font-size:20px;font-weight:bold"><?= e(setting('store_name', 'Store')) ?></td></tr>
    <tr><td style="padding:28px"><?= $content ?></td></tr>
    <tr><td style="background:#f8f9fc;padding:16px 28px;font-size:12px;color:#6b7280">
      WARNING: <?= e(setting('nicotine_warning', 'This product contains nicotine. Nicotine is an addictive chemical.')) ?><br>
      <?= e(setting('store_name', 'Store')) ?><?= setting('store_email') ? ' &middot; ' . e(setting('store_email')) : '' ?>
    </td></tr>
  </table>
</td></tr></table>
</body></html>
