<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description'
    ];

    public function assignees(): belongsToMany
    {
        //this project belongs to many users
        return $this->belongsToMany(User::class);
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
