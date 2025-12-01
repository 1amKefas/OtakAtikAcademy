<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $table = 'certificate_templates';
    
    protected $fillable = [
        'name',
        'description',
        'background_image_path',
        'placeholders',
        'signature_image_path',
        'issuer_name',
        'issuer_title'
    ];

    protected $casts = [
        'placeholders' => 'json',
    ];

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'certificate_template_id');
    }
}
