<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qualification;
use App\Models\QualificationDomain;
use App\Models\QualificationSubdomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class AdminQualificationsController extends Controller
{
    public function index(Request $request)
    {
        $qualificationId = $request->query('qualification_id');
        $domainId = $request->query('domain_id');

        $mode = 'qualification';
        $qualification = null;
        $domain = null;
        $qualifications = collect();
        $domains = collect();
        $subdomains = collect();

        if ($qualificationId) {
            $qualification = Qualification::query()
                ->where('qualification_id', $qualificationId)
                ->firstOrFail();

            $mode = 'domain';
            $domains = QualificationDomain::query()
                ->where('qualification_id', $qualificationId)
                ->orderBy('name')
                ->get();

            if ($domainId) {
                $domain = QualificationDomain::query()
                    ->where('qualification_domains_id', $domainId)
                    ->where('qualification_id', $qualificationId)
                    ->firstOrFail();

                $mode = 'subdomain';
                $subdomains = QualificationSubdomain::query()
                    ->where('qualification_domains_id', $domainId)
                    ->orderBy('name')
                    ->get();
            }
        } else {
            $qualifications = Qualification::query()
                ->orderBy('name')
                ->get();
        }

        return view('admin.qualifications', compact(
            'mode',
            'qualification',
            'domain',
            'qualifications',
            'domains',
            'subdomains',
        ));
    }

    public function updateQualification(Request $request, Qualification $qualification): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $qualification->update([
                'name' => $data['name'],
            ]);
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '変更に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '変更しました',
        ]);
    }

    public function updateDomain(Request $request, QualificationDomain $domain): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $domain->update([
                'name' => $data['name'],
            ]);
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '変更に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '変更しました',
        ]);
    }

    public function updateSubdomain(Request $request, QualificationSubdomain $subdomain): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $subdomain->update([
                'name' => $data['name'],
            ]);
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '変更に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '変更しました',
        ]);
    }

    public function destroyQualification(Qualification $qualification): RedirectResponse
    {
        try {
            $qualification->delete();
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '削除に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '削除しました',
        ]);
    }

    public function destroyDomain(QualificationDomain $domain): RedirectResponse
    {
        try {
            $domain->delete();
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '削除に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '削除しました',
        ]);
    }

    public function destroySubdomain(QualificationSubdomain $subdomain): RedirectResponse
    {
        try {
            $subdomain->delete();
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '削除に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '削除しました',
        ]);
    }
}
