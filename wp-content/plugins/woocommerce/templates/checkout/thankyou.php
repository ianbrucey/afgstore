<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous"></script>
<div class="woocommerce-order">

	<?php
    $paymentSuccessful = false;
	if ( $order ) :

        $item_sku = null;

        foreach ($order->get_items() as $item) {
            $product = wc_get_product($item->get_product_id());
            $item_sku = $product->get_sku();
            break;
        }

        // send ajax request to confirm user
        $confirm_url = sprintf("%s/%s", SLOT_CONFIRM, $item_sku);
        $return_url = sprintf("%s/%s", ROUND_TRIP, $item_sku);
        $access = TOKEN_FROM_WP;
        $email = $order->get_billing_email();
        $total = (int) $order->get_total();
        // depending on ajax response, we will

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>
        <?php $paymentSuccessful = true; ?>
			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thanks! You will be redirected back shortly. If you are not redirected, please click the button below', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

			<a href="<?php echo sprintf("%s?a=%s&t=%s", $confirm_url, $access, $total) ?>"> Return back to your game </a>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>
<?php
if($paymentSuccessful) {
    ?>

    <script>
        let goBack = "<?php echo sprintf("%s?a=%s&t=%s", $confirm_url, $access, $total) ?>";
        // swal("Thanks! You will be redirected back shortly. If you are not redirected, please click the button below" + "<br><a href='"+goBack+"'></a>"); // we need a button
        swal({
            title: "Thanks!",
            text: "You will be redirected back shortly. If you are not redirected, please click the return button on the page",
            type: "success"
        }).then(function () {
            window.location.href = goBack;
        });
        // run ajax confirm
        setTimeout(function () {
            window.location.href = goBack;
            console.log("We outchea");
        },2000);

    </script>
    <?php
}
?>