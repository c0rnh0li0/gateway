<?php

namespace AdminModule;

/**
 * Admin homepage.
 * 
 * @author Lukas Bruha
 */
class DefaultPresenter extends BasePresenter {
 
    
    /*public function actionDefault() {       
        $str = '[{"state":"new","status":"pending","coupon_code":null,"protect_code":"e2158d","shipping_description":"Shipping Option - ParcelShipping","is_virtual":"0","store_id":"2","customer_id":"17","base_discount_amount":"0.0000","base_discount_canceled":null,"base_discount_invoiced":null,"base_discount_refunded":null,"base_grand_total":"22.9500","base_shipping_amount":"5.0000","base_shipping_canceled":null,"base_shipping_invoiced":null,"base_shipping_refunded":null,"base_shipping_tax_amount":"1.0000","base_shipping_tax_refunded":null,"base_subtotal":"14.1250","base_subtotal_canceled":null,"base_subtotal_invoiced":null,"base_subtotal_refunded":null,"base_tax_amount":"3.8250","base_tax_canceled":null,"base_tax_invoiced":null,"base_tax_refunded":null,"base_to_global_rate":"1.0000","base_to_order_rate":"1.0000","base_total_canceled":null,"base_total_invoiced":null,"base_total_invoiced_cost":null,"base_total_offline_refunded":null,"base_total_online_refunded":null,"base_total_paid":null,"base_total_qty_ordered":null,"base_total_refunded":null,"discount_amount":"0.0000","discount_canceled":null,"discount_invoiced":null,"discount_refunded":null,"grand_total":"22.9500","shipping_amount":"5.0000","shipping_canceled":null,"shipping_invoiced":null,"shipping_refunded":null,"shipping_tax_amount":"1.0000","shipping_tax_refunded":null,"store_to_base_rate":"1.0000","store_to_order_rate":"1.0000","subtotal":"14.1250","subtotal_canceled":null,"subtotal_invoiced":null,"subtotal_refunded":null,"tax_amount":"3.8250","tax_canceled":null,"tax_invoiced":null,"tax_refunded":null,"total_canceled":null,"total_invoiced":null,"total_offline_refunded":null,"total_online_refunded":null,"total_paid":null,"total_qty_ordered":"1.0000","total_refunded":null,"can_ship_partially":null,"can_ship_partially_item":null,"customer_is_guest":"0","customer_note_notify":"1","billing_address_id":"57","customer_group_id":"6","edit_increment":null,"email_sent":"1","forced_shipment_with_invoice":null,"gift_message_id":null,"payment_auth_expiration":null,"paypal_ipn_customer_notified":null,"quote_address_id":null,"quote_id":"85","shipping_address_id":"58","adjustment_negative":null,"adjustment_positive":null,"base_adjustment_negative":null,"base_adjustment_positive":null,"base_shipping_discount_amount":"0.0000","base_subtotal_incl_tax":"16.9500","base_total_due":null,"payment_authorization_amount":null,"shipping_discount_amount":"0.0000","subtotal_incl_tax":"16.9500","total_due":null,"weight":"0.4800","customer_dob":"1912-12-12 00:00:00","increment_id":"200000024","applied_rule_ids":null,"base_currency_code":"EUR","customer_email":"andreas.bruendl@etron.at","customer_firstname":"testimit\u00fc_umlaut","customer_lastname":"\u00e4testfrau\u00f6","customer_middlename":null,"customer_prefix":"Frau","customer_suffix":null,"customer_taxvat":null,"discount_description":null,"ext_customer_id":null,"ext_order_id":null,"global_currency_code":"EUR","hold_before_state":null,"hold_before_status":null,"order_currency_code":"EUR","original_increment_id":null,"relation_child_id":null,"relation_child_real_id":null,"relation_parent_id":null,"relation_parent_real_id":null,"remote_ip":"178.190.54.65","shipping_method":"productmatrix_ParcelShipping","store_currency_code":"EUR","store_name":"Main Website\nMain Website Store\nDeutsch","x_forwarded_for":null,"customer_note":null,"created_at":"2012-11-21 17:49:30","updated_at":"2012-11-21 17:49:34","total_item_count":"1","customer_gender":null,"base_custbalance_amount":null,"custbalance_amount":null,"is_multi_payment":null,"hidden_tax_amount":"0.0000","base_hidden_tax_amount":"0.0000","shipping_hidden_tax_amount":"0.0000","base_shipping_hidden_tax_amnt":"0.0000","hidden_tax_invoiced":null,"base_hidden_tax_invoiced":null,"hidden_tax_refunded":null,"base_hidden_tax_refunded":null,"shipping_incl_tax":"6.0000","base_shipping_incl_tax":"6.0000","coupon_rule_name":null,"base_paypal_surcharge":null,"paypal_surcharge":null,"payment_authorization_expiration":null,"forced_do_shipment_with_invoice":null,"base_shipping_hidden_tax_amount":"0.0000","order_id":"29","shipping_address":{"parent_id":"29","customer_address_id":"18","quote_address_id":null,"region_id":null,"customer_id":"17","fax":null,"region":null,"postcode":"23455","lastname":"testfrau","street":"testring","city":"Madrid","email":"andreas.bruendl@etron.at","telephone":"23450005","country_id":"AT","firstname":"testimit\u00fc_umlaut \u00e4testfrau\u00f6","address_type":"shipping","prefix":"Frau","middlename":null,"suffix":null,"company":null,"vat_id":null,"vat_is_valid":null,"vat_request_id":null,"vat_request_date":null,"vat_request_success":null,"address_id":"58"},"billing_address":{"parent_id":"29","customer_address_id":"18","quote_address_id":null,"region_id":null,"customer_id":"17","fax":null,"region":null,"postcode":"23455","lastname":"testfrau","street":"testring","city":"Madrid","email":"andreas.bruendl@etron.at","telephone":"23450005","country_id":"AT","firstname":"testimit\u00fc_umlaut \u00e4testfrau\u00f6","address_type":"billing","prefix":"Frau","middlename":null,"suffix":null,"company":null,"vat_id":null,"vat_is_valid":null,"vat_request_id":null,"vat_request_date":null,"vat_request_success":null,"address_id":"57"},"items":[{"item_id":"64","order_id":"29","parent_item_id":null,"quote_item_id":"160","store_id":"2","created_at":"2012-11-21 17:49:30","updated_at":"2012-11-21 17:49:30","product_id":"4518","product_type":"simple","product_options":"a:1:{s:15:\"info_buyRequest\";a:4:{s:4:\"uenc\";s:120:\"aHR0cDovL21hZ2VudG8ucDE2ODQxNS53ZWJzcGFjZWNvbmZpZy5kZS90YWJsZS10ZW5uaXMtcmFja2V0LXNldC12M3RlYy1jaGFtcC0xMDAwNjg4Lmh0bWw,\";s:7:\"product\";s:4:\"4518\";s:15:\"related_product\";s:0:\"\";s:3:\"qty\";s:1:\"1\";}}","weight":"0.4800","is_virtual":"0","sku":"2110000132972","name":"Tischtennis Schl\u00e4gerset V3Tec Champ 1000688","description":null,"applied_rule_ids":null,"additional_data":null,"free_shipping":"0","is_qty_decimal":"0","no_discount":"0","qty_backordered":null,"qty_canceled":"0.0000","qty_invoiced":"0.0000","qty_ordered":"1.0000","qty_refunded":"0.0000","qty_shipped":"0.0000","base_cost":null,"price":"14.1250","base_price":"14.1250","original_price":"14.1250","base_original_price":"14.1250","tax_percent":"20.0000","tax_amount":"2.8250","base_tax_amount":"2.8250","tax_invoiced":"0.0000","base_tax_invoiced":"0.0000","discount_percent":"0.0000","discount_amount":"0.0000","base_discount_amount":"0.0000","discount_invoiced":"0.0000","base_discount_invoiced":"0.0000","amount_refunded":"0.0000","base_amount_refunded":"0.0000","row_total":"14.1250","base_row_total":"14.1250","row_invoiced":"0.0000","base_row_invoiced":"0.0000","row_weight":"0.4800","gift_message_id":null,"gift_message_available":"0","base_tax_before_discount":null,"tax_before_discount":null,"weee_tax_applied":"a:0:{}","weee_tax_applied_amount":"0.0000","weee_tax_applied_row_amount":"0.0000","base_weee_tax_applied_amount":"0.0000","base_weee_tax_applied_row_amnt":"0.0000","base_weee_tax_applied_row_amount":"0.0000","weee_tax_disposition":"0.0000","weee_tax_row_disposition":"0.0000","base_weee_tax_disposition":"0.0000","base_weee_tax_row_disposition":"0.0000","ext_order_item_id":null,"locked_do_invoice":null,"locked_do_ship":null,"hidden_tax_amount":null,"base_hidden_tax_amount":null,"hidden_tax_invoiced":null,"base_hidden_tax_invoiced":null,"hidden_tax_refunded":null,"base_hidden_tax_refunded":null,"is_nominal":"0","price_incl_tax":"16.9500","base_price_incl_tax":"16.9500","row_total_incl_tax":"16.9500","base_row_total_incl_tax":"16.9500","tax_canceled":null,"hidden_tax_canceled":null,"tax_refunded":null,"base_tax_refunded":null,"discount_refunded":null,"base_discount_refunded":null,"paypal_surcharge":null}],"payment":{"parent_id":"29","base_shipping_captured":null,"shipping_captured":null,"amount_refunded":null,"base_amount_paid":null,"amount_canceled":null,"base_amount_authorized":null,"base_amount_paid_online":null,"base_amount_refunded_online":null,"base_shipping_amount":"5.0000","shipping_amount":"5.0000","amount_paid":null,"amount_authorized":null,"base_amount_ordered":"22.9500","base_shipping_refunded":null,"shipping_refunded":null,"base_amount_refunded":null,"amount_ordered":"22.9500","base_amount_canceled":null,"ideal_transaction_checked":null,"quote_payment_id":null,"additional_data":null,"cc_exp_month":"0","cc_ss_start_year":"0","echeck_bank_name":null,"method":"bankpayment","cc_debug_request_body":null,"cc_secure_verify":null,"cybersource_token":null,"ideal_issuer_title":null,"protection_eligibility":null,"cc_approval":null,"cc_last4":null,"cc_status_description":null,"echeck_type":null,"paybox_question_number":null,"cc_debug_response_serialized":null,"cc_ss_start_month":"0","echeck_account_type":null,"last_trans_id":null,"cc_cid_status":null,"cc_owner":null,"cc_type":null,"ideal_issuer_id":null,"po_number":null,"cc_exp_year":"0","cc_status":null,"echeck_routing_number":null,"account_status":null,"anet_trans_method":null,"cc_debug_response_body":null,"cc_ss_issue":null,"echeck_account_name":null,"cc_avs_status":null,"cc_number_enc":null,"cc_trans_id":null,"flo2cash_account_id":null,"paybox_request_number":null,"address_status":null,"additional_information":[],"payment_id":"29"},"status_history":[{"parent_id":"29","is_customer_notified":"1","is_visible_on_front":"0","comment":null,"status":"pending","created_at":"2012-11-21 17:49:34","entity_name":"order","store_id":"2"}]}]';
        
        dump(\Yourface\Utils\Helpers::json_unescaped_unicode($str));
        
        
        exit;
    }*/
    
    /**
     * Global log load.
     * 
     * @param string $realtpath
     */
    public function actionDetail($realpath) {
        // load file log
        $file = false;
        if (file_exists($realpath)) {
            $file = @file_get_contents($realpath);
        }

        $this->template->logFile = $file;
    }

    ////////////////
    // COMPONENTS //
    ////////////////
    /**
     * Logs overview.
     * 
     * @param string $name
     */
    public function createComponentLogsGrid($name) {
        $logDir = $this->logger->getLogDir();

        $logs = array();

        $dir = \Nette\Utils\Finder::findFiles('[0-9\-]*.log');
        $processed = '';
        
        // decide what logs to load into grid
        if ($name == 'logsGrid') {
            // global logs
            $dir->date('>', '- 2 days')->from($logDir)->exclude('gateway');
        } else {
            // schedule reports
            $logDir .= '/gateway';
                    
            $processed =$this->getService('database')
                            ->table('gw_report')
                            ->select('log')
                            ->fetchPairs('log');

            $processed = implode(',', array_keys($processed));
            //$processed = array_keys($processed);
            
            $dir->date('>', '- 1 days')->from($logDir);
        }
        
        // grid datasource prepare
        foreach ($dir as $key => $log) {
            if (!strpos($processed, $log->getBasename())) {
                $logs[$log->getRealPath()] = array(
                    'name' => $log->getBasename(),
                    'size' => \Nette\Templating\Helpers::bytes($log->getSize()),
                    'id' => $log->getRealPath(),
                    'updated_at' => date("d.m.Y, H:i:s", $log->getMTime()),
                );
            }
        }

        // global logs sorting
        if ($name == 'logsGrid') {
            arsort($logs);
        } else {
            // reports sorting
            uasort($logs, function($a, $b) {
                if ($a['updated_at'] == $b['updated_at']) {
                    return 0;
                }
                
                return ($a['updated_at'] > $b['updated_at']) ? -1 : 1;
            });
        }

        // grid itself
        return new \AdminModule\Component\LogsGrid($logs, true);
    }

    /**
     * Reports log.
     * 
     */
    public function createComponentConnectionLogsGrid() {
        return $this->createComponentLogsGrid('connectionLogsGrid');
    }

    /**
     * Loads currently running schedules list.
     * 
     * @return \AdminModule\Component\ScheduleGrid
     */
    public function createComponentGrid() {
        $data = $this->getService('database')
                ->table('gw_schedule')
                ->select('gw_schedule.*, 
                                            gw_connection.name, 
                                            gw_connection.id AS connection_id, 
                                            gw_source:gw_handler.id AS handler_id,
                                            gw_source:gw_handler.type,
                                            gw_source:gw_handler.description AS handler_description')
                ->where('gw_schedule.is_archived != ?', 1)
                ->where('gw_schedule.status = ?', 'processing')
                ->order('id DESC');

        return new \AdminModule\Component\ScheduleGrid($data, true);
    }
	
    /** @var string */
    private $maintenanceFile;

    public function handleChangeVariable()
    {
		$logDir = $this->logger->getLogDir();
		
		if (file_exists($logDir . '/maintenance-started')) {
			@unlink($logDir . '/maintenance-started');            
        }else{
			@file_put_contents($logDir . '/maintenance-started', 'maintenance was started. All handlers on hold.'); // @ - file may not be writable
		}
				
        if ($this->isAjax()) {
            $this->invalidateControl('ajaxChange');
        }
    }


    public function renderDefault()
    {
		$logDir = $this->logger->getLogDir();
		
		if (file_exists($logDir . '/maintenance-started')) {
           $this->maintenanceFile = 'Start Gateway';
		   $this->flashMessage('Gateway stopped! Scheduled transfers will NOT be processed.', 'error');
        }else{
			$this->maintenanceFile = 'Stop Gateway!';
			$this->flashMessage('Gateway is running. Scheduled transfers will be processed.', 'success');
		}
        $this->template->maintenanceFile = $this->maintenanceFile;
    }	

}
