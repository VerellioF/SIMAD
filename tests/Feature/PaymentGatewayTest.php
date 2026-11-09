<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\PaymentTransaction;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\TuitionBill;
use App\Models\TuitionPayment;
use App\Models\User;
use App\Models\WhatsAppLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $parent;
    protected User $otherParent;
    protected Student $student;
    protected TuitionBill $bill;
    protected AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone_number' => '081299990001',
        ]);

        $this->parent = User::factory()->create([
            'role' => 'parent',
            'name' => 'Bapak Joko Santoso',
            'phone_number' => '081234567890',
        ]);

        $this->otherParent = User::factory()->create([
            'role' => 'parent',
            'name' => 'Bapak Hendra',
            'phone_number' => '081299998888',
        ]);

        $this->year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $classroom = Classroom::create([
            'academic_year_id' => $this->year->id,
            'name' => 'Kelas 1-A Ibnu Sina',
            'grade_level' => 1,
        ]);

        $this->student = Student::create([
            'parent_id' => $this->parent->id,
            'classroom_id' => $classroom->id,
            'full_name' => 'Muhammad Bilal',
            'nis' => '262701001',
            'nisn' => '0123456789',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-05-10',
            'address' => 'Kalipucung, Sanankulon, Blitar',
            'status' => 'active',
        ]);

        $this->bill = TuitionBill::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->year->id,
            'bill_title' => 'SPP Bulan Juli 2026',
            'month' => 7,
            'year' => 2026,
            'amount' => 150000,
            'status' => 'unpaid',
            'due_date' => '2026-07-10',
        ]);
    }

    public function test_parent_can_initiate_online_payment_for_tuition(): void
    {
        $response = $this->actingAs($this->parent)
            ->get(route('parent.tuition.pay_online', $this->bill));

        $this->assertDatabaseHas('payment_transactions', [
            'payable_type' => TuitionBill::class,
            'payable_id' => $this->bill->id,
            'amount' => 150000,
            'status' => 'pending',
        ]);

        $transaction = PaymentTransaction::where('payable_id', $this->bill->id)->first();
        $response->assertRedirect(route('payment.checkout', $transaction->reference));
    }

    public function test_other_parent_cannot_initiate_payment_for_unrelated_student(): void
    {
        $response = $this->actingAs($this->otherParent)
            ->get(route('parent.tuition.pay_online', $this->bill));

        $response->assertForbidden();
    }

    public function test_checkout_page_renders_with_qris_information(): void
    {
        $this->actingAs($this->parent)
            ->get(route('parent.tuition.pay_online', $this->bill));

        $transaction = PaymentTransaction::where('payable_id', $this->bill->id)->first();

        $response = $this->get(route('payment.checkout', $transaction->reference));

        $response->assertOk();
        $response->assertSee('Pembayaran Biaya Pendidikan');
        $response->assertSee($transaction->reference);
        $response->assertSee('Rp 150.000');
        $response->assertSee('QRIS');
    }

    public function test_realtime_status_endpoint_returns_json(): void
    {
        $this->actingAs($this->parent)
            ->get(route('parent.tuition.pay_online', $this->bill));

        $transaction = PaymentTransaction::where('payable_id', $this->bill->id)->first();

        $response = $this->getJson(route('payment.status', $transaction->reference));

        $response->assertOk();
        $response->assertJson([
            'status' => 'pending',
            'is_paid' => false,
        ]);
    }

    public function test_simulation_marks_tuition_bill_as_paid_and_creates_payment(): void
    {
        $this->actingAs($this->parent)
            ->get(route('parent.tuition.pay_online', $this->bill));

        $transaction = PaymentTransaction::where('payable_id', $this->bill->id)->first();

        $response = $this->post(route('payment.simulate', $transaction->reference));

        $response->assertRedirect(route('payment.checkout', $transaction->reference));

        $transaction->refresh();
        $this->assertTrue($transaction->isPaid());

        $this->bill->refresh();
        $this->assertEquals('paid', $this->bill->status->value);

        $this->assertDatabaseHas('tuition_payments', [
            'tuition_bill_id' => $this->bill->id,
            'amount_paid' => 150000,
        ]);

        // WhatsApp log recorded
        $this->assertDatabaseHas('whatsapp_logs', [
            'type' => 'tuition_payment',
        ]);
    }

    public function test_ppdb_applicant_can_initiate_online_payment_and_simulate_success(): void
    {
        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-TEST01',
            'child_name' => 'Fatimah Zahra',
            'nik' => '3505012345670001',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-08-15',
            'previous_school' => 'RA Perwanida',
            'parent_name' => 'Bapak Ridwan',
            'parent_whatsapp' => '085712345678',
            'address' => 'Jl. Merdeka No. 10 Blitar',
            'file_birth_certificate' => 'berkas/akta.pdf',
            'file_family_card' => 'berkas/kk.pdf',
            'file_photo' => 'berkas/foto.jpg',
            'registration_fee' => 50000,
            'payment_status' => 'unpaid',
            'payment_method' => 'transfer',
            'status' => 'pending',
        ]);

        $response = $this->get(route('ppdb.pay', $registration->registration_number));

        $transaction = PaymentTransaction::where('payable_type', PpdbRegistration::class)
            ->where('payable_id', $registration->id)
            ->first();

        $this->assertNotNull($transaction);
        $response->assertRedirect(route('payment.checkout', $transaction->reference));

        // Simulate payment
        $this->post(route('payment.simulate', $transaction->reference));

        $registration->refresh();
        $this->assertTrue($registration->isPaid());
        $this->assertNotNull($registration->paid_at);

        // WhatsApp log recorded for PPDB payment
        $this->assertDatabaseHas('whatsapp_logs', [
            'type' => 'ppdb_payment',
        ]);
    }

    public function test_webhook_marks_transaction_as_paid_idempotently(): void
    {
        $this->actingAs($this->parent)
            ->get(route('parent.tuition.pay_online', $this->bill));

        $transaction = PaymentTransaction::where('payable_id', $this->bill->id)->first();

        $webhookPayload = [
            'merchant_ref' => $transaction->reference,
            'status' => 'PAID',
            'amount' => 150000,
        ];

        $response = $this->postJson(route('payment.webhook'), $webhookPayload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $transaction->refresh();
        $this->assertTrue($transaction->isPaid());
    }

    public function test_admin_can_view_payment_transactions_hub(): void
    {
        $this->actingAs($this->admin);

        // Create transaction
        $this->actingAs($this->parent)
            ->get(route('parent.tuition.pay_online', $this->bill));

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payment.index'));

        $response->assertOk();
        $response->assertSee('Transaksi Pembayaran Online');
        $response->assertSee($this->bill->student->full_name);
    }
}
