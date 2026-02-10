<?php

return [
    /*
    | アプリ内で使う成功・エラーメッセージ（コントローラーやJSに渡す用）
    | 使用例: __('messages.success.updated')
    */
    'success' => [
        'updated' => '更新しました',
        'saved' => '保存しました',
        'created' => '登録しました',
        'deleted' => '削除しました',
        'backup_created' => 'バックアップを作成しました',
        'backup_deleted' => 'バックアップを削除しました',
        'setting_saved' => '自動バックアップ設定を保存しました',
        'plan_created' => '計画を生成しました',
        'reschedule_done' => 'リスケジュールが完了しました。',
        'record_saved' => '実績を保存しました。',
    ],

    'error' => [
        'update_failed' => '更新に失敗しました',
        'save_failed' => '保存に失敗しました',
        'delete_failed' => '削除に失敗しました',
        'create_failed' => '登録に失敗しました',
        'network_error' => '通信に失敗しました。',
        'backup_failed' => 'バックアップに失敗しました',
        'setting_save_failed' => '設定の保存に失敗しました',
        'load_failed' => '情報の取得に失敗しました。',
        'validation' => [
            'select_qualification' => '対象資格を選択してください。',
            'study_hours_required' => '1日の学習時間を入力してください。',
            'start_date_required' => 'リスケ開始日を入力してください。',
            'start_date_required_plan' => '勉強開始日を入力してください。',
            'exam_date_required' => '受験日を入力してください。',
            'margin_rate_range' => '余裕確保率は0〜99で入力してください。',
            'domains_required' => '分野と重みを入力してください。',
            'subdomains_required' => 'サブ分野と重みを入力してください。',
            'minutes_required' => '実績分数を入力してください。',
            'weight_fetch_failed' => '分野の重みが取得できませんでした。',
        ],
    ],

    'confirm' => [
        'reschedule' => '明日以降の計画を削除して再生成します。よろしいですか？',
        'csv_upload' => 'CSVのヘッダーは "qualification_name,domain_name,subdomain_name" が必須です。続けますか？',
    ],
];
