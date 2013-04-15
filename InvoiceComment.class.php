<?php

require_once("InvoiceException.class.php");
require_once("InvoiceElement.class.php");
require_once("InvoiceValidator.class.php");


/*
 * Invoice Comment class
 * */
class InvoiceComment extends InvoiceElement
{
	/*
	 * Possible invoice elements.
	 * */
	protected $arrElements = array(
		"Continut" => NULL
	);

	/*
	 * Required invoice elements.
	 * */
	protected $arrRequiredElements = array(
		"Continut"
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

		/* Create invoice structure defined by arrElements */
		foreach($arrElements as $strKey => $mixedValue)
		{
			switch($strKey)
			{
				case "Continut":
					$this->setCommentContent($mixedValue);
					break;

				default:
					/* Ignore invalid parts of the array */
					break;
			}
		}

		restore_error_handler();
	}

	/*
	 * Getter function for CommentContent.
	 *
	 * @return	the comment content
	 * */
	public function getCommentContent()
	{
		return $this->arrElements["Continut"];
	}

	/*
	 * Setter function for CommentContent.
	 *
	 * @throws	InvoiceException	if the provided parameter is not valid throw a proper exception
	 * @param	strCommentContent	the comment content as a string
	 * */
	public function setCommentContent($strCommentContent)
	{
		set_error_handler("InvoiceException::error_handler");

		if(!InvoiceValidator::checkString($strCommentContent))
			throw new InvoiceException("Invalid CommentContent", InvoiceException::INVALID_COMMENT_CONTENT);

		$this->arrElements["Continut"] = $strCommentContent;

		restore_error_handler();
	}
}
