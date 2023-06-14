<?php

namespace Hasanablak\JwtAuth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    public function scopeControl(
        $query,
        $notifiable_id,
        $type,
        $status,
        $code
    ) {
        $query->where('notifiable_id', $notifiable_id)
            ->where('type', $type)
            ->whereJsonContains('data', ['status' => $status])
            ->whereJsonContains('data', ['code' => intval($code)])
            ->where(function ($q) {
                if (!env('APP_DEBUG', false)) {
                    return $q->where('created_at', '>=', Carbon::now()->subMinutes(5)->toDateTimeString());
                }
            });
    }
}
