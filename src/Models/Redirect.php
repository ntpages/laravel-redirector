<?php

namespace Ntpages\LaravelRedirector\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read int $id
 * @property string $from_url
 * @property string $to_url
 * @property int $status_code
 * @property bool|null $healthy
 * @property Authenticatable|null $created_by
 * @property Authenticatable|null $updated_by
 * @method static Builder whereUrl(string $url)
 * @method static Builder healthy()
 */
class Redirect extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'from_url',
        'to_url',
        'status_code',
        'healthy'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('redirector.database.table');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        parent::boot();

        if (config('redirector.database.auditable')) {
            static::creating(function ($redirect) {
                $redirect->created_by = Auth::id();
            });

            static::updating(function ($redirect) {
                $redirect->updated_by = Auth::id();
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeWhereUrl(Builder $query, string $url): Builder
    {
        return $query
            ->where('healthy', 1)
            ->where(function (Builder $where) use ($url) {
                $where
                    ->where('from_url', $url)
                    ->orWhereRaw("from_url LIKE '$url?%'");
            });
    }

    public function scopeHealthy(Builder $query): Builder
    {
        return $query->where('healthy', 1);
    }
}
