<?php

if (!defined('EVENT_ESPRESSO_VERSION') )
	exit('NO direct script access allowed');

/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package		Event Espresso
 * @ author			Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license		http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link			http://www.eventespresso.com
 * @ version		4.0
 *
 * ------------------------------------------------------------------------
 *
 * EE_Messages_Preview_incoming_data
 *
 * This prepares dummy data for all messages previews run in the back end.  The Preview Data is going to use a given event id for the data.  If that event is NOT provided then we'll retrieve the first three published events from the users database as a sample (or whatever is available if there aren't three).
 *
 * To assemble the preview data, I basically used the EE_Single_Page_Checkout class to server as a guide for what data objects are setup etc.  Any place there is input expected from registrants we just setup some dummy inputs.  Remember none of this is actually saved to the database.  It is all one time use for any generated previews.
 *
 * @package		Event Espresso
 * @subpackage	includes/core/messages/data_class/EE_Messages_Preview_incoming_data.core.php
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */

class EE_Messages_Preview_incoming_data extends EE_Messages_incoming_data {

	//some specific properties we need for this class
	private $_events = array();
	private $_attendees = array();
	private $_running_total = 0;


	/**
	 * For the constructor of this special preview class.  We're either looking for an event id or empty data.  If we have an event id (or ids) then we'll use that as the source for the "dummy" data.  If the data is empty then we'll get the first three published events from the users database and use that as a source.
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		
		$data = empty($data) ? array() : $data['event_ids'];
		$this->_setup_attendees_events();
		parent::__construct($data);
	}



	/**
	 * This will just setup the _events property in the expected format.
	 * @return void
	 */
	private function _setup_attendees_events() {

		//setup some attendee objects
		$attendees = $this->_get_some_attendees();

		//if empty $data we'll do a query to get some events from the server. otherwise we'll retrieve the event data for the given ids.
		$events = empty($this->_data) ? $this->_get_some_events() : $this->_get_some_events($this->_data);

		$answers_n_questions = $this->_get_some_q_and_as();

		if ( count( $events ) < 1 ) {
			throw new EE_Error( __('We can\'t generate a preview for you because there are no active events in your database', 'event_espresso' ) );
		}



		//now let's loop and set up the _events property.  At the same time we'll set up attendee properties.
		

		//we'll actually use the generated line_item identifiers for our loop
		foreach( $events as $id => $event ) {
			$this->_events[$id]['ID'] = $id;
			$this->_events[$id]['name'] = $event->get('EVT_name');
			$tickets = $event->get_first_related('Datetime')->get_many_related('Ticket');
			$this->_events[$id]['event'] = $event;
			$this->_events[$id]['reg_objs'] = array();
			$this->_events[$id]['tkt_objs'] = $tickets;

			$dtts = array();
			$dttcache = array();
			foreach ( $tickets as $ticket ) {
				$tkts[$ticket->ID()]['ticket'] = $ticket;
				$reldatetime = $ticket->get_many_related('Datetime');
				$tkts[$ticket->ID()]['dtt_objs'] = $reldatetime;
				$tkts[$ticket->ID()]['att_objs'] = $attendees;
				foreach ( $reldatetime as $datetime ) {
					if ( !isset( $dtts[$datetime->ID()] ) ) {
						$this->_events[$id]['dtt_objs'][$datetime->ID()] = $datetime;
						$dtts[$datetime->ID()]['datetime'] = $datetime;
						$dtts[$datetime->ID()]['tkt_objs'][] = $ticket;
						$dtts[$datetime->ID()]['evt_objs'][] = $event;
						$dttcache[$datetime->ID()] = $datetime;
					}
				}
			}


			$this->_events[$id]['pre_approval'] = 0; //we're going to ignore the event settings for this.
			$this->_events[$id]['total_attendees'] = count( $attendees );
			$this->_events[$id]['att_objs'] = $attendees;

			//let's also setup the dummy attendees property!
			foreach ( $attendees as $att_key => $attendee ) {
				$this->_attendees[$att_key]['line_ref'][] = $id;  //so later it can be determined what events this attendee registered for!
				$this->_attendees[$att_key]['evt_objs'][] = $event;
				$this->_attendees[$att_key]['att_obj'] = $attendee;
				$this->_attendees[$att_key]['reg_objs'] = NULL;
				$this->_attendees[$att_key]['registration_id'] = 0;
				$this->_attendees[$att_key]['attendee_email'] = $attendee->email();
				$this->_attendees[$att_key]['tkt_objs'] = $tickets;
				if ( $att_key == 999999991 ) {
					$this->_attendees[$att_key]['ans_objs'][999] = $answers_n_questions['answers'][999];
					$this->_attendees[$att_key]['ans_objs'][1002] = $answers_n_questions['answers'][1002];
					$this->_attendees[$att_key]['ans_objs'][1005] = $answers_n_questions['answers'][1005];
				} elseif ( $att_key == 999999992 ) {
					$this->_attendees[$att_key]['ans_objs'][1000] = $answers_n_questions['answers'][1000];
					$this->_attendees[$att_key]['ans_objs'][1003] = $answers_n_questions['answers'][1003];
					$this->_attendees[$att_key]['ans_objs'][1006] = $answers_n_questions['answers'][1006];
				} elseif ( $att_key == 999999993 ) {
					$this->_attendees[$att_key]['ans_objs'][1001] = $answers_n_questions['answers'][1001];
					$this->_attendees[$att_key]['ans_objs'][1004] = $answers_n_questions['answers'][1004];
					$this->_attendees[$att_key]['ans_objs'][1007] = $answers_n_questions['answers'][1007];
				}
			}
		}

		$this->tickets = $tkts;
		$this->datetimes = $dtts;
		$this->answers = $answers_n_questions['answers'];
		$this->questions = $answers_n_questions['questions'];

	}



	/**
	 * This just returns an array of dummy attendee objects that we'll use to attach to events for our preview data
	 *
	 * @access private
	 * @return array an array of attendee objects
	 */
	private function _get_some_attendees() {
		//let's just setup a dummy array of various attendee details
		$dummy_attendees = array(
			0 => array(
				'Luke',
				'Skywalker',
				'farfaraway@galaxy.sp',
				'804 Bantha Dr.',
				'',
				'Mos Eisley',
				32,
				'US',
				'f0r3e',
				'',
				'',
				'',
				'',
				FALSE,
				'999999991'
				),
			1 => array(
				'Princess',
				'Leia',
				'buns@fcn.al',
				'1456 Valley Way Boulevard',
				'',
				'Alderaan',
				15,
				'US',
				'c1h2c',
				'',
				'',
				'',
				'',
				FALSE,
				'999999992'
				),
			2 => array(
				'Yoda',
				'I Am',
				'arrivenot@emailbad.fr',
				'4th Tree',
				'',
				'Marsh',
				22,
				'US',
				'l18n',
				'',
				'',
				'',
				'',
				FALSE,
				'999999993'
				),
		);

		//let's generate the attendee objects
		$attendees = array();
		$var_array = array('fname','lname','email','address','address2','city','staid','cntry','zip','phone','social','comments','notes','deleted','attid');

		EE_Registry::instance()->load_class( 'Attendee', array(), FALSE, TRUE, TRUE );
		foreach ( $dummy_attendees as $dummy ) {
			$att = array_combine( $var_array, $dummy );
			extract($att);
			$attendees[$attid] = EE_Attendee::new_instance(
				array(
					'ATT_fname' => $fname,
					'ATT_lname' => $lname,
					'ATT_address' => $address,
					'ATT_address2' => $address2,
					'ATT_city' => $city,
					'STA_ID' => $staid,
					'CNT_ISO' => $cntry,
					'ATT_zip' => $zip,
					'ATT_email' => $email,
					'ATT_phone' => $phone,
					'ATT_social' => $social,
					'ATT_comments' => $comments,
					'ATT_notes' => $notes,
					'ATT_ID' => $attid
				)
			);
		}

		return $attendees;
	}




	/**
	 * Return an array of dummy question objects indexed by answer id and dummy answer objects indexed by answer id.  This will be used in our dummy data setup
	 * @return array
	 */
	private function _get_some_q_and_as() {


		$quests_array = array(
			0 => array(
				555,
				__('What is your favorite planet?', 'event_espresso'),
				0
				),
			1 => array(
				556,
				__('What is your favorite food?', 'event_espresso'),
				0
				),
			2 => array(
				557,
				__('How many lightyears have you travelled', 'event_espresso'),
				0
				)
			);


		$ans_array = array(
			0 => array(
				999,
				555,
				'Tattoine'
				),
			1 => array(
				1000,
				555,
				'Alderaan'
				),
			2 => array(
				1001,
				555,
				'Dantooine'
				),
			3 => array(
				1002,
				556,
				'Fish Fingers'
				),
			4 => array(
				1003,
				556,
				'Sushi'
				),
			5 => array(
				1004,
				557,
				'Water'
				),
			6 => array(
				1005,
				557,
				'A lot',
				),
			7 => array(
				1006,
				557,
				"That's none of your business."
				),
			8 => array(
				1007,
				557,
				"People less travel me then."
				)
		);

		$qst_columns = array('QST_ID', 'QST_display_text', 'QST_system');
		$ans_columns = array('ANS_ID', 'QST_ID', 'ANS_value');

		EE_Registry::instance()->load_class( 'Question', array(), FALSE, TRUE, TRUE );
		EE_Registry::instance()->load_class( 'Answer', array(), FALSE, TRUE, TRUE );

		//first the questions
		foreach ( $quests_array as $qst ) {
			$qstobj = array_combine( $qst_columns, $qst );
			$qsts[$qst['QST_ID']] = EE_Question::new_instance($qstobj);
		}

		//now the answers (and we'll setup our arrays)
		$q_n_as = array();
		foreach ( $ans_array as $ans ) {
			$ansobj = array_combine( $ans_columns, $ans );
			$ansobj = EE_Answer::new_instance($ansobj);
			$q_n_as['answers'][$ans['ANS_ID']] = $ansobj;
			$q_n_as['questions'][$ans['ANS_ID']] = $qst[$ans['QST_ID']];
		}

		return $q_n_as;

	}





	/**
	 * Return an array of event objects from the database
	 *
	 * If event ids are not included then we'll just retrieve the first published event from the database.
	 * 
	 * @param  array  $event_ids if set, this will be an array of event ids to obtain events for.
	 * @return array    An array of event objects from the db.
	 */
	private function _get_some_events( $event_ids = array() ) {
		global $wpdb;

		//HEY, if we have an evt_id then we want to make sure we use that for the preview (because a specific event template is being viewed);
		$event_ids = isset( $_REQUEST['evt_id'] ) && !empty($_REQUEST['evt_id'] ) ? array( $_REQUEST['evt_id'] ) : array();

		$limit = !empty( $event_ids ) ? '' : apply_filters( 'FHEE_EE_Messages_Preview_incoming_data_get_some_events_limit', '0,1' );

		$where = !empty($event_ids) ? array('EVT_ID' => array( 'IN', $event_ids ) ) : array();

		$events = EE_Registry::instance()->load_model('Event')->get_all(array($where, 'limit' => $limit ) );
		
		return $events;
	}






	protected function _setup_data() {

		//need to figure out the running total for test purposes so... we're going to create a temp cart and add the tickets to it!
		$cart = EE_Cart::instance();

		//add tickets to cart
		foreach ( $this->tickets as $ticket ) {
			$cart->add_ticket_to_cart($ticket['ticket']);
		}

		$grand_total = EEH_Template::format_currency($cart->get_cart_grand_total(), true);


		//setup billing property
		//todo:  I'm only using this format for the array because its how the gateways currently setup this data.  I HATE IT and it needs fixed but I have no idea how many places in the code this data structure currently touches.  Once its fixed we'll have to fix it here and in the shortcode parsing where this particular property is accessed.  (See https://events.codebasehq.com/projects/event-espresso/tickets/2271) for related ticket.
		$this->billing = array(
			'first name' => 'Luke',
			'last name' => 'Skywalker',
			'email address' => 'farfaraway@galaxy.com',
			'address' => '804 Bantha Dr.',
			'city' => 'Mos Eisley',
			'state' => 'Section 7',
			'country' => 'Tatooine',
			'zip' => 'f0r3e',
			'ccv code' => 'xxx',
			'credit card #' => '999999xxxxxxxx',
			'expiry date' => '12 / 3000',
			'total_due' => $grand_total
			);



		//setup txn property
		$this->txn = EE_Transaction::new_instance(
			array(
				'TXN_timestamp' => current_time('mysql'), //unix timestamp
				'TXN_total' => $grand_total, //txn_total
				'TXN_paid' => $grand_total, //txn_paid
				'STS_ID' => 'PAP', //sts_id
				'TXN_session_data' => NULL, //dump of txn session object (we're just going to leave blank here)
				'TXN_hash_salt' => NULL, //hash salt blank as well
				'TXN_tax_data' => $cart->get_applied_taxes(),
				'TXN_ID' => 999999
			)
		);

		//setup reg_objects
		//note we're seting up a reg object for each attendee in each event but ALSO adding to the reg_object array.
		$this->reg_objs = array();
		foreach ( $this->_attendees as $key => $attendee ) {
			//note we need to setup reg_objects for each event this attendee belongs to
			foreach ( $attendee['line_ref'] as $evtid ) {
				$regid = 9999990;
				foreach ( $this->_events[$evtid]['tkt_objs'] as $ticket ) {
					$reg_array = array(
						'EVT_ID' => $evtid,
						'ATT_ID' => $attendee['att_obj']->ID(),
						'TXN_ID' => $this->txn->ID(),
						'TKT_ID' => $ticket->ID(),
						'STS_ID' => 'RAP',
						'REG_date' => current_time('mysql'),
						'REG_final_price' => $ticket->get('TKT_price'),
						'REG_session' => 'dummy_session_id',
						'REG_code' => $regid . '-dummy_generated_reg_code',
						'REG_url_link' => '#',
						'REG_count' => $key,
						'REG_group_size' => $this->_events[$evtid]['total_attendees'],
						'REG_att_is_going' => TRUE,
						'REG_ID' => $regid
						);
					$REG_OBJ =  EE_Registration::new_instance( $reg_array );
					$this->_attendees[$key]['reg_objs'][$evtid][] = $REG_OBJ;
					$this->_events[$evtid]['reg_objs'][] = $REG_OBJ;
					$this->reg_objs[] = $REG_OBJ;
					$regid++;
				}
			}
		}

		//events and attendees
		$this->events = $this->_events;
		$this->attendees = $this->_attendees;

		//setup primary attendee property
		$this->primary_attendee = array(
			'fname' => $this->_attendees[999999991]['att_obj']->fname(),
			'lname' => $this->_attendees[999999991]['att_obj']->lname(),
			'email' => $this->_attendees[999999991]['att_obj']->email()
			);

		//reg_info property
		//note this isn't referenced by any shortcode parsers so we'll ignore for now.
		$this->reg_info = array();


		//the below are just dummy items.
		$this->user_id = 1;
		$this->ip_address = '192.0.2.1';
		$this->user_agent = '';
		$this->init_access = current_time('mysql');
		$this->last_access = current_time('mysql');

	}

} //end EE_Messages_Preview_incoming_data class
