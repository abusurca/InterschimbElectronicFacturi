<?php

require_once("InvoiceException.class.php");
require_once("InvoiceElement.class.php");
require_once("InvoiceValidator.class.php");


/*
 * Invoice class.
 * */
class Invoice extends InvoiceElement
{
	/*
	 * Possible invoice elements.
	 * */
	protected $arrElements = array(
		"Serie" => NULL, "Numar" => NULL, "DataEmitere" => NULL,
		"DataScadenta" => NULL, "Cumparator" => NULL, "Furnizor" => NULL,
		"Bunuri" => array(), "Comentarii" => array()
	);

	/*
	 * Required invoice elements.
	 * */
	protected $arrRequiredElements = array(
		"Serie", "Numar", "DataEmitere", "DataScadenta",
		"Cumparator", "Furnizor", "Bunuri"
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
					case "Serie":
						$this->setInvoiceSeries($mixedValue);
						break;

					case "Numar":
						$this->setInvoiceNumber($mixedValue);
						break;

					case "DataEmitere":
						$this->setInvoiceDate($mixedValue);
						break;

					case "DataScadenta":
						$this->setInvoiceMaturityDate($mixedValue);
						break;

					case "Cumparator":
						if(!is_object($mixedValue))
							/* If the value is an array create the InvoiceCustomer using the structure defined in the array */
							$mixedValue = new InvoiceCustomer($mixedValue, $arrOptions);
						$this->setInvoiceCustomer($mixedValue);
						break;

					case "Furnizor": case "Prestator":
						if(!is_object($mixedValue))
							/* If the value is an array create the InvoiceSupplier using the structure defined in the array */
							$mixedValue = new InvoiceSupplier($mixedValue, $arrOptions);
						$this->setInvoiceSupplier($mixedValue);
						break;

					case "Bunuri": case "Servicii":
						if(is_array($mixedValue))
						{
							/* Iterate through the array and create the InvoiceItems using the structure defined in the array values */
							foreach($mixedValue as &$mixedItemInfo)
								if(is_array($mixedItemInfo))
									$mixedItemInfo = new InvoiceItem($mixedItemInfo, $arrOptions);
						}
						$this->setInvoiceItems($mixedValue);
						break;

					case "Comentarii":
						if(is_array($mixedValue))
						{
							/* Iterate through the array and create the InvoiceComments using the structure defined in the array values */
							foreach($mixedValue as &$mixedCommentInfo)
								if(is_array($mixedCommentInfo))
									$mixedCommentInfo = new InvoiceComment($mixedCommentInfo, $arrOptions);
						}
						$this->setInvoiceComments($mixedValue);
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
	 * Validates an invoice.
	 *
	 * @throws	InvoiceException	if the invoice is not valid throws a proper exception
	 * */
	protected function validate()
	{
		/* Check for empty required fields  */
		parent::validate();

		/* Validate class specific fields */
		$arrToBeValidated = array_merge(
			array(
				$this->arrElements["Cumparator"],
				$this->arrElements["Furnizor"]
			),
			$this->arrElements["Bunuri"],
			$this->arrElements["Comentarii"]
		);
		foreach($arrToBeValidated as $oToBeValidated)
			$oToBeValidated->validate();
	}

	/*
	 * Gets the content of the invoice as an array.
	 *
	 * @return	the content of the invoice as an array
	 * */
	protected function get()
	{
		/* Clone the basic fields, delete the empty optional ones and empty the array fields */
		$arrClonedElements = parent::get();

		/* Get class specific fields */
		$arrClonedElements["Cumparator"] = $this->arrElements["Cumparator"]->get();
		$arrClonedElements["Furnizor"] = $this->arrElements["Furnizor"]->get();

		/* Repopulate the array fields */
		foreach($this->arrElements["Bunuri"] as $oInvoiceItem)
			$arrClonedElements["Bunuri"][] = $oInvoiceItem->get();

		foreach($this->arrElements["Comentarii"] as $oInvoiceComment)
			$arrClonedElements["Comentarii"][] = $oInvoiceComment->get();

		return $arrClonedElements;
	}

	/*
	 * Getter function for InvoiceSeries.
	 *
	 * @return	the invoice series
	 * */
	public function getInvoiceSeries()
	{
		return $this->arrElements["Serie"];
	}

	/*
	 * Setter function for InvoiceSeries.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strInvoiceSeries	the invoice series as a string
	 * */
	public function setInvoiceSeries($strInvoiceSeries)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strInvoiceSeries))
			throw new InvoiceException("Invalid Invoice Series", InvoiceException::INVALID_INVOICE_SERIES);

		$this->arrElements["Serie"] = $strInvoiceSeries;

		restore_error_handler();
	}

	/*
	 * Getter function for InvoiceNumber.
	 *
	 * @return	the invoice number
	 * */
	public function getInvoiceNumber()
	{
		return $this->arrElements["Numar"];
	}

	/*
	 * Setter function for InvoiceNumber.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strInvoiceNumber	the invoice number as a string
	 * */
	public function setInvoiceNumber($strInvoiceNumber)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strInvoiceNumber))
			throw new InvoiceException("Invalid Invoice Number", InvoiceException::INVALID_INVOICE_NUMBER);

		$this->arrElements["Numar"] = $strInvoiceNumber;

		restore_error_handler();
	}

	/*
	 * Getter function for InvoiceDate.
	 *
	 * @return	the invoice date
	 * */
	public function getInvoiceDate()
	{
		return $this->arrElements["DataEmitere"];
	}

	/*
	 * Setter function for InvoiceDate.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strInvoiceDate		the invoice date as a string
	 * */
	public function setInvoiceDate($strInvoiceDate)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkDate($strInvoiceDate))
			throw new InvoiceException("Invalid Invoice Date", InvoiceException::INVALID_INVOICE_DATE);

		$this->arrElements["DataEmitere"] = $strInvoiceDate;

		restore_error_handler();
	}

	/*
	 * Getter function for InvoiceMaturityDate.
	 *
	 * @return	the invoice maturity date
	 * */
	public function getInvoiceMaturityDate()
	{
		return $this->arrElements["DataScadenta"];
	}

	/*
	 * Setter function for InvoiceMaturityDate.
	 *
	 * @throws	InvoiceException		if the provided parameter is not valid throw a proper exception
	 * @param	strInvoiceMaturityDate	the invoice maturity date as a string
	 * */
	public function setInvoiceMaturityDate($strInvoiceMaturityDate)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkDate($strInvoiceMaturityDate))
			throw new InvoiceException("Invalid Invoice Maturity Date", InvoiceException::INVALID_INVOICE_MATURITY_DATE);

		$this->arrElements["DataScadenta"] = $strInvoiceMaturityDate;

		restore_error_handler();
	}

	/*
	 * Getter function for InvoiceCustomer.
	 *
	 * @return	the invoice customer
	 * */
	public function getInvoiceCustomer()
	{
		return $this->arrElements["Cumparator"];
	}

	/*
	 * Setter function for InvoiceCustomer.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	oInvoiceCustomer	the invoice customer object
	 * */
	public function setInvoiceCustomer($oInvoiceCustomer)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObject($oInvoiceCustomer, "InvoiceCustomer"))
			throw new InvoiceException("Invalid Invoice Customer", InvoiceException::INVALID_INVOICE_CUSTOMER);

		$this->arrElements["Cumparator"] = $oInvoiceCustomer;

		restore_error_handler();
	}

	/*
	 * Getter function for InvoiceSupplier.
	 *
	 * @return	the invoice supplier
	 * */
	public function getInvoiceSupplier()
	{
		return $this->arrElements["Furnizor"];
	}

	/*
	 * Setter function for InvoiceSupplier.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	oInvoiceSupplier	the invoice supplier object
	 * */
	public function setInvoiceSupplier($oInvoiceSupplier)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObject($oInvoiceSupplier, "InvoiceSupplier"))
			throw new InvoiceException("Invalid Invoice Supplier", InvoiceException::INVALID_INVOICE_SUPPLIER);

		$this->arrElements["Furnizor"] = $oInvoiceSupplier;

		restore_error_handler();
	}

	/*
	 * Getter function for InvoiceItems.
	 *
	 * @return	the invoice items array
	 * */
	public function getInvoiceItems()
	{
		return $this->arrElements["Bunuri"];
	}

	/*
	 * Setter function for InvoiceItems.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	arrInvoiceItems		the invoice items array
	 * */
	public function setInvoiceItems($arrInvoiceItems)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObjectArray($arrInvoiceItems, "InvoiceItem"))
			throw new InvoiceException("Invalid Invoice Items", InvoiceException::INVALID_INVOICE_ITEMS);

		$this->arrElements["Bunuri"] = $arrInvoiceItems;

		restore_error_handler();
	}

	/*
	 * Adds a item to the invoice items array.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	oInvoiceItem		the invoice item object
	 * */
	public function addInvoiceItem($oInvoiceItem)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObject($oInvoiceItem, "InvoiceItem"))
			throw new InvoiceException("Invalid Invoice Item", InvoiceException::INVALID_ITEM);

		$this->arrElements["Bunuri"][] = $oInvoiceItem;

		restore_error_handler();
	}

	/*
	 * Getter function for InvoiceComments.
	 *
	 * @return	the invoice comments array
	 * */
	public function getInvoiceComments()
	{
		return $this->arrElements["Comentarii"];
	}

	/*
	 * Setter function for InvoiceComments.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	arrInvoiceComments	the invoice comments array
	 * */
	public function setInvoiceComments($arrInvoiceComments)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObjectArray($arrInvoiceComments, "InvoiceComment"))
			throw new InvoiceException("Invalid Invoice Comments", InvoiceException::INVALID_INVOICE_COMMENTS);

		$this->arrElements["Comentarii"] = $arrInvoiceComments;

		restore_error_handler();
	}

	/*
	 * Adds a comment to the invoice comments array.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	oInvoiceComment		the invoice comment object
	 * */
	public function addInvoiceComment($oInvoiceComment)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkObject($oInvoiceComment, "InvoiceComment"))
			throw new InvoiceException("Invalid Invoice Comment", InvoiceException::INVALID_INVOICE_COMMENT);

		$this->arrElements["Comentarii"][] = $oInvoiceComment;

		restore_error_handler();
	}
}
