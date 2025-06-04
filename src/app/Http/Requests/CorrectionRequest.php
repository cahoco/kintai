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
        $rules = [
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i', 'after:clock_in'],
            'note' => ['required', 'string', 'max:255'],
        ];
        for ($i = 1; $i <= 10; $i++) {
            $rules["break_start_$i"] = ['nullable', 'date_format:H:i'];
            $rules["break_end_$i"] = ['nullable', 'date_format:H:i', "after:break_start_$i"];
        }
        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'clock_in.required' => '出勤時刻を入力してください。',
            'clock_in.date_format' => '出勤時刻は「HH:MM」形式で入力してください。',
            'clock_out.required' => '退勤時刻を入力してください。',
            'clock_out.date_format' => '退勤時刻は「HH:MM」形式で入力してください。',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です。',
            'note.required' => '備考を記入してください。',
            'note.max' => '備考は255文字以内で入力してください。',
        ];
        for ($i = 1; $i <= 10; $i++) {
            $messages["break_start_{$i}.date_format"] = "休憩{$i}の開始は「HH:MM」形式で入力してください。";
            $messages["break_end_{$i}.date_format"] = "休憩{$i}の終了は「HH:MM」形式で入力してください。";
            $messages["break_end_{$i}.after"] = "休憩{$i}の終了は開始時刻より後にしてください。";
        }
        return $messages;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $clockIn = Carbon::createFromFormat('H:i', $this->clock_in);
                $clockOut = Carbon::createFromFormat('H:i', $this->clock_out);
            } catch (\Exception $e) {
                return;
            }
            $intervals = [];
            for ($i = 1; $i <= 10; $i++) {
                $start = $this->input("break_start_$i");
                $end = $this->input("break_end_$i");
                if ($start && $end) {
                    try {
                        $startTime = Carbon::createFromFormat('H:i', $start);
                        $endTime = Carbon::createFromFormat('H:i', $end);
                        if ($startTime->gt($clockOut)) {
                            $validator->errors()->add("break_start_$i", "休憩時間が不適切な値です。");
                        }
                        if ($endTime->gt($clockOut)) {
                            $validator->errors()->add("break_end_$i", "出勤時間もしくは退勤時間が不適切な値です。");
                        }
                        foreach ($intervals as $j => [$prevStart, $prevEnd]) {
                            if ($startTime->lt($prevEnd) && $endTime->gt($prevStart)) {
                                $validator->errors()->add("break_start_$i", "休憩{$i}が休憩" . ($j + 1) . "と重複しています。");
                                break;
                            }
                        }
                        $intervals[] = [$startTime, $endTime];
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        });
    }

}
