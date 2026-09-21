#!/usr/bin/env bash
# Dev Cycle SIAKAD — menjalankan gate verifikasi (Fase 2/3/4).
#
# Dipakai SETELAH proyek Laravel sudah di-scaffold (prompt/01).
# Jalankan: bash scripts/dev-cycle.sh [filter-pest] [spec-playwright]
#
# Tahapan:
#   1) migrate + seed          (Fase 2 · Gate 2)
#   2) Pest unit/feature test  (Fase 3 · Gate 3)
#   3) npm run build           (Fase 2/4)
#   4) Playwright E2E          (Fase 4 · Gate 4, bila sudah dikonfigurasi)
set -euo pipefail

cd "$(dirname "$0")/.."   # repo root

step() { printf '\n\033[1;34m==> %s\033[0m\n' "$1"; }

# --- Fase 2 · Gate 2 : migrate + seed ---------------------------------------
step "Migrasi & seed database"
if [ -f artisan ]; then
  php artisan migrate --force
  php artisan db:seed --force
else
  echo "SKIP: artisan tidak ditemukan — jalankan prompt/01 (setup project) dulu."
fi

# --- Fase 3 · Gate 3 : Pest ------------------------------------------------
step "Unit test (Pest)"
if [ -f vendor/bin/pest ]; then
  ./vendor/bin/pest "${@:1:1}"
else
  echo "SKIP: vendor/bin/pest tidak ditemukan."
fi

# --- Fase 2/4 : build frontend ---------------------------------------------
step "Build frontend (Vite)"
if [ -f package.json ]; then
  npm run build
else
  echo "SKIP: package.json tidak ditemukan."
fi

# --- Fase 4 · Gate 4 : Playwright E2E --------------------------------------
step "QA (Playwright E2E)"
if [ -f playwright.config.ts ] || [ -f playwright.config.js ]; then
  npx playwright test "${@:2:1}"
else
  echo "SKIP: Playwright belum dikonfigurasi (lihat prompt/29)."
fi

printf '\n\033[1;32mDev cycle selesai.\033[0m\n'
