<?php
/**
 * Hero class for rendering the hero section
 * 
 * @package TransfertMarrakech
 * @since 1.0.0
 */

namespace TM\Core;

use TM\Template\Renderer;
use TM\Utils\HeroHelper;

/**
 * Classe pour gérer le rendu du Hero
 */
class Hero {
	
	/**
	 * Instance unique de la classe (Singleton)
	 * 
	 * @var Hero|null
	 */
	private static ?Hero $instance = null;
	
	/**
	 * Renderer pour les templates
	 * 
	 * @var Renderer
	 */
	private Renderer $renderer;
	
	/**
	 * Constructeur privé (Singleton)
	 */
	private function __construct() {
		$this->renderer = new Renderer();
	}
	
	/**
	 * Récupère l'instance unique de la classe
	 * 
	 * @return Hero
	 */
	public static function get_instance(): Hero {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}
		return static::$instance;
	}
	
	/**
	 * Rend le Hero complet
	 *
	 * @return void
	 */
	public function render(): void {
		$this->renderer->render( 'hero', [
			'hero_images'  => HeroHelper::get_hero_images(),
			'hero_heading' => HeroHelper::get_hero_heading(),
		] );
	}
}

