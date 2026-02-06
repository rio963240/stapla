<?php

namespace App\Http\Controllers;

use App\Models\QualificationDomain;
use App\Models\QualificationSubdomain;
use Illuminate\Http\JsonResponse;

class QualificationController extends Controller
{
    public function domains(int $qualificationId): JsonResponse
    {
        // 資格に紐づく有効な分野一覧を取得
        $domains = QualificationDomain::query()
            ->where('qualification_id', $qualificationId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['qualification_domains_id', 'name']);

        // フロント向けのid/name形式に整形して返却
        return response()->json(
            $domains->map(fn ($domain) => [
                'id' => $domain->qualification_domains_id,
                'name' => $domain->name,
            ]),
        );
    }

    public function subdomains(int $qualificationId): JsonResponse
    {
        // 資格に紐づく分野/サブ分野を結合して取得
        $subdomains = QualificationSubdomain::query()
            ->join(
                'qualification_domains',
                'qualification_subdomains.qualification_domains_id',
                '=',
                'qualification_domains.qualification_domains_id',
            )
            ->where('qualification_domains.qualification_id', $qualificationId)
            ->where('qualification_domains.is_active', true)
            ->where('qualification_subdomains.is_active', true)
            ->orderBy('qualification_domains.name')
            ->orderBy('qualification_subdomains.name')
            ->get([
                'qualification_subdomains.qualification_subdomains_id',
                'qualification_subdomains.name as subdomain_name',
                'qualification_domains.name as domain_name',
            ]);

        // フロント向けのid/name/domain_name形式に整形して返却
        return response()->json(
            $subdomains->map(fn ($subdomain) => [
                'id' => $subdomain->qualification_subdomains_id,
                'name' => $subdomain->subdomain_name,
                'domain_name' => $subdomain->domain_name,
            ]),
        );
    }
}
