<?php


namespace App\Models;

use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $avatar
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $avatar_url
 * @property-read string $full_name
 * @method static \Database\Factories\SupplierFactory factory($count = null, $state = [])
 * @method static Builder<static>|Supplier newModelQuery()
 * @method static Builder<static>|Supplier newQuery()
 * @method static Builder<static>|Supplier query()
 * @method static Builder<static>|Supplier whereAddress($value)
 * @method static Builder<static>|Supplier whereAvatar($value)
 * @method static Builder<static>|Supplier whereCreatedAt($value)
 * @method static Builder<static>|Supplier whereEmail($value)
 * @method static Builder<static>|Supplier whereFirstName($value)
 * @method static Builder<static>|Supplier whereId($value)
 * @method static Builder<static>|Supplier whereLastName($value)
 * @method static Builder<static>|Supplier wherePhone($value)
 * @method static Builder<static>|Supplier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'avatar'
    ];

    // Define relationships here (e.g., with the Product model)

    /**
     * Get the supplier's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the supplier avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        }

        return asset('images/avatar-placeholder.png');
    }
}
