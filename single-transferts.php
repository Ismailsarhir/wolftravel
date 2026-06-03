<?php

/**
 * Template for displaying single transfer posts
 * 
 * @package TransfertMarrakech
 * @since 1.0.0
 */

// Empêche l'accès direct
if (! defined('ABSPATH')) {
	exit;
}

get_header();

// Récupère le post actuel
$transfer = \TM\Utils\MetaHelper::get_current_post();
if (! $transfer instanceof \WP_Post) {
	\get_footer();
	return;
}

$transfer_id = $transfer->ID;

// Récupère les meta du transfert
$transfer_meta = \TM\Utils\MetaHelper::get_transfer_meta($transfer_id);

// Extraction des données
$title = \TM\Utils\MetaHelper::get_post_title($transfer);
$content = \apply_filters('the_content', $transfer->post_content);
$thumbnail_id = \get_post_thumbnail_id($transfer_id);
$price = $transfer_meta[\TM\Core\Constants::META_TRANSFER_PRICE] ?? '';
$price_formatted = \TM\Utils\MetaHelper::format_price($price);
$pickup = $transfer_meta[\TM\Core\Constants::META_TRANSFER_PICKUP] ?? '';
$dropoff = $transfer_meta[\TM\Core\Constants::META_TRANSFER_DROPOFF] ?? '';
$duration_estimate = $transfer_meta[\TM\Core\Constants::META_TRANSFER_DURATION_ESTIMATE] ?? '';
$description = $transfer_meta[\TM\Core\Constants::META_TRANSFER_DESCRIPTION] ?? '';

// Liste des lieux : départ et arrivée (dédoublonnés si identiques)
$cities_visited = [];
if (! empty($pickup)) {
	$cities_visited[] = $pickup;
}
if (! empty($dropoff)) {
	$cities_visited[] = $dropoff;
}
$cities_visited = \array_unique($cities_visited);

// Formatage de la durée estimée
$duration_display = \TM\Utils\MetaHelper::format_duration($duration_estimate);

// Récupère les données de destination pour le backlink
$destination_data = \TM\Utils\MetaHelper::get_destination_backlink($transfer_id);
$destination_link = $destination_data['link'];
$destination_name = $destination_data['name'];

// URL de partage
$share_url = \get_permalink($transfer_id);
$share_title = \esc_attr($title);

// Préparation des données pour les templates
$renderer = new \TM\Template\Renderer();

// Card info items pour le header
$card_info_items = [];
if (! empty($pickup)) {
	$card_info_items[] = [
		'label' => esc_html__('Departure:', 'transfertmarrakech'),
		'value' => $pickup,
	];
}
if (! empty($dropoff)) {
	$card_info_items[] = [
		'label' => esc_html__('Arrival:', 'transfertmarrakech'),
		'value' => $dropoff,
	];
}
if (! empty($duration_display)) {
	$card_info_items[] = [
		'label' => esc_html__('Estimated Duration:', 'transfertmarrakech'),
		'value' => $duration_display,
	];
}

// Affiche le header du produit
$renderer->render('product-header', [
	'title'           => $title,
	'thumbnail_id'    => $thumbnail_id,
	'card_info_items' => $card_info_items,
	'price_formatted' => $price_formatted,
	'post_id'         => $transfer_id,
]);

// Affiche le backlink
if (! empty($destination_name)) {
	$renderer->render('product-backlink', [
		'destination_link' => $destination_link,
		'destination_name' => $destination_name,
	]);
}
?>

<main class="product-body">
	<?php
	// Affiche les keywords/tags
	$renderer->render('product-keywords', [
		'primary_tags' => array_filter([$pickup, $dropoff]),
		'secondary_tags' => [
			esc_html__('Guaranteed Transfer', 'transfertmarrakech'),
			esc_html__('Recommended', 'transfertmarrakech'),
			esc_html__('New Route', 'transfertmarrakech'),
		],
	]);

	// Préparation des sections de description
	$description_sections = [];
	
	if (! empty($cities_visited)) {
		$description_sections[] = [
			'title'   => esc_html__('Pickup & drop-off', 'transfertmarrakech'),
			'content' => $cities_visited,
			'type'    => 'list',
			'class'   => '',
		];
	}
	
	if (! empty($description)) {
		$description_sections[] = [
			'title'   => esc_html__('Description', 'transfertmarrakech'),
			'content' => $description,
			'type'    => 'text',
			'class'   => 'summary',
		];
	}
	
	if (! empty($content)) {
		$description_sections[] = [
			'title'   => esc_html__('Summary', 'transfertmarrakech'),
			'content' => $content,
			'type'    => 'text',
			'class'   => 'summary',
		];
	}

	// Affiche la description
	if (! empty($description_sections)) {
		$renderer->render('product-description', [
			'sections' => $description_sections,
		]);
	}

	// Construit le message WhatsApp
	$whatsapp_message = sprintf(
		'Hello, %sI am interested in: %s%s%s',
		"\n",
		esc_html($title) . ' ' . esc_html__('for a transfer from', 'transfertmarrakech') . ' ' . esc_html($pickup) . ' ' . esc_html__('to', 'transfertmarrakech') . ' ' . esc_html($dropoff) . ' ',
		"\n",
		esc_url($share_url)
	);
	$whatsapp_url = \TM\Utils\MetaHelper::build_whatsapp_url($whatsapp_message);

	// Affiche la bannière
	$renderer->render('product-banner', [
		'title'         => $title,
		'thumbnail_id' => $thumbnail_id,
		'share_url'     => $share_url,
		'share_title'   => $share_title,
		'whatsapp_url'  => $whatsapp_url,
		'whatsapp_label' => esc_html__('Contact an agency', 'transfertmarrakech'),
	]);
	?>
</main>

<?php
get_footer();
