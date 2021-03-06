<?php defined('SYSPATH') or die('No direct access allowed.');
/*
BeansBooks
Copyright (C) System76, Inc.

This file is part of BeansBooks.

BeansBooks is free software; you can redistribute it and/or modify
it under the terms of the BeansBooks Public License.

BeansBooks is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the BeansBooks Public License for more details.

You should have received a copy of the BeansBooks Public License
along with BeansBooks; if not, email info@beansbooks.com.
*/


class View_Dash_Payables extends View_Template {
	
	public function report_date()
	{
		if( ! isset($this->report_payables_result) )
			return FALSE;

		if( $this->report_payables_result->data->date == date("Y-m-d") )
			return FALSE;

		return $this->report_payables_result->data->date;
	}

	public function report_vendor_name()
	{
		if( ! isset($this->report_payables_result) )
			return FALSE;

		if( ! $this->report_payables_result->data->vendor_id OR 
		 	count($this->report_payables_result->data->vendors) > 1 )
			return FALSE;

		foreach( $this->report_payables_result->data->vendors as $vendor_id => $vendor )
			return $vendor->vendor_company_name;
	}

	public function report_vendor_id()
	{
		if( ! isset($this->report_payables_result) )
			return FALSE;

		if( ! $this->report_payables_result->data->vendor_id OR 
		 	count($this->report_payables_result->data->vendors) > 1 )
			return FALSE;

		foreach( $this->report_payables_result->data->vendors as $vendor_id => $vendor )
			return $vendor_id;
	}

	public function report_totals()
	{
		$return_array = array();

		$return_array['balance_total_formatted'] = 
			( $this->report_payables_result->data->balance_total < 0 ? '<span class="text-red">-' : '' ).
			number_format(abs($this->report_payables_result->data->balance_total),2,'.',',').
			( $this->report_payables_result->data->balance_total < 0 ? '</span>' : '' );

		foreach( $this->report_payables_result->data->balances as $days => $balance )
			$return_array['balance_'.$days.'_formatted'] = 
				( $balance < 0 ? '<span class="text-red">-' : '' ).
				number_format(abs($balance),2,'.',',').
				( $balance < 0 ? '</span>' : '' );

		$return_array['vendor_count'] = count($this->report_payables_result->data->vendors);

		return $return_array;
	}

	public function vendor_reports()
	{
		if( ! isset($this->report_payables_result) )
			return FALSE;

		$return_array = array();

		foreach( $this->report_payables_result->data->vendors as $vendor_report )
		{
			$return_array[] = $this->_vendor_report_array($vendor_report);
		}

		return $return_array;
	}

	public function _vendor_report_array($vendor_report)
	{
		$settings = $this->beans_settings();

		$return_array = array();

		$return_array['company_name'] = $vendor_report->vendor_company_name;
		$return_array['phone_number'] = $vendor_report->vendor_phone_number;
		$return_array['balance_total_formatted'] = 
			( $vendor_report->balance_total < 0 ? '<span class="text-red">-' : '' ).
			$settings->company_currency.
			number_format(abs($vendor_report->balance_total),2,'.',',').
			( $vendor_report->balance_total < 0 ? '</span>' : '' );


		$return_array['vendor_balance_total_formatted'] = 
			( $vendor_report->balance_total < 0 ? '<span class="text-red">-' : '' ).
			number_format(abs($vendor_report->balance_total),2,'.',',').
			( $vendor_report->balance_total < 0 ? '</span>' : '' );

		foreach( $vendor_report->balances as $days => $balance )
			$return_array['vendor_balance_'.$days.'_formatted'] = 
				( $balance < 0 ? '<span class="text-red">-' : '' ).
				number_format(abs($balance),2,'.',',').
				( $balance < 0 ? '</span>' : '' );

		$return_array['purchases'] = array();

		foreach( $vendor_report->purchases as $purchase )
			$return_array['purchases'][] = $this->_vendor_report_purchase_array($purchase);

		return $return_array;
	}

	public function _vendor_report_purchase_array($purchase)
	{
		$return_array = array();

		$return_array['date_created'] = $purchase->date_created;
		$return_array['purchase_id'] = $purchase->id;
		$return_array['purchase_number'] = $purchase->purchase_number;
		$return_array['date_due'] = $purchase->date_due;
		$return_array['days_late'] = ( $purchase->days_late > 0 ) ? $purchase->days_late : FALSE;
		$return_array['balance_formatted'] = 
			( $purchase->balance < 0 ? '<span class="text-red">-' : '' ).
			number_format(abs($purchase->balance),2,'.',',').
			( $purchase->balance < 0 ? '</span>' : '' );

		$balance_range = 'current';
		if( $purchase->days_late >= 90 )
			$balance_range = '90';
		else if( $purchase->days_late >= 60 )
			$balance_range = '60';
		else if( $purchase->days_late >= 30 )
			$balance_range = '30';
		else if( $purchase->days_late > 0 )
			$balance_range = '0';

		$return_array['balance_'.$balance_range.'_formatted'] = 
			( $purchase->balance < 0 ? '<span class="text-red">-' : '' ).
			number_format(abs($purchase->balance),2,'.',',').
			( $purchase->balance < 0 ? '</span>' : '' );


		return $return_array;		
	}


}