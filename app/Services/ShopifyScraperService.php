<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShopifyScraperService
{
    protected $token;
    protected $apiVersion = '2024-10';

    public function __construct()
    {
        $this->token = env('SHOPIFY_TOKEN');
    }

    /**
     * Normalize store input
     */
    protected function normalizeStore($store)
    {
        return trim(str_replace(
            ['https://', 'http://', '.myshopify.com', '/'],
            '',
            $store
        ));
    }

    /**
     * Fetch products (Admin API)
     */
    public function fetchProducts($store)
    {
        if (!$store || !$this->token) {
            return null;
        }

        $store = $this->normalizeStore($store);

        $url = "https://{$store}.myshopify.com/admin/api/{$this->apiVersion}/products.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->get($url, [
            'limit' => 250
        ]);

        if (!$response->successful()) {
            return null;
        }

        return $response->json('products');
    }

    /**
     * ✅ Fetch Product SEO Metafields (meta_title & meta_description)
     */
    public function fetchProductSeo($store, $productId)
    {
        if (!$store || !$productId || !$this->token) {
            return [
                'meta_title' => null,
                'meta_description' => null,
            ];
        }

        $store = $this->normalizeStore($store);

        $url = "https://{$store}.myshopify.com/admin/api/{$this->apiVersion}/products/{$productId}/metafields.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->get($url);

        $seo = [
            'meta_title' => null,
            'meta_description' => null,
        ];

        if (!$response->successful()) {
            return $seo;
        }

        foreach ($response->json('metafields') ?? [] as $meta) {
            if ($meta['namespace'] === 'global') {
                if ($meta['key'] === 'title_tag') {
                    $seo['meta_title'] = $meta['value'];
                }
                if ($meta['key'] === 'description_tag') {
                    $seo['meta_description'] = $meta['value'];
                }
            }
        }

        return $seo;
    }

    /**
     * Fetch collections (Admin API)
     */
    public function fetchCollections($store)
    {
        $store = $this->normalizeStore($store);

        $url = "https://{$store}.myshopify.com/admin/api/{$this->apiVersion}/custom_collections.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response->successful()
            ? $response->json('custom_collections')
            : [];
    }
    public function fetchCollects($store)
{
    $token = env('SHOPIFY_TOKEN');

    $store = trim(str_replace(['https://', 'http://', '.myshopify.com'], '', $store));

    $url = "https://{$store}.myshopify.com/admin/api/2024-10/collects.json?limit=250";

    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $token,
        'Content-Type' => 'application/json',
    ])->get($url);

    if (!$response->ok()) {
        return [];
    }

    return $response->json()['collects'] ?? [];
}
}
