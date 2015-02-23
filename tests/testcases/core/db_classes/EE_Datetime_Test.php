<?php

if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 *
 * EE_Datetime_Test
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
/**
 * @group core/db_classes
 */
class EE_Datetime_Test extends EE_UnitTestCase{

	function test_increase_sold(){
		$d = EE_Datetime::new_instance();
		$this->assertEquals($d->get('DTT_sold'),0);
		$d->increase_sold();
		$this->assertEquals($d->get('DTT_sold'),1);
		$d->increase_sold(2);
		$this->assertEquals($d->get('DTT_sold'),3);
	}
	function test_decrease_sold(){
		$d = EE_Datetime::new_instance(array('DTT_sold'=>5));
		$d->decrease_sold();
		$this->assertEquals(4,$d->get('DTT_sold'));
		$d->decrease_sold(2);
		$this->assertEquals(2,$d->get('DTT_sold'));
	}
	/**
	 * because at one point EE_Datetime overrode ID() from its parent
	 * (not really for any good reason at the time of writing)
	 */
	function test_id(){
		$d = EE_Datetime::new_instance();
		$id = $d->save();
		$this->assertEquals($id,$d->ID());
	}
	function test_start(){
		$start_time = new DateTime("now");
		$d = EE_Datetime::new_instance(array('DTT_EVT_start'=>$start_time->format('U')));
		$this->assertEquals($start_time->format('U'),$d->start());
	}
	function test_end(){
		$end_time =new DateTime("now");
		$d = EE_Datetime::new_instance(array('DTT_EVT_end'=>$end_time->format('U')));
		$this->assertEquals($end_time->format('U'),$d->end());
	}
	function test_reg_limit(){
		$d = EE_Datetime::new_instance(array('DTT_reg_limit'=>10));
		$this->assertEquals(10,$d->get('DTT_reg_limit'));
	}
	function test_sold(){
		$d = EE_Datetime::new_instance(array('DTT_sold'=>10));
		$this->assertEquals(10,$d->sold());
	}
	function test_sold_out(){
		$d = EE_Datetime::new_instance(array('DTT_reg_limit'=>10));
		$this->assertFalse($d->sold_out());
		$d->set_sold(10);
		$this->assertTrue($d->sold_out());
		$d->set('DTT_reg_limit',INF);
		$this->assertFalse($d->sold_out());
	}
	function test_spaces_remaining(){
		$d = EE_Datetime::new_instance(array('DTT_reg_limit'=>20,'DTT_sold'=>5));
		$this->assertEquals(15,$d->spaces_remaining());
	}
	function test_is_upcoming(){
		$d = EE_Datetime::new_instance(array('DTT_EVT_start'=>current_time('timestamp') + 1000 ));
		$this->assertTrue($d->is_upcoming());
		$d->set('DTT_EVT_start',current_time('timestamp') - 1000 );
		$this->assertFalse($d->is_upcoming());
	}
	function test_is_active(){
		$d = EE_Datetime::new_instance(array('DTT_EVT_start'=>current_time('timestamp') - 1000, 'DTT_EVT_end'=>current_time('timestamp') + 1000));
		$this->assertTrue($d->is_active());
		$d->set('DTT_EVT_start',current_time('timestamp') + 500);
		$this->assertFalse($d->is_active());
	}
	function test_is_expired(){
		$d = EE_Datetime::new_instance(array('DTT_EVT_end'=>current_time('timestamp') - 1000));
		$this->assertTrue($d->is_expired());
		$d->set('DTT_EVT_end',current_time('timestamp') + 1000);
		$this->assertFalse($d->is_expired());
	}
	function test_datetime_display(){
		$sdate = new DateTime( "now" );
		$edate = new DateTime( "now +2days" );
		$d = EE_Datetime::new_instance(array('DTT_name'=>'monkey time', 'DTT_EVT_start'=>$sdate->format('U'), 'DTT_EVT_end'=>$edate->format('U')));
		$d->set_date_format( 'Y-m-d' );
		$d->set_time_format( 'h:i a' );
		$this->assertEquals( $sdate->format('M j\, g:i a') . ' - ' . $edate->format('M j\, g:i a Y'),$d->get_dtt_display_name());
		$this->assertEquals('monkey time',$d->get_dtt_display_name(true));
	}




	/**
	 * This tests the ticket_types_available_for_purchase method.
	 * @since 4.6.0
	 */
	public function test_ticket_types_available_for_purchase() {
		//setup some dates we'll use for testing with.
		$timezone = new DateTimeZone( 'America/Toronto' );
		$upcoming_start_date = new DateTime( "now +2days", $timezone );
		$past_start_date = new DateTime( "now -2days", $timezone );
		$current_end_date = new DateTime( "now +2hours", $timezone );
		$current = new DateTime( "now", $timezone );
		$formats = array( 'Y-d-m',  'h:i a' );
		$full_format = implode( ' ', $formats );

		//create some tickets
		$tickets = array(
			'expired_ticket' => array( 'TKT_start_date' => $past_start_date->format($full_format), 'TKT_end_date' => $past_start_date->format($full_format), 'timezone' => 'America/Toronto', 'formats' =>$formats ),
			'upcoming_ticket' => array( 'TKT_start_date' => $past_start_date->format( $full_format ), 'TKT_end_date' => $upcoming_start_date->format( $full_format ), 'timezone' => 'America/Toronto', 'formats' => $formats )
			);

		$datetimes = array(
			'expired_datetime' => $this->factory->datetime->create( array( 'DTT_EVT_start' => $past_start_date->format( $full_format ), 'DTT_EVT_end' => $past_start_date->format( $full_format), 'timezone' => 'America/Toronto', 'formats' =>  $formats ) ),
			'upcoming_datetime' => $this->factory->datetime->create( array( 'DTT_EVT_start' => $upcoming_start_date->format( $full_format ), 'DTT_EVT_end' => $upcoming_start_date->format( $full_format), 'timezone' => 'America/Toronto', 'formats' => $formats ) ),
			'active_datetime' => $this->factory->datetime->create( array( 'DTT_EVT_start' => $current->format( $full_format ), 'DTT_EVT_end' => $current_end_date->format( $full_format), 'timezone' => 'America/Toronto', 'formats' =>  $formats ) ),
			'sold_out_datetime' => $this->factory->datetime->create( array( 'DTT_EVT_start' => $upcoming_start_date->format( $full_format ), 'DTT_EVT_end' => $upcoming_start_date->format( $full_format), 'DTT_reg_limit' => 10, 'DTT_sold' => 10,  'timezone' => 'America/Toronto', 'formats' =>  $formats ) )
			);

		//assign tickets to all datetimes
		foreach ( $datetimes as $datetime ) {
			foreach( $tickets as $ticket_args ) {
				$tkt = $this->factory->ticket->create ( $ticket_args );
				$datetime->_add_relation_to( $tkt, 'Ticket' );
				$datetime->save();
				$dtt_id = $datetime;
			}
		}

		//okay NOW we have some objects for testing with.

		//test expired_datetime
		$this->assertEmpty( $datetimes['expired_datetime']->ticket_types_available_for_purchase() );

		//test upcoming datetime
		$tickets = $datetimes['upcoming_datetime']->ticket_types_available_for_purchase();
		$this->assertEquals( 1, count( $tickets ) );
		$this->assertInstanceOf( 'EE_Ticket', reset( $tickets ) );

		//test active datetime
		$tickets = $datetimes['active_datetime']->ticket_types_available_for_purchase();
		$this->assertEquals( 1, count( $tickets ) );
		$this->assertInstanceOf( 'EE_Ticket', reset( $tickets ) );

		//test sold out datetime
		$this->assertEmpty( $datetimes['sold_out_datetime']->ticket_types_available_for_purchase() );
	}


}

// End of file EE_Datetime_Test.php
