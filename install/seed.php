<?php
/**
 * Seed data used by the installer.
 * All brands, products and images below are FICTIONAL demo content - replace them in the admin panel.
 */

function seed_settings(PDO $pdo, array $o): void
{
    $defaults = [
        'store_name'          => $o['store_name'],
        'store_tagline'       => 'Premium vapes. Fast US delivery.',
        'store_email'         => $o['admin_email'],
        'order_notify_email'  => $o['admin_email'],
        'store_phone'         => '',
        'store_address'       => '',
        'support_hours'       => 'Support hours: 9am - 9pm (ET), 7 days',
        'announcement'        => 'Free shipping on orders over $100  |  21+ only  |  Adult signature required',
        'footer_about'        => 'Premium vapes, pods and devices for adult customers. Fast, discreet US delivery.',
        'telegram_username'   => '',
        'whatsapp_number'     => '',
        'min_age'             => '21',
        'nicotine_warning'    => 'This product contains nicotine. Nicotine is an addictive chemical.',
        'restricted_states'   => '',
        'payment_methods'     => "Zelle | Send payment to your-zelle@email.com and put your order number in the memo.\nCash App | Send payment to \$YourCashTag and put your order number in the note.\nBank transfer | We will email you our bank details after you place the order.",
        'shipping_flat'       => '9.99',
        'free_shipping_over'  => '100',
        'tax_rate'            => '0',
        'min_order'           => '0',
        'delivery_note'       => 'Orders ship in 1-2 business days',
        'seo_title'           => $o['store_name'] . ' | Online Vape Shop USA',
        'seo_description'     => 'Shop disposable vapes, pod systems, pods and devices with fast US shipping. 21+ only.',
        'mail_from_name'      => $o['store_name'],
        'mail_from_email'     => $o['admin_email'],
        'smtp_host' => '', 'smtp_port' => '465', 'smtp_user' => '', 'smtp_pass' => '', 'smtp_encryption' => 'ssl',
    ];
    $st = $pdo->prepare('INSERT INTO settings (skey, svalue) VALUES (?, ?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)');
    foreach ($defaults as $k => $v) $st->execute([$k, $v]);
}

function seed_pages(PDO $pdo): void
{
    $notice = '<p><em>This is a starter template. Have a qualified attorney review and adapt it to your business and the states you ship to before launching.</em></p>';
    $pages = [
        ['Terms of Service', 'terms-of-service', 1, $notice . '<h2>1. Eligibility</h2><p>You must be at least 21 years old (or the legal minimum age in your state, if higher) to use this website and purchase products. By placing an order you confirm that you meet this requirement. We may cancel any order and request proof of age at any time.</p><h2>2. Products</h2><p>Our products contain nicotine, which is an addictive chemical. Products are intended for adult smokers and vapers only and must be kept away from children and pets. Product images are for illustration; packaging may vary.</p><h2>3. Orders and payment</h2><p>Placing an order is an offer to buy. Orders are confirmed once payment has been received and verified. Prices and availability can change without notice. We reserve the right to refuse or cancel any order, including for suspected fraud or where shipping is restricted by law.</p><h2>4. Delivery</h2><p>All orders require an adult signature on delivery. See our Shipping Policy for details. We do not ship to states or addresses where sale of these products is prohibited.</p><h2>5. Returns</h2><p>See our Refund Policy.</p><h2>6. Liability</h2><p>To the extent permitted by law, we are not liable for indirect or consequential damages arising from the use of our products or website. Use products as directed by the manufacturer.</p><h2>7. Contact</h2><p>Questions about these terms? Use our contact page.</p>'],
        ['Privacy Policy', 'privacy-policy', 2, $notice . '<h2>What we collect</h2><p>When you place an order or contact us we collect your name, email, phone number, shipping address, and (optionally) your Telegram username. We also store the order details and basic technical data such as IP address for fraud prevention.</p><h2>How we use it</h2><p>To process and deliver orders, communicate with you about your order, provide support, prevent fraud and meet legal obligations. If you subscribe to our newsletter we use your email to send offers; you can opt out at any time.</p><h2>Sharing</h2><p>We share information only with the service providers needed to fulfil your order (for example shipping carriers and our email provider) or when required by law. We do not sell your personal information.</p><h2>Cookies</h2><p>We use essential cookies for your cart and to remember your age confirmation.</p><h2>Your rights</h2><p>You may request access to, correction of, or deletion of your data by contacting us.</p>'],
        ['Shipping Policy', 'shipping-policy', 3, $notice . '<h2>Where we ship</h2><p>We ship within the United States only, except to states and territories where the sale or delivery of these products is restricted.</p><h2>Processing time</h2><p>Orders are processed within 1-2 business days after payment is confirmed.</p><h2>Delivery time and cost</h2><p>Standard delivery typically takes 2-7 business days depending on your location. Shipping is a flat rate, and free above the amount shown at checkout.</p><h2>Adult signature required</h2><p>An adult aged 21 or older must be present to sign for the package and may be asked to show a valid government-issued photo ID. Packages that cannot be delivered because no qualifying adult is available may be returned to us.</p><h2>Tracking</h2><p>We email your carrier tracking number when your order ships. You can also check status on our Track Order page.</p>'],
        ['Refund Policy', 'refund-policy', 4, $notice . '<h2>Damaged or incorrect items</h2><p>If your order arrives damaged or incorrect, contact us within 48 hours of delivery with your order number and photos. We will replace the item or refund you.</p><h2>Opened or used products</h2><p>For health and safety reasons we cannot accept returns of opened or used products.</p><h2>Cancellations</h2><p>Orders can be cancelled before they ship. Contact us as soon as possible with your order number.</p><h2>Refunds</h2><p>Approved refunds are returned using the original payment method within 5-10 business days.</p>'],
        ['Age Verification Policy', 'age-verification-policy', 5, $notice . '<p>We sell only to adults. You must be at least 21 years old to enter this site and place an order. By confirming your age you declare that you are legally allowed to purchase nicotine products where you live.</p><p>Every delivery requires an adult signature, and the carrier may check valid photo ID. We reserve the right to cancel orders where we cannot verify that the buyer meets the age requirement.</p>'],
    ];
    $st = $pdo->prepare('INSERT INTO pages (title, slug, body, show_in_footer, is_active, sort_order) VALUES (?,?,?,1,1,?)');
    foreach ($pages as [$t, $s, $o, $b]) $st->execute([$t, $s, $b, $o]);
}

function seed_faqs(PDO $pdo): void
{
    $faqs = [
        ['How old do I have to be to order?', 'You must be at least 21 years old. An adult must sign for every delivery and may be asked to show valid photo ID.'],
        ['How do I pay?', 'Choose a payment method at checkout. After you place the order you will see exact payment instructions on screen and in your confirmation email. Your order ships as soon as payment is confirmed.'],
        ['How long does shipping take?', 'Orders ship within 1-2 business days after payment is confirmed. Delivery usually takes 2-7 business days depending on your location.'],
        ['Do you offer free shipping?', 'Yes, orders over $100 ship free. Smaller orders have a flat shipping rate shown at checkout.'],
        ['Can I track my order?', 'Yes. Use the Track Order page with your order number and email, or use the carrier tracking number we email you when your order ships.'],
        ['My item arrived damaged. What now?', 'Contact us within 48 hours with your order number and photos, and we will make it right.'],
    ];
    $st = $pdo->prepare('INSERT INTO faqs (question, answer, sort_order, is_active) VALUES (?,?,?,1)');
    foreach ($faqs as $i => [$q, $a]) $st->execute([$q, $a, $i]);
}

/** Generic, brand-free device illustration. */
function demo_svg(string $label, string $c1, string $c2, string $shape = 'bar'): string
{
    $label = htmlspecialchars($label, ENT_QUOTES);
    if ($shape === 'pod') {
        $body = '<rect x="150" y="90" width="100" height="240" rx="34" fill="url(#b)"/><rect x="168" y="70" width="64" height="40" rx="12" fill="#2b2b2f" opacity=".9"/><rect x="176" y="150" width="48" height="90" rx="10" fill="#ffffff" opacity=".55"/>';
    } elseif ($shape === 'device') {
        $body = '<rect x="140" y="110" width="120" height="200" rx="40" fill="url(#b)"/><rect x="172" y="84" width="56" height="34" rx="10" fill="#2b2b2f" opacity=".9"/><circle cx="200" cy="205" r="22" fill="#ffffff" opacity=".55"/>';
    } else {
        $body = '<rect x="160" y="60" width="80" height="280" rx="34" fill="url(#b)"/><rect x="180" y="40" width="40" height="34" rx="10" fill="#2b2b2f" opacity=".9"/><rect x="178" y="130" width="44" height="120" rx="12" fill="#ffffff" opacity=".55"/>';
    }
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="' . $c1 . '"/><stop offset="1" stop-color="' . $c2 . '"/></linearGradient><radialGradient id="g" cx=".5" cy=".4" r=".7"><stop offset="0" stop-color="' . $c1 . '" stop-opacity=".18"/><stop offset="1" stop-color="#ffffff" stop-opacity="0"/></radialGradient></defs><rect width="400" height="400" fill="#f6f6f7"/><circle cx="200" cy="190" r="190" fill="url(#g)"/>' . $body . '<text x="200" y="378" text-anchor="middle" font-family="Arial,sans-serif" font-size="15" fill="#6b6b73" letter-spacing="2">' . strtoupper($label) . '</text></svg>';
}

function seed_demo(PDO $pdo, string $baseDir): void
{
    $dir = $baseDir . '/uploads/demo';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $ins = fn(string $sql, array $p) => $pdo->prepare($sql)->execute($p);

    // Categories
    $cats = [
        ['Disposable Vapes', 'disposable-vapes', 'Ready to use, no refills. Just puff.'],
        ['Pod Systems', 'pod-systems', 'Rechargeable devices that use prefilled pods.'],
        ['Pods & Refills', 'pods-refills', 'Prefilled pods and refill cartridges.'],
        ['Devices & Batteries', 'devices-batteries', 'Reusable batteries and starter kits.'],
    ];
    $catId = [];
    foreach ($cats as $i => [$n, $s, $d]) {
        $ins('INSERT INTO categories (name, slug, description, sort_order, is_active) VALUES (?,?,?,?,1)', [$n, $s, $d, $i]);
        $catId[$s] = (int)$pdo->lastInsertId();
    }
    // Brands (fictional)
    $brandId = [];
    foreach (['Nimbus', 'Aurora', 'Zenith Labs', 'Pulse', 'Vortex'] as $i => $n) {
        $ins('INSERT INTO brands (name, slug, description, sort_order, is_active) VALUES (?,?,?,?,1)', [$n, strtolower(str_replace(' ', '-', $n)), 'Demo brand - replace in Admin > Brands.', $i]);
        $brandId[$n] = (int)$pdo->lastInsertId();
    }

    $flavors = ['Blue Razz Ice', 'Mango Peach', 'Strawberry Kiwi', 'Watermelon Chill', 'Mint Frost', 'Grape Soda', 'Cola Fizz', 'Lychee Ice'];

    // name, cat, brand, price, compare, puffs, nic, battery, capacity, coil, shape, c1, c2, flags(best,new,feat), nFlavors
    $products = [
        ['Nimbus Cloud 15K', 'disposable-vapes', 'Nimbus', 24.00, 28.00, 15000, '5% (50mg)', '650mAh USB-C rechargeable', '18ml', 'Mesh coil', 'bar', '#7c5cff', '#22d3ee', [1,0,1], 8],
        ['Nimbus Cloud Mini 6K', 'disposable-vapes', 'Nimbus', 17.00, 19.00, 6000, '5% (50mg)', '500mAh USB-C rechargeable', '12ml', 'Mesh coil', 'bar', '#6a5cff', '#3b82f6', [1,0,0], 6],
        ['Aurora Glow 20K', 'disposable-vapes', 'Aurora', 31.00, 36.00, 20000, '5% (50mg)', '850mAh USB-C rechargeable', '24ml', 'Dual mesh coil', 'bar', '#ec4899', '#f59e0b', [1,1,0], 8],
        ['Aurora Spark 9K', 'disposable-vapes', 'Aurora', 21.00, null, 9000, '3% (30mg)', '600mAh USB-C rechargeable', '15ml', 'Mesh coil', 'bar', '#f43f5e', '#fb923c', [0,1,0], 6],
        ['Zenith X 12K', 'disposable-vapes', 'Zenith Labs', 26.00, 29.00, 12000, '5% (50mg)', '650mAh USB-C rechargeable', '20ml', 'Mesh coil', 'bar', '#10b981', '#22d3ee', [1,0,0], 8],
        ['Zenith Zero 8K (0% nic)', 'disposable-vapes', 'Zenith Labs', 20.00, null, 8000, '0% nicotine', '550mAh USB-C rechargeable', '14ml', 'Mesh coil', 'bar', '#14b8a6', '#84cc16', [0,1,0], 4],
        ['Pulse Pocket 5K', 'disposable-vapes', 'Pulse', 15.00, 18.00, 5000, '5% (50mg)', '500mAh USB-C rechargeable', '10ml', 'Mesh coil', 'bar', '#f59e0b', '#ef4444', [0,0,0], 6],
        ['Vortex Storm 30K', 'disposable-vapes', 'Vortex', 39.00, 46.00, 30000, '5% (50mg)', '1000mAh USB-C rechargeable', '30ml', 'Dual mesh coil', 'bar', '#3b82f6', '#8b5cf6', [1,1,0], 8],
        ['Nimbus Pod Kit', 'pod-systems', 'Nimbus', 34.00, 39.00, null, 'Depends on pod', '700mAh USB-C rechargeable', '2ml pod', 'Ceramic coil', 'device', '#7c5cff', '#22d3ee', [1,0,0], 0],
        ['Pulse Slim Pod Device', 'pod-systems', 'Pulse', 29.00, null, null, 'Depends on pod', '400mAh USB-C rechargeable', '1.8ml pod', 'Ceramic coil', 'device', '#f59e0b', '#ef4444', [0,1,0], 0],
        ['Vortex Pro Pod Device', 'pod-systems', 'Vortex', 49.00, 59.00, null, 'Depends on pod', '1100mAh USB-C rechargeable', '3ml pod', 'Mesh coil', 'device', '#3b82f6', '#8b5cf6', [0,0,0], 0],
        ['Nimbus Prefilled Pod (2-pack)', 'pods-refills', 'Nimbus', 18.00, 20.00, 1200, '5% (50mg)', null, '2ml x 2', 'Ceramic coil', 'pod', '#7c5cff', '#22d3ee', [1,0,0], 8],
        ['Pulse Prefilled Pod (2-pack)', 'pods-refills', 'Pulse', 16.00, null, 1000, '5% (50mg)', null, '1.8ml x 2', 'Ceramic coil', 'pod', '#f59e0b', '#ef4444', [0,0,0], 6],
        ['Vortex Mesh Pod (2-pack)', 'pods-refills', 'Vortex', 21.00, 24.00, 1800, '5% (50mg)', null, '3ml x 2', 'Mesh coil', 'pod', '#3b82f6', '#8b5cf6', [0,1,0], 6],
        ['Zenith Battery Device', 'devices-batteries', 'Zenith Labs', 19.00, null, null, null, '600mAh USB-C rechargeable', null, null, 'device', '#10b981', '#22d3ee', [0,0,0], 0],
        ['Aurora Starter Kit', 'devices-batteries', 'Aurora', 44.00, 52.00, null, null, '900mAh USB-C rechargeable', '3ml pod', 'Mesh coil', 'device', '#ec4899', '#f59e0b', [0,1,0], 0],
    ];

    $pi = $pdo->prepare('INSERT INTO products (category_id, brand_id, name, slug, sku, short_description, description, price, compare_price, puffs, nicotine, battery, capacity, coil, stock, is_featured, is_bestseller, is_new, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)');
    $ii = $pdo->prepare('INSERT INTO product_images (product_id, path, alt, sort_order) VALUES (?,?,?,?)');
    $vi = $pdo->prepare('INSERT INTO product_variants (product_id, name, price, stock, sort_order, is_active) VALUES (?,?,NULL,?,?,1)');

    foreach ($products as $n => $p) {
        [$name, $cat, $brand, $price, $cmp, $puffs, $nic, $bat, $cap, $coil, $shape, $c1, $c2, $flags, $nf] = $p;
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        $short = $puffs ? number_format($puffs) . ' puffs of smooth flavor in a compact, rechargeable design.' : 'Reliable, easy to use and built to last.';
        $desc = '<p>This is demo content. Replace this description in <strong>Admin &rarr; Products</strong>.</p><ul><li>Rechargeable via USB-C</li><li>Consistent flavor from start to finish</li><li>Compact and pocket friendly</li></ul>';
        $pi->execute([$catId[$cat], $brandId[$brand], $name, $slug, 'DEMO-' . str_pad((string)($n + 1), 3, '0', STR_PAD_LEFT), $short, $desc, $price, $cmp, $puffs, $nic, $bat, $cap, $coil, $nf ? 0 : 25, $flags[2] && $n === 0 ? 1 : 0, $flags[0], $flags[1]]);
        $pid = (int)$pdo->lastInsertId();

        file_put_contents("$dir/$slug-1.svg", demo_svg($name, $c1, $c2, $shape));
        file_put_contents("$dir/$slug-2.svg", demo_svg($brand, $c2, $c1, $shape));
        $ii->execute([$pid, "demo/$slug-1.svg", $name, 0]);
        $ii->execute([$pid, "demo/$slug-2.svg", $name, 1]);
        for ($f = 0; $f < $nf; $f++) {
            $vi->execute([$pid, $flavors[$f], $f === 3 ? 0 : 12 + $f, $f]); // one sold-out flavor to demo the state
        }
    }

    // Hero + promo banners
    $sl = $pdo->prepare('INSERT INTO sliders (title, subtitle, button_text, link, image, placement, sort_order, is_active) VALUES (?,?,?,?,NULL,?,?,1)');
    $sl->execute(['Big flavor. Bigger puff counts.', 'New arrivals', 'Shop new arrivals', 'collection/new-arrivals', 'hero', 0]);
    $sl->execute(['Best sellers, handpicked by you', 'Customer favorites', 'See best sellers', 'collection/best-selling', 'hero', 1]);
    $sl->execute(['Free shipping over $100', 'Stock up and save', 'Shop all', 'shop', 'hero', 2]);
    $sl->execute(['Pod systems', 'Rechargeable', 'Shop pods', 'category/pod-systems', 'promo', 0]);
    $sl->execute(['Hot deals', 'Limited time', 'View deals', 'collection/deals', 'promo', 1]);
    $sl->execute(['30K puff devices', 'Longest lasting', 'Shop now', 'brand/vortex', 'promo', 2]);

    // Blog
    $bp = $pdo->prepare('INSERT INTO posts (title, slug, excerpt, body, is_published) VALUES (?,?,?,?,1)');
    $bp->execute(['Disposable vs pod system: which one is right for you?', 'disposable-vs-pod-system', 'A quick guide to the two most popular vape formats, with pros, cons and who each suits best.', '<p>Demo article - replace it in Admin &rarr; Blog.</p><h2>Disposable vapes</h2><p>Ready to use out of the box, no charging fuss with low-puff models and no refills. Great for trying flavors or travel.</p><h2>Pod systems</h2><p>Reusable battery with swappable prefilled pods. Better value over time, and you can switch flavors without buying a new device.</p><h2>Which should you pick?</h2><p>If you want zero maintenance, go disposable. If you vape daily, a pod system usually costs less in the long run.</p>']);
    $bp->execute(['How to choose a nicotine strength', 'how-to-choose-nicotine-strength', 'Nicotine strength explained in plain language, and tips for finding what works for you.', '<p>Demo article - replace it in Admin &rarr; Blog.</p><p>Nicotine strength is shown as a percentage or in mg/ml. Higher strengths deliver a stronger throat hit. Current smokers often start higher; occasional users usually prefer lower. Nicotine is addictive - use only if you are an adult who already uses nicotine.</p>']);
    $bp->execute(['Vape care: making your device last longer', 'vape-care-tips', 'Simple habits that improve flavor and battery life.', '<p>Demo article - replace it in Admin &rarr; Blog.</p><ul><li>Charge before the battery is completely empty.</li><li>Store upright in a cool place, away from direct sun.</li><li>Take gentle puffs - long, hard drags burn the coil faster.</li></ul>']);
}
