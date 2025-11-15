<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppFlow extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_flows';

    protected $fillable = [
        'flow_id',
        'name',
        'json_definition',
        'status',
        'version',
        'description',
    ];

    /**
     * Scope for published flows
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for draft flows
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get flow definition as array
     */
    public function getDefinition(): array
    {
        return json_decode($this->json_definition, true);
    }

    /**
     * Set flow definition from array
     */
    public function setDefinition(array $definition): void
    {
        $this->json_definition = json_encode($definition);
    }
}
