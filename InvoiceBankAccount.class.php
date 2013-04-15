<?php

require_once("InvoiceException.class.php");
require_once("InvoiceElement.class.php");
require_once("InvoiceValidator.class.php");


/*
 * Invoice Bank Account class
 * */
class InvoiceBankAccount extends InvoiceElement
{
	/*
	 * Possible invoice elements.
	 * */
	protected $arrElements = array(
		"NumeBanca" => NULL, "NumarContBancar" => NULL
	);

	/*
	 * Required invoice elements.
	 * */
	protected $arrRequiredElements = array(
		"NumeBanca", "NumarContBancar"
	);


	/*
	 * Class constructor.
	 *
	 * @throws	InvoiceException	when arrElements is provided and contains invalid values a proper exception is thrown
	 * @param	arrElements			array from which the invoice is constructed
	 * @param	arrOptions			array which contains flags that enable various validations and options
	 * */
	public function __construct($arrElements = array(), $arrOptions = array())
	{
		set_error_handler("InvoiceException::error_handler");

		/* Set option flags */
		parent::__construct(array("validate_iban" => false), $arrOptions);

		if(!is_array($arrElements))
			throw new InvoiceException("Invalid arguments", InvoiceException::INVALID_ARGUMENTS);

		$oInvoiceException = NULL;

		/* Create invoice structure defined by arrElements */
		foreach($arrElements as $strKey => $mixedValue)
		{
			try
			{
				switch($strKey)
				{
					case "NumeBanca":
						$this->setBankName($mixedValue);
						break;

					case "NumarContBancar":
						$this->setBankAccountNumber($mixedValue);
						break;

					default:
						/* Ignore invalid parts of the array */
						break;
				}
			}
			/* Create and populate multiple exception if allowed */
			catch(Exception $e)
			{
				if(!$this->arrOptions["allow_multiple_exceptions"])
					throw $e;

				if(!isset($oInvoiceException))
					$oInvoiceException = new InvoiceException("Multiple exception", InvoiceException::MULTIPLE_EXCEPTION);
				$oInvoiceException->addException($e);
			}
		}

		/* Throw multiple exception if it has been created */
		if(isset($oInvoiceException))
			throw $oInvoiceException;

		restore_error_handler();
	}

	/*
	 * Getter function for BankName.
	 *
	 * @return	the bank name
	 * */
	public function getBankName()
	{
		return $this->arrElements["NumeBanca"];
	}

	/*
	 * Setter function for BankName.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strBankName			the bank name as a string
	 * */
	public function setBankName($strBankName)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strBankName))
			throw new InvoiceException("Invalid BankName", InvoiceException::INVALID_BANK_NAME);

		$this->arrElements["NumeBanca"] = $strBankName;

		restore_error_handler();
	}

	/*
	 * Getter function for BankAccountNumber.
	 *
	 * @return	the bank account number
	 * */
	public function getBankAccountNumber()
	{
		return $this->arrElements["NumarContBancar"];
	}

	/*
	 * Setter function for BankAccountNumber.
	 *
	 * @throws	InvoiceException		if the provided parameter is not valid throw a proper exception
	 * @param	strBankAccountNumber	the bank account number as a string
	 * */
	public function setBankAccountNumber($strBankAccountNumber)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkIBAN($strBankAccountNumber, $this->arrOptions["validate_iban"]))
			throw new InvoiceException("Invalid BankAccountNumber", InvoiceException::INVALID_BANK_ACCOUNT_NUMBER);

		$this->arrElements["NumarContBancar"] = $strBankAccountNumber;

		restore_error_handler();
	}
}
