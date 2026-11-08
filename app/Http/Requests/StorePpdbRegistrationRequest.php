<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePpdbRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'previous_school' => ['required', 'string', 'max:150'],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_whatsapp' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'parent_job' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
            'file_birth_certificate' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_family_card' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'payment_method' => ['nullable', 'in:transfer,cash'],
            'file_payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK calon siswa harus terdiri dari tepat 16 digit angka.',
            'parent_whatsapp.regex' => 'Nomor WhatsApp tidak valid. Gunakan format 08xxxxxxxxxx atau 628xxxxxxxxxx.',
            'file_birth_certificate.required' => 'Dokumen Akta Kelahiran wajib diunggah.',
            'file_family_card.required' => 'Dokumen Kartu Keluarga wajib diunggah.',
            'file_birth_certificate.max' => 'Ukuran file Akta Kelahiran maksimal 2MB.',
            'file_family_card.max' => 'Ukuran file Kartu Keluarga maksimal 2MB.',
            'file_photo.max' => 'Ukuran pasfoto maksimal 2MB.',
        ];
    }
}
