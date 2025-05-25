<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 必要なら認可ロジックを追加
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i', 'after:clock_in'],
            'break_start_1' => ['nullable', 'date_format:H:i'],
            'break_end_1' => ['nullable', 'date_format:H:i', 'after:break_start_1'],
            'break_start_2' => ['nullable', 'date_format:H:i'],
            'break_end_2' => ['nullable', 'date_format:H:i', 'after:break_start_2'],
            'note' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時刻を入力してください。',
            'clock_in.date_format' => '出勤時刻は「HH:MM」形式で入力してください。',
            'clock_out.required' => '退勤時刻を入力してください。',
            'clock_out.date_format' => '退勤時刻は「HH:MM」形式で入力してください。',
            'clock_out.after' => '退勤時刻は出勤時刻より後にしてください。',
            'break_start_1.date_format' => '休憩開始は「HH:MM」形式で入力してください。',
            'break_end_1.date_format' => '休憩終了は「HH:MM」形式で入力してください。',
            'break_end_1.after' => '休憩終了は開始時刻より後にしてください。',
            'break_start_2.date_format' => '休憩2の開始は「HH:MM」形式で入力してください。',
            'break_end_2.date_format' => '休憩2の終了は「HH:MM」形式で入力してください。',
            'break_end_2.after' => '休憩2の終了は開始時刻より後にしてください。',
            'note.required' => '備考を入力してください。',
            'note.max' => '備考は255文字以内で入力してください。',
        ];
    }
}
