<?php
/* LCConfig is singleton class
 * To instantiate call static function getInstance
 * $lcconfigRef = LCConfig::getInstance();
 */
class LCConfig extends BitBase {

	private static $uniqueInstance;

	private $mConfig;

	private function __construct(){
		BitBase::BitBase();
		$this->loadConfig();
	}

	public static function getInstance(){
		if( !isset( self::$uniqueInstance ) ){
			self::$uniqueInstance = new LCConfig();
		}
		return self::$uniqueInstance;
	}

	public function loadConfig(){
		if ( empty( $this->mConfig ) ) {
			$this->mConfig = array();
			$query = "SELECT `config_name`, `content_type_guid`, `config_value` FROM `" . BIT_DB_PREFIX . "lc_types_config` ";
			if( $rs = $this->mDb->query( $query, array(), -1, -1 ) ) {
				while( $row = $rs->fetchRow() ) {
					$this->mConfig[$row['content_type_guid']][$row['config_name']] = $row['config_value'];
				}
			}
		}
		return count( $this->mConfig );
	}

	public function reloadConfig(){
		$this->mConfig = array();
		return $this->loadConfig();
	}

	public function getConfig( $pName, $pContentTypeGuid, $pDefault = NULL ){
		if( empty( $this->mConfig ) ) {
			$this->loadConfig();
		}
		return( empty( $this->mConfig[$pContentTypeGuid][$pName] ) ? $pDefault : $this->mConfig[$pContentTypeGuid][$pName] );
	}

	public function setConfig( $pName, $pContentTypeGuid, $pValue ){
		$this->mConfig[$pContentTypeGuid][$pName] = $pValue;
		return( TRUE );
	}

	public function getAllConfig( $pContentTypeGuid = NULL ){
		$ret = array();
		if( empty( $this->mConfig ) ) {
			$this->loadConfig();
		}
		if( !empty( $pContentTypeGuid ) ){
			if( !empty( $this->mConfig[$pContentTypeGuid] ) ){
				$ret = $this->mConfig[$pContentTypeGuid];
			}
		}else{
			$ret = $this->mConfig;
		}
		return $ret;
	}

	public function storeConfig( $pName, $pContentTypeGuid, $pValue ) {
		// make sure the value doesn't exceede database limitations
		$pValue = substr( $pValue, 0, 250 );

		// store the preference in multisites, if used
		$query = "DELETE FROM `".BIT_DB_PREFIX."lc_types_config` WHERE `config_name`=? AND `content_type_guid`=?";
		$result = $this->mDb->query( $query, array( $pName, $pContentTypeGuid ) );

		// make sure only non-empty values get saved, including '0'
		if( isset( $pValue ) && ( !empty( $pValue ) || is_numeric( $pValue ))) {
			$query = "INSERT INTO `".BIT_DB_PREFIX."lc_types_config`(`config_name`,`content_type_guid`,`config_value`) VALUES (?,?,?)";
			$result = $this->mDb->query( $query, array( $pName, $pContentTypeGuid, $pValue ));
		}

		// Force the ADODB cache to flush
		$isCaching = $this->mDb->isCachingActive();
		$this->mDb->setCaching( FALSE );
		$this->loadConfig();
		$this->mDb->setCaching( $isCaching );

		$this->setConfig( $pName, $pContentTypeGuid, $pValue );
		return TRUE;
	}

	public function expungeConfig( $pName, $pContentTypeGuid ){
		if( !empty( $pName ) && !empty( $pContentTypeGuid ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."lc_types_config` WHERE `config_name`=? AND `content_type_guid`=?";
			$result = $this->mDb->query( $query, array( $pName, $pContentTypeGuid ) );
			// let's force a reload of the prefs
			unset( $this->mConfig );
			$this->loadConfig();
		}
	}
}

function lcconfig_content_edit( &$pObject, &$pParamHash ){
	global $gLibertySystem;
	$LCConfig = LCConfig::getInstance();
	$guids = $LCConfig->getAllConfig( $pObject->getContentType() );
	if( !empty( $guids ) ){ 
		foreach( $guids as $key=>$value ){
			// look for format plugin values
			if( substr( $key, 0, 7 ) == 'format_' ){
				$guid = substr( $key, 7 );
				if( !empty( $gLibertySystem->mPlugins[$guid] ) ){
					// unset any matching formats from liberty system
					// a little invasive but instantly effective on existing templates
					// this would be a problem if one where editing multiple content on
					// the same page and needed the full list of plugins
					unset( $gLibertySystem->mPlugins[$guid] );
				}
			}
		}
	}
}

function lcconfig_content_verify( &$pObject, &$pParamHash ){
	// @TODO reject any edits not conforming to config values
}
