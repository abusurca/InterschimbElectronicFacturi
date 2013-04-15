<?php

require_once("InvoiceException.class.php");
require_once("InvoiceElement.class.php");
require_once("InvoiceValidator.class.php");


/*
 * Invoice Item class
 * */
class InvoiceItem extends InvoiceElement
{
	/*
	 * Possible invoice elements.
	 * */
	protected $arrElements = array(
		"NrCrt" => NULL, "Denumire" => NULL, "UnitateMonetara" => NULL,
		"Cantitate" => NULL, "ValoareUnitara" => NULL, "TVA" => NULL
	);

	/*
	 * Required invoice elements.
	 * */
	protected $arrRequiredElements = array(
		"NrCrt", "Denumire", "UnitateMonetara",
		"Cantitate", "ValoareUnitara", "TVA"
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
		parent::__construct(array("validate_currency" => false), $arrOptions);

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
					case "NrCrt":
						$this->setItemID($mixedValue);
						break;

					case "Denumire":
						$this->setItemDescription($mixedValue);
						break;

					case "UnitateMonetara":
						$this->setItemCurrency($mixedValue);
						break;

					case "Cantitate":
						$this->setItemQuantity($mixedValue);
						break;

					case "ValoareUnitara":
						$this->setItemPrice($mixedValue);
						break;

					case "TVA":
						$this->setItemVAT($mixedValue);
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
	 * Getter function for ItemID.
	 *
	 * @return	the item id
	 * */
	public function getItemID()
	{
		return $this->arrElements["NrCrt"];
	}

	/*
	 * Setter function for ItemID.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	iItemID				the item ID as a integer
	 * */
	public function setItemID($iItemID)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkNumber($iItemID, true, true))
			throw new InvoiceException("Invalid ItemID", InvoiceException::INVALID_ITEM_ID);

		$this->arrElements["NrCrt"] = (int)$iItemID;

		restore_error_handler();
	}

	/*
	 * Getter function for ItemDescription.
	 *
	 * @return	the item description
	 * */
	public function getItemDescription()
	{
		return $this->arrElements["Denumire"];
	}

	/*
	 * Setter function for ItemDescription.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strItemDescription	the item description as a string
	 * */
	public function setItemDescription($strItemDescription)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strItemDescription))
			throw new InvoiceException("Invalid Item Description", InvoiceException::INVALID_ITEM_DESCRIPTION);

		$this->arrElements["Denumire"] = $strItemDescription;

		restore_error_handler();
	}

	/*
	 * Getter function for ItemCurrency.
	 *
	 * @return	the item currency
	 * */
	public function getItemCurrency()
	{
		return $this->arrElements["UnitateMonetara"];
	}

	/*
	 * Setter function for ItemCurrency.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strItemCurrency		the item currency as a string
	 * */
	public function setItemCurrency($strItemCurrency)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkCurrency($strItemCurrency, $this->arrOptions["validate_currency"]))
			throw new InvoiceException("Invalid Item Currency", InvoiceException::INVALID_ITEM_CURRENCY);

		$this->arrElements["UnitateMonetara"] = $strItemCurrency;

		restore_error_handler();
	}

	/*
	 * Getter function for ItemQuantity.
	 *
	 * @return	the item quantity
	 * */
	public function getItemQuantity()
	{
		return $this->arrElements["Cantitate"];
	}

	/*
	 * Setter function for ItemQuantity.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	fItemQuantity		the item quantity as a float
	 * */
	public function setItemQuantity($fItemQuantity)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkNumber($fItemQuantity, false, true))
			throw new InvoiceException("Invalid ItemQuantity", InvoiceException::INVALID_ITEM_QUANTITY);

		$this->arrElements["Cantitate"] = (float)$fItemQuantity;

		restore_error_handler();
	}

	/*
	 * Getter function for ItemPrice.
	 *
	 * @return	the item price
	 * */
	public function getItemPrice()
	{
		return $this->arrElements["ValoareUnitara"];
	}

	/*
	 * Setter function for ItemPrice.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	fItemPrice			the item price as a float
	 * */
	public function setItemPrice($fItemPrice)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkNumber($fItemPrice, true, true))
			throw new InvoiceException("Invalid ItemPrice", InvoiceException::INVALID_ITEM_PRICE);

		$this->arrElements["ValoareUnitara"] = (float)$fItemPrice;

		restore_error_handler();
	}

	/*
	 * Getter function for ItemVAT.
	 *
	 * @return	the item VAT
	 * */
	public function getItemVAT()
	{
		return $this->arrElements["TVA"];
	}

	/*
	 * Setter function for ItemVAT.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	fItemVAT			the item VAT as a float
	 * */
	public function setItemVAT($fItemVAT)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkNumber($fItemVAT, true, true))
			throw new InvoiceException("Invalid ItemVAT", InvoiceException::INVALID_ITEM_VAT);

		$this->arrElements["TVA"] = (float)$fItemVAT;

		restore_error_handler();
	}
}
