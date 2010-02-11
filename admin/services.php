<?php
require_once( '../../kernel/setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );

require_once( '../LCConfig.php' );
$LCConfig = LCConfig::getInstance();

// deal with service preferences
if( !empty( $_REQUEST['save'] )) {
	$gBitUser->verifyTicket();
	$LCConfig->mDb->StartTrans();

	// store prefs
	foreach( array_keys( $gLibertySystem->mContentTypes ) as $ctype ) {
		foreach( $gLibertySystem->mServices as $guid=>$pkg ) {
			if( !empty( $_REQUEST['service_guids'][$guid][$ctype] ) ) {
				vd( 'service_'.$guid );
			}
		}
	}

	if( empty( $feedback['error'] ) ){
		$LCConfig->mDb->CompleteTrans();
		$feedback['success'] = tra( "some error?" );
	}
	else{
		$LCConfig->mDb->RollbackTrans();
		$LCConfig->reloadConfig();
	}
}
$gBitSmarty->assign_by_ref( 'feedback', $feedback );

$gBitSmarty->assign_by_ref( 'LCConfig', $LCConfig->getAllConfig() );
$gBitSystem->display( 'bitpackage:lcconfig/admin_services.tpl', tra( 'Set Service Preferences' ), array( 'display_mode' => 'admin' ));
