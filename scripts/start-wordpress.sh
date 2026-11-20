#!/usr/bin/env bash
set -euo pipefail

cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.."

if [[ ! -e .env ]]; then
  (
    umask 077
    set -o noclobber
    printf 'NEXUS_DB_PASSWORD=%s\nNEXUS_DB_ROOT_PASSWORD=%s\n' \
      "$(openssl rand -hex 24)" "$(openssl rand -hex 24)" > .env
  )
fi

site_url="${NEXUS_WP_URL:-http://localhost:${NEXUS_WP_PORT:-8080}}"
wp_cli=(docker compose run --no-deps --rm -T wpcli wp)

# Apache and WP-CLI both read the public theme as a non-root user.
chmod -R a+rX wp-content/themes/nexus-portal
docker compose up -d --wait wordpress

if ! "${wp_cli[@]}" core is-installed; then
  docker compose run --no-deps --rm -T wpcli sh -ec '
    admin_password=$(php -r "echo bin2hex(random_bytes(24));")
    wp core install --url="$1" --title=NEXUS --admin_user=nexus-admin \
      --admin_email=nexus-admin@example.invalid --skip-email --admin_password="$admin_password"
  ' wp-setup "$site_url"
fi

if ! "${wp_cli[@]}" theme is-active nexus-portal; then
  "${wp_cli[@]}" theme activate nexus-portal
fi

printf 'NEXUS WordPress: %s\n' "$site_url"
