<?php

require_once("InvoiceException.class.php");
require_once("InvoiceElement.class.php");
require_once("InvoiceValidator.class.php");


/*
 * Invoice Supplier class.
 * */
class InvoiceSupplier extends InvoiceElement
{
	/*
	 * Possible invoice elements.
	 * */
	protected $arrElements = array(
		"Nume" => NULL, "Adresa" => NULL, "CIF" => NULL,
		"NumarRegistruComert" => NULL, "ConturiBancare" => array()
	);

	/*
	 * Required invoice elements.
	 * */
	protected $arrRequiredElements = array(
		"Nume", "Adresa", "CIF",
		"NumarRegistruComert", "ConturiBancare"
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
					case "Nume":
						$this->setSupplierName($mixedValue);
						break;

					case "Adresa":
						$this->setSupplierAddress($mixedValue);
						break;

					case "CIF":
						$this->setSupplierVAT($mixedValue);
						break;

					case "NumarRegistruComert":
						$this->setSupplierTradeRegistryNumber($mixedValue);
						break;

					case "ConturiBancare":
						if(is_array($mixedValue))
						{
							/* Iterate through the array and create the InvoiceBankAccounts using the structure defined in the array values */
							foreach($mixedValue as &$mixedBankAccountInfo)
								if(is_array($mixedBankAccountInfo))
									$mixedBankAccountInfo = new InvoiceBankAccount($mixedBankAccountInfo, $arrOptions);
						}
						$this->setSupplierBankAccounts($mixedValue);
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
	 * Validates an invoice supplier.
	 *
	 * @throws	InvoiceException	if the invoice supplier is not valid throws a proper exception
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
	 * Gets the content of the invoice supplier as an array.
	 *
	 * @return	the content of the invoice supplier as an array
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
	 * Getter function for SupplierName.
	 *
	 * @return	the supplier name
	 * */
	public function getSupplierName()
	{
		return $this->arrElements["Nume"];
	}

	/*
	 * Setter function for SupplierName.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strSupplierName		the invoice name as a string
	 * */
	public function setSupplierName($strSupplierName)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strSupplierName))
			throw new InvoiceException("Invalid SupplierName", InvoiceException::INVALID_SUPPLIER_NAME);

		$this->arrElements["Nume"] = $strSupplierName;

		restore_error_handler();
	}

	/*
	 * Getter function for SupplierAddress.
	 *
	 * @return	the supplier address
	 * */
	public function getSupplierAddress()
	{
		return $this->arrElements["Adresa"];
	}

	/*
	 * Setter function for SupplierAddress.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strSupplierAddress	the supplier address as a string
	 * */
	public function setSupplierAddress($strSupplierAddress)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strSupplierAddress))
			throw new InvoiceException("Invalid SupplierAddress", InvoiceException::INVALID_SUPPLIER_ADDRESS);

		$this->arrElements["Adresa"] = $strSupplierAddress;

		restore_error_handler();
	}

	/*
	 * Getter function for SupplierVAT.
	 *
	 * @return	the supplier VAT
	 * */
	public function getSupplierVAT()
	{
		return $this->arrElements["CIF"];
	}

	/*
	 * Setter function for SupplierVAT.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strSupplierVAT		the supplier VAT as a string
	 * */
	public function setSupplierVAT($strSupplierVAT)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strSupplierVAT))
			throw new InvoiceException("Invalid SupplierVAT", InvoiceException::INVALID_SUPPLIER_VAT);

		$this->arrElements["CIF"] = $strSupplierVAT;

		restore_error_handler();
	}

	/*
	 * Getter function for SupplierTradeRegistryNumber.
	 *
	 * @return	the supplier trade registry number
	 * */
	public function getSupplierTradeRegistryNumber()
	{
		return $this->arrElements["NumarRegistruComert"];
	}

	/*
	 * Setter function for SupplieTradeRegistryNumber.
	 *
	 * @throws	InvoiceException				if the provided parameter is not valid throw a proper exception
	 * @param	strSupplierTradeRegistryNumber	the supplier trade registry number as a string
	 * */
	public function setSupplierTradeRegistryNumber($strSupplierTradeRegistryNumber)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strSupplierTradeRegistryNumber))
			throw new InvoiceException("Invalid SupplierTradeRegistryNumber", InvoiceException::INVALID_SUPPLIER_TRADE_REGISTRY_NUMBER);

		$this->arrElements["NumarRegistruComert"] = $strSupplierTradeRegistryNumber;

		restore_error_handler();
	}

	/*
	 * Getter function for SupplierBankAccounts.
	 *
	 * @return	the supplier bank accounts
	 * */
	public function getSupplierBankAccounts()
	{
		return $this->arrElements["ConturiBancare"];
	}

	/*
	 * Setter function for SupplierBankAccounts.
	 *
	 * @throws	InvoiceException		if the provided parameter is not valid throw a proper exception
	 * @param	arrSupplierBankAccounts	the supplier bank accounts as an array
	 * */
	public function setSupplierBankAccounts($arrSupplierBankAccounts)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObjectArray($arrSupplierBankAccounts, "InvoiceBankAccount"))
			throw new InvoiceException("Invalid Supplier Bank Accounts", InvoiceException::INVALID_SUPPLIER_BANK_ACCOUNTS);

		$this->arrElements["ConturiBancare"] = $arrSupplierBankAccounts;

		restore_error_handler();
	}

	/*
	 * Adds a bank account to the supplier bank accounts array.
	 *
	 * @throws	InvoiceException		if the provided parameter is not valid throw a proper exception
	 * @param	oSupplierBankAccount	the supplier bank account object
	 * */
	public function addSupplierBankAccount($oSupplierBankAccount)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObject($oSupplierBankAccount, "InvoiceBankAccount"))
			throw new InvoiceException("Invalid Supplier Bank Account", InvoiceException::INVALID_SUPPLIER_BANK_ACCOUNT);

		array_push($this->arrElements["ConturiBancare"], $oSupplierBankAccount);

		restore_error_handler();
	}
}
