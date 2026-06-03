<?php
/**
 * Template part pour la section articles de blog en page d'accueil
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 *
 * @var array $posts Tableau des posts avec leurs données formatées
 */

if ( ! isset( $posts ) || empty( $posts ) ) {
	return;
}

$renderer  = new \TM\Template\Renderer();
$blog_url  = \get_permalink( \get_option( 'page_for_posts' ) ) ?: \home_url( '/blog/' );
?>

<div class="modules">
	<section class="module postsList">
		<div class="postsList__inner">
			<h2 class="postsList__title animated-title">
				<?php \esc_html_e( 'Latest Articles', 'transfertmarrakech' ); ?>
			</h2>
			<div class="postsList__list">
				<?php foreach ( $posts as $post_data ) :
					$renderer->render( 'post-card', [ 'post_data' => $post_data ] );
				endforeach; ?>
			</div>
			<div class="postsList__cta">
				<a
					href="<?php echo \esc_url( $blog_url ); ?>"
					class="cta primary">
					<span class="cta__inner" data-label="<?php \esc_attr_e( 'View All Articles', 'transfertmarrakech' ); ?>">
						<span class="cta__txt">
							<?php \esc_html_e( 'View All Articles', 'transfertmarrakech' ); ?>
						</span>
					</span>
				</a>
			</div>
		</div>
	</section>
</div>
