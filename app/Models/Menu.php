<?php

namespace App\Models;

use App\Traits\HasAuditTrails;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasAuditTrails, HasFactory;

    public static function paginateForManagement(): LengthAwarePaginator
    {
        return static::query()
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->paginate(10);
    }

    /**
     * @return Collection<int, self>
     */
    public static function parentMenusForSelection(): Collection
    {
        return static::query()
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }

    public static function countForDashboard(): int
    {
        return static::query()->count();
    }

    /**
     * @return Collection<int, self>
     */
    public static function activeMenusForSidebar(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'category',
        'title',
        'route_or_url',
        'icon',
        'permission_name',
        'order',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Parent menu relationship.
     *
     * @return BelongsTo<Menu, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Child submenus relationship.
     *
     * @return HasMany<Menu, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }
}
