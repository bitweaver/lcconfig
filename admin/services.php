<?php
require_once( '../../kernel/setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );

require_once( '../LCConfig.php' );
$LCConfig = LCConfig::getInstance();

// sort services by required state and name to help make the table more legible
foreach( $gLibertySystem->mServices as $sguid=>$serv ){
	$required[$sguid] = $serv['required'];
	$name[$sguid] = $sguid;
}
array_multisort( $required, SORT_ASC, $name, SORT_ASC, $gLibertySystem->mServices );

// deal with service preferences
if( !empty( $_REQUEST['save'] )) {
	$gBitUser->verifyTicket();
	$LCConfig->mDb->StartTrans();


	// store prefs
	foreach( array_keys( $gLibertySystem->mContentTypes ) as $ctype ) {
		foreach( $gLibertySystem->mServices as $guid=>$service ) {
			if( empty( $service['required'] ) ){
				if( empty( $_REQUEST['service_guids'][$guid][$ctype] ) || $_REQUEST['service_guids'][$guid][$ctype] == 'y' ){
					// for service config we actually store the negation, so remove a positive record to keep the db records light
					$LCConfig->expungeConfig( 'service_'.$guid, $ctype );
				} else {
					// for service config we actually store the negation or a special value
					// valid params are 'n' and 'required'
					$LCConfig->storeConfig( 'service_'.$guid, $ctype, $_REQUEST['service_guids'][$guid][$ctype] );
				}
			}
		}
	}

	if( empty( $feedback['error'] ) ){
		$LCConfig->mDb->CompleteTrans();
		$feedback['success'] = tra( "Services preferences were updated." );
		$LCConfig->reloadConfig();
	}
	else{
		$LCConfig->mDb->RollbackTrans();
		$LCConfig->reloadConfig();
	}
}
$gBitSmarty->assign_by_ref( 'feedback', $feedback );

$gBitSmarty->assign_by_ref( 'LCConfigSettings', $LCConfig->getAllConfig() );
$gBitSystem->display( 'bitpackage:lcconfig/admin_services.tpl', tra( 'Set Service Preferences' ), array( 'display_mode' => 'admin' ));
