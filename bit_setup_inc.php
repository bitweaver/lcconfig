<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'lcconfig',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'lcconfig' ) ) {
	// service funcs
	define( 'LIBERTY_SERVICE_LCCONFIG', 'liberty content config' );
	$gLibertySystem->registerService( LIBERTY_SERVICE_LCCONFIG, LCCONFIG_PKG_NAME, array(
		'content_preview_function'		=> 'lcconfig_content_edit',
		'content_edit_function'			=> 'lcconfig_content_edit',
		'content_verify_function'		=> 'lcconfig_content_verify',
	) );
	require_once( 'LCConfig.php' );
}


