<?php

// Common Content tables
$tables = array(

'lc_types_config' => "
	config_name C(40) NOTNULL,
	content_type_guid C(16) NOTNULL,
	config_value C(250)
	CONSTRAINT '
		, CONSTRAINT `lc_type_guid_ref` FOREIGN KEY (`content_type_guid`) REFERENCES `".BIT_DB_PREFIX."liberty_content_types`( `content_type_guid` )
",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( LCCONFIG_PKG_NAME, $tableName, $tables[$tableName], TRUE );
}

$gBitInstaller->registerPackageInfo( LCCONFIG_PKG_NAME, array(
	'description' => "Liberty Content Type Configuration.",
	'license' => 'Package must be licensed from <a href="http://tekimaki.com">Tekimaki LLC</a>, unauthorized use is prohibited.',
));

// Package requirements
$gBitInstaller->registerRequirements( KERNEL_PKG_NAME, array(
	'liberty'   => array( 'min' => '2.1.0' ),
));

