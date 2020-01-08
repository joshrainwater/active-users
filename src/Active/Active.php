<?php

namespace Rainwater\Active;

use Illuminate\Database\Eloquent\Model;

class Active extends Model
{
    /**
     * The activity model uses the 'sessions' database.
     *
     * @var string
     */
    protected $table = 'sessions';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * sessions do not use incremental ids, but unique strings as identifier.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * There are no timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public static function scopeUsers($query, $minutes = 5)
    {
        return $query->usersWithinMinutes($minutes);
    }

    public function scopeUsersWithinSeconds($query, $seconds)
    {
        return $query->with(['user'])->whereNotNull('user_id')->where('last_activity', '>=', time() - $seconds);
    }

    public function scopeUsersWithinMinutes($query, $minutes)
    {
        return $query->usersWithinSeconds($minutes * 60);
    }

    public function scopeUsersWithinHours($query, $hours)
    {
        return $query->usersWithinSeconds($hours * 60 * 60);
    }

    public function scopeMostRecent($query)
    {
        return $query->latest('last_activity');
    }

    public function scopeOrderByUser($query, $column, $dir = 'ASC')
    {
        $activeTable = $this->getTable();
        $userModel = config('auth.providers.users.model');
        $user = new $userModel;
        $userTable = $user->getTable();
        $userKey = $user->getKeyName();

        return $query
            ->join("{$userTable}", "{$userTable}.{$userKey}", '=', "{$activeTable}.user_id")
            ->select("{$activeTable}.*", "{$userTable}.{$column} as sort")->orderBy('sort', $dir);
    }


    public static function scopeGuests($query, $minutes = 5)
    {
        return $query->guestsWithinMinutes($minutes);
    }

    public function scopeGuestsWithinSeconds($query, $seconds)
    {
        return $query->with(['user'])->whereNull('user_id')->where('last_activity', '>=', time() - $seconds);
    }

    public function scopeGuestsWithinMinutes($query, $minutes)
    {
        return $query->guestsWithinSeconds($minutes * 60);
    }

    public function scopeGuestsWithinHours($query, $hours)
    {
        return $query->guestsWithinSeconds($hours * 60 * 60);
    }
}
