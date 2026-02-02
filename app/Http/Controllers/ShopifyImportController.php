<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Collection;
use App\Models\Variant;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductTag;
use App\Models\Upload;
use App\Services\ShopifyScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopifyImportController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyScraperService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
        set_time_limit(0);
    }

    public function import(Request $request)
    {
        $request->validate([
            'store' => 'required|string'
        ]);

        $store = $request->store;

        $products    = $this->shopifyService->fetchProducts($store);
        $collections = $this->shopifyService->fetchCollections($store);
        $collects    = $this->shopifyService->fetchCollects($store);

        if (!$products) {
            return back()->with('error', 'Unable to fetch products.');
        }

        /* ================= COLLECT MAP ================= */
        $collectMap = [];
        foreach ($collects as $c) {
            $collectMap[$c['product_id']][] = $c['collection_id'];
        }

        DB::beginTransaction();

        try {
            /* ================= COLLECTIONS ================= */
            $collectionMap = [];

            if ($collections) {
                foreach ($collections as $col) {
                    $collection = Collection::create([
                        'shop_id'         => 1,
                        'title'           => $col['title'] ?? '',
                        'description'     => $col['body_html'] ?? '',
                        'handle'          => $col['handle'] ?? '',
                        'collection_type' => $col['published_scope'] ?? '',
                        'status'          => ($col['status'] ?? '') === 'active' ? 1 : 0,
                    ]);

                    $collectionMap[$col['id']] = $collection->id;
                }
            }

            /* ================= PRODUCTS ================= */
            foreach ($products as $prod) {

                /* SEO (metafields) */
                $seo = $this->shopifyService->fetchProductSeo($store, $prod['id']);

                $product = Product::create([
                    'shop_id'           => 1,
                    'title'             => $prod['title'] ?? 'Untitled',
                    'body_html'         => $prod['body_html'] ?? '',
                    'handle'            => $prod['handle'] ?? '',
                    'status'            => ($prod['status'] ?? '') === 'active' ? 1 : 0,
                    'meta_title'        => $seo['meta_title'] ?? null,
                    'meta_description'  => $seo['meta_description'] ?? null,
                    'min_price'         => collect($prod['variants'])->min('price') ?? 0,
                    'max_price'         => collect($prod['variants'])->max('price') ?? 0,
                ]);

                /* ================= TAGS ================= */
                if (!empty($prod['tags'])) {
                    $tags = is_array($prod['tags'])
                        ? $prod['tags']
                        : explode(',', $prod['tags']);

                    foreach ($tags as $tag) {
                        ProductTag::create([
                            'product_id' => $product->id,
                            'tag'        => trim($tag),
                        ]);
                    }
                }

                /* ================= PRODUCT → COLLECTION ================= */
                if (!empty($collectMap[$prod['id']])) {
                    foreach ($collectMap[$prod['id']] as $shopifyColId) {
                        if (isset($collectionMap[$shopifyColId])) {
                            $product->collections()->attach($collectionMap[$shopifyColId]);
                        }
                    }
                }

                /* ================= OPTIONS ================= */
                $optionMap = [];

                foreach ($prod['options'] ?? [] as $opt) {
                    $option = ProductOption::create([
                        'product_id' => $product->id,
                        'title'      => $opt['name'],
                    ]);

                    foreach ($opt['values'] ?? [] as $val) {
                        $value = ProductOptionValue::create([
                            'option_id' => $option->id,
                            'title'     => $val,
                        ]);
                        $optionMap[$val] = $value->id;
                    }
                }

                /* ================= VARIANTS ================= */
                foreach ($prod['variants'] ?? [] as $v) {
                    $variant = Variant::create([
                        'product_id'       => $product->id,
                        'price'            => $v['price'] ?? 0,
                        'sale_price'       => $v['compare_at_price'] ?? null,
                        'sku'              => $v['sku'] ?? '',
                        'inventory_status' => $v['inventory_management'] ?? null,
                        'weight'           => $v['weight'] ?? 0,
                        'weight_unit'      => $v['weight_unit'] ?? 'kg',
                        'inventoryItem_id' => $v['inventory_item_id'] ?? null,
                    ]);

                    foreach ($v['option_values'] ?? [] as $val) {
                        if (isset($optionMap[$val])) {
                            $variant->option_values()->attach($optionMap[$val]);
                        }
                    }
                }

                /* ================= IMAGES ================= */
                foreach ($prod['images'] ?? [] as $index => $img) {
                    $upload = Upload::create([
                        'shop_id'     => 1,
                        'product_id'  => $product->id,
                        'file_system' => 'remote',
                        'object_key'  => $img['src'],
                        'position'    => $index + 1,
                        'width'       => $img['width'] ?? 0,
                        'height'      => $img['height'] ?? 0,
                        'discr'       => 'product_image',
                    ]);

                    foreach ($img['variant_ids'] ?? [] as $variantId) {
                        Variant::where('inventoryItem_id', $variantId)
                            ->update(['image_id' => $upload->id]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        return back()->with('success', count($products) . ' products imported successfully!');
    }
}
