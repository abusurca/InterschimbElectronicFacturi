<?php

require_once("InvoiceException.class.php");
require_once("InvoiceValidator.class.php");


/*
 * Invoice Element class.
 *
 * All the invoice elements inherit this class including the invoice itself.
 * */
class InvoiceElement
{
	/*
	 * Invoice element options.
	 * */
	protected $arrOptions = array(
		"allow_multiple_exceptions" => true
	);

	/*
	 * Possible invoice elements.
	 * */
	protected $arrElements = array();

	/*
	 * Required invoice elements.
	 * */
	protected $arrRequiredElements = array();


	/*
	 * Class constructor.
	 *
	 * @throws	InvoiceException	when arrElements is provided and contains invalid values a proper exception is thrown
	 * @param	arrElements			array from which the invoice is constructed
	 * @param	arrOptions			array which contains flags that enable various validations and options
	 * */
	protected function __construct($arrChildValidationFlags = array(), $arrOptions = array())
	{
		if(!is_array($arrOptions))
			throw new InvoiceException("Invalid arguments", InvoiceException::INVALID_ARGUMENTS);

		/* Set the possible options pool */
		$this->arrOptions = array_merge($this->arrOptions, $arrChildValidationFlags);

		/* Set the valid option values */
		foreach($this->arrOptions as $strKey => &$mixedValue)
			if(isset($arrOptions[$strKey]) && is_bool($arrOptions[$strKey]))
				$mixedValue = $arrOptions[$strKey];
	}

	/*
	 * Validates an invoice element.
	 *
	 * @throws	InvoiceException	if the invoice element is not valid throws a proper exception
	 * */
	protected function validate()
	{
		/* Check if the required fields are empty */
		foreach($this->arrRequiredElements as $strRequiredElement)
		{
			if(is_null($this->arrElements[$strRequiredElement])	|| (is_array($this->arrElements[$strRequiredElement])
				&& !count($this->arrElements[$strRequiredElement]))
			)
				throw new InvoiceException($strRequiredElement." is empty", InvoiceException::EMPTY_FIELD);
		}
	}

	/*
	 * Gets the content of the invoice element as an array.
	 *
	 * @return	the content of the invoice element as an array
	 * */
	protected function get()
	{
		/* Clone the arrElements array */
		$arrClonedElements = array_merge($this->arrElements);

		/* Iterate through the cloned array and update certain values */
		foreach($arrClonedElements as $strKey => &$mixedValue)
		{
			/* If field is optional and it is empty, delete it */
			if(!in_array($strKey, $this->arrRequiredElements)
				&& ((is_array($mixedValue) && !count($mixedValue))
				|| is_null($mixedValue))
			)
				unset($arrClonedElements[$strKey]);
			/* If the field is an array empty. It will be repopulated */
			else if(is_array($mixedValue))
				$mixedValue = array();
		}

		return $arrClonedElements;
	}

	/*
	 * Computes and returns the json of the invoice element.
	 *
	 * @return	the json of the invoice element as a string
	 * */
	public function json_encode()
	{
		set_error_handler("InvoiceException::error_handler");

		/* Validate the invoice element */
		$this->validate();

		/* Get, encode and return the content */
		return json_encode($this->get());

		restore_error_handler();
	}
}
