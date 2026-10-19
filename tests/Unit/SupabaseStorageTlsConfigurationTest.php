<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupabaseStorageTlsConfigurationTest extends TestCase
{
    protected function tearDown(): void
    {
        Storage::forgetDisk('supabase');

        parent::tearDown();
    }

    public function test_supabase_s3_client_keeps_tls_verification_enabled_by_default(): void
    {
        config([
            'filesystems.disks.supabase.key' => 'test-key',
            'filesystems.disks.supabase.secret' => 'test-secret',
            'filesystems.disks.supabase.region' => 'ap-southeast-1',
            'filesystems.disks.supabase.http.verify' => true,
        ]);

        $verify = Storage::disk('supabase')->getConfig()['http']['verify'];

        $this->assertTrue($verify);
    }

    public function test_supabase_s3_client_accepts_an_explicit_ca_bundle_path(): void
    {
        $caBundle = 'C:/laragon/etc/ssl/cacert.pem';

        config([
            'filesystems.disks.supabase.key' => 'test-key',
            'filesystems.disks.supabase.secret' => 'test-secret',
            'filesystems.disks.supabase.region' => 'ap-southeast-1',
            'filesystems.disks.supabase.http.verify' => $caBundle,
        ]);

        $verify = Storage::disk('supabase')->getConfig()['http']['verify'];

        $this->assertSame($caBundle, $verify);
    }
}
