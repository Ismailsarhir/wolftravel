<?php
/**
 * Template for displaying single blog posts
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 */

// Empêche l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$post = \get_post();
if ( ! $post instanceof \WP_Post ) {
	\get_footer();
	return;
}

$post_id      = $post->ID;
$title        = \esc_html( \get_the_title( $post_id ) );
$content      = \apply_filters( 'the_content', $post->post_content );
$thumbnail_id = \get_post_thumbnail_id( $post_id );
$thumbnail_url = $thumbnail_id ? \wp_get_attachment_image_url( $thumbnail_id, 'full' ) : '';
$date         = \get_the_date( 'M j, Y', $post );
$author       = \get_the_author_meta( 'display_name', $post->post_author );
$share_url    = \get_permalink( $post_id );
$share_title  = \esc_attr( \get_the_title( $post_id ) );

$categories    = \get_the_category( $post_id );
$category      = ! empty( $categories ) ? $categories[0]->name : '';
$category_link = ! empty( $categories ) ? \get_category_link( $categories[0]->term_id ) : '';

$word_count   = \str_word_count( \strip_tags( $post->post_content ) );
$reading_time = max( 1, (int) \ceil( $word_count / 200 ) );

// Lien retour vers le blog
$blog_url = \get_permalink( \get_option( 'page_for_posts' ) ) ?: \home_url( '/blog/' );
?>

<?php if ( ! empty( $thumbnail_url ) ) : ?>
<div class="post-hero">
	<div class="post-hero__bg">
		<div class="parallax">
			<img
				src="<?php echo \esc_url( $thumbnail_url ); ?>"
				alt="<?php echo \esc_attr( $title ); ?>"
				fetchpriority="high"
			>
		</div>
	</div>
	<div class="post-hero__inner">
		<?php if ( ! empty( $category ) ) : ?>
			<a class="post-hero__category tag" href="<?php echo \esc_url( $category_link ); ?>">
				<?php echo \esc_html( $category ); ?>
			</a>
		<?php endif; ?>
		<h1 class="post-hero__title animated-title">
			<?php echo $title; // Already escaped above ?>
		</h1>
		<div class="post-hero__meta">
			<time class="post-hero__meta-date"><?php echo \esc_html( $date ); ?></time>
			<span class="post-hero__meta-sep">·</span>
			<span class="post-hero__meta-reading-time">
				<?php
				printf(
					/* translators: %d: number of minutes */
					\esc_html( \_n( '%d min read', '%d min read', $reading_time, 'transfertmarrakech' ) ),
					(int) $reading_time
				);
				?>
			</span>
			<?php if ( ! empty( $author ) ) : ?>
				<span class="post-hero__meta-sep">·</span>
				<span class="post-hero__meta-author"><?php echo \esc_html( $author ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<main class="post-body">
	<div class="post-body__inner">
		<div class="post-body__backlink">
			<a href="<?php echo \esc_url( $category_link ?: $blog_url ); ?>" class="backlink">
				← <?php \esc_html_e( 'All Articles', 'transfertmarrakech' ); ?>
			</a>
		</div>

		<?php if ( ! empty( $content ) ) : ?>
			<article class="post-body__content">
				<?php echo $content; // Content filtered via apply_filters('the_content') ?>
			</article>
		<?php endif; ?>

		<div class="post-body__footer">
			<div class="post-body__share">
				<span class="post-body__share-label">
					<?php \esc_html_e( 'Share:', 'transfertmarrakech' ); ?>
				</span>
				<a
					class="post-body__share-btn post-body__share-btn--whatsapp"
					href="https://wa.me/?text=<?php echo \rawurlencode( $share_title . ' ' . $share_url ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php \esc_attr_e( 'Share on WhatsApp', 'transfertmarrakech' ); ?>"
				>
					<img
						src="<?php echo \esc_url( \get_template_directory_uri() . '/assets/images/icons/whatsapp.svg' ); ?>"
						alt="WhatsApp"
						width="20"
						height="20"
					>
				</a>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
