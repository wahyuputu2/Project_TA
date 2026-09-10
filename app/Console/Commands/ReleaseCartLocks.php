<?php
namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Console\Command;

class ReleaseCartLocks extends Command
{
    protected $signature   = 'cart:release-locks';
    protected $description = 'Release expired cart locks';

    public function handle(): void
    {
        Product::releaseExpiredLocks();
        Cart::where('expires_at', '<', now())->delete();
        $this->info('Expired locks released.');
    }
}