<?php
require_once( '../../bit_setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );

require_once( '../LCConfig.php' );
$LCConfig = LCConfig::getInstance();

$feedback = array();

// deal with content type formats
if( !empty( $_REQUEST['save'] )) {
	$gBitUser->verifyTicket();
	foreach( array_keys( $gLibertySystem->mContentTypes ) as $ctype ) {
		foreach( $gLibertySystem->mPlugins as $guid=>$plugin ) {
			if($plugin['is_active'] == 'y' && 
				!empty( $plugin['edit_field'] ) &&
				$plugin['plugin_type'] == 'format'){
				if( !empty( $_REQUEST['plugin_guids'][$guid][$ctype] )) {
					// for format config we actually store the negation, so remove a positive record to keep the db records light
					$LCConfig->expungeConfig( 'format_'.$guid, $ctype );
				} else {
					// for format config we actually store the negation
					$LCConfig->storeConfig( 'format_'.$guid, $ctype, 'n' );
				}
			}
		}
	}

	$feedback['success'] = tra( "The formats were assigned to the selected content types." );
}

$gBitSmarty->assign_by_ref( 'LCConfigs', $LCConfig->getAllConfig() );
$gBitSystem->display( 'bitpackage:lcconfig/admin_formats.tpl', tra( 'Assign Content Type Formats' ), array( 'display_mode' => 'admin' ));
