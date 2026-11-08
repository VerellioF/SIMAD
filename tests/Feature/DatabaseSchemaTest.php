<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_fifteen_tables_exist_in_database(): void
    {
        $expectedTables = [
            'users',
            'academic_years',
            'classrooms',
            'subjects',
            'students',
            'ppdb_registrations',
            'attendances',
            'leave_requests',
            'tahfidz_records',
            'academic_grades',
            'tuition_bills',
            'tuition_payments',
            'articles',
            'school_galleries',
            'announcements',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} does not exist in schema.");
        }
    }

    public function test_users_table_has_custom_simad_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'id',
            'name',
            'email',
            'phone_number',
            'password',
            'role',
            'avatar_url',
            'is_active',
        ]));
    }

    public function test_students_and_classrooms_schema_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('classrooms', [
            'id',
            'academic_year_id',
            'homeroom_teacher_id',
            'name',
            'grade_level',
        ]));

        $this->assertTrue(Schema::hasColumns('students', [
            'id',
            'parent_id',
            'classroom_id',
            'nis',
            'nisn',
            'full_name',
            'gender',
            'status',
        ]));
    }

    public function test_ppdb_and_finance_schema_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('ppdb_registrations', [
            'id',
            'registration_number',
            'child_name',
            'nik',
            'parent_whatsapp',
            'status',
        ]));

        $this->assertTrue(Schema::hasColumns('tuition_bills', [
            'id',
            'student_id',
            'academic_year_id',
            'bill_title',
            'amount',
            'status',
        ]));

        $this->assertTrue(Schema::hasColumns('tuition_payments', [
            'id',
            'tuition_bill_id',
            'receipt_number',
            'amount_paid',
            'payment_method',
        ]));
    }
}
