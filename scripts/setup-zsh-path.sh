#!/usr/bin/env bash
# Add Homebrew libpq bin to PATH in ~/.zshrc (for pg_restore, psql, etc.)
# Run from repo root: ./scripts/setup-zsh-path.sh

LINE='export PATH="/opt/homebrew/opt/libpq/bin:$PATH"'
ZSHRC="${HOME}/.zshrc"

if grep -q 'opt/libpq/bin' "$ZSHRC" 2>/dev/null; then
  echo "PATH for libpq is already set in ~/.zshrc"
  exit 0
fi

echo "" >> "$ZSHRC"
echo "# PostgreSQL client tools (pg_restore, psql) via Homebrew libpq" >> "$ZSHRC"
echo "$LINE" >> "$ZSHRC"
echo "Added libpq bin to PATH in ~/.zshrc. Run: source ~/.zshrc"
