<?php
require_once( '../../kernel/setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );

require_once( '../LCConfig.php' );
$LCConfig = LCConfig::getInstance();

// vd( $_REQUEST );

// deal with service preferences
if( !empty( $_REQUEST['save'] )) {
	$gBitUser->verifyTicket();
	$LCConfig->mDb->StartTrans();

	// store prefs
	foreach( array_keys( $gLibertySystem->mContentTypes ) as $ctype ) {
		foreach( $gLibertySystem->mServices as $guid=>$service ) {
			if( empty( $service['required'] ) ){
				if( !empty( $_REQUEST['service_guids'][$guid][$ctype] ) ) {
					// for service config we actually store the negation, so remove a positive record to keep the db records light
					$LCConfig->expungeConfig( 'service_'.$guid, $ctype );
				} else {
					// for service config we actually store the negation
					$LCConfig->storeConfig( 'service_'.$guid, $ctype, 'n' );
				}
			}
		}
	}

	if( empty( $feedback['error'] ) ){
		$LCConfig->mDb->CompleteTrans();
		$feedback['success'] = tra( "Services preferences were updated." );
	}
	else{
		$LCConfig->mDb->RollbackTrans();
		$LCConfig->reloadConfig();
	}
}
$gBitSmarty->assign_by_ref( 'feedback', $feedback );

$gBitSmarty->assign_by_ref( 'LCConfigSettings', $LCConfig->getAllConfig() );
$gBitSystem->display( 'bitpackage:lcconfig/admin_services.tpl', tra( 'Set Service Preferences' ), array( 'display_mode' => 'admin' ));
