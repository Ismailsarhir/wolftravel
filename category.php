<?php
/**
 * Template for displaying blog post category archives
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 */

get_header();

global $wp_query;

$archive_title    = \get_the_archive_title();
$archive_subtitle = \get_the_archive_description();

// Get category thumbnail if set (compatible with WooCommerce-style term meta).
$archive_image_id = 0;
$queried_obj = \get_queried_object();
if ( $queried_obj instanceof \WP_Term ) {
	$archive_image_id = (int) \get_term_meta( $queried_obj->term_id, 'thumbnail_id', true );
}

// Build an explicit query for the current category so all posts are fetched
// and the global $wp_query is correct for pagination.
$paged = max( 1, (int) \get_query_var( 'paged' ) );

$category_query = new \WP_Query( [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'cat'            => $queried_obj instanceof \WP_Term ? $queried_obj->term_id : 0,
	'posts_per_page' => (int) \get_option( 'posts_per_page' ),
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => false,
] );

// Override the global so the pagination template reads the right max_num_pages.
$wp_query = $category_query;

$blog_list = \TM\Core\BlogList::get_instance();
$posts     = [];

foreach ( $category_query->posts as $post ) {
	if ( $post instanceof \WP_Post ) {
		$post_data = $blog_list->format_post_data( $post );
		if ( $post_data ) {
			$posts[] = $post_data;
		}
	}
}
?>

<div class="archive-hero">
	<div class="archive-hero__inner">
		<h1 class="archive-hero__title animated-title">
			<?php echo \wp_kses_post( $archive_title ?: \__( 'Articles', 'transfertmarrakech' ) ); ?>
		</h1>
		<?php if ( ! empty( $archive_subtitle ) ) : ?>
			<p class="archive-hero__subtitle animated-title">
				<?php echo \esc_html( $archive_subtitle ); ?>
			</p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $archive_image_id ) ) : ?>
		<div class="archive-hero__bg">
			<div class="parallax">
				<?php
				$full_image_url = \wp_get_attachment_image_url( $archive_image_id, 'full' );
				$image_alt      = \get_post_meta( $archive_image_id, '_wp_attachment_image_alt', true );
				if ( empty( $image_alt ) ) {
					$image_alt = \esc_attr( $archive_title );
				}
				?>
				<img
					src="<?php echo \esc_url( $full_image_url ); ?>"
					alt="<?php echo \esc_attr( $image_alt ); ?>"
					fetchpriority="high">
			</div>
		</div>
	<?php endif; ?>
</div>

<div class="modules">
	<section class="module postsList postsList--category">
		<div class="postsList__inner">
			<?php if ( ! empty( $posts ) ) : ?>
				<div class="postsList__list">
					<?php
					$renderer = new \TM\Template\Renderer();
					foreach ( $posts as $post_data ) :
						$renderer->render( 'post-card', [ 'post_data' => $post_data ] );
					endforeach;
					?>
				</div>
				<?php $renderer->render( 'pagination' ); ?>
			<?php else : ?>
				<p><?php \esc_html_e( 'No articles found.', 'transfertmarrakech' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</div>

<?php get_footer(); ?>
