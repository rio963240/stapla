<?php

namespace App\Http\Requests;

use App\Models\QualificationDomain;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PlanRegisterDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ここでは認可条件なし（認証済み前提）
        return true;
    }

    public function rules(): array
    {
        // 基本バリデーション
        return [
            'start_date' => ['required', 'date'],
            'exam_date' => ['required', 'date', 'after:start_date'],
            'qualification_id' => ['required', 'integer', 'exists:qualification,qualification_id'],
            'daily_study_time' => ['required', 'integer', 'min:1', 'max:999'],
            'buffer_rate' => ['required', 'integer', 'min:0', 'max:99'],
            'domains' => ['required', 'array', 'min:1'],
            'domains.*.id' => ['required', 'integer', 'exists:qualification_domains,qualification_domains_id'],
            'domains.*.weight' => ['required', 'integer', 'min:1', 'max:999'],
            'no_study_days' => ['array'],
            'no_study_days.*' => ['date'],
        ];
    }

    public function messages(): array
    {
        // バリデーションメッセージの上書き
        return [
            'exam_date.after' => '受験日は勉強開始日以降の日付を入力してください。',
            'domains.*.weight.min' => '分野の重みは1以上で入力してください。',
            'domains.*.weight.max' => '分野の重みは999以下で入力してください。',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        // 追加ルール（日付・分野の整合性）
        $validator->after(function (Validator $validator): void {
            $startDate = $this->input('start_date');
            $examDate = $this->input('exam_date');
            if ($startDate && $examDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $exam = Carbon::parse($examDate)->startOfDay();
                if ($exam->lt($start->copy()->addDays(7))) {
                    $validator->errors()->add('exam_date', '登録は一週間以上からです。');
                }
            }

            $domains = $this->input('domains');
            if (is_array($domains)) {
                $qualificationId = $this->input('qualification_id');
                if ($qualificationId) {
                    // 分野が資格に紐づいているかを検証
                    $domainIds = collect($domains)->pluck('id')->unique()->values();
                    if ($domainIds->isNotEmpty()) {
                        $domainsCount = QualificationDomain::query()
                            ->where('qualification_id', (int) $qualificationId)
                            ->whereIn('qualification_domains_id', $domainIds)
                            ->count();

                        if ($domainsCount !== $domainIds->count()) {
                            $validator->errors()->add('domains', '資格に紐づかない分野が含まれています。');
                        }
                    }
                }

                // 重みの合計が0でないことを確認
                $totalWeight = collect($domains)->sum('weight');
                if ($totalWeight <= 0) {
                    $validator->errors()->add('domains', '重みの合計が0です。');
                }
            }
        });
    }
}
