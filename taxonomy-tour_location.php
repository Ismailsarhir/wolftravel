<?php
/**
 * Template for displaying tour_location taxonomy archives
 * Optimized PHP pagination (no AJAX)
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 */

get_header();

// Récupère le terme de taxonomie actuel
$queried_object = get_queried_object();
if ( ! $queried_object || ! isset( $queried_object->term_id ) ) {
	get_footer();
	return;
}

$term    = $queried_object;
$term_id = $term->term_id;

// Récupère l'image du terme depuis les meta
$archive_image_id = get_term_meta( $term_id, 'tm_term_image', true );
$archive_image_id = $archive_image_id ? absint( $archive_image_id ) : 0;

// Utilise le nom du terme comme titre
$archive_title = $term->name;

// Utilise la description du terme comme sous-titre (si disponible)
$archive_subtitle = ! empty( $term->description ) ? $term->description : '';

$renderer       = new \TM\Template\Renderer();
$current_page   = max( 1, get_query_var( 'paged' ) ?: 1 );
$items_per_page = 9;

$tax_query_arg = [
	[
		'taxonomy' => 'tour_location',
		'field'    => 'term_id',
		'terms'    => $term_id,
	],
];

$args_base = [
	'post_status'    => 'publish',
	'posts_per_page' => $items_per_page,
	'paged'          => $current_page,
	'no_found_rows'  => false,
	'tax_query'      => $tax_query_arg,
];

// Query Tours
$tours      = [];
$tours_list = \TM\Core\ToursList::get_instance();

$tours_query = new \WP_Query( array_merge( $args_base, [ 'post_type' => 'tours' ] ) );
if ( $tours_query->have_posts() ) {
	foreach ( $tours_query->posts as $post ) {
		$tour_data = $tours_list->format_tour_data( $post );
		if ( $tour_data ) {
			$tours[] = $tour_data;
		}
	}
}
$tours_max_pages = $tours_query->max_num_pages;
wp_reset_postdata();

// Query Circuits
$circuits      = [];
$circuits_list = \TM\Core\CircuitsList::get_instance();

$circuits_query = new \WP_Query( array_merge( $args_base, [ 'post_type' => 'circuits' ] ) );
if ( $circuits_query->have_posts() ) {
	foreach ( $circuits_query->posts as $post ) {
		$circuit_data = $circuits_list->format_circuit_data( $post );
		if ( $circuit_data ) {
			$circuits[] = $circuit_data;
		}
	}
}
$circuits_max_pages = $circuits_query->max_num_pages;
wp_reset_postdata();

$tours_count    = count( $tours );
$circuits_count = count( $circuits );
$total_results  = $tours_count + $circuits_count;
$max_num_pages  = max( $tours_max_pages, $circuits_max_pages );

?>
<!-- Hero avec image parallax et titre -->
<div class="archive-hero">
	<div class="archive-hero__inner">
		<h1 class="archive-hero__title animated-title">
			<?php echo esc_html( $archive_title ); ?>
		</h1>
		<?php if ( ! empty( $archive_subtitle ) ) : ?>
			<p class="archive-hero__subtitle animated-title">
				<?php echo esc_html( $archive_subtitle ); ?>
			</p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $archive_image_id ) ) : ?>
		<div class="archive-hero__bg">
			<div class="parallax">
				<?php
				$full_image_url = wp_get_attachment_image_url( $archive_image_id, 'full' );
				$image_alt      = get_post_meta( $archive_image_id, '_wp_attachment_image_alt', true );
				if ( empty( $image_alt ) ) {
					$image_alt = esc_attr( $archive_title );
				}
				?>
				<img
					src="<?php echo esc_url( $full_image_url ); ?>"
					alt="<?php echo esc_attr( $image_alt ); ?>"
					fetchpriority="high">
			</div>
		</div>
	<?php endif; ?>
</div>

<div class="modules">
	<?php if ( $total_results > 0 ) : ?>

		<!-- Section Tours -->
		<?php if ( $tours_count > 0 ) : ?>
			<section class="module toursList">
				<div class="toursList__inner">
					<h2 class="toursList__title animated-title">
						<?php esc_html_e( 'Tours', 'transfertmarrakech' ); ?>
						<span class="module__count">(<?php echo esc_html( $tours_count ); ?>)</span>
					</h2>
					<div class="toursList__list">
						<?php
						foreach ( $tours as $tour_data ) {
							$renderer->render( 'tour-card', [ 'tour_data' => $tour_data ] );
						}
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- Section Circuits -->
		<?php if ( $circuits_count > 0 ) : ?>
			<section class="module circuitsList">
				<div class="circuitsList__inner">
					<h2 class="circuitsList__title animated-title">
						<?php esc_html_e( 'Circuits', 'transfertmarrakech' ); ?>
						<span class="module__count">(<?php echo esc_html( $circuits_count ); ?>)</span>
					</h2>
					<div class="circuitsList__list">
						<?php
						foreach ( $circuits as $circuit_data ) {
							$renderer->render( 'circuit-card', [ 'circuit_data' => $circuit_data ] );
						}
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- Pagination -->
		<?php
		if ( $max_num_pages > 1 ) {
			global $wp_query;
			$original_max_pages        = $wp_query->max_num_pages;
			$wp_query->max_num_pages   = $max_num_pages;
			$renderer->render( 'pagination' );
			$wp_query->max_num_pages   = $original_max_pages;
		}
		?>

	<?php else : ?>
		<section class="module">
			<div class="module__inner">
				<p><?php esc_html_e( 'No tours or circuits found for this location.', 'transfertmarrakech' ); ?></p>
			</div>
		</section>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
