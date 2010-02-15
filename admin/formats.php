<?php
require_once( '../../kernel/setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );

require_once( '../LCConfig.php' );
$LCConfig = LCConfig::getInstance();

// deal with content type formats
if( !empty( $_REQUEST['save'] )) {
	$gBitUser->verifyTicket();
	$LCConfig->mDb->StartTrans();
	foreach( array_keys( $gLibertySystem->mContentTypes ) as $ctype ) {
		foreach( $gLibertySystem->mPlugins as $guid=>$plugin ) {
			// pluck text formats from all plugins
			if($plugin['is_active'] == 'y' && 
				!empty( $plugin['edit_field'] ) &&
				$plugin['plugin_type'] == 'format'){
				if( !empty( $_REQUEST['plugin_guids'][$guid][$ctype] ) ) {
					// for format config we actually store the negation, so remove a positive record to keep the db records light
					$LCConfig->expungeConfig( 'format_'.$guid, $ctype );
					// crude storage of custom default
					if( !empty( $_REQUEST['default_format_'.$ctype] ) && $_REQUEST['default_format_'.$ctype] != $gBitSystem->getConfig('default_format') ){
						$LCConfig->storeConfig( 'default_format', $ctype, $guid );
					}else{
						// if not set make sure we remove it incase it was set before
						$LCConfig->expungeConfig( 'default_format', $ctype );
					}
				} else {
					// for format config we actually store the negation
					$LCConfig->storeConfig( 'format_'.$guid, $ctype, 'n' );
					// if format is not supported it can't be the default
					$LCConfig->expungeConfig( 'default_format', $ctype );
					if( !empty( $_REQUEST['default_format_'.$ctype] ) && $_REQUEST['default_format_'.$ctype] == $guid ){
						$feedback['error'] = tra( 'You can not select the disabled format '.$plugin['edit_label'].' as the default format. Please choose another for content type '.$ctype );
					}
				}
			}
		}
	}

	if( empty( $feedback['error'] ) ){
		$LCConfig->mDb->CompleteTrans();
		$feedback['success'] = tra( "The formats were assigned to the selected content types." );
	}
	else{
		$LCConfig->mDb->RollbackTrans();
		$LCConfig->reloadConfig();
	}
}
$gBitSmarty->assign_by_ref( 'feedback', $feedback );


$gBitSmarty->assign_by_ref( 'LCConfigSettings', $LCConfig->getAllConfig() );
$gBitSystem->display( 'bitpackage:lcconfig/admin_formats.tpl', tra( 'Assign Content Type Formats' ), array( 'display_mode' => 'admin' ));
