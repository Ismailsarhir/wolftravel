<?php
/**
 * Header class for rendering the site header
 * 
 * @package TransfertMarrakech
 * @since 1.0.0
 */

namespace TM\Core;

use TM\Admin\SocialLinksSettings;
use TM\Utils\HeaderHelper;

/**
 * Classe pour gérer le rendu du header
 */
class Header {
	
	/**
	 * Instance unique de la classe (Singleton)
	 * 
	 * @var Header|null
	 */
	private static ?Header $instance = null;
	
	/**
	 * Constructeur privé (Singleton)
	 */
	private function __construct() {
		// Constructor privé pour pattern Singleton
	}
	
	/**
	 * Récupère l'instance unique de la classe
	 * 
	 * @return Header
	 */
	public static function get_instance(): Header {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}
		return static::$instance;
	}
	
	/**
	 * Rend le header complet
	 * 
	 * @return void
	 */
	public function render(): void {
		?>
		<header class="header">
			<?php $this->render_logo(); ?>
			<div class="header__right">
				<?php $this->render_header_actions(); ?>
			</div>
			<?php $this->render_mobile_nav(); ?>
		</header>
		<?php $this->render_search(); ?>
		<?php
	}
	
	/**
	 * Rend le logo
	 * 
	 * @param string $link_class Classe CSS pour le lien (par défaut: 'header__logo-link')
	 * @param string $img_class Classe CSS pour l'image (par défaut: 'header__logo')
	 * @return void
	 */
	public function render_logo( string $link_class = 'header__logo-link', string $img_class = 'header__logo' ): void {
		$logo_url = HeaderHelper::get_logo_url();
		$home_url = home_url( '/' );
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" class="<?php echo esc_attr( $link_class ); ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>" class="<?php echo esc_attr( $img_class ); ?>" alt="<?php bloginfo( 'name' ); ?>">
		</a>
		<?php
	}
	
	/**
	 * Rend les actions du header (recherche, portail agents, menu toggle)
	 * 
	 * @return void
	 */
	private function render_header_actions(): void {
		$search_icon_url = HeaderHelper::get_search_icon_url();
		$agent_portal_url = HeaderHelper::get_agent_portal_url();
		?>
		<?php $this->render_social_links(); ?>
		<button class="header__search-toggle" aria-label="<?php esc_attr_e( 'Search', 'transfertmarrakech' ); ?>">
			<img src="<?php echo esc_url( $search_icon_url ); ?>" alt="<?php esc_attr_e( 'Search', 'transfertmarrakech' ); ?>">
		</button>

		<button class="header__menu-toggle" aria-label="<?php esc_attr_e( 'Menu', 'transfertmarrakech' ); ?>">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<?php
	}
	
	/**
	 * Rend les liens sociaux (Facebook, Instagram, TripAdvisor) depuis les options BO
	 *
	 * @return void
	 */
	public function render_social_links(): void {
		$links = SocialLinksSettings::get_links();

		$has_links = array_filter( $links );
		if ( empty( $has_links ) ) {
			return;
		}
		?>
		<ul class="social-links">
			<?php if ( ! empty( $links['facebook'] ) ) : ?>
			<li>
				<a href="<?php echo esc_url( $links['facebook'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Facebook', 'transfertmarrakech' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 640 640" aria-hidden="true"><path fill="currentColor" d="M240 363.3V576h116V363.3h86.5l18-97.8H356v-34.6c0-51.7 20.3-71.5 72.7-71.5 16.3 0 29.4.4 37 1.2V71.9C451.4 68 416.4 64 396.2 64 289.3 64 240 114.5 240 223.4v42.1h-66v97.8z"/></svg>
				</a>
			</li>
			<?php endif; ?>
			<?php if ( ! empty( $links['instagram'] ) ) : ?>
			<li>
				<a href="<?php echo \esc_url( $links['instagram'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php \esc_attr_e( 'Instagram', 'transfertmarrakech' ); ?>">
					<svg class="icon icon-instagram" viewBox="0 0 20 20" aria-hidden="true"><path fill="currentColor" fill-rule="evenodd" d="M13.23 3.492c-.84-.037-1.096-.046-3.23-.046-2.144 0-2.39.01-3.238.055-.776.027-1.195.164-1.487.273a2.4 2.4 0 0 0-.912.593 2.5 2.5 0 0 0-.602.922c-.11.282-.238.702-.274 1.486-.046.84-.046 1.095-.046 3.23s.01 2.39.046 3.229c.004.51.097 1.016.274 1.495.145.365.319.639.602.913.282.282.538.456.92.602.474.176.974.268 1.479.273.848.046 1.103.046 3.238.046s2.39-.01 3.23-.046c.784-.036 1.203-.164 1.486-.273.374-.146.648-.329.921-.602.283-.283.447-.548.602-.922.177-.476.27-.979.274-1.486.037-.84.046-1.095.046-3.23s-.01-2.39-.055-3.229c-.027-.784-.164-1.204-.274-1.495a2.4 2.4 0 0 0-.593-.913 2.6 2.6 0 0 0-.92-.602c-.284-.11-.703-.237-1.488-.273ZM6.697 2.05c.857-.036 1.131-.045 3.302-.045a63 63 0 0 1 3.302.045c.664.014 1.321.14 1.943.374a4 4 0 0 1 1.414.922c.41.397.728.88.93 1.414.23.622.354 1.279.365 1.942C18 7.56 18 7.824 18 10.005c0 2.17-.01 2.444-.046 3.292-.036.858-.173 1.442-.374 1.943-.2.53-.474.976-.92 1.423a3.9 3.9 0 0 1-1.415.922c-.51.191-1.095.337-1.943.374-.857.036-1.122.045-3.302.045-2.171 0-2.445-.009-3.302-.055-.849-.027-1.432-.164-1.943-.364a4.15 4.15 0 0 1-1.414-.922 4.1 4.1 0 0 1-.93-1.423c-.183-.51-.329-1.085-.365-1.943C2.009 12.45 2 12.167 2 10.004c0-2.161 0-2.435.055-3.302.027-.848.164-1.432.365-1.942a4.4 4.4 0 0 1 .92-1.414 4.2 4.2 0 0 1 1.415-.93c.51-.183 1.094-.33 1.943-.366Zm.427 4.806a4.105 4.105 0 1 1 5.805 5.805 4.105 4.105 0 0 1-5.805-5.805m1.882 5.371a2.668 2.668 0 1 0 2.042-4.93 2.668 2.668 0 0 0-2.042 4.93m5.922-5.942a.958.958 0 1 1-1.355-1.355.958.958 0 0 1 1.355 1.355" clip-rule="evenodd"/></svg>
				</a>
			</li>
			<?php endif; ?>
			<?php if ( ! empty( $links['tripadvisor'] ) ) : ?>
			<li>
				<a href="<?php echo \esc_url( $links['tripadvisor'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php \esc_attr_e( 'TripAdvisor', 'transfertmarrakech' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill="currentColor" d="M6.781 12.894v.016c0 .266-.11.505-.286.677a.93.93 0 0 1-.672.286h-.011.001-.002a.98.98 0 0 1-.978-.978v-.012c0-.264.11-.502.286-.672a.94.94 0 0 1 .677-.287h.017-.001a.98.98 0 0 1 .969.968v.001zm12.019-.013a.979.979 0 1 1-1.958 0v-.017c0-.263.11-.501.286-.67a.99.99 0 0 1 1.386.001.92.92 0 0 1 .287.671v.017-.001zm-10.832.01a2.009 2.009 0 1 0-4.017.001 2.009 2.009 0 0 0 4.017 0zm12.009-.01a2.009 2.009 0 1 0-.589 1.422c.363-.353.589-.846.589-1.391zv.002zm-11.125.01a2.901 2.901 0 0 1-5.802 0v-.041c0-.788.328-1.499.854-2.005l.001-.001A2.8 2.8 0 0 1 5.912 10h.048-.002.041A2.78 2.78 0 0 1 8 10.846l.001.001c.524.508.85 1.218.85 2.005v.044-.002zm12.02-.01v.004a2.901 2.901 0 1 1-.849-2.051c.524.508.849 1.218.849 2.004v.045zm-10.041.031.001-.07a4.63 4.63 0 0 0-1.41-3.33l-.001-.001A4.62 4.62 0 0 0 6.09 8.099l-.074.001h.004-.032c-.875 0-1.694.24-2.394.658l.021-.012c-1.443.85-2.397 2.397-2.397 4.166s.953 3.316 2.374 4.154l.022.012a4.63 4.63 0 0 0 2.373.646h.036-.002l.07.001a4.63 4.63 0 0 0 3.33-1.41l.001-.001a4.62 4.62 0 0 0 1.412-3.331l-.001-.073v.004zm6.96-5.969c-1.66-.729-3.594-1.154-5.628-1.154l-.172.001H12l-.238-.002c-2.068 0-4.038.422-5.827 1.184l.097-.037h.017c.832 0 1.624.175 2.34.489l-.037-.015c.739.315 1.37.746 1.901 1.276a6.1 6.1 0 0 1 1.255 1.867l.015.039c.3.679.474 1.471.474 2.303v.018-.001c0-1.619.641-3.088 1.682-4.168l-.002.002a6.1 6.1 0 0 1 1.811-1.264l.038-.016a5.65 5.65 0 0 1 2.252-.521h.007zm4.99 5.968a4.807 4.807 0 1 0-1.405 3.402 4.63 4.63 0 0 0 1.406-3.327l-.001-.079zm-2.77-5.895H24a5.5 5.5 0 0 0-.766 1.16l-.014.032a4.5 4.5 0 0 0-.412 1.163l-.005.03a5.92 5.92 0 0 1 1.144 3.515 5.97 5.97 0 0 1-10.594 3.776l-.008-.011c-.436.541-.879 1.15-1.291 1.781l-.054.087a9 9 0 0 0-.576-.882l.016.023q-.442-.63-.776-1.015a5.97 5.97 0 0 1-9.452-7.288l-.012.018a4.6 4.6 0 0 0-.429-1.218l.012.025a5.5 5.5 0 0 0-.789-1.2L0 7.019h3.802a12.4 12.4 0 0 1 3.61-1.611l.088-.02a16.6 16.6 0 0 1 4.425-.588h.079H12h.107c1.523 0 2.995.213 4.39.611l-.113-.027c1.378.383 2.584.942 3.672 1.665l-.047-.029z"/></svg>
				</a>
			</li>
			<?php endif; ?>
		</ul>
		<?php
	}

	/**
	 * Rend la navigation mobile depuis le menu WordPress (main-header)
	 *
	 * @return void
	 */
	private function render_mobile_nav(): void {
		if ( ! \has_nav_menu( 'main-header' ) ) {
			return;
		}
		?>
		<nav class="nav">
			<div class="nav__inner">
				<?php
				\wp_nav_menu( [
					'theme_location' => 'main-header',
					'container'      => false,
					'fallback_cb'    => false,
				] );
				?>
			</div>
		</nav>
		<?php
	}
	
	/**
	 * Rend le formulaire de recherche optimisé
	 * 
	 * @return void
	 */
	private function render_search(): void {
		$search_url = \home_url( '/?s=' );
		?>
		<search class="search" data-lenis-prevent="">
			<div class="search__inner">
				<?php $this->render_search_form(); ?>
			</div>
			<button 
				type="button" 
				class="search__close" 
				aria-label="<?php \esc_attr_e( 'Close search', 'transfertmarrakech' ); ?>"
			>
				<span></span>
			</button>
		</search>
		<?php
	}
	
	/**
	 * Rend le formulaire de recherche (réutilisable)
	 * 
	 * @param string $input_id ID unique pour l'input (par défaut: 'search-input')
	 * @return void
	 */
	public function render_search_form( string $input_id = 'search-input' ): void {
		$search_url = \home_url( '/?s=' );
		?>
		<form 
			class="ais-SearchBox-form" 
			action="<?php echo \esc_url( $search_url ); ?>" 
			method="get" 
			novalidate
		>
			<label class="search-bar__label results">
				<div class="search-bar__label-title">
					<?php \esc_html_e( 'Search', 'transfertmarrakech' ); ?>
				</div>
				<div class="instantsearch search-bar__label-value">
					<div class="ais-SearchBox">
						<input 
							class="ais-SearchBox-input" 
							type="search" 
							name="s"
							id="<?php echo \esc_attr( $input_id ); ?>"
							placeholder="<?php \esc_attr_e( 'A region, a city...', 'transfertmarrakech' ); ?>" 
							autocomplete="off" 
							autocorrect="off" 
							autocapitalize="off" 
							spellcheck="false" 
							maxlength="512" 
							aria-label="<?php \esc_attr_e( 'Search', 'transfertmarrakech' ); ?>"
							required
						>
						<button 
							class="ais-SearchBox-submit" 
							type="submit" 
							aria-label="<?php \esc_attr_e( 'Submit the search query', 'transfertmarrakech' ); ?>"
						>
							<?php $this->render_search_icon(); ?>
						</button>
					</div>
				</div>
			</label>
		</form>
		<?php
	}
	
	/**
	 * Rend l'icône de recherche SVG
	 * 
	 * @return void
	 */
	public function render_search_icon(): void {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none" aria-hidden="true">
			<circle cx="11.3137" cy="11.3137" r="7" transform="rotate(-45 11.3137 11.3137)" stroke="#0A254D" stroke-width="2" stroke-linejoin="round"/>
			<path d="M21.2115 22.6274C21.602 23.0179 22.2352 23.0179 22.6257 22.6274C23.0162 22.2369 23.0162 21.6037 22.6257 21.2132L21.2115 22.6274ZM15.5546 16.9705L21.2115 22.6274L22.6257 21.2132L16.9688 15.5563L15.5546 16.9705Z" fill="#0A254D"/>
		</svg>
		<?php
	}
}
