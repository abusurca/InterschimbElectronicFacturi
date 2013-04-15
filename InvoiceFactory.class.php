<?php

require_once("Invoice.class.php");
require_once("InvoiceCustomer.class.php");
require_once("InvoiceSupplier.class.php");
require_once("InvoiceBankAccount.class.php");
require_once("InvoiceItem.class.php");
require_once("InvoiceComment.class.php");


/*
 * Invoice Factory class.
 *
 * It contains methods that allow the creation of invoice elements.
 * */
class InvoiceFactory
{
	/*
	 * Invoice element options.
	 * */
	private $_arrOptions = array();

	/*
	 * Possible invoice elements.
	 * */
	private $_arrElements = array(
		"Invoice", "InvoiceCustomer", "InvoiceSupplier",
		"InvoiceBankAccount", "InvoiceItem", "InvoiceComment"
	);


	/*
	 * Class constructor.
	 *
	 * @param	$arrOptions	invoice elements options
	 * */
	public function __construct($arrOptions = array())
	{
		set_error_handler("InvoiceException::error_handler");

		foreach($arrOptions as $strKey => $mixedValue)
		{
			switch($strKey)
			{
				case "validation_flags":
					$this->_arrOptions = (array)$mixedValue;
					break;

				default:
					break;
			}
		}

		restore_error_handler();
	}

	/*
	 * Sets the arrOptions and creates the invoice element.
	 *
	 * @throws	Exception		it does not catch the exceptions raised by the invoice elements creation process.
	 * @param	strElementName	the name of the invoice element as a string
	 * @param	arrElements		the structure of the invoice element as an array
	 * @param	arrOptions		the invoice element options
	 * @return	the newly created invoice element
	 * */
	private function _createInvoiceEntity($strElementName, $arrElements, $arrOptions)
	{
		assert(in_array($strElementName, $this->_arrElements));

		if(is_null($arrOptions))
			$arrOptions = $this->_arrOptions;

		return new $strElementName($arrElements, $arrOptions);
	}

	/*
	 * Creates invoices.
	 *
	 * @throws	Exception	it does not catch the exceptions raised by the invoice elements creation process.
	 * @param	arrElements	the structure of the invoice as an array
	 * @param	arrOptions	the invoice options
	 * */
	public function createInvoice($arrElements = array(), $arrOptions = NULL)
	{
		set_error_handler("InvoiceException::error_handler");
		$oInvoice = $this->_createInvoiceEntity("Invoice", $arrElements, $arrOptions);
		restore_error_handler();

		return $oInvoice;
	}

	/*
	 * Creates invoice customer.
	 *
	 * @throws	Exception	it does not catch the exceptions raised by the invoice elements creation process.
	 * @param	arrElements	the structure of the invoice customer as an array
	 * @param	arrOptions	the invoice customer options
	 * */
	public function createInvoiceCustomer($arrElements = array(), $arrOptions = NULL)
	{
		set_error_handler("InvoiceException::error_handler");
		$oInvoiceCustomer = $this->_createInvoiceEntity("InvoiceCustomer", $arrElements, $arrOptions);
		restore_error_handler();

		return $oInvoiceCustomer;
	}

	/*
	 * Creates invoice suppliers.
	 *
	 * @throws	Exception	it does not catch the exceptions raised by the invoice elements creation process.
	 * @param	arrElements	the structure of the invoice supplier as an array
	 * @param	arrOptions	the invoice supplier options
	 * */
	public function createInvoiceSupplier($arrElements = array(), $arrOptions = NULL)
	{
		set_error_handler("InvoiceException::error_handler");
		$oInvoiceSupplier = $this->_createInvoiceEntity("InvoiceSupplier", $arrElements, $arrOptions);
		restore_error_handler();

		return $oInvoiceSupplier;
	}

	/*
	 * Creates invoice bank accounts.
	 *
	 * @throws	Exception	it does not catch the exceptions raised by the invoice elements creation process.
	 * @param	arrElements	the structure of the invoice bank account as an array
	 * @param	arrOptions	the invoice bank account options
	 * */
	public function createInvoiceBankAccount($arrElements = array(), $arrOptions = NULL)
	{
		set_error_handler("InvoiceException::error_handler");
		$oInvoiceBankAccount = $this->_createInvoiceEntity("InvoiceBankAccount", $arrElements, $arrOptions);
		restore_error_handler();

		return $oInvoiceBankAccount;
	}

	/*
	 * Creates invoice items.
	 *
	 * @throws	Exception	it does not catch the exceptions raised by the invoice elements creation process.
	 * @param	arrElements	the structure of the invoice item as an array
	 * @param	arrOptions	the invoice item options
	 * */
	public function createInvoiceItem($arrElements = array(), $arrOptions = NULL)
	{
		set_error_handler("InvoiceException::error_handler");
		$oInvoiceItem = $this->_createInvoiceEntity("InvoiceItem", $arrElements, $arrOptions);
		restore_error_handler();

		return $oInvoiceItem;;
	}

	/*
	 * Creates invoice comments.
	 *
	 * @throws	Exception	it does not catch the exceptions raised by the invoice elements creation process.
	 * @param	arrElements	the structure of the invoice comment as an array
	 * @param	arrOptions	the invoice comment options
	 * */
	public function createInvoiceComment($arrElements = array(), $arrOptions = NULL)
	{
		set_error_handler("InvoiceException::error_handler");
		$oInvoiceComment = $this->_createInvoiceEntity("InvoiceComment", $arrElements, $arrOptions);
		restore_error_handler();

		return $oInvoiceComment;
	}
}
