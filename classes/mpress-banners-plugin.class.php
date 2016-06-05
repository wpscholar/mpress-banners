<?php

if ( ! class_exists( 'mPress_Banners_Plugin' ) ) {

	class mPress_Banners_Plugin {

		private $basename,
				$path,
				$file,
				$name,
				$slug,
				$url,
				$version;

		public $properties = array();

		function __construct( $name, $slug, $version, $file ) {
			$this->name = $name;
			$this->slug = $slug;
			$this->version = $version;
			$this->file = $file;
			$this->basename = plugin_basename( $file );
			$this->path = plugin_dir_path( $file );
			$this->url = plugin_dir_url( $file );
		}

		function __set( $property, $value ) {
			if( property_exists( __CLASS__, $property ) ) {
				throw new Exception( sprintf('Property "%s" already exists and cannot be overwritten', $property) );
			} else {
				$this->properties[ $property ] = $value;
			}
		}

		function __get( $property ) {
			if ( property_exists( __CLASS__, $property ) ) {
				return $this->$property;
			} else if ( isset( $this->properties[ $property ] ) ) {
				return $this->properties[ $property ];
			} else {
				throw new Exception( sprintf( 'Undefined property "%s" via __get()', $property ) );
			}
		}

	}

}