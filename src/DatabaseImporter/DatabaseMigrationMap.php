<?php
namespace Iannazzi\Generators\DatabaseImporter;


use Crypt;
//this is the file used to map the migration and the import
class DatabaseMigrationMap
{

    public static function getTenantTablesFromBluehost()
    {
        return array(
            'pre_table_insert' => ['BluehostMigrationMap', 'preTableInsertFunction'],
            'preFieldRenameFunction' => ['BluehostMigrationMap', 'preFieldRenameFunction'],
            'tables' => [

                'pos_accounts' =>
                    array(
                        'new_name' => 'accounts',
                        'type' => 'regular',
                        'rename_columns' => ['pos_account_id' => 'id'],
                        'modify_data_function' => function($data){self::recryptAccountNumber($data);return $data;},
                    ),
                'pos_accounts_to_chart_of_accounts' =>
                    array(
                        'new_name' => 'account_chart_of_account',
                        'type' => 'pivot',
                    ),
                'pos_addresses' =>
                    array(
                        'new_name' => 'addresses',
                        'type' => 'regular',
                        'rename_columns' => ['pos_address_id' => 'id'],

                    ),
                'pos_categories' =>
                    array(
                        'new_name' => 'product_categories',
                        'type' => 'regular',
                        'rename_columns' => ['pos_category_id' => 'id'],

                    ),

                'pos_chart_of_accounts' =>
                    array(
                        'new_name' => 'chart_of_accounts',
                        'type' => 'regular',
                        'rename_columns' => ['pos_chart_of_accounts_id' => 'id'],

                    ),
                'pos_chart_of_accounts_required' =>
                    array(
                        'new_name' => 'required_chart_of_accounts',
                        'type' => 'regular',
                        'rename_columns' => ['pos_chart_of_accounts_required_id' => 'id'],

                    ),
                'pos_customer_addresses' =>
                    array(
                        'new_name' => 'address_customer',
                        'type' => 'pivot',
                    ),
                'pos_customer_emails' =>
                    array(
                        'new_name' => 'customer_email',
                        'type' => 'pivot',
                    ),

                'pos_customer_payments' =>
                    array(
                        'new_name' => 'customer_payments',
                        'type' => 'regular',
                        'rename_columns' => ['pos_customer_payment_id' => 'id'],

                    ),
                'pos_customers' =>
                    array(
                        'new_name' => 'customers',
                        'type' => 'regular',
                        'rename_columns' => ['pos_customer_id' => 'id'],

                    ),
                'pos_discount_product_lookup' =>
                    array(
                        'new_name' => 'discount_product',
                        'rename_columns' => ['pos_category_id' => 'product_category_id'],
                        'type' => 'pivot',
                    ),
                'pos_discounts' =>
                    array(
                        'new_name' => 'discounts',
                        'type' => 'regular',
                        'rename_columns' => ['pos_discount_id' => 'id'],

                    ),
                'pos_documents' =>
                    array(
                        'new_name' => 'documents',
                        'type' => 'regular',
                        'rename_columns' => ['pos_document_id' => 'id'],

                    ),
                'pos_documents_backup' =>
                    array(
                        'new_name' => 'documents_backup',
                        'type' => 'regular',
                        'rename_columns' => ['pos_document_backup_id' => 'id'],

                    ),
                'pos_general_journal' =>
                    array(
                        'new_name' => 'general_invoices',
                        'type' => 'regular',
                        'rename_columns' => ['pos_general_journal_id' => 'id'],

                    ),

                'pos_inventory_complete_dates' =>
                    array(
                        'new_name' => 'inventory_complete_dates',
                        'type' => 'regular',
                        'rename_columns' => ['pos_inventory_complete_date_id' => 'id'],

                    ),
                'pos_inventory_event' =>
                    array(
                        'new_name' => 'inventory_events',
                        'type' => 'regular',
                        'rename_columns' => ['pos_inventory_event_id' => 'id'],

                    ),
                'pos_inventory_event_contents' =>
                    array(
                        'new_name' => 'inventory_event_contents',
                        'type' => 'regular',
                        'rename_columns' => ['pos_inventory_content_id' => 'id'],

                    ),
                'pos_inventory_log' =>
                    array(
                        'new_name' => 'inventory_logs',
                        'type' => 'regular',
                        'rename_columns' => ['pos_inventory_log_id' => 'id'],

                    ),
                'pos_invoice_to_credit_memo' =>
                    array(
                        'new_name' => 'credit_memo_invoice',
                        'type' => 'pivot',
                    ),
                'pos_invoice_to_payment' =>
                    array(
                        'new_name' => 'invoice_payment',
                        'type' => 'pivot',
                        'rename_columns' => ['pos_journal_id' => 'invoice_id',
                        'pos_payments_journal_id' => 'payment_id'],

                    ),
                'pos_journal_to_coa_link' =>
                    array(
                        'new_name' => 'journal_to_coa_link',
                        'type' => 'regular',
                        'rename_columns' => ['pos_journal_to_coa_link_id' => 'id'],

                    ),
                'pos_location_groups' =>
                    array(
                        'new_name' => 'location_groups',
                        'type' => 'regular',
                        'rename_columns' => ['pos_location_group_id' => 'id'],

                    ),
                'pos_locations' =>
                    array(
                        'new_name' => 'locations',
                        'type' => 'regular',
                        'rename_columns' => ['pos_location_id' => 'id'],

                    ),
                'pos_manufacturer_accounts' =>
                    array(
                        'new_name' => 'account_manufacturer',
                        'type' => 'pivot',
                    ),
                'pos_manufacturer_brand_sizes' =>
                    array(
                        'new_name' => 'brand_sizes',
                        'type' => 'regular',
                        'rename_columns' => ['pos_manufacturer_brand_size_id' => 'id'],

                    ),
                'pos_manufacturer_brands' =>
                    array(
                        'new_name' => 'brands',
                        'type' => 'regular',
                        'rename_columns' => ['pos_manufacturer_brand_id' => 'id'],

                    ),

                'pos_manufacturers' =>
                    array(
                        'new_name' => 'manufacturers',
                        'type' => 'regular',
                        'rename_columns' => ['pos_manufacturer_id' => 'id'],

                    ),
                'pos_messages' =>
                    array(
                        'new_name' => 'messages',
                        'type' => 'regular',
                        'rename_columns' => ['pos_message_id' => 'id'],

                    ),
                'pos_payment_gateways' =>
                    array(
                        'new_name' => 'payment_gateways',
                        'type' => 'regular',
                        'rename_columns' => ['pos_payment_gateway_id' => 'id'],

                    ),
                'pos_payments_journal' =>
                    array(
                        'new_name' => 'payments',
                        'type' => 'regular',
                        'rename_columns' => ['pos_payments_journal_id' => 'id'],

                    ),
                'pos_printers' =>
                    array(
                        'new_name' => 'printers',
                        'type' => 'regular',
                        'rename_columns' => ['pos_printer_id' => 'id'],

                    ),
                'pos_product_attributes' =>
                    array(
                        'new_name' => 'product_attributes',
                        'type' => 'regular',
                        'rename_columns' => ['pos_product_attribute_id' => 'id'],

                    ),
                'pos_product_image_lookup' =>
                    array(
                        'new_name' => 'image_product',
                        'type' => 'pivot',
                        'rename_columns' => ['pos_product_sub_id' => 'product_sku_id'],

                    ),
                'pos_product_images' =>
                    array(
                        'new_name' => 'product_images',
                        'make_factory' => 'false',

                        'type' => 'regular',
                        'rename_columns' => ['pos_product_image_id' => 'id'],

                    ),
                'pos_product_options' =>
                    array(
                        'new_name' => 'product_options',
                        'make_factory' => 'false',

                        'type' => 'regular',
                        'rename_columns' => ['pos_product_option_id' => 'id',
                                            'option_name' => 'value'],

                    ),
               /* 'pos_product_secondary_categories' =>
                    array(
                        'new_name' => 'category_product',
                        'type' => 'pivot',
                    ),*/
                'pos_product_sub_id_options' =>
                    array(
                        'new_name' => 'product_option_product_sku',
                        'type' => 'pivot',
                        'rename_columns' => ['pos_product_sub_id' => 'product_sku_id'],


                    ),
                'pos_product_sub_sale_price' =>
                    array(
                        'new_name' => 'product_sku_sale_prices',
                        'type' => 'regular',
                        'rename_columns' => ['pos_product_sub_id' => 'product_sku_id'],

                    ),
                'pos_products' =>
                    array(
                        'new_name' => 'products',
                        'type' => 'regular',
                        'drop_columns' => ['pos_manufacturer_id', 'product_id', 'is_taxable','tax_class_id'],
                        'rename_columns' => ['pos_product_id' => 'id', 'pos_category_id'=>'product_category_id'],
                    ),
                'pos_products_attributes' =>
                    array(
                        'new_name' => 'product_attributes',
                        'type' => 'regular',
                        'rename_columns' => ['pos_products_attribute_id' => 'id',
                            'attribute_name' => 'name'],
                    ),
                'pos_products_sub_id' =>
                    array(
                        'new_name' => 'product_skus',
                        'type' => 'regular',
                        'rename_columns' => ['pos_products_sub_id' => 'id',
                                            'product_upc' => 'upc'],


                    ),
                'pos_promotion_buy' =>
                    array(
                        'new_name' => 'promotion_buys',
                        'type' => 'regular',

                    ),
                'pos_promotion_lookup' =>
                    array(
                        'new_name' => 'promotion_lookup',
                        'type' => 'regular',
                         'rename_columns' => ['pos_category_id'=>'product_category_id'],

                    ),
                'pos_promotions' =>
                    array(
                        'new_name' => 'promotions',
                        'type' => 'regular',
                        'rename_columns' => ['pos_promotion_id' => 'id'],

                    ),
                'pos_purchase_order_categories' =>
                    array(
                        'new_name' => 'po_categories',
                        'type' => 'regular',
                        'rename_columns' => ['pos_purchase_order_category_id' => 'id'],

                    ),
                'pos_purchase_order_contents' =>
                    array(
                        'new_name' => 'po_contents',
                        'type' => 'regular',
                        'rename_columns' => ['pos_purchase_order_id' => 'po_id',
                            'pos_purchase_order_content_id' => 'id','pos_category_id'=>'product_category_id',
                                                'pos_product_sub_id' => 'pos_product_sku_id'],

                    ),
                'pos_purchase_order_receive_contents' =>
                    array(
                        'new_name' => 'po_receive_contents',
                        'type' => 'regular',
                        'rename_columns' => ['pos_purchase_order_receive_event_id' => 'po_receive_event_id',
                                'pos_purchase_order_receive_content_id' => 'id',
                        'pos_purchase_order_content_id' => 'po_content_id'],

                    ),
                'pos_purchase_order_receive_event' =>
                    array(
                        'new_name' => 'po_receive_events',
                        'type' => 'regular',
                        'rename_columns' => ['pos_purchase_order_receive_event_id' => 'id',
                        'pos_purchase_order_id'=>'po_id'],

                    ),
                'pos_purchase_orders' =>
                    array(
                        'new_name' => 'pos',
                        'type' => 'regular',
                        'rename_columns' => ['pos_purchase_order_id' => 'id',
                                            'purchase_order_number'=>'po_number',
                            'manufacturer_purchase_order_number' => 'manufacturer_po_number',
                            'purchase_order_type'=>'po_type',
                            'purchase_order_status'=>'po_status'],

                    ),
                'pos_purchases_credit_memo_to_po' =>
                    array(
                        'new_name' => 'purchases_credit_memo_po',
                        'type' => 'pivot',
                        'rename_columns' => ['pos_purchase_order_id'=>'po_id'],

                    ),
                'pos_purchases_invoice_to_po' =>
                    array(
                        'new_name' => 'po_purchase_invoice',
                        'type' => 'pivot',
                        'rename_columns' => ['pos_purchase_order_id'=>'po_id'],

                    ),

                'pos_purchases_journal' =>
                    array(
                        'new_name' => 'purchase_invoices',
                        'type' => 'regular',
                        'rename_columns' => ['pos_purchases_journal_id' => 'id'],

                    ),
                'pos_sales_invoice' =>
                    array(
                        'new_name' => 'sales_invoices',
                        'type' => 'regular',
                        'rename_columns' => ['pos_sales_invoice_id' => 'id'],

                    ),
                'pos_sales_invoice_contents' =>
                    array(
                        'new_name' => 'sales_invoice_contents',
                        'type' => 'regular',
                        'rename_columns' => ['pos_sales_invoice_content_id' => 'id'],

                    ),
                'pos_sales_invoice_promotions' =>
                    array(
                        'new_name' => 'promotion_sales_invoice',
                        'type' => 'pivot',

                    ),
                'pos_sales_invoice_to_payment' =>
                    array(
                        'new_name' => 'payment_sales_invoice',
                        'type' => 'pivot',
                    ),

                'pos_services' =>
                    array(
                        'new_name' => 'services',
                        'type' => 'regular',
                        'rename_columns' => ['pos_service_id' => 'id'],

                    ),
                'pos_settings' =>
                    array(
                        'new_name' => 'settings',
                        'type' => 'regular',
                    ),
                'pos_shipping_options' =>
                    array(
                        'new_name' => 'shipping_options',
                        'type' => 'regular',
                        'rename_columns' => ['pos_shipping_option_id' => 'id'],

                    ),
                'pos_store_credit' =>
                    array(
                        'new_name' => 'store_credit_cards',
                        'type' => 'regular',
                        'rename_columns' => ['pos_store_credit_id' => 'id'],

                    ),
                'pos_store_credit_card_numbers' =>
                    array(
                        'new_name' => 'store_credit_card_numbers',
                        'type' => 'regular',
                        'rename_columns' => ['pos_store_credit_card_number_id' => 'id'],

                    ),
                'pos_stores' =>
                    array(
                        'new_name' => 'stores',
                        'type' => 'regular',
                        'rename_columns' => ['pos_store_id' => 'id'],

                    ),
                'pos_terminals' =>
                    array(
                        'new_name' => 'terminals',
                        'type' => 'regular',
                        'drop_columns' => [],
                        'rename_columns' => ['pos_terminal_id' => 'id',],
                    ),
                'pos_user_binder_access' =>
                    array(
                        'new_name' => 'binder_user',
                        'type' => 'pivot',
                    ),
                'pos_user_groups' =>
                    array(
                        'new_name' => 'groups',
                        'type' => 'regular',
                        'rename_columns' => ['pos_user_group_id' => 'id',],


                    ),
                'pos_user_log' =>
                    array(
                        'new_name' => 'user_hits',
                        'make_factory' => 'false',

                        'import_data' => false,
                        'type' => 'regular',
                        'rename_columns' => ['pos_user_log_id' => 'id',],


                    ),
                'pos_users' =>
                    array(
                        'new_name' => 'users',
                        'type' => 'regular',
                        'make_model' => 'false',
                        'drop_columns' => ['database_access',
                            'default_room',
                            'default_start_page',
                            'default_store_id',
                            'created_date',
                            'session_id',
                            'session_date',
                            'block_date',
                            'login_errors',
                            'notifications',
                            'last_access',
                            'last_update',
                            'admin',
                            'activation_code',
                            'default_view_date_range_days',
                            'ip_address_restrictions',
                            'last_room',
                            'level',
                            'timeout_minutes',
                            'title',
                            'role',
                            'rights',
                            'relogin_on_ip_address_change',
                            'relogin_on_browser_change',
                            'max_connections',
                            'locked',
                            'activation_code',
                            'default_view_date_range_days',],
                        'rename_columns' => ['pos_user_id' => 'id', 'login' => 'username'],
                        'modify_data_function' => function($data){self::recryptUserPassword($data);return $data;},
                        'generate_data_function' => function(){self::generatePasscode();},

                    ),
                'pos_users_in_groups' =>
                    array(
                        'new_name' => 'group_user',
                        'type' => 'pivot',
                    ),
                'pos_users_logged_in' =>
                    array(
                        'new_name' => 'logged_in_users',
                        'type' => 'regular',
                    ),]
        );
    }

    public static function getCraigloriousTablesFromBluehost()
    {
        //call_user_func(array('MyClass', 'myCallbackMethod'));
        return array(
            'pre_table_insert' => ['BluehostMigrationMap', 'preTableInsertFunction'],
            'preFieldRenameFunction' => ['BluehostMigrationMap', 'preFieldRenameFunction'],

            'tables' => [
                'pos_account_type' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'account_types',
                        'type' => 'regular',
                        'rename_columns' => ['pos_account_type_id' => 'id'],

                    ),
                'pos_binders' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'binders',
                        'type' => 'regular',
                        'rename_columns' => ['pos_binder_id' => 'id'],

                    ),
                'pos_chart_of_account_types' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'chart_of_account_types',
                        'type' => 'regular',
                        'rename_columns' => ['pos_chart_of_account_type_id' => 'id'],

                    ),
                'pos_chart_of_accounts_required' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'chart_of_accounts_required',
                        'type' => 'regular',
                        'rename_columns' => ['pos_chart_of_accounts_required_id' => 'id'],

                    ),
                'pos_counties' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'counties',
                        'type' => 'regular',
                        'rename_columns' => ['pos_county_id' => 'id'],

                    ),
                'pos_currencies' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'currencies',
                        'type' => 'regular',
                        'rename_columns' => ['pos_currency' => 'id'],

                    ),
                'pos_customer_payment_methods' =>
                    array(
                        'new_name' => 'customer_payment_methods',
                        'type' => 'regular',
                        'rename_columns' => ['pos_customer_payment_method_id' => 'id'],

                    ),
                'pos_manufacturer_upc' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'manufacturer_upcs',
                        'type' => 'regular',
                        'import_data' => false,
                        'rename_columns' => ['pos_manufacturer_upc_id' => 'id'],

                    ),
                'pos_sales_tax_categories' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'sales_tax_categories',
                        'type' => 'regular',
                        'rename_columns' => ['pos_sales_tax_category_id' => 'id'],

                    ),

                'pos_sales_tax_rates' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'sales_tax_rates',
                        'type' => 'regular',
                        'rename_columns' => ['pos_sales_tax_rate_id' => 'id'],

                    ),

                'pos_states' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'states',
                        'type' => 'regular',
                        'rename_columns' => ['pos_state_id' => 'id'],

                    ),
                'pos_tax_jurisdictions' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'tax_jurisdictions',
                        'type' => 'regular',
                        'rename_columns' => ['pos_tax_jurisdiction_id' => 'id'],

                    ),

                'pos_zip_codes' =>
                    array(
                        'make_factory' => 'false',

                        'new_name' => 'zip_codes',
                        'type' => 'regular',
                        'rename_columns' => ['pos_zip_code_id' => 'id'],

                    ),]
        );
    }





    public static function recryptAccountNumber($data)
    {
        for($i=0;$i<sizeof($data);$i++)
        {
            $account_number = $data[ $i ]['account_number'];
            $account_number = craigsDecryption($data[ $i ]['account_number']);
            $data[$i]['account_number']  = Crypt::encrypt($account_number);
            //var_dump(Crypt::decrypt($data[$i]['account_number']));
        }
        return $data;
    }
    public static function recryptUserPassword($data)
    {
        for($i=0;$i<sizeof($data);$i++)
        {
            $password = $data[ $i ]['password'];
            $password = craigsDecryption($data[ $i ]['password']);
            $data[$i]['password']  = Crypt::encrypt($password);
            //var_dump(Crypt::decrypt($data[$i]['account_number']));
        }
        return $data;
    }
    public static function generatePasscode()
    {
        //dd('generate passcode ');
        //i know this is for users table
    }
    public static function craigsDecryption($string)
    {

        $key =env('POS_KEY');
        if($string == '')
        {
            return '';
        }
        else
        {
            //return cmdecrypt($string, $key);
            return bcryptHashEncryption($key, $string, 'decrypt');
        }
    }




}