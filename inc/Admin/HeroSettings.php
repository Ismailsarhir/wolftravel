<?php
/**
 * Admin Settings Page for Hero Section
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 */

namespace TM\Admin;

/**
 * Classe pour gérer la page d'administration du Hero
 */
class HeroSettings {

	private const OPTION_GROUP = 'tm_hero_settings';
	private const PAGE_SLUG    = 'tm-hero';
	private const IMAGE_COUNT  = 5;

	public function register(): void {
		\add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
		\add_action( 'admin_init', [ $this, 'register_settings' ] );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function add_admin_page(): void {
		\add_theme_page(
			\__( 'Hero Section', 'transfertmarrakech' ),
			\__( 'Hero Section', 'transfertmarrakech' ),
			'manage_options',
			self::PAGE_SLUG,
			[ $this, 'render_page' ]
		);
	}

	public function enqueue_scripts( string $hook_suffix ): void {
		if ( $hook_suffix !== 'appearance_page_' . self::PAGE_SLUG ) {
			return;
		}
		\wp_enqueue_media();
	}

	public function register_settings(): void {
		\register_setting(
			self::OPTION_GROUP,
			'tm_hero_heading',
			[
				'type'              => 'string',
				'sanitize_callback' => 'wp_kses_post',
				'default'           => '',
			]
		);

		for ( $i = 1; $i <= self::IMAGE_COUNT; $i++ ) {
			\register_setting(
				self::OPTION_GROUP,
				"tm_hero_image_{$i}",
				[
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
					'default'           => 0,
				]
			);
		}

		\add_settings_section(
			'tm_hero_content_section',
			\__( 'Hero Content', 'transfertmarrakech' ),
			[ $this, 'render_content_section_description' ],
			self::PAGE_SLUG
		);

		\add_settings_field(
			'tm_hero_heading',
			\__( 'Heading Text', 'transfertmarrakech' ),
			[ $this, 'render_heading_field' ],
			self::PAGE_SLUG,
			'tm_hero_content_section'
		);

		\add_settings_section(
			'tm_hero_images_section',
			\__( 'Hero Images', 'transfertmarrakech' ),
			[ $this, 'render_images_section_description' ],
			self::PAGE_SLUG
		);

		for ( $i = 1; $i <= self::IMAGE_COUNT; $i++ ) {
			\add_settings_field(
				"tm_hero_image_{$i}",
				/* translators: %d: image number */
				sprintf( \__( 'Image %d', 'transfertmarrakech' ), $i ),
				[ $this, 'render_image_field' ],
				self::PAGE_SLUG,
				'tm_hero_images_section',
				[ 'index' => $i ]
			);
		}
	}

	public function render_content_section_description(): void {
		echo '<p>' . \esc_html__( 'Configure the text displayed in the hero section.', 'transfertmarrakech' ) . '</p>';
	}

	public function render_images_section_description(): void {
		echo '<p>' . \esc_html__( 'Select up to 5 images for the hero gallery. Leave empty to use the default theme images.', 'transfertmarrakech' ) . '</p>';
	}

	public function render_heading_field(): void {
		$value = \get_option( 'tm_hero_heading', '' );
		$placeholder = \__( 'We design objects that carry the weight of their own conviction, where every curve and joint exists not for beauty but because the material demanded it.', 'transfertmarrakech' );
		?>
		<textarea
			name="tm_hero_heading"
			rows="4"
			class="large-text"
			placeholder="<?php echo \esc_attr( $placeholder ); ?>"
		><?php echo \esc_textarea( $value ); ?></textarea>
		<p class="description">
			<?php \esc_html_e( 'The main heading displayed in the hero section. Leave empty to use the default text.', 'transfertmarrakech' ); ?>
		</p>
		<?php
	}

	public function render_image_field( array $args ): void {
		$i          = $args['index'];
		$option_key = "tm_hero_image_{$i}";
		$image_id   = \absint( \get_option( $option_key, 0 ) );
		$image_url  = $image_id ? \wp_get_attachment_image_url( $image_id, 'medium' ) : '';
		$is_main    = ( $i === 3 );
		?>
		<div class="tm-hero-image-picker" style="display:flex;align-items:flex-start;gap:12px;">
			<div class="tm-hero-image-preview" style="width:150px;height:100px;background:#f0f0f0;border:1px solid #ccc;overflow:hidden;display:flex;align-items:center;justify-content:center;">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo \esc_url( $image_url ); ?>" style="width:100%;height:100%;object-fit:cover;" alt="" />
				<?php else : ?>
					<span style="color:#999;font-size:12px;"><?php \esc_html_e( 'No image', 'transfertmarrakech' ); ?></span>
				<?php endif; ?>
			</div>
			<div>
				<input type="hidden" name="<?php echo \esc_attr( $option_key ); ?>" value="<?php echo \esc_attr( $image_id ); ?>" class="tm-hero-image-id" />
				<button type="button" class="button tm-hero-select-image"><?php \esc_html_e( 'Select Image', 'transfertmarrakech' ); ?></button>
				<button type="button" class="button tm-hero-remove-image" <?php echo ! $image_id ? 'style="display:none;"' : ''; ?>><?php \esc_html_e( 'Remove', 'transfertmarrakech' ); ?></button>
				<?php if ( $is_main ) : ?>
					<p class="description"><?php \esc_html_e( 'This is the main (center) hero image.', 'transfertmarrakech' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function render_page(): void {
		if ( ! \current_user_can( 'manage_options' ) ) {
			\wp_die( \__( 'You do not have the necessary permissions to access this page.', 'transfertmarrakech' ) );
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			\add_settings_error(
				'tm_hero_messages',
				'tm_hero_message',
				\__( 'Settings saved successfully.', 'transfertmarrakech' ),
				'success'
			);
		}

		\settings_errors( 'tm_hero_messages' );
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
		<script>
		(function($) {
			$('.tm-hero-select-image').on('click', function() {
				var $btn     = $(this);
				var $picker  = $btn.closest('.tm-hero-image-picker');
				var $input   = $picker.find('.tm-hero-image-id');
				var $preview = $picker.find('.tm-hero-image-preview');
				var $remove  = $picker.find('.tm-hero-remove-image');

				var frame = wp.media({
					title: '<?php echo \esc_js( \__( 'Select Hero Image', 'transfertmarrakech' ) ); ?>',
					button: { text: '<?php echo \esc_js( \__( 'Use this image', 'transfertmarrakech' ) ); ?>' },
					multiple: false,
					library: { type: 'image' }
				});

				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					$input.val(attachment.id);
					var previewUrl = attachment.sizes && attachment.sizes.medium
						? attachment.sizes.medium.url
						: attachment.url;
					$preview.html('<img src="' + previewUrl + '" style="width:100%;height:100%;object-fit:cover;" alt="" />');
					$remove.show();
				});

				frame.open();
			});

			$('.tm-hero-remove-image').on('click', function() {
				var $picker  = $(this).closest('.tm-hero-image-picker');
				var $input   = $picker.find('.tm-hero-image-id');
				var $preview = $picker.find('.tm-hero-image-preview');
				$input.val(0);
				$preview.html('<span style="color:#999;font-size:12px;"><?php echo \esc_js( \__( 'No image', 'transfertmarrakech' ) ); ?></span>');
				$(this).hide();
			});
		})(jQuery);
		</script>
		<?php
	}
}
