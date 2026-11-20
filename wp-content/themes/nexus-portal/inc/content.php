<?php
/**
 * Editorial content for the Japanese and English versions of the portal.
 */

defined( 'ABSPATH' ) || exit;

function nexus_portal_copy( string $language ): array {
	if ( 'en' === $language ) {
		return array(
			'title'            => 'NEXUS Inc. | Good things grow together',
			'meta_description' => 'NEXUS Inc. brings people together through C365 and CHAIN NEXUS. Explore cafés, social events, mobility, cruises and diving in Japan.',
			'skip'             => 'Skip to main content',
			'nav_about'        => 'About',
			'nav_brands'       => 'Brands',
			'nav_contact'      => 'Get in touch',
			'hero_label'       => 'HUMAN CONNECTIONS. REAL EXPERIENCES.',
			'hero_description' => 'Over a meal, across the city or out on the water, we bring people together through experiences worth sharing.',
			'explore'          => 'Explore our brands',
			'about_label'      => 'Our philosophy',
			'about_title'      => 'A shared interest can take you further.',
			'about_paragraphs' => array(
				'An extra chair, an unfamiliar road, a first breath underwater. The moments we remember often start with someone saying, “Want to come along?” We love what happens when a little curiosity makes the day bigger than we planned.',
				'At C365 Cafe, it starts around a table. With VINCROSS, a film or a glass of wine gets people talking. CHAIN NEXUS puts a different set of keys in your hand. C365 CRUISE takes you out into Yokohama Harbor, EXCELLENT MARINE to the waters below Mount Fuji, and Diving Shop C365 beneath the surface. UMIMORI 365 works with small-boat operators to make time on the water safer.',
				'It takes nerve to show up somewhere new or ask someone to come with you. At NEXUS, we build experiences through C365 and CHAIN NEXUS that make taking that first step feel worth it. We want people to leave with a reason to see each other again and a new plan already taking shape.',
			),
			'about_statement'  => 'Who’s coming with you next?',
			'brands_title'     => 'Our brands',
			'brands_lead'      => 'Find your next experience, from the city to the sea.',
			'service_action'   => 'Visit website',
			'contact_label'    => 'Contact',
			'contact_title'    => 'Let’s connect',
			'contact_text'     => 'Use the form to tell us what you have in mind, whether it’s a partnership, a business inquiry or a question about NEXUS.',
		);
	}

	return array(
		'title'            => '株式会社NEXUS | C365・CHAIN NEXUS ブランドポータル',
		'meta_description' => '株式会社NEXUSはC365とCHAIN NEXUSを通じて、カフェ、交流イベント、モビリティ、クルーズ、ダイビングなど、人と人がつながる体験を届けます。',
		'skip'             => '本文へスキップ',
		'nav_about'        => 'NEXUSについて',
		'nav_brands'       => 'ブランド',
		'nav_contact'      => 'お問い合わせ',
		'hero_label'       => 'HUMAN CONNECTIONS. REAL EXPERIENCES.',
		'hero_description' => '食卓を囲む時間、街での出会い、海への冒険。NEXUSは、体験を通じて人と人がつながる場をつくります。',
		'explore'          => 'ブランドを見る',
		'about_label'      => '私たちが大切にすること',
		'about_title'      => 'つながりから、次の景色へ。',
		'about_paragraphs' => array(
			'初めて会う人と、好きな映画の話に夢中になる。いつもとは違うクルマで、まだ知らない道へ向かう。ほんの少し踏み出しただけで、見慣れた街まで新しく見える。私たちは、そんな瞬間をもっと増やしたいと思っています。',
			'横浜のC365 Cafeで食卓を囲み、VINCROSSで好きなことを語る。CHAIN NEXUSのクルマで街を抜け、C365 CRUISEの船上から横浜の夜景を眺める。EXCELLENT MARINEで富士山を望む駿河湾へ、Diving Shop C365で海の中へ。海守365は、そうした海の体験を支える小型船の安全運航をサポートします。',
			'初めての場所に出かけたり、誰かを誘ったりするには、少し勇気がいります。だから株式会社NEXUSは、C365とCHAIN NEXUSを通じて、その一歩を踏み出したくなる場をつくります。一日を一緒に過ごした人と「今度は何をしよう」と話せる。そんなふうに次の約束が生まれるところから、コミュニティは育っていくと信じています。',
		),
		'about_statement'  => '次は、誰と、どこへ行こう。',
		'brands_title'     => 'Our brands',
		'brands_lead'      => '街から海まで、次に出かけたくなる体験を。',
		'service_action'   => 'サイトを見る',
		'contact_label'    => 'お問い合わせ',
		'contact_title'    => 'Let’s connect',
		'contact_text'     => '事業連携のご相談や株式会社NEXUSへのご質問は、下のフォームからお寄せください。',
	);
}

/**
 * Each service links to its own website, including the café's new address.
 */
function nexus_portal_projects( string $language ): array {
	$english = 'en' === $language;

	return array(
		array(
			'slug'        => 'cafe',
			'name'        => 'C365 Cafe & Restaurant YOKOYAMA',
			'category'    => $english ? 'Café & dining' : 'カフェ・レストラン',
			'description' => $english
				? 'A café, restaurant and marine shop in Yokohama, where a meal can turn into a new connection.'
				: '横浜のカフェ・レストランとマリンショップ。食事を楽しむひとときから、新しい会話が生まれます。',
			'url'         => 'https://cafe.c365.jp/',
			'label'       => 'C365 CAFE',
			'image'       => 'cafe.webp',
		),
		array(
			'slug'        => 'vincross',
			'name'        => 'VINCROSS',
			'category'    => $english ? 'Social events' : '交流イベント',
			'description' => $english
				? 'From film and wine to seasonal gatherings, meet people in Yokohama through the things you enjoy.'
				: '映画やワイン、季節のイベント。好きなことをきっかけに、横浜で新しい人と出会うコミュニティ。',
			'url'         => 'https://vincross.c365.jp/',
			'label'       => 'VINCROSS',
			'image'       => 'vincross.webp',
		),
		array(
			'slug'        => 'chain-nexus',
			'name'        => 'CHAIN NEXUS',
			'category'    => $english ? 'Luxury mobility' : 'プレミアムモビリティ',
			'description' => $english
				? 'Choose a different way to get there, from luxury car rentals and car sharing to motorcycles and boats.'
				: '輸入車のレンタルやカーシェアから、バイクやボートまで。移動そのものを楽しむための選択肢。',
			'url'         => 'https://chain-nexus.co.jp/',
			'label'       => 'CHAIN NEXUS',
			'image'       => 'chain-nexus.webp',
		),
		array(
			'slug'        => 'cruise',
			'name'        => 'C365 CRUISE',
			'category'    => $english ? 'Private cruises' : '貸切クルーズ',
			'description' => $english
				? 'See Yokohama and Minato Mirai from the water on a private daytime or evening cruise.'
				: '横浜・みなとみらいを海から眺める貸切クルーズ。昼の港も夜景も、大切な人たちとゆっくり楽しめます。',
			'url'         => 'https://cruise.c365.jp/',
			'label'       => 'C365 CRUISE',
			'image'       => 'cruise.webp',
		),
		array(
			'slug'        => 'excellentmarine',
			'name'        => 'EXCELLENT MARINE',
			'category'    => $english ? 'Jetboat cruises' : 'ジェットボートクルーズ',
			'description' => $english
				? 'Set out from Numazu on a private jetboat cruise with views of Suruga Bay and Mount Fuji.'
				: '沼津から駿河湾へ。富士山を望む海を、貸切ジェットボートで巡ります。',
			'url'         => 'https://excellentmarine.jp/',
			'label'       => 'EXCELLENT MARINE',
			'image'       => 'excellentmarine-scarab-255.webp',
			'image_alt'   => $english ? 'Scarab 255 on Suruga Bay with Mount Fuji in the background' : '富士山を背景に駿河湾を走るScarab 255',
		),
		array(
			'slug'        => 'umimori',
			'name'        => $english ? 'UMIMORI 365' : '海守365',
			'category'    => $english ? 'Marine operations & safety' : '船舶運航・安全サポート',
			'description' => $english
				? 'Practical support for small-vessel operations, from passenger route registration to safety management.'
				: '小型船舶の運航と安全管理を支援。旅客航路の届出など、海の事業に必要な実務をサポートします。',
			'url'         => 'https://umimori.c365.jp/',
			'label'       => 'UMIMORI 365',
			'image'       => 'umimori.webp',
		),
		array(
			'slug'        => 'dive',
			'name'        => 'Diving Shop C365',
			'category'    => $english ? 'Scuba diving' : 'スキューバダイビング',
			'description' => $english
				? 'Start in Sakuragicho, Yokohama, and head underwater with introductory dives, PADI courses and dive trips.'
				: '横浜・桜木町から海の中へ。体験ダイビング、PADI講習、ツアーを通じて、潜る楽しさを広げます。',
			'url'         => 'https://dive.c365.jp/',
			'label'       => 'C365 DIVE',
			'image'       => 'dive.webp',
		),
	);
}
