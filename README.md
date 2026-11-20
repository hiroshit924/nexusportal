# 株式会社NEXUS ブランドポータル

株式会社NEXUSのWordPressテーマです。「GOOD THINGS GROW TOGETHER.」を軸に、C365とCHAIN NEXUSの各サービスを日英で紹介します。公開予定地は [c365.jp](https://c365.jp/) です。

## 開発環境

Docker Engine、Docker Compose v2、OpenSSLがある環境で実行します。

```sh
bash scripts/start-wordpress.sh
```

初回起動時にローカル用のWordPressとMariaDBを初期化し、NEXUS Portalテーマを有効化します。日本語は `http://localhost:8080/`、英語は `http://localhost:8080/?lang=en` です。停止は `docker compose down` で、WordPressとデータベースのボリュームは保持されます。

開発用のWordPress管理者は `nexus-admin` です。管理画面を使う場合は、下のコマンドで開発用パスワードを生成し、表示された値で `/wp-admin/` にログインします。

```sh
docker compose run --no-deps --rm -T wpcli wp user reset-password nexus-admin --skip-email --show-password
```

## テーマを公開する

1. カフェの新URL `https://cafe.c365.jp/` が開くことを確認します。カフェの移行や旧ページのリダイレクトは、テーマの有効化とは別にホスティング側で設定します。
2. 以下のコマンドでテーマZIPを作ります。
3. 公開先のWordPressで「外観 → テーマ → 新規追加 → テーマのアップロード」からZIPを選び、有効化します。公開先にはWordPress 6.4以上とPHP 8.1以上が必要です。
4. WordPressのホームURLを `https://c365.jp` に設定し、日本語トップ、英語版 `/?lang=en`、各ブランドのリンク、メール導線を確認します。

```sh
git archive --format=zip --prefix=nexus-portal/ --output=nexus-portal.zip HEAD:wp-content/themes/nexus-portal
```

日英の文章とサービス一覧は `wp-content/themes/nexus-portal/inc/content.php` で管理します。初回訪問時のスプラッシュには各サービスの公式ロゴを使用し、トップのヘッダーとフッターにはCHAIN NEXUS公式ロゴの暗背景用配色を使っています。掲載写真はポータル用に生成したイメージです。

デザインでは、タイポグラフィを中心に据えたステートメント、体験を先に見せる構成、コンパクトなナビゲーションを参照しました。[Bulletproof](https://www.awwwards.com/sites/bulletproof)・[Antfood](https://www.awwwards.com/sites/antfood)・[Creative Reference Library](https://www.awwwards.com/sites/creative-reference-library)
