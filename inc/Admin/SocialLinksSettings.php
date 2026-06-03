<?php
/**
 * Admin Settings Page for Social Links
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 */

namespace TM\Admin;

use TM\Core\Constants;

class SocialLinksSettings {

	private const OPTION_GROUP = 'tm_social_links_settings';
	private const PAGE_SLUG    = 'tm-social-links';

	public function register(): void {
		\add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
		\add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	public function add_admin_page(): void {
		\add_theme_page(
			\__( 'Social Links', 'transfertmarrakech' ),
			\__( 'Social Links', 'transfertmarrakech' ),
			'manage_options',
			self::PAGE_SLUG,
			[ $this, 'render_page' ]
		);
	}

	public function register_settings(): void {
		$networks = $this->get_networks();

		foreach ( $networks as $key => $label ) {
			\register_setting(
				self::OPTION_GROUP,
				$key,
				[
					'type'              => 'string',
					'sanitize_callback' => 'esc_url_raw',
					'default'           => '',
				]
			);
		}

		\add_settings_section(
			'tm_social_links_section',
			\__( 'Social Network URLs', 'transfertmarrakech' ),
			[ $this, 'render_section_description' ],
			self::PAGE_SLUG
		);

		foreach ( $networks as $key => $label ) {
			\add_settings_field(
				$key,
				$label,
				[ $this, 'render_url_field' ],
				self::PAGE_SLUG,
				'tm_social_links_section',
				[ 'option_key' => $key, 'label' => $label ]
			);
		}
	}

	public function render_section_description(): void {
		echo '<p>' . \esc_html__( 'Enter the full URL for each social network. Leave blank to hide the icon in the header.', 'transfertmarrakech' ) . '</p>';
	}

	public function render_url_field( array $args ): void {
		$value = \get_option( $args['option_key'], '' );
		?>
		<input
			type="url"
			name="<?php echo \esc_attr( $args['option_key'] ); ?>"
			value="<?php echo \esc_attr( $value ); ?>"
			class="regular-text"
			placeholder="https://"
		>
		<?php
	}

	public function render_page(): void {
		if ( ! \current_user_can( 'manage_options' ) ) {
			\wp_die( \__( 'You do not have the necessary permissions to access this page.', 'transfertmarrakech' ) );
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			\add_settings_error( 'tm_social_links_messages', 'tm_social_links_message', \__( 'Settings saved successfully.', 'transfertmarrakech' ), 'success' );
		}

		\settings_errors( 'tm_social_links_messages' );
		?>
		<div class="wrap">
			<h1><?php echo \esc_html( \get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				\settings_fields( self::OPTION_GROUP );
				\do_settings_sections( self::PAGE_SLUG );
				\submit_button( \__( 'Save Changes', 'transfertmarrakech' ) );
				?>
			</form>
		</div>
		<?php
	}

	private function get_networks(): array {
		return [
			Constants::OPTION_SOCIAL_FACEBOOK    => \__( 'Facebook URL', 'transfertmarrakech' ),
			Constants::OPTION_SOCIAL_INSTAGRAM   => \__( 'Instagram URL', 'transfertmarrakech' ),
			Constants::OPTION_SOCIAL_TRIPADVISOR => \__( 'TripAdvisor URL', 'transfertmarrakech' ),
		];
	}

	public static function get_links(): array {
		return [
			'facebook'    => \get_option( Constants::OPTION_SOCIAL_FACEBOOK, '' ),
			'instagram'   => \get_option( Constants::OPTION_SOCIAL_INSTAGRAM, '' ),
			'tripadvisor' => \get_option( Constants::OPTION_SOCIAL_TRIPADVISOR, '' ),
		];
	}
}
