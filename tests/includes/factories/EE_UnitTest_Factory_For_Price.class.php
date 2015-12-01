<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }



/**
 * EE Factory Class for Prices
 *
 * Note that prices do  have a chained option.
 * However, this only applies to a price type automatically created and attached to the price.
 * Details about this price type can be included with the (optional) arguments for create, and create many.
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Price extends EE_UnitTest_Factory_for_Model_Object {

	/**
	 * constructor
	 *
	 * @param EE_UnitTest_Factory $factory
	 * @param array | null        $properties_and_relations
	 *          pass null (or nothing) to just get the default properties with NO relations
	 *          or pass empty array for default properties AND relations
	 *          or non-empty array to override default properties and manually set related objects and their properties,
	 */
	public function __construct( $factory, $properties_and_relations = null ) {
		//echo "\n\n " . __LINE__ . ") " . __METHOD__ . "()";
		$this->set_model_object_name( 'Price' );
		parent::__construct( $factory, $properties_and_relations );
	}



	/**
	 * _set_default_properties_and_relations
	 *
	 * @access protected
	 * @param string $called_class in order to avoid recursive application of relations,
	 *                             we need to know which class is making this request
	 * @return void
	 */
	protected function _set_default_properties_and_relations( $called_class ) {
		//echo "\n\n " . __LINE__ . ") " . __METHOD__ . "()";
		// set some sensible defaults for this model object
		if ( empty( $this->_default_properties ) ) {
			static $counter = 1;
			$this->_default_properties = array(
				'PRT_ID' 	 => 1,
				'PRC_name'   => sprintf( 'Price %s', $counter ),
				'PRC_desc'   => sprintf( 'Price Description %s', $counter ),
				'PRC_amount' => 0,
			);
			$counter++;
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			$this->_default_relations = array(
				'Price_Type'    => array(),
			);
			$this->_resolve_default_relations( $called_class );
			//echo "\n\n RESOLVED_RELATIONS for " . __CLASS__ . " ";
			//echo implode( ' ', array_keys( $this->_default_relations ) ) . ":";
			//echo " \n  {{ " . __LINE__ . ") " . __METHOD__ . "() }} \n";
			//var_dump( $resolved_relations );
			//$this->_default_relations = $resolved_relations;
			//$this->_default_properties = array_merge( $this->_default_properties, $this->_default_relations );
		}
	}



}
// End of file EE_UnitTest_Factory_For_Price.class.php
// Location: /EE_UnitTest_Factory_For_Price.class.php