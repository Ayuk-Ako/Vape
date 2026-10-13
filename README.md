# Vapor Haus Store (PHP MVC / MySQL / Bootstrap 5)

A complete, self-hosted e-commerce store for the **United States** market, built without heavy frameworks so it runs on Hostinger shared hosting (and similar) with no Composer or Node step.

## Features

**Storefront**
- Age gate (21+) on first visit, adult-signature notices, nicotine warnings (footer, product pages, emails)
- Home page: hero slider, trust strip, categories, best sellers, new arrivals, promo tiles, home-page spotlight product, brands, FAQ, blog
- Shop: category / brand / collection pages (best sellers, new arrivals, deals), search, filters (price, puff count, in stock), sorting, pagination
- Product pages: gallery, flavor/option picker with per-flavor stock, sale badges, spec table, related products, SEO + JSON-LD
- Session cart (prices always re-read from the database), AJAX add-to-cart, free-shipping progress
- Checkout: US address + state list, **manual payment methods you define** (Zelle, Cash App, bank transfer ...), 21+ confirmation, terms, restricted-state blocking, minimum order, flat shipping, optional tax rate
- Atomic stock reservation at checkout (no overselling)
- Order confirmation page with payment instructions + "Message us on Telegram" button (prefilled with the order number)
- Order tracking page (order number + email), emails to customer and store owner (PHPMailer, SMTP supported)
- Blog, FAQ, contact form, newsletter signup, CMS pages (Terms, Privacy, Shipping, Refund, Age policy templates), sitemap.xml, robots.txt
- Floating Telegram and WhatsApp buttons (set in Settings)

**Admin panel (`/admin`)**
- Dashboard (orders, revenue, awaiting payment, low stock)
- Orders: search/filter, mark paid, change status, carrier tracking number, email the customer, auto-restock on cancel
- Products: images (multi-upload, main image), flavors/options with stock, specs, flags (best seller / new / spotlight), SEO
- Categories, brands, banners/hero slides, blog, pages, FAQs, messages inbox, subscribers (CSV export)
- Settings: store info, logo/favicon, payment methods, shipping/tax, restricted states, Telegram/WhatsApp, SEO, SMTP + test email
- Account page (change email / password)

**Security**: CSRF tokens on every POST, prepared statements everywhere, output escaping, HTML sanitising for CMS content, image-only uploads (re-validated, random names, PHP execution blocked in `/uploads`), login throttling, session fixation protection, honeypots on public forms, private folders denied via `.htaccess`, installer locks itself after use.

## Install on Hostinger (or any PHP 8+ host)

1. In hPanel create a **MySQL database + user** (note the name, user, password).
2. Upload everything in this zip to `public_html/` (File Manager -> Upload, then Extract). Make sure `.htaccess` came across (hidden file).
3. Visit `https://yourdomain.com/install/`, fill in the database details, store name and your admin login, and click **Install**.
4. **Delete the `/install` folder.**
5. Log in at `https://yourdomain.com/admin` and go to **Settings**:
   - *Payment & shipping*: write your payment methods, one per line as `Name | instructions`
   - *Contact channels*: your Telegram username / WhatsApp number
   - *Email*: create `orders@yourdomain.com` in hPanel and enter its SMTP details, then press **Send test**
   - *Compliance*: set restricted states, review the warning text
6. Replace the demo products, banners, brands, blog posts and page text with your own. (Demo brands/products are fictional.)
7. Turn on the free SSL certificate in hPanel and force HTTPS.

Requirements: PHP 8.0+ with `pdo_mysql`, `mbstring`, `gd` (or `getimagesize`), MySQL 5.7+ / MariaDB 10.3+, Apache/LiteSpeed with `mod_rewrite` and `.htaccess` enabled. If installed in a sub-folder it works too (links are relative to the folder).

## Folder map

```
index.php          front controller
.htaccess          rewrites + blocks private folders
install/           web installer (delete after use)
app/core           router, DB, cart, auth, mailer, helpers
app/controllers    storefront controllers (+ admin/ for the panel)
app/views          templates (shop/, admin/, emails/, layout/)
assets/            css, js, bundled Bootstrap 5 + icons
uploads/           product / banner / blog images
database/          schema.sql
config/            config.php (created by the installer)
storage/           error + mail logs
```

## Day-to-day

- **New order** -> you get an email and see it under Orders. When the customer's payment arrives, set *Payment = Paid* and status to *Confirmed/Processing*, add the tracking number when shipped and tick "Email the customer".
- **Out of stock** -> a flavor with stock 0 shows as sold out automatically; a product with all flavors at 0 shows "Sold out".
- **Backups** -> export the database from phpMyAdmin and download `/uploads` regularly.

## Legal & compliance (important)

This software gives you the *mechanics* (age gate, signature notice, restricted-state blocking, warnings). It does **not** make a business compliant. Selling nicotine / vape products in the US is regulated: federal age limit of 21, FDA premarket authorization (PMTA) for most e-cigarette products, the PACT Act (delivery sales rules, carrier restrictions, state/local tax reporting), state licensing, flavor bans, taxes, and card-processor rules. Many popular disposable brands are not FDA-authorized. The included policy pages are starter templates. Get advice from a qualified attorney before you launch, and only list products you are legally allowed to sell.
