<?php

namespace App\Http\Requests;

use App\Models\QualificationSubdomain;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PlanRegisterSubdomainRequest extends FormRequest
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
            'subdomains' => ['required', 'array', 'min:1'],
            'subdomains.*.id' => ['required', 'integer', 'exists:qualification_subdomains,qualification_subdomains_id'],
            'subdomains.*.weight' => ['required', 'integer', 'min:1', 'max:999'],
            'no_study_days' => ['array'],
            'no_study_days.*' => ['date'],
        ];
    }

    public function messages(): array
    {
        // バリデーションメッセージの上書き
        return [
            'exam_date.after' => '受験日は勉強開始日以降の日付を入力してください。',
            'subdomains.*.weight.min' => 'サブ分野の重みは1以上で入力してください。',
            'subdomains.*.weight.max' => 'サブ分野の重みは999以下で入力してください。',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        // 追加ルール（日付・サブ分野の整合性）
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

            $subdomains = $this->input('subdomains');
            if (is_array($subdomains)) {
                // 同じサブ分野の重複を禁止
                $ids = collect($subdomains)->pluck('id');
                if ($ids->count() !== $ids->unique()->count()) {
                    $validator->errors()->add('subdomains', '同じ分野があります。');
                }

                $qualificationId = $this->input('qualification_id');
                if ($qualificationId) {
                    // サブ分野が資格に紐づいているかを検証
                    $subdomainIds = collect($subdomains)->pluck('id')->unique()->values();
                    if ($subdomainIds->isNotEmpty()) {
                        $subdomainsCount = QualificationSubdomain::query()
                            ->join(
                                'qualification_domains',
                                'qualification_subdomains.qualification_domains_id',
                                '=',
                                'qualification_domains.qualification_domains_id',
                            )
                            ->where('qualification_domains.qualification_id', (int) $qualificationId)
                            ->whereIn('qualification_subdomains.qualification_subdomains_id', $subdomainIds)
                            ->count();

                        if ($subdomainsCount !== $subdomainIds->count()) {
                            $validator->errors()->add('subdomains', '資格に紐づかないサブ分野が含まれています。');
                        }
                    }
                }

                // 重みの合計が0でないことを確認
                $totalWeight = collect($subdomains)->sum('weight');
                if ($totalWeight <= 0) {
                    $validator->errors()->add('subdomains', '重みの合計が0です。');
                }
            }
        });
    }
}
