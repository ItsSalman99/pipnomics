<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class UserActivity extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * The user that the activity belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method to log an activity for a user.
     */
    public static function log(
        User|int $user,
        string $activityType,
        string $description,
        ?array $properties = null,
        ?Request $request = null
    ): self {
        $userId = $user instanceof User ? $user->id : $user;
        $req = $request ?: request();

        return self::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => $req?->ip(),
            'user_agent' => $req?->userAgent(),
            'properties' => $properties,
        ]);
    }
}
