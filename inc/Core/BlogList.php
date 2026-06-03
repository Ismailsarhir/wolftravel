<?php
/**
 * Blog posts list class for rendering the featured posts section
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 */

namespace TM\Core;

use TM\Template\Renderer;
use TM\Repository\PostRepository;
use TM\Utils\MetaHelper;

class BlogList {

	private const MAX_POSTS = 3;

	private static ?BlogList $instance = null;

	private Renderer $renderer;
	private PostRepository $repository;

	private function __construct() {
		$this->renderer   = new Renderer();
		$this->repository = PostRepository::get_instance();
	}

	public static function get_instance(): BlogList {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Formate les données d'un post pour l'affichage
	 *
	 * @param \WP_Post $post
	 * @return array|null
	 */
	public function format_post_data( \WP_Post $post ): ?array {
		$post_id = $post->ID;

		$thumbnail_url = MetaHelper::get_post_thumbnail_url_with_fallback( $post_id );

		$title = MetaHelper::get_post_title( $post );

		$excerpt = $post->post_excerpt;
		if ( empty( $excerpt ) ) {
			$excerpt = \wp_trim_words( \strip_tags( $post->post_content ), 20, '...' );
		}

		$categories    = \get_the_category( $post_id );
		$category      = ! empty( $categories ) ? $categories[0]->name : '';
		$category_link = ! empty( $categories ) ? \get_category_link( $categories[0]->term_id ) : '';

		$word_count   = \str_word_count( \strip_tags( $post->post_content ) );
		$reading_time = max( 1, (int) \ceil( $word_count / 200 ) );

		return [
			'post'          => $post,
			'post_id'       => $post_id,
			'title'         => $title,
			'permalink'     => \get_permalink( $post_id ),
			'thumbnail'     => $thumbnail_url,
			'date'          => \get_the_date( 'M j, Y', $post ),
			'excerpt'       => $excerpt,
			'category'      => $category,
			'category_link' => $category_link,
			'reading_time'  => $reading_time,
			'author'        => \get_the_author_meta( 'display_name', $post->post_author ),
		];
	}

	/**
	 * Récupère les posts récents pour la home
	 *
	 * @param int $limit
	 * @return array
	 */
	private function get_featured_posts( int $limit = self::MAX_POSTS ): array {
		$posts = $this->repository->get_by_args( 'post', [
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_status'    => 'publish',
		] );

		if ( empty( $posts ) ) {
			return [];
		}

		$featured = [];
		foreach ( $posts as $post ) {
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}
			$post_data = $this->format_post_data( $post );
			if ( $post_data ) {
				$featured[] = $post_data;
			}
		}

		return $featured;
	}

	public function render(): void {
		$posts = $this->get_featured_posts( self::MAX_POSTS );

		if ( empty( $posts ) ) {
			return;
		}

		$this->renderer->render( 'posts-list', [
			'posts' => $posts,
		] );
	}

	public function has_posts(): bool {
		return ! empty( $this->get_featured_posts( 1 ) );
	}
}
