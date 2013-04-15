<?php

require_once("InvoiceException.class.php");
require_once("InvoiceElement.class.php");
require_once("InvoiceValidator.class.php");


/*
 * Invoice Customer class.
 * */
class InvoiceCustomer extends InvoiceElement
{
	/*
	 * Possible invoice elements.
	 * */
	protected $arrElements = array(
		"ID" => NULL, "Nume" => NULL, "CIF" => NULL,
		"Adresa" => NULL, "ConturiBancare" => array()
	);

	/*
	 * Required invoice elements.
	 * */
	protected $arrRequiredElements = array(
		"Nume", "CIF", "Adresa"
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
		parent::__construct(array(), $arrOptions);

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
					case "ID":
						$this->setCustomerID($mixedValue);
						break;

					case "Nume":
						$this->setCustomerName($mixedValue);
						break;

					case "CIF":
						$this->setCustomerVAT($mixedValue);
						break;

					case "Adresa":
						$this->setCustomerAddress($mixedValue);
						break;

					case "ConturiBancare":
						if(is_array($mixedValue))
						{
							/* Iterate through the array and create the InvoiceBankAccounts using the structure defined in the array values */
							foreach($mixedValue as &$mixedBankAccountInfo)
								if(is_array($mixedBankAccountInfo))
									$mixedBankAccountInfo = new InvoiceBankAccount($mixedBankAccountInfo, $arrOptions);
						}
						$this->setCustomerBankAccounts($mixedValue);
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
	 * Validates an invoice customer.
	 *
	 * @throws	InvoiceException	if the invoice customer is not valid throws a proper exception
	 * */
	protected function validate()
	{
		/* Check for empty required fields  */
		parent::validate();

		/* Validate class specific fields */
		$arrToBeValidated = array_merge(
			$this->arrElements["ConturiBancare"]
		);

		foreach($arrToBeValidated as $oToBeValidated)
			$oToBeValidated->validate();
	}

	/*
	 * Gets the content of the invoice customer as an array.
	 *
	 * @return	the content of the invoice customer as an array
	 * */
	protected function get()
	{
		/* Clone the basic fields, delete the empty optional ones and empty the array fields */
		$arrClonedElements = parent::get();

		/* Repopulate the array fields */
		foreach($this->arrElements["ConturiBancare"] as $oInvoiceBankAccount)
			array_push($arrClonedElements["ConturiBancare"], $oInvoiceBankAccount->get());

		return $arrClonedElements;
	}

	/*
	 * Getter function for CustomerID.
	 *
	 * @return	the customer id
	 * */
	public function getCustomerID()
	{
		return $this->arrElements["ID"];
	}

	/*
	 * Setter function for CustomerID.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strCustomerID		the customer ID as a string
	 * */
	public function setCustomerID($strCustomerID)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strCustomerID))
			throw new InvoiceException("Invalid CustomerID", InvoiceException::INVALID_CUSTOMER_ID);

		$this->arrElements["ID"] = $strCustomerID;

		restore_error_handler();
	}

	/*
	 * Getter function for CustomerName.
	 *
	 * @return	the customer name
	 * */
	public function getCustomerName()
	{
		return $this->arrElements["Nume"];
	}

	/*
	 * Setter function for CustomerName.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strCustomerName		the customer name as a string
	 * */
	public function setCustomerName($strCustomerName)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strCustomerName))
			throw new InvoiceException("Invalid CustomerName", InvoiceException::INVALID_CUSTOMER_NAME);

		$this->arrElements["Nume"] = $strCustomerName;

		restore_error_handler();
	}

	/*
	 * Getter function for CustomerAddress.
	 *
	 * @return	the customer address
	 * */
	public function getCustomerAddress()
	{
		return $this->arrElements["Adresa"];
	}

	/*
	 * Setter function for CustomerAddress.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strCustomerAddress	the customer address as a string
	 * */
	public function setCustomerAddress($strCustomerAddress)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strCustomerAddress))
			throw new InvoiceException("Invalid CustomerAddress", InvoiceException::INVALID_CUSTOMER_ADDRESS);

		$this->arrElements["Adresa"] = $strCustomerAddress;

		restore_error_handler();
	}

	/*
	 * Getter function for CustomerVAT.
	 *
	 * @return	the customer VAT
	 * */
	public function getCustomerVAT()
	{
		return $this->arrElements["CIF"];
	}

	/*
	 * Setter function for CustomerVAT.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strCustomerVAT		the customer VAT as a string
	 * */
	public function setCustomerVAT($strCustomerVAT)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strCustomerVAT))
			throw new InvoiceException("Invalid CustomerVAT", InvoiceException::INVALID_CUSTOMER_VAT);

		$this->arrElements["CIF"] = $strCustomerVAT;

		restore_error_handler();
	}

	/*
	 * Getter function for CustomerBankAccounts.
	 *
	 * @return	the customer bank accounts
	 * */
	public function getCustomerBankAccounts()
	{
		return $this->arrElements["ConturiBancare"];
	}

	/*
	 * Setter function for CustomerBankAccounts.
	 *
	 * @throws	InvoiceException		if the provided parameter is not valid throw a proper exception
	 * @param	arrCustomerBankAccounts	the customer bank accounts as an array
	 * */
	public function setCustomerBankAccounts($arrCustomerBankAccounts)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObjectArray($arrCustomerBankAccounts, "InvoiceBankAccount"))
			throw new InvoiceException("Invalid Customer Bank Accounts", InvoiceException::INVALID_CUSTOMER_BANK_ACCOUNTS);

		$this->arrElements["ConturiBancare"] = $arrCustomerBankAccounts;

		restore_error_handler();
	}

	/*
	 * Adds a bank account to the customer bank accounts array.
	 *
	 * @throws	InvoiceException		if the provided parameter is not valid throw a proper exception
	 * @param	oCustomerBankAccount	the customer bank account object
	 * */
	public function addCustomerBankAccount($oCustomerBankAccount)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObject($oCustomerBankAccount, "InvoiceBankAccount"))
			throw new InvoiceException("Invalid Invoice Bank Account", InvoiceException::INVALID_CUSTOMER_BANK_ACCOUNT);

		array_push($this->arrElements["ConturiBancare"], $oCustomerBankAccount);

		restore_error_handler();
	}
}
