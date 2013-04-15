<?php

/*
 * Invoice Exception class.
 *
 * These are the only exceptions thrown by the application.
 * */
class InvoiceException extends Exception
{
	/*
	 * Error codes.
	 * */
	const MULTIPLE_EXCEPTION = 0;
	const EMPTY_FIELD = 1;
	const INVALID_ARGUMENTS = 2;
	const INVALID_INVOICE_SERIES = 3;
	const INVALID_INVOICE_NUMBER = 4;
	const INVALID_INVOICE_DATE = 5;
	const INVALID_INVOICE_MATURITY_DATE = 6;
	const INVALID_INVOICE_CUSTOMER = 7;
	const INVALID_INVOICE_SUPPLIER = 8;
	const INVALID_INVOICE_ITEMS = 9;
	const INVALID_INVOICE_ITEM = 10;
	const INVALID_INVOICE_COMMENTS = 11;
	const INVALID_INVOICE_COMMENT = 12;
	const INVALID_CUSTOMER_ID = 13;
	const INVALID_CUSTOMER_NAME = 14;
	const INVALID_CUSTOMER_VAT = 15;
	const INVALID_CUSTOMER_ADDRESS = 16;
	const INVALID_CUSTOMER_BANK_ACCOUNT = 17;
	const INVALID_CUSTOMER_BANK_ACCOUNTS = 18;
	const INVALID_SUPPLIER_NAME = 19;
	const INVALID_SUPPLIER_ADDRESS = 20;
	const INVALID_SUPPLIER_VAT = 21;
	const INVALID_SUPPLIER_TRADE_REGISTRY_NUMBER = 22;
	const INVALID_SUPPLIER_BANK_ACCOUNT = 23;
	const INVALID_SUPPLIER_BANK_ACCOUNTS = 24;
	const INVALID_BANK_NAME = 25;
	const INVALID_BANK_ACCOUNT_NUMBER = 26;
	const INVALID_ITEM_ID = 27;
	const INVALID_ITEM_DESCRIPTION = 28;
	const INVALID_ITEM_CURRENCY = 29;
	const INVALID_ITEM_QUANTITY = 30;
	const INVALID_ITEM_PRICE = 31;
	const INVALID_ITEM_VAT = 32;
	const INVALID_COMMENT_CONTENT = 33;

	/*
	 * Multiple exception collection.
	 * */
	private $_arrMultipleExceptionCollection = array();


	/*
	 * Adds an exception to the multiple exception collection.
	 *
	 * @param	e	the exception to be added to the collection
	 * */
	public function addException($e)
	{
		if(!strcmp(get_class($e), get_class()) && $e->getCode() == InvoiceException::MULTIPLE_EXCEPTION)
		{
			$this->_arrMultipleExceptionCollection = array_merge(
				$this->_arrMultipleExceptionCollection,
				$e->_arrMultipleExceptionCollection
			);
		}
		else
			$this->_arrMultipleExceptionCollection[] = $e;
	}

	/*
	 * Gets the multiple exceptions collection.
	 *
	 * @return	the exceptions collection as an array
	 * */
	public function getExceptions()
	{
		assert($this->getCode() == InvoiceException::MULTIPLE_EXCEPTION);

		return $this->_arrMultipleExceptionCollection;
	}

	public static function error_handler($nNumber, $strMessage, $strFilePath, $nLineNumber)
	{
		throw new ErrorException($strMessage, $nNumber, 0, $strFilePath, $nLineNumber);
	}
}
