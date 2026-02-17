<x-app-layout :show-navigation="false">
    @push('styles')
        @vite('resources/css/home/study-progress.css')
        @vite('resources/css/home/profile-menu.css')
    @endpush

    @php($isAdmin = $isAdmin ?? request()->routeIs('admin.*'))
    <div class="h-screen bg-gray-100 overflow-hidden">
        <x-sidebar-layout context="study-progress" :is-admin="$isAdmin">
            <div class="study-progress-page" data-study-progress>
                    <div class="study-progress-container">
                        <div class="study-progress-controls">
                            <div class="study-progress-control">
                                <label class="study-progress-label" for="study-progress-target">資格選択</label>
                                <select id="study-progress-target" class="study-progress-select"
                                    data-study-progress-target>
                                    @forelse ($targets as $target)
                                        <option value="{{ $target->user_qualification_targets_id }}"
                                            @selected($initialData['selected_target_id'] === $target->user_qualification_targets_id)>
                                            {{ $target->qualification_name }}
                                        </option>
                                    @empty
                                        <option value="">対象の資格がありません</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="study-progress-control">
                                <label class="study-progress-label">期間選択</label>
                                <div class="study-progress-date-range">
                                    <input type="date" class="study-progress-date" data-study-progress-start
                                        value="{{ $initialData['period_start'] ?? '' }}" />
                                    <span class="study-progress-date-separator">〜</span>
                                    <input type="date" class="study-progress-date" data-study-progress-end
                                        value="{{ $initialData['period_end'] ?? '' }}" />
                                    <button type="button" class="study-progress-apply" data-study-progress-apply>
                                        更新
                                    </button>
                                </div>
                            </div>

                            <div class="study-progress-summary" data-study-progress-summary>
                                <div class="study-progress-summary-item">
                                    <span class="study-progress-summary-label">累積達成率</span>
                                    <span class="study-progress-summary-value" data-study-progress-summary-rate></span>
                                </div>
                                <div class="study-progress-summary-item">
                                    <span class="study-progress-summary-label">累積実績</span>
                                    <span class="study-progress-summary-value" data-study-progress-summary-actual></span>
                                </div>
                            </div>
                        </div>

                        <div class="study-progress-grid">
                            <section class="study-progress-card">
                                <div class="study-progress-card-header">
                                    <div>
                                        <h3 class="study-progress-card-title">累積進捗グラフ</h3>
                                        <p class="study-progress-card-subtitle">実績累積 / 計画累積</p>
                                    </div>
                                </div>
                                <div class="study-progress-chart">
                                    <canvas id="study-progress-cumulative"></canvas>
                                </div>
                                <p class="study-progress-empty" data-study-progress-empty="cumulative">
                                    累積データがありません。
                                </p>
                            </section>

                            <section class="study-progress-card">
                                <div class="study-progress-card-header">
                                    <div>
                                        <h3 class="study-progress-card-title">分野別達成率（全期間）</h3>
                                        <p class="study-progress-card-subtitle">分野ごとの達成度合い</p>
                                    </div>
                                </div>
                                <div class="study-progress-domain-list" data-study-progress-domain-list></div>
                                <p class="study-progress-empty" data-study-progress-empty="domain">
                                    分野データがありません。
                                </p>
                            </section>

                            <section class="study-progress-card study-progress-card-wide">
                                <div class="study-progress-card-header">
                                    <div>
                                        <h3 class="study-progress-card-title">期間別達成率</h3>
                                        <p class="study-progress-card-subtitle">日ごとの達成率</p>
                                    </div>
                                    <div class="study-progress-legend">
                                        <span class="study-progress-legend-item is-good">100%以上</span>
                                        <span class="study-progress-legend-item is-mid">80%以上</span>
                                        <span class="study-progress-legend-item is-low">80%未満</span>
                                    </div>
                                </div>
                                <div class="study-progress-chart">
                                    <canvas id="study-progress-period"></canvas>
                                </div>
                                <p class="study-progress-empty" data-study-progress-empty="period">
                                    期間内の実績データがありません。
                                </p>
                            </section>
                        </div>
                </div>
            </div>
        </x-sidebar-layout>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            window.studyProgressInitialData = @json($initialData);
        </script>
        @vite('resources/js/home/study-progress.js')
    @endpush
</x-app-layout>
