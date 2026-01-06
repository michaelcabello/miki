<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public function tags()
    {
        return $this->belongsToMany(LeadTag::class, 'lead_lead_tag', 'lead_id', 'lead_tag_id');
    }
}
