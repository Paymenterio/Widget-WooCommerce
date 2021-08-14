<div class="wrap">
<img id="paymenterio-admin-logo" src="<?= plugins_url('paymenterio-payments/img/logo.svg') ?>" />
    <h1><?= __('Ustawienia Paymenterio', 'paymenterio-payments'); ?></h1>
    <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
	<table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="paymenterio_is_enabled"><?= __('Moduł aktywny', 'paymenterio-payments'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?= __('Włączony', 'paymenterio-payments'); ?></span></legend>
                        <label for="paymenterio_is_enabled">
                            <input type="checkbox" name="paymenterio_is_enabled" id="paymenterio_is_enabled" value="1" <?= $paymenterio_is_enabled == true ? "checked" : ""?>> <?= __('Włącz', 'paymenterio-payments'); ?></label>
                        <p class="description">
                            <?= __('Włączenie tej opcji umożliwia pełne działanie bramki płatniczej Paymenterio', 'paymenterio-payments'); ?>
                            <br/>
                            <strong><?= __('Odznaczenie tej opcji spowoduje wyłączenie bramki płatniczej.', 'paymenterio-payments'); ?></strong>
                        </p>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="paymenterio_shop_id"><?= __('Identyfikator punktu płatności', 'paymenterio-payments'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?= __('ID Sklepu', 'paymenterio-payments'); ?></span></legend>
                        <input class="input-text regular-input " type="text" name="paymenterio_shop_id" id="paymenterio_shop_id" value="<?= $paymenterio_shop_id ?>">
                        <p class="description"><?= __('Wprowadź Identyfikator lub Hash sklepu z panelu klienta.', 'paymenterio-payments'); ?></p>
                    </fieldset>
                </td>
            </tr>
			<tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="paymenterio_api_key"><?= __('Klucz API', 'paymenterio-payments'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?= __('Klucz API', 'paymenterio-payments'); ?></span></legend>
                        <input class="input-text regular-input larger-input" type="password" name="paymenterio_api_key" id="paymenterio_api_key" value="<?= $paymenterio_api_key ?>">
                        <p class="description"><?= __('Wprowadź klucz API, który umożliwi połączenie z bramką płatności.', 'paymenterio-payments'); ?></p>
                    </fieldset>
                </td>
            </tr>
		</tbody>
    </table>
    <?php
        wp_nonce_field('paymenterio_settings_save', 'paymenterio_settings_request');
        submit_button();
    ?>
    </form>
    <script>
    </script>
</div>