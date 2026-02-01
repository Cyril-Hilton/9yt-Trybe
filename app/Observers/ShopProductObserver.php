<?php

namespace App\Observers;

use App\Models\ShopProduct;
use App\Services\SEO\IndexNowService;

class ShopProductObserver
{
    public function created(ShopProduct $product): void
    {
        $this->submitIfLive($product);
    }

    public function updated(ShopProduct $product): void
    {
        if ($product->wasChanged(['status', 'slug', 'is_active'])) {
            $this->submitIfLive($product);
        }
    }

    private function submitIfLive(ShopProduct $product): void
    {
        if ($product->status !== 'approved' || !$product->is_active) {
            return;
        }

        $url = url('/shop/' . $product->slug);
        app(IndexNowService::class)->submitUrls([$url]);
    }
}
