<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です。',
            'break_start_1.date_format' => '休憩開始は「HH:MM」形式で入力してください。',
            'break_end_1.date_format' => '休憩終了は「HH:MM」形式で入力してください。',
            'break_end_1.after' => '休憩終了は開始時刻より後にしてください。',
            'break_start_2.date_format' => '休憩2の開始は「HH:MM」形式で入力してください。',
            'break_end_2.date_format' => '休憩2の終了は「HH:MM」形式で入力してください。',
            'break_end_2.after' => '休憩2の終了は開始時刻より後にしてください。',
            'note.required' => '備考を記入してください。',
            'note.max' => '備考は255文字以内で入力してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockIn = Carbon::createFromFormat('H:i', $this->clock_in);
            $clockOut = Carbon::createFromFormat('H:i', $this->clock_out);

            // 勤務時間外に休憩が設定されていないか確認
            foreach ([1, 2] as $i) {
                $start = $this->input("break_start_$i");
                $end = $this->input("break_end_$i");

                if ($start && $end) {
                    try {
                        $startTime = Carbon::createFromFormat('H:i', $start);
                        $endTime = Carbon::createFromFormat('H:i', $end);

                        if ($startTime->lt($clockIn) || $endTime->gt($clockOut)) {
                            $validator->errors()->add("break_start_$i", '休憩時間が勤務時間外です。');
                        }
                    } catch (\Exception $e) {
                        // 無効な時刻はスキップ（他のバリデーションで弾くため）
                    }
                }
            }
        });
    }
}
