#!/usr/bin/env python3
"""Build tree from find output (sorted paths).行末に # コメントは DESCRIPTIONS、除外は EXCLUDE で指定。"""
import sys
from collections import defaultdict

# ツリーに含めないパス（このパス自身とその配下を除外）。必要なら編集してください。
EXCLUDE = [
    ".git",
    "node_modules",
    "vendor",
    "storage/framework/views",   # コンパイル済み Blade
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/testing",
    "storage/logs",
    "bootstrap/cache",
    "public/build",             # Vite ビルド成果物
    "storage/app/private/backups",  # バックアップ実体（一覧だけ残す場合は storage/app/private ごと外すか検討）
    "storage/app/public/profile-photos",  # アップロード画像
]

# 除外するファイル名（パスに関係なく）
EXCLUDE_NAMES = {".DS_Store", ".phpunit.result.cache"}

def is_excluded(path):
    if not path:
        return False
    if path.split("/")[-1] in EXCLUDE_NAMES:
        return True
    for prefix in EXCLUDE:
        if path == prefix or path.startswith(prefix + "/"):
            return True
    return False

# パス（find の出力と同じ。先頭の ./ なし）→ 行末に付けるコメント
DESCRIPTIONS = {
    "app": "アプリケーションのコアコード",
    "app/Actions": "認証・ユーザー操作などのアクションクラス",
    "app/Actions/Fortify": "登録・パスワード・プロフィール更新など",
    "app/Actions/Jetstream": "アカウント削除など",
    "app/Console": "Artisan コンソール",
    "app/Console/Commands": "artisan コマンド（バックアップ・LINE通知など）",
    "app/Http": "HTTP リクエスト処理",
    "app/Http/Controllers": "コントローラ",
    "app/Http/Controllers/Admin": "管理画面用コントローラ",
    "app/Http/Controllers/Auth": "認証用コントローラ",
    "app/Http/Middleware": "HTTP ミドルウェア",
    "app/Http/Requests": "フォームリクエスト（バリデーション）",
    "app/Http/Responses": "ログイン・登録時のレスポンス",
    "app/Jobs": "キューで実行するジョブ",
    "app/Mail": "送信メールクラス",
    "app/Models": "Eloquent モデル",
    "app/Providers": "サービスプロバイダ",
    "app/Services": "ビジネスロジック（バックアップ・LINE など）",
    "app/View/Components": "Blade レイアウトコンポーネント",
    "bootstrap": "Laravel 起動・キャッシュ",
    "config": "設定ファイル",
    "database": "DB まわり",
    "database/migrations": "マイグレーション",
    "database/seeders": "シーダー",
    "database/factories": "モデルファクトリ",
    "docs": "ドキュメント",
    "lang": "多言語文言（ja など）",
    "public": "公開ディレクトリ（index.php・静的ファイル）",
    "public/build": "Vite ビルド成果物",
    "resources/views": "Blade テンプレート",
    "resources/views/admin": "管理画面のビュー",
    "resources/views/auth": "認証画面のビュー",
    "resources/css": "CSS ソース",
    "resources/js": "JavaScript ソース",
    "routes": "ルート定義",
    "scripts": "運用・開発用スクリプト",
    "storage": "ログ・キャッシュ・アップロード・バックアップ",
    "tests": "テスト",
    "tests/Feature": "Feature テスト",
    "tests/Unit": "Unit テスト",
    "vendor": "Composer の PHP パッケージ",
    "node_modules": "npm のパッケージ",
    # ルート直下のファイル
    ".dockerignore": "Docker ビルド時に無視するファイル",
    ".editorconfig": "エディタの共通設定",
    ".env": "環境変数（本番用・秘密は含めない）",
    ".env.example": "環境変数のサンプル",
    ".gitattributes": "Git の属性設定",
    ".gitignore": "Git の無視リスト",
    "artisan": "Laravel の CLI エントリ",
    "compose.yaml": "Docker Compose 設定",
    "composer.json": "PHP 依存関係の定義",
    "composer.lock": "PHP 依存のロック",
    "Dockerfile": "コンテナビルド定義",
    "package-lock.json": "npm 依存のロック",
    "package.json": "フロントエンド依存関係の定義",
    "phpunit.xml": "PHPUnit 設定",
    "postcss.config.js": "PostCSS 設定",
    "README.md": "プロジェクト説明",
    "stapla_backup.dump": "DB バックアップダンプ（手動など）",
    "tailwind.config.js": "Tailwind CSS 設定",
    "vite.config.js": "Vite ビルド設定",
}

paths = [
    line.rstrip()
    for line in sys.stdin
    if line.strip() and line.strip() != "." and not is_excluded(line.rstrip())
]

def add_path(tree, path):
    parts = path.split('/')
    for i in range(1, len(parts) + 1):
        prefix = '/'.join(parts[:i])
        if prefix not in tree:
            tree[prefix] = []
    if path:
        parent = '/'.join(parts[:-1]) if len(parts) > 1 else ''
        if parent not in tree:
            tree[parent] = []
        if path not in tree[parent]:
            tree[parent].append(path)

tree = {}
for p in paths:
    add_path(tree, p)

def children_of(prefix):
    cand = [c for c in tree.get(prefix, []) if c]
    def key(c):
        name = c.split('/')[-1]
        is_dir = c in tree and tree[c]
        return (0 if is_dir else 1, name.lower())
    return sorted(cand, key=key)

def print_node(prefix, indent, is_last):
    name = prefix.split('/')[-1] if prefix else 'stapla'
    conn = '└── ' if is_last else '├── '
    comment = DESCRIPTIONS.get(prefix, "")
    suffix = "  # " + comment if comment else ""
    print(indent + conn + name + suffix)
    child_indent = indent + ('    ' if is_last else '│   ')
    kids = children_of(prefix)
    for i, c in enumerate(kids):
        print_node(c, child_indent, i == len(kids) - 1)

print_node('', '', False)
