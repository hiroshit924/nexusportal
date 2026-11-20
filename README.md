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
4. WordPressのホームURLを `https://c365.jp` に設定し、日本語と英語のトップページ・会社概要、各ブランドへのリンク、ファビコンを確認します。
5. 下記の手順でMicrosoft 365を設定し、管理画面からのテスト送信と公開フォームからの送信を確認します。

```sh
git archive --format=zip --prefix=nexus-portal/ --output=nexus-portal.zip HEAD:wp-content/themes/nexus-portal
```

日英の文章とサービス一覧は `wp-content/themes/nexus-portal/inc/content.php` で管理します。会社概要はテーマに含まれ、通常は `/company/`、基本パーマリンクを使用する環境などでは `/?company=1` で表示します。いずれも `lang=en` で英語版に切り替わります。会社情報はテーマの `company.php` と `inc/company.php` で管理します。

初回訪問時はNEXUSのロゴから7サービスの公式ロゴへ展開します。メインロゴは従来のマークとNEXUSの文字を継承し、サービス一覧ではCHAIN NEXUSの公式ロゴを使用しています。ポータルの公開ページでは、テーマ付属のNEXUSファビコンを表示します。WordPress管理画面のサイトアイコン設定は保持されます。ヒーローには、NEXUSのロゴカラーで「つながり」を表現した幾何学アートを掲載しています。サービス紹介の写真はポータル用に生成したイメージです。

デザインでは、タイポグラフィを中心に据えたステートメント、体験を先に見せる構成、コンパクトなナビゲーション、ロゴが現れてからトップへ移る短いイントロを参照しました。[Bulletproof](https://www.awwwards.com/sites/bulletproof)・[Antfood](https://www.awwwards.com/sites/antfood)・[Creative Reference Library](https://www.awwwards.com/sites/creative-reference-library)・[VINCROSS](https://vincross.c365.jp/)

## お問い合わせのMicrosoft 365設定

お問い合わせは指定したExchange OnlineメールボックスからMicrosoft Graph経由で送信され、メールのコピーは送信済みアイテムに残ります。WordPressにはフォームの送信内容を保存しません。パスワード再設定など、`wp_mail()` を利用するWordPressのメールにはサイト側のメール設定が適用されます。

### Microsoft 365管理者による準備

1. Microsoft Entra IDでこのフォーム専用の単一テナントアプリを登録し、テナントID、アプリケーション（クライアント）ID、クライアントシークレットの**値**、エンタープライズアプリケーションのオブジェクトIDを確認します。
2. Exchange Online PowerShellで、送信元メールボックスだけを含むスコープに `Application Mail.Send` を割り当てます。アプリのオブジェクトIDには「エンタープライズ アプリケーション」に表示される値を使用します。送信権限はExchange OnlineのRBACだけで管理します。Entra側に組織全体の `Mail.Send` が付与済みの場合、その権限が加算されるため削除します。[Exchange OnlineのアプリケーションRBAC](https://learn.microsoft.com/ja-jp/exchange/permissions-exo/application-rbac)

```powershell
Connect-ExchangeOnline

$clientId = '<アプリケーションのクライアントID>'
$servicePrincipalId = '<エンタープライズアプリケーションのオブジェクトID>'
$mailboxId = '<送信元メールボックスのEntraオブジェクトID>'
$scopeName = 'NEXUS Portal Sender'
$filter = "ExternalDirectoryObjectId -eq '$mailboxId'"

Get-Recipient -Filter $filter | Format-Table DisplayName, PrimarySmtpAddress
```

一覧に送信元メールボックス1件だけが表示されたことを確認してから、同じPowerShellセッションで次に進みます。[利用可能なフィルター属性](https://learn.microsoft.com/en-us/powershell/exchange/recipientfilter-properties?view=exchange-ps#externaldirectoryobjectid)

```powershell
New-ServicePrincipal -AppId $clientId -ObjectId $servicePrincipalId -DisplayName 'NEXUS Portal'
New-ManagementScope -Name $scopeName -RecipientRestrictionFilter $filter
New-ManagementRoleAssignment -Name 'NEXUS Portal Send Mail' -App $servicePrincipalId -Role 'Application Mail.Send' -CustomResourceScope $scopeName
Test-ServicePrincipalAuthorization -Identity $servicePrincipalId -Resource '<送信元メールボックスのアドレス>'
Test-ServicePrincipalAuthorization -Identity $servicePrincipalId -Resource '<別のメールボックスのアドレス>'
```

送信元で `InScope=True`、別のメールボックスで `InScope=False` を確認します。Graphの実リクエストに権限変更が反映されるまで、Microsoftのキャッシュにより30分から2時間かかる場合があります。[権限テスト](https://learn.microsoft.com/en-us/powershell/module/exchangepowershell/test-serviceprincipalauthorization?view=exchange-ps)

### WordPress管理者による設定と確認

1. 公開サーバーから `login.microsoftonline.com` と `graph.microsoft.com` へのHTTPS通信を許可します。
2. 「設定 → NEXUS お問い合わせ」でテナントID、クライアントID、クライアントシークレット、送信元メールボックスのEntraオブジェクトIDまたはUPN、問い合わせの受信先メールアドレスを設定します。管理画面からシークレットを保存する場合は、PHPのOpenSSL拡張が必要です。サーバー側で管理する場合は、`wp-config.php` に `NEXUS_PORTAL_M365_CLIENT_SECRET` を定義できます。シークレットはソース管理の外で保管し、有効期限に合わせて更新します。
3. 設定を保存し、管理画面でテスト送信します。成功表示はGraphが送信を受け付けたことを示します。受信箱でもメールを確認し、公開フォームから日英それぞれ送信して、受信メールの返信先がフォームの入力アドレスになることを確かめます。[Microsoft Graphの送信API](https://learn.microsoft.com/ja-jp/graph/api/user-sendmail?view=graph-rest-1.0)

WordPressの認証用ソルトを変更した場合は、管理画面に保存したシークレットを再入力します。ページキャッシュを使う場合は、フォームのあるトップページのキャッシュを12時間未満に設定し、フォームのPOSTと `contact=sent` の応答をキャッシュ対象外にします。
