<?php
/**
 * Template part pour le Hero
 *
 * @package TransfertMarrakech
 * @since 1.0.0
 *
 * @var array<int, string> $hero_images  URLs des 5 images hero, indexées de 1 à 5
 * @var string             $hero_heading Texte du heading principal
 */

$hero_images  = $hero_images ?? [];
$hero_heading = $hero_heading ?? '';
?>

<div class="preloader-overlay">
	<div class="preloader"></div>
</div>

<section class="hero">
	<div class="intro-img"><img src="<?php echo \esc_url( $hero_images[1] ); ?>" alt="" /></div>
	<div class="intro-img"><img src="<?php echo \esc_url( $hero_images[2] ); ?>" alt="" /></div>
	<div class="intro-img hero-img"><img src="<?php echo \esc_url( $hero_images[3] ); ?>" alt="" /></div>
	<div class="intro-img"><img src="<?php echo \esc_url( $hero_images[4] ); ?>" alt="" /></div>
	<div class="intro-img"><img src="<?php echo \esc_url( $hero_images[5] ); ?>" alt="" /></div>

	<div class="hero-content">
		<div class="hero-header">
			<h1><?php echo \wp_kses_post( $hero_heading ); ?></h1>
		</div>
	</div>
</section>
