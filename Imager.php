<?php
/**
 * Imager
 *
 * @package  Imager
 * @author   Stefan Jocić
 * @license  GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Imager
 * Description: Auto populate image alt from file name on upload | Auto generate and populate image alt on image load on front end
 * Version: 1.0.1
 * Requires at least: 5.2
 * Tested up to: 5.7.2
 * Requires PHP: 7.4
 * Author: Stefan Jocić
 * License: GPLv2 or later
 * Text Domain: imager
 */

defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

if ( ! class_exists( 'Imager' ) ) {
	class Imager {
		public function __construct() {
			add_action( 'add_attachment', [ $this, 'imageAltPopulate' ] );
			add_filter( 'wp_get_attachment_image_attributes', [ $this, 'imageAltInsert' ], 10, 3 );
		}

		/**
		 * Auto populate image alt from its title
		 *
		 * @param $attachment_id
		 */
		public function imageAltPopulate( $attachment_id ) {
			// Check if uploaded file is an image
			if ( wp_attachment_is_image( $attachment_id ) ) {
				$image_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

				if ( $image_alt ) {
					return;
				}

				$image_title = get_post( $attachment_id )->post_title;

				// Sanitize the title
				$image_title = $this->cleanImageTitle( $image_title );

				$this->updateImage( $attachment_id, $image_title, [ 'post_title' => $image_title ] );
			}
		}

		/**
		 * Auto fill image alt if not present when image is showing on front
		 *
		 * @param array   $attr
		 * @param WP_Post $attachment
		 * @param         $size
		 *
		 * @return array
		 */
		public function imageAltInsert( array $attr, WP_Post $attachment, $size ): array {
			if ( array_key_exists( 'alt', $attr ) && $attr[ 'alt' ] === '' ) {
				$alt           = $this->cleanImageTitle( $attachment->post_title );
				$attr[ 'alt' ] = $alt;

				$this->updateImage( $attachment->ID, $alt );
			}

			return $attr;
		}

		/**
		 * Sanitize the title:  remove hyphens, underscores & extra spaces
		 * capitalize first letter of every word (other letters lower case)
		 *
		 * @param string $title
		 *
		 * @return array|string|string[]|null
		 */
		private function cleanImageTitle( string $title ) {
			return preg_replace( '%\s*[-_\s]+\s*%', ' ', ucwords( strtolower( esc_attr( $title ) ) ) );
		}

		/**
		 * @param int    $attachment_id
		 * @param string $alt
		 * @param array  $args ['post_title' => 'Image title', 'post_excerpt' => 'Image caption', 'post_content' => 'Image description']
		 */
		private function updateImage( int $attachment_id, string $alt, array $args = [] ) {
			// Set the image Alt-Text
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', esc_attr( $alt ) );

			if ( ! empty( $args ) ) {
				// Set the image meta (e.g. Title, Excerpt, Content)
				wp_update_post( [
					                'ID' => $attachment_id
				                ] + $args );
			}
		}
	}
}

new Imager();