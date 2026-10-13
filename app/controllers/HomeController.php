<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('shop/home', [
            'title'       => setting('seo_title', setting('store_name', 'Store') . ' | Online Vape Shop'),
            'metaDesc'    => setting('seo_description', ''),
            'heroSlides'  => Database::all("SELECT * FROM sliders WHERE is_active=1 AND placement='hero' ORDER BY sort_order, id"),
            'promoBanners'=> Database::all("SELECT * FROM sliders WHERE is_active=1 AND placement='promo' ORDER BY sort_order, id LIMIT 3"),
            'bestsellers' => Catalog::flag('is_bestseller', 12),
            'newArrivals' => Catalog::flag('is_new', 12),
            'featured'    => Catalog::flag('is_featured', 1),
            'categories'  => Catalog::categories(),
            'brands'      => Catalog::brands(),
            'posts'       => Database::all('SELECT * FROM posts WHERE is_published=1 ORDER BY id DESC LIMIT 3'),
            'faqs'        => Database::all('SELECT * FROM faqs WHERE is_active=1 ORDER BY sort_order, id LIMIT 5'),
        ]);
    }
}
