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
        return view('home', $this->buildViewData());
    }

    public function adminIndex(Request $request)
    {
        $data = $this->buildViewData();
        $data['isAdmin'] = true;

        return view('home', $data);
    }

    private function buildViewData(): array
    {
        $qualifications = Qualification::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['qualification_id', 'name']);

        $userId = Auth::id();
        $targets = collect();
        if ($userId) {
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

        return compact('qualifications', 'targets');
    }
}
