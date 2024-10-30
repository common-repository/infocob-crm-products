<?php if(\Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB::testConnection()): ?>
    <div class="notice notice-success">
        <p><?php echo esc_html_x( 'Database connection success !', "Notice success", "infocob-crm-products" ); ?></p>
    </div>
<?php else: ?>
    <div class="notice notice-error">
        <p><?php echo esc_html_x( 'Database connection failed !', "Notice error", "infocob-crm-products" ); ?></p>
    </div>
<?php endif; ?>
