## 主要ディレクトリの説明

| ディレクトリ | 役割 |
|-------------|------|
| **app/** | アプリケーションのコアコード。Controllers（HTTP）、Models（Eloquent）、Services、Jobs、認証まわり（Actions/Fortify・Jetstream）など。 |
| **bootstrap/** | Laravel の起動処理（`app.php`）とキャッシュ。 |
| **config/** | 設定ファイル（DB・認証・メール・バックアップなど）。 |
| **database/** | マイグレーション、シーダー、ファクトリ、DB用テンプレート。 |
| **docs/** | ドキュメント（本ファイル、操作マニュアル、テスト仕様など）。 |
| **lang/** | 多言語用の文言（日本語など）。 |
| **public/** | 公開ディレクトリ。`index.php`、ビルド済みアセット、画像など。 |
| **resources/** | ソースのビュー（Blade）、CSS、JS、Markdown（利用規約・ポリシー）。 |
| **routes/** | ルート定義（`web.php`・`api.php`・`console.php`）。 |
| **scripts/** | 運用・開発用スクリプト（例: ツリー生成 `make_tree.py`）。 |
| **storage/** | ログ・キャッシュ・セッション・アップロード・バックアップ（ツリーでは一部のみ表示）。 |
| **tests/** | Feature テスト・Unit テスト。 |

※ `vendor`・`node_modules`・ビルド成果物などはツリーから除外しています。

---

## ツリー

```
├── stapla
│   ├── app  # アプリケーションのコアコード
│   │   ├── Actions  # 認証・ユーザー操作などのアクションクラス
│   │   │   ├── Fortify  # 登録・パスワード・プロフィール更新など
│   │   │   │   ├── CreateNewUser.php
│   │   │   │   ├── PasswordValidationRules.php
│   │   │   │   ├── ResetUserPassword.php
│   │   │   │   ├── UpdateUserPassword.php
│   │   │   │   └── UpdateUserProfileInformation.php
│   │   │   └── Jetstream  # アカウント削除など
│   │   │       └── DeleteUser.php
│   │   ├── Console  # Artisan コンソール
│   │   │   └── Commands  # artisan コマンド（バックアップ・LINE通知など）
│   │   │       ├── RunAutoBackup.php
│   │   │       ├── SendLineEveningNotification.php
│   │   │       └── SendLineMorningNotification.php
│   │   ├── Http  # HTTP リクエスト処理
│   │   │   ├── Controllers  # コントローラ
│   │   │   │   ├── Admin  # 管理画面用コントローラ
│   │   │   │   │   ├── AdminBackupsController.php
│   │   │   │   │   ├── AdminQualificationsController.php
│   │   │   │   │   └── AdminUsersController.php
│   │   │   │   ├── Auth  # 認証用コントローラ
│   │   │   │   │   └── LoginViaEmailController.php
│   │   │   │   ├── CalendarController.php
│   │   │   │   ├── Controller.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── LineWebhookController.php
│   │   │   │   ├── PlanRegisterController.php
│   │   │   │   ├── PlanRegisterSubdomainController.php
│   │   │   │   ├── PlanRescheduleController.php
│   │   │   │   ├── QualificationController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   ├── StudyProgressController.php
│   │   │   │   └── StudyRecordController.php
│   │   │   ├── Middleware  # HTTP ミドルウェア
│   │   │   │   ├── AdminMiddleware.php
│   │   │   │   ├── LineWebhookRawBody.php
│   │   │   │   └── RedirectIfAuthenticated.php
│   │   │   ├── Requests  # フォームリクエスト（バリデーション）
│   │   │   │   ├── Admin
│   │   │   │   │   ├── AdminBackupSettingsRequest.php
│   │   │   │   │   ├── AdminDomainStoreRequest.php
│   │   │   │   │   ├── AdminQualificationNameRequest.php
│   │   │   │   │   ├── AdminQualificationsImportRequest.php
│   │   │   │   │   ├── AdminSubdomainStoreRequest.php
│   │   │   │   │   └── AdminUserUpdateRequest.php
│   │   │   │   ├── CalendarEventsRequest.php
│   │   │   │   ├── PlanRegisterDomainRequest.php
│   │   │   │   ├── PlanRegisterSubdomainRequest.php
│   │   │   │   ├── PlanRescheduleDataRequest.php
│   │   │   │   ├── PlanRescheduleStoreRequest.php
│   │   │   │   ├── SettingsDestroyRequest.php
│   │   │   │   ├── StudyProgressDataRequest.php
│   │   │   │   └── StudyRecordStoreRequest.php
│   │   │   └── Responses  # ログイン・登録時のレスポンス
│   │   │       ├── LoginResponse.php
│   │   │       └── RegisterResponse.php
│   │   ├── Jobs  # キューで実行するジョブ
│   │   │   └── RunBackupJob.php
│   │   ├── Mail  # 送信メールクラス
│   │   │   └── RegistrationCompleteMail.php
│   │   ├── Models  # Eloquent モデル
│   │   │   ├── BackupFile.php
│   │   │   ├── BackupSetting.php
│   │   │   ├── BaseModel.php
│   │   │   ├── LineAccount.php
│   │   │   ├── Qualification.php
│   │   │   ├── QualificationDomain.php
│   │   │   ├── QualificationSubdomain.php
│   │   │   ├── StudyPlan.php
│   │   │   ├── StudyPlanItem.php
│   │   │   ├── StudyRecord.php
│   │   │   ├── Todo.php
│   │   │   ├── User.php
│   │   │   ├── UserDomainPreference.php
│   │   │   ├── UserNoStudyDay.php
│   │   │   ├── UserQualificationTarget.php
│   │   │   └── UserSubdomainPreference.php
│   │   ├── Providers  # サービスプロバイダ
│   │   │   ├── AppServiceProvider.php
│   │   │   ├── FortifyServiceProvider.php
│   │   │   └── JetstreamServiceProvider.php
│   │   ├── Services  # ビジネスロジック（バックアップ・LINE など）
│   │   │   ├── BackupService.php
│   │   │   └── LineMessagingService.php
│   │   └── View
│   │       └── Components  # Blade レイアウトコンポーネント
│   │           ├── AppLayout.php
│   │           └── GuestLayout.php
│   ├── bootstrap  # Laravel 起動・キャッシュ
│   │   ├── app.php
│   │   └── providers.php
│   ├── config  # 設定ファイル
│   │   ├── app.php
│   │   ├── auth.php
│   │   ├── backup.php
│   │   ├── cache.php
│   │   ├── database.php
│   │   ├── filesystems.php
│   │   ├── fortify.php
│   │   ├── jetstream.php
│   │   ├── logging.php
│   │   ├── mail.php
│   │   ├── queue.php
│   │   ├── sanctum.php
│   │   ├── services.php
│   │   └── session.php
│   ├── database  # DB まわり
│   │   ├── factories  # モデルファクトリ
│   │   │   └── UserFactory.php
│   │   ├── migrations  # マイグレーション
│   │   │   ├── 0001_01_01_000000_create_users_table.php
│   │   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   │   ├── 2025_12_18_000944_add_two_factor_columns_to_users_table.php
│   │   │   ├── 2025_12_18_000957_create_personal_access_tokens_table.php
│   │   │   ├── 2026_01_29_015518_add_stapla_columns_to_users_table.php
│   │   │   ├── 2026_01_29_021516_create_line_accounts_table.php
│   │   │   ├── 2026_01_29_060434_create_qualification_table.php
│   │   │   ├── 2026_01_30_000103_create_qualification_domains_table.php
│   │   │   ├── 2026_01_30_003619_create_qualification_subdomains_table.php
│   │   │   ├── 2026_01_30_010439_create_user_qualification_targets_table.php
│   │   │   ├── 2026_01_30_021607_create_user_no_study_days_table.php
│   │   │   ├── 2026_01_30_032344_create_user_domain_preferences_table.php
│   │   │   ├── 2026_01_30_050910_create_user_subdomain_preferences_table.php
│   │   │   ├── 2026_01_30_051531_create_study_plans_table.php
│   │   │   ├── 2026_01_30_060643_add_unique_user_qualification_to_user_qualification_targets_table.php
│   │   │   ├── 2026_01_30_061500_create_todo_table.php
│   │   │   ├── 2026_01_30_064627_create_study_plan_items_table.php
│   │   │   ├── 2026_02_01_235132_create_study_records_table.php
│   │   │   ├── 2026_02_02_022323_create_backup_files_table.php
│   │   │   ├── 2026_02_02_022748_create_backup_settings_table.php
│   │   │   ├── 2026_02_12_060000_make_line_accounts_line_user_id_nullable.php
│   │   │   ├── 2026_02_13_100000_add_notification_times_to_line_accounts.php
│   │   │   ├── 2026_02_13_120000_remove_notification_times_from_line_accounts.php
│   │   │   └── 2026_02_13_130000_add_line_notify_enabled_to_users.php
│   │   ├── seeders  # シーダー
│   │   │   ├── data
│   │   │   │   └── qualification_template.csv
│   │   │   ├── DatabaseSeeder.php
│   │   │   └── QualificationTemplateSeeder.php
│   │   ├── templates
│   │   │   └── qualification_template.csv
│   │   ├── .gitignore
│   │   └── database.sqlite
│   ├── docs  # ドキュメント
│   │   ├── FOLDER_STRUCTURE.md
│   │   ├── OPERATION_MANUAL.md
│   │   └── テスト仕様書.md
│   ├── lang  # 多言語文言（ja など）
│   │   ├── ja
│   │   │   ├── auth.php
│   │   │   ├── console.php
│   │   │   ├── messages.php
│   │   │   ├── passwords.php
│   │   │   └── validation.php
│   │   └── ja.json
│   ├── public  # 公開ディレクトリ（index.php・静的ファイル）
│   │   ├── images
│   │   │   ├── line_qr_code.png
│   │   │   ├── no-image.jpeg
│   │   │   └── stapla-logo.png
│   │   ├── .htaccess
│   │   ├── favicon.ico
│   │   ├── index.php
│   │   ├── robots.txt
│   │   └── storage
│   ├── resources
│   │   ├── css  # CSS ソース
│   │   │   ├── admin
│   │   │   │   ├── admin-backups.css
│   │   │   │   ├── admin-qualifications.css
│   │   │   │   └── admin-users.css
│   │   │   ├── home
│   │   │   │   ├── full-calendar.css
│   │   │   │   ├── home.css
│   │   │   │   ├── plan-register-modal.css
│   │   │   │   ├── profile-menu.css
│   │   │   │   ├── responsive.css
│   │   │   │   ├── study-progress.css
│   │   │   │   └── study-record-modal.css
│   │   │   ├── app.css
│   │   │   └── settings.css
│   │   ├── js  # JavaScript ソース
│   │   │   ├── admin
│   │   │   │   ├── admin-backups.js
│   │   │   │   ├── admin-qualifications.js
│   │   │   │   ├── admin-users.js
│   │   │   │   └── profile-menu.js
│   │   │   ├── home
│   │   │   │   ├── home.js
│   │   │   │   ├── plan-register-subdomain.js
│   │   │   │   ├── plan-register.js
│   │   │   │   ├── plan-reschedule.js
│   │   │   │   ├── study-progress.js
│   │   │   │   └── study-record-modal.js
│   │   │   ├── app.js
│   │   │   ├── bootstrap.js
│   │   │   └── settings.js
│   │   ├── markdown
│   │   │   ├── policy.md
│   │   │   └── terms.md
│   │   └── views  # Blade テンプレート
│   │       ├── admin  # 管理画面のビュー
│   │       │   ├── backups
│   │       │   │   ├── _auto-card.blade.php
│   │       │   │   ├── _header.blade.php
│   │       │   │   ├── _history-card.blade.php
│   │       │   │   └── _manual-card.blade.php
│   │       │   ├── qualifications
│   │       │   │   ├── _create-modal.blade.php
│   │       │   │   ├── _delete-modal.blade.php
│   │       │   │   ├── _edit-modal.blade.php
│   │       │   │   ├── _header.blade.php
│   │       │   │   └── _table.blade.php
│   │       │   ├── users
│   │       │   │   ├── _delete-modal.blade.php
│   │       │   │   ├── confirm.blade.php
│   │       │   │   └── edit.blade.php
│   │       │   ├── backups.blade.php
│   │       │   ├── qualifications.blade.php
│   │       │   └── users.blade.php
│   │       ├── auth  # 認証画面のビュー
│   │       │   ├── confirm-password.blade.php
│   │       │   ├── forgot-password.blade.php
│   │       │   ├── login.blade.php
│   │       │   ├── register.blade.php
│   │       │   ├── reset-password.blade.php
│   │       │   ├── two-factor-challenge.blade.php
│   │       │   └── verify-email.blade.php
│   │       ├── components
│   │       │   ├── action-message.blade.php
│   │       │   ├── action-section.blade.php
│   │       │   ├── application-logo.blade.php
│   │       │   ├── application-mark.blade.php
│   │       │   ├── authentication-card-logo.blade.php
│   │       │   ├── authentication-card.blade.php
│   │       │   ├── banner.blade.php
│   │       │   ├── button.blade.php
│   │       │   ├── checkbox.blade.php
│   │       │   ├── confirmation-modal.blade.php
│   │       │   ├── confirms-password.blade.php
│   │       │   ├── danger-button.blade.php
│   │       │   ├── dialog-modal.blade.php
│   │       │   ├── dropdown-link.blade.php
│   │       │   ├── dropdown.blade.php
│   │       │   ├── form-section.blade.php
│   │       │   ├── input-error.blade.php
│   │       │   ├── input.blade.php
│   │       │   ├── label.blade.php
│   │       │   ├── modal.blade.php
│   │       │   ├── nav-link.blade.php
│   │       │   ├── responsive-nav-link.blade.php
│   │       │   ├── secondary-button.blade.php
│   │       │   ├── section-border.blade.php
│   │       │   ├── section-title.blade.php
│   │       │   ├── sidebar-layout.blade.php
│   │       │   └── validation-errors.blade.php
│   │       ├── emails
│   │       │   ├── password-reset.blade.php
│   │       │   └── registration-complete.blade.php
│   │       ├── home
│   │       │   ├── plan-register-choice-modal.blade.php
│   │       │   ├── plan-register-domain-modal.blade.php
│   │       │   ├── plan-register-modals.blade.php
│   │       │   ├── plan-register-subdomain-modal.blade.php
│   │       │   ├── plan-reschedule-modal.blade.php
│   │       │   ├── profile-menu.blade.php
│   │       │   ├── sidebar-nav.blade.php
│   │       │   └── study-record-modal.blade.php
│   │       ├── layouts
│   │       │   ├── app.blade.php
│   │       │   └── guest.blade.php
│   │       ├── profile
│   │       │   ├── delete-user-form.blade.php
│   │       │   ├── logout-other-browser-sessions-form.blade.php
│   │       │   ├── show.blade.php
│   │       │   ├── two-factor-authentication-form.blade.php
│   │       │   ├── update-password-form.blade.php
│   │       │   └── update-profile-information-form.blade.php
│   │       ├── vendor
│   │       │   ├── mail
│   │       │   │   └── html
│   │       │   │       └── header.blade.php
│   │       │   └── notifications
│   │       │       └── email.blade.php
│   │       ├── home.blade.php
│   │       ├── navigation-menu.blade.php
│   │       ├── settings.blade.php
│   │       └── study-progress.blade.php
│   ├── routes  # ルート定義
│   │   ├── api.php
│   │   ├── console.php
│   │   └── web.php
│   ├── scripts  # 運用・開発用スクリプト
│   │   ├── make_tree.py
│   │   └── setup-zsh-path.sh
│   ├── storage  # ログ・キャッシュ・アップロード・バックアップ
│   │   ├── app
│   │   │   ├── private
│   │   │   │   ├── .gitignore
│   │   │   │   └── backup-temp
│   │   │   ├── public
│   │   │   │   └── .gitignore
│   │   │   ├── .gitignore
│   │   │   └── templates
│   │   └── framework
│   │       └── .gitignore
│   ├── tests  # テスト
│   │   ├── Feature  # Feature テスト
│   │   │   ├── ApiTokenPermissionsTest.php
│   │   │   ├── AuthenticationTest.php
│   │   │   ├── BrowserSessionsTest.php
│   │   │   ├── CreateApiTokenTest.php
│   │   │   ├── DeleteAccountTest.php
│   │   │   ├── DeleteApiTokenTest.php
│   │   │   ├── EmailVerificationTest.php
│   │   │   ├── ExampleTest.php
│   │   │   ├── PasswordConfirmationTest.php
│   │   │   ├── PasswordResetTest.php
│   │   │   ├── PlanRescheduleTest.php
│   │   │   ├── ProfileInformationTest.php
│   │   │   ├── RegistrationTest.php
│   │   │   ├── SettingsNotificationsTest.php
│   │   │   ├── StudyProgressDataTest.php
│   │   │   ├── TwoFactorAuthenticationSettingsTest.php
│   │   │   └── UpdatePasswordTest.php
│   │   ├── Unit  # Unit テスト
│   │   │   └── ExampleTest.php
│   │   └── TestCase.php
│   ├── .dockerignore  # Docker ビルド時に無視するファイル
│   ├── .editorconfig  # エディタの共通設定
│   ├── .env  # 環境変数（本番用・秘密は含めない）
│   ├── .env.example  # 環境変数のサンプル
│   ├── .gitattributes  # Git の属性設定
│   ├── .gitignore  # Git の無視リスト
│   ├── artisan  # Laravel の CLI エントリ
│   ├── compose.yaml  # Docker Compose 設定
│   ├── composer.json  # PHP 依存関係の定義
│   ├── composer.lock  # PHP 依存のロック
│   ├── Dockerfile  # コンテナビルド定義
│   ├── package-lock.json  # npm 依存のロック
│   ├── package.json  # フロントエンド依存関係の定義
│   ├── phpunit.xml  # PHPUnit 設定
│   ├── postcss.config.js  # PostCSS 設定
│   ├── README.md  # プロジェクト説明
│   ├── stapla_backup.dump  # DB バックアップダンプ（手動など）
│   ├── tailwind.config.js  # Tailwind CSS 設定
│   └── vite.config.js  # Vite ビルド設定
```
