<?php
/**
 * Template part pour une carte article de blog
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 *
 * @var array $post_data Données du post formatées
 */

if ( ! isset( $post_data ) || empty( $post_data ) ) {
	return;
}

$post = $post_data['post'] ?? null;
if ( ! $post instanceof \WP_Post ) {
	return;
}

$title         = $post_data['title'] ?? '';
$permalink     = $post_data['permalink'] ?? '';
$thumbnail     = $post_data['thumbnail'] ?? '';
$date          = $post_data['date'] ?? '';
$excerpt       = $post_data['excerpt'] ?? '';
$category      = $post_data['category'] ?? '';
$category_link = $post_data['category_link'] ?? '';
$reading_time  = $post_data['reading_time'] ?? 1;

if ( empty( $title ) || empty( $permalink ) ) {
	return;
}
?>

<a class="post-card" href="<?php echo \esc_url( $permalink ); ?>">
	<div class="post-card__img-wrapper">
		<?php if ( ! empty( $thumbnail ) ) : ?>
			<div class="parallax">
				<img
					class="post-card__img"
					src="<?php echo \esc_url( $thumbnail ); ?>"
					alt="<?php echo \esc_attr( $title ); ?>"
					fetchpriority="low"
					decoding="async"
					loading="lazy"
				>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $category ) ) : ?>
			<div class="tag"><?php echo \esc_html( $category ); ?></div>
		<?php endif; ?>
	</div>
	<div class="post-card__infos">
		<div class="post-card__infos-meta">
			<?php if ( ! empty( $date ) ) : ?>
				<time class="post-card__infos-date"><?php echo \esc_html( $date ); ?></time>
			<?php endif; ?>
			<span class="post-card__infos-reading-time">
				<?php
				printf(
					/* translators: %d: number of minutes */
					\esc_html( \_n( '%d min read', '%d min read', $reading_time, 'transfertmarrakech' ) ),
					(int) $reading_time
				);
				?>
			</span>
		</div>
		<h5 class="post-card__infos-title">
			<?php echo \esc_html( $title ); ?>
		</h5>
		<?php if ( ! empty( $excerpt ) ) : ?>
			<p class="post-card__infos-excerpt">
				<?php echo \esc_html( $excerpt ); ?>
			</p>
		<?php endif; ?>
	</div>
</a>
