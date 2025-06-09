<?php
// This file is generated. Do not modify it manually.
return array(
	'gb-splide' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'generateblocks/splide',
		'version' => '1.0.0',
		'title' => 'Splide',
		'category' => 'generateblocks',
		'icon' => 'smiley',
		'description' => 'Slider/Carousel for GenerateBlocks',
		'supports' => array(
			'html' => false
		),
		'attributes' => array(
			'type' => array(
				'type' => 'string',
				'default' => 'slide'
			),
			'perPage' => array(
				'type' => 'number',
				'default' => 1
			),
			'perPageResponsive' => array(
				'type' => 'object',
				'default' => array(
					'desktop' => 1,
					'tablet' => 1,
					'mobile' => 1
				)
			),
			'perMove' => array(
				'type' => 'number',
				'default' => 1
			),
			'gap' => array(
				'type' => 'string',
				'default' => '1rem'
			),
			'autoplay' => array(
				'type' => 'boolean',
				'default' => true
			),
			'interval' => array(
				'type' => 'number',
				'default' => 5000
			),
			'pauseOnHover' => array(
				'type' => 'boolean',
				'default' => true
			),
			'speed' => array(
				'type' => 'number',
				'default' => 600
			),
			'arrows' => array(
				'type' => 'boolean',
				'default' => true
			),
			'pagination' => array(
				'type' => 'boolean',
				'default' => true
			),
			'direction' => array(
				'type' => 'string',
				'default' => 'ltr'
			),
			'rewind' => array(
				'type' => 'boolean',
				'default' => false
			),
			'height' => array(
				'type' => 'string',
				'default' => 'auto'
			),
			'width' => array(
				'type' => 'string',
				'default' => '100%'
			),
			'slides' => array(
				'type' => 'array',
				'default' => array(
					
				)
			)
		),
		'textdomain' => 'generateblocks',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	)
);
