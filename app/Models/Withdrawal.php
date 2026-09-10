<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'store_id','amount','status','bank_name',
        'bank_account_number','bank_account_name','notes','processed_at',
    ];
    protected $casts = ['processed_at' => 'datetime', 'amount' => 'decimal:2'];

    public function store() { return $this->belongsTo(Store::class); }

    public function statusLabel(): string {
        return match($this->status) {
            'pending'   => 'Menunggu',
            'processed' => 'Diproses',
            'rejected'  => 'Ditolak',
            default     => $this->status,
        };
    }
}