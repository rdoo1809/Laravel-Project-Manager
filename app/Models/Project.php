<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description'
    ];

    public function assignees(): belongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function tasks(): hasMany
    {
        return $this->hasMany(Task::class);
    }

    public function addMembers(array $members): void
    {
        foreach ($members as $member) {
            if (!$this->assignees->contains($member)) {
                $this->assignees()->attach($member);
            }
        }
    }

    public function removeMembers(array $members): void
    {
        foreach ($members as $member) {
            $this->assignees()->detach($member);
        }
    }
}
