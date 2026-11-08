<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class EnvironmentSetupTest extends TestCase
{
    public function test_simad_config_exists_and_configured(): void
    {
        $this->assertNotEmpty(config('simad.school_name'));
        $this->assertNotEmpty(config('simad.blitar_coordinates.latitude'));
        $this->assertNotEmpty(config('simad.blitar_coordinates.longitude'));
    }

    public function test_dompdf_service_provider_is_registered(): void
    {
        $this->assertTrue(class_exists(\Barryvdh\DomPDF\ServiceProvider::class));
    }

    public function test_excel_service_provider_is_registered(): void
    {
        $this->assertTrue(class_exists(\Maatwebsite\Excel\ExcelServiceProvider::class));
    }
}
