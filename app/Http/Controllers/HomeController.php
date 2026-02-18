<?php

namespace App\Http\Controllers;

use App\Models\Qualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 一般ユーザーのホーム表示
        return view('home', $this->buildViewData());
    }

    public function adminIndex(Request $request)
    {
        // 管理者としてホーム表示（管理者フラグを付与）
        $data = $this->buildViewData();
        $data['isAdmin'] = true;

        return view('home', $data);
    }

    private function buildViewData(): array
    {
        // 資格一覧（有効なもののみ）
        $qualifications = Qualification::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['qualification_id', 'name']);

        $userId = Auth::id();
        $targets = collect();
        if ($userId) {
            // ログインユーザーの目標資格とアクティブ計画を取得
            $targets = DB::table('user_qualification_targets as uqt')
                ->join('qualification as q', 'uqt.qualification_id', '=', 'q.qualification_id')
                ->leftJoin('study_plans as sp', function ($join) {
                    $join->on('sp.user_qualification_targets_id', '=', 'uqt.user_qualification_targets_id')
                        ->where('sp.is_active', true);
                })
                ->where('uqt.user_id', $userId)
                ->orderBy('uqt.created_at', 'desc')
                ->select([
                    'uqt.user_qualification_targets_id',
                    'uqt.exam_date',
                    'q.name as qualification_name',
                    'sp.study_plans_id as active_plan_id',
                ])
                ->get();
        }

        // ビューに渡すデータをまとめる
        return compact('qualifications', 'targets');
    }
}
