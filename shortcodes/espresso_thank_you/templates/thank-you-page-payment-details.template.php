<h2 class="section-heading display-box-heading">
	<?php _e('Payment Overview', 'event_espresso'); ?>
</h2>

<div id="espresso-thank-you-page-payment-details-dv">
<?php if ( ! empty( $payments )){	 ?>
	<table class="ee-table">
		<thead>
			<tr>
				<th width="35%" class="jst-left">
					<?php _e('Payment Date','event_espresso')?>
				</th>
				<th width="20%" class="jst-left">
					<?php _e('Type','event_espresso');?>
				</th>
				<th width="20%" class="jst-rght">
					<?php _e('Amount','event_espresso');?>
				</th>
				<th width="25%" class="jst-rght">
					<?php _e('Status','event_espresso');?>
				</th>
			</tr>
		</thead>
		<tbody>
	<?php foreach ( $payments as $payment ) { echo $payment; } ?>
		</tbody>
	</table>
<?php
	} else {
		
		if ( $transaction->total() ){ 
			echo apply_filters( 
				'FHEE__payment_overview_template__no_payments_made',
				sprintf ( 
					__('%sNo payments towards this transaction have been received.%s', 'event_espresso' ),
					'<p class="important-notice">',
					'</p>'
				)
			); 
		} else {
			echo apply_filters( 
				'FHEE__payment_overview_template__no_payment_required',
				sprintf ( 
					__('%sNo payment is required for this transaction.%s', 'event_espresso' ),
					'<p>',
					'</p>'
				)
			); 
		 }
			
	}
	if ( ! empty( $gateway_content ) && ! $transaction->is_completed() ){
		echo $gateway_content;
	 }	
?>
	<br/>	
</div>