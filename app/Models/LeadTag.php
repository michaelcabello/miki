<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadTag extends Model
{
    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'lead_lead_tag', 'lead_tag_id', 'lead_id');
    }
}
