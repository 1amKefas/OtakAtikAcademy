<?php

namespace App\Services;

class SupabaseStorageService
{
    /**
     * Get logo URLs
     */
    public function getLogoUrls()
    {
        return [
            'otakatik' => 'https://kmlfzivhroexxldwygkf.supabase.co/storage/v1/object/public/certificates/logos/logo_OtakAtik.png',
            'pnj' => 'https://kmlfzivhroexxldwygkf.supabase.co/storage/v1/object/public/certificates/logos/logo_PNJ.png',
            'tik' => 'https://kmlfzivhroexxldwygkf.supabase.co/storage/v1/object/public/certificates/logos/logo_TIK.png',
        ];
    }
}
