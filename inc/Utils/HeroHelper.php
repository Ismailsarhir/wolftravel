<?php
/**
 * Helper pour récupérer les données du Hero
 * 
 * @package TransfertMarrakech
 * @since 1.0.0
 */

namespace TM\Utils;

use TM\Repository\PostRepository;

/**
 * Classe helper pour le Hero
 */
class HeroHelper {
	
	/**
	 * Récupère le post à afficher dans le Hero
	 * Retourne le post le plus récent avec la meta 'tm_show_in_hero' = '1'
	 * 
	 * @return WP_Post|null
	 */
	public static function get_hero_post() {
		$args = [
			'post_type'      => 'post',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_query'     => [
				[
					'key'   => 'tm_show_in_hero',
					'value' => '1',
					'compare' => '=',
				],
			],
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true, // Optimisation : ne compte pas le total
			'update_post_meta_cache' => true, // Optimisation : charge les meta en une fois
		];
		
		$query = new \WP_Query( $args );
		
		if ( $query->have_posts() ) {
			$post = $query->posts[0];
			// Note: wp_reset_postdata() n'est pas nécessaire car nous n'utilisons pas the_post()
			return $post;
		}
		
		return null;
	}
	
	/**
	 * Récupère le titre du Hero (titre du post)
	 * Optimisé pour utiliser la propriété de l'objet directement
	 * 
	 * @param \WP_Post $post Post object
	 * @return string
	 */
	public static function get_hero_title( \WP_Post $post ): string {
		return \TM\Utils\MetaHelper::get_post_title( $post );
	}
	
	/**
	 * Récupère l'URL de la vidéo depuis la médiathèque WordPress pour le Hero
	 * 
	 * @param WP_Post $post Post object
	 * @return string URL de la vidéo ou chaîne vide
	 */
	public static function get_hero_video_url( $post ): string {
		$video_id = \get_post_meta( $post->ID, 'tm_hero_video_id', true );
		
		if ( empty( $video_id ) ) {
			return '';
		}
		
		$video_url = \wp_get_attachment_url( $video_id );
		
		return $video_url ? $video_url : '';
	}
	
	/**
	 * Récupère l'ID de l'attachment vidéo pour le Hero
	 *
	 * @param WP_Post $post Post object
	 * @return int ID de l'attachment ou 0
	 */
	public static function get_hero_video_id( $post ): int {
		$video_id = \get_post_meta( $post->ID, 'tm_hero_video_id', true );

		return ! empty( $video_id ) ? \absint( $video_id ) : 0;
	}

	/**
	 * Récupère le texte du heading hero depuis les options du thème.
	 * Retourne le texte par défaut si aucune valeur n'est enregistrée.
	 *
	 * @return string
	 */
	public static function get_hero_heading(): string {
		$saved = \get_option( 'tm_hero_heading', '' );

		if ( ! empty( $saved ) ) {
			return $saved;
		}

		return \__(
			'We design objects that carry the weight of their own conviction, where every curve and joint exists not for beauty but because the material demanded it.',
			'transfertmarrakech'
		);
	}

	/**
	 * Récupère les 5 URLs des images hero depuis les options du thème.
	 * Utilise les images par défaut du thème si aucune image n'est sélectionnée.
	 *
	 * @return array<int, string> Tableau indexé de 1 à 5 avec les URLs des images
	 */
	public static function get_hero_images(): array {
		$images = [];

		for ( $i = 1; $i <= 5; $i++ ) {
			$attachment_id = \absint( \get_option( "tm_hero_image_{$i}", 0 ) );
			$url           = '';

			if ( $attachment_id ) {
				$url = \wp_get_attachment_image_url( $attachment_id, 'large' ) ?: '';
			}

			if ( empty( $url ) ) {
				$url = \get_template_directory_uri() . "/assets/images/img-{$i}.jpg";
			}

			$images[ $i ] = $url;
		}

		return $images;
	}
}

