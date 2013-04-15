<?php

/*
 * Invoice Validator class.
 *
 * It contains field validation methods.
 * */
class InvoiceValidator
{
	/*
	 * Validates a string field.
	 *
	 * @param	strString	the variable to be validated
	 * @param	bAllowEmpty	flag the specifies if the string is allowed to be empty
	 * @return	true/false if it is valid or not
	 * */
	public static function checkString($strString, $bAllowEmpty = false)
	{
		if(!is_string($strString))
			return false;
		if(!$bAllowEmpty && !strlen($strString))
			return false;

		return true;
	}

	/*
	 * Validates a number field.
	 *
	 * @param	nNumber		the variable to be validated
	 * @param	bAllowNull	flag the specifies if the number is allowed to be null
	 * @param	bIsUnsigned	flag the specifies if the number should be unsigned or not
	 * @return	true/false if it is valid or not
	 * */
	public static function checkNumber($nNumber, $bAllowNull = true, $bIsUnsigned = false)
	{
		if(!is_numeric($nNumber))
			return false;
		if(!$bAllowNull && !$nNumber)
			return false;
		if($bIsUnsigned && $nNumber < 0)
			return false;

		return true;
	}

	/*
	 * Validates a date field.
	 *
	 * @param	strDate	the variable to be validated
	 * @return	true/false if it is valid or not
	 * */
	public static function checkDate($strDate)
	{
		if(!InvoiceValidator::checkString($strDate))
			return false;

		static $strDateFormatRegex = "/^2[0-2][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/";
		if(!preg_match($strDateFormatRegex, $strDate))
			return false;

		$arrDate = explode("-", $strDate);
		if(!checkdate($arrDate[1], $arrDate[2], $arrDate[0]))
			return false;

		return true;
	}

	/*
	 * Validates a object field.
	 *
	 * @param	oObject			the variable to be validated
	 * @param	strClassName	the required class name of the object
	 * @return	true/false if it is valid or not
	 * */
	public static function checkObject($oObject, $strClassName)
	{
		if(!is_object($oObject))
			return false;
		if(get_class($oObject) != $strClassName)
			return false;

		return true;
	}

	/*
	 * Validates a object array field.
	 *
	 * @param	arrObjects		the variable to be validated
	 * @param	strClassName	the required class name of the objects
	 * @return	true/false if it is valid or not
	 * */
	public static function checkObjectArray($arrObjects, $strClassName)
	{
		if(!is_array($arrObjects))
			return false;

		foreach($arrObjects as $oObject)
			if(!InvoiceValidator::checkObject($oObject, $strClassName))
				return false;

		return true;
	}

	/*
	 * Validates a currency field.
	 *
	 * @param	strCurrencyCode			the variable to be validated
	 * @param	bValidateCurrencyCode	flag that specifies if the currency code should be validated against a list of known currencies
	 * @return	true/false if it is valid or not
	 * */
	public static function checkCurrency($strCurrencyCode, $bValidateCurrencyCode = false)
	{
		if(!InvoiceValidator::checkString($strCurrencyCode))
			return false;

		if(!$bValidateCurrencyCode)
			return true;

		static $arrCurrencyCodes = array(
			"AED", "AFN", "ALL", "AMD", "ANG", "AOA", "ARS", "AUD", "AWG", "AZN", "BAM", "BBD", "BDT", "BGN", "BHD",
			"BIF", "BMD", "BND", "BOB", "BOV", "BRL", "BSD", "BTN", "BWP", "BYR", "BZD", "CAD", "CDF", "CHE", "CHF",
			"CHW", "CLF", "CLP", "CNY", "COP", "COU", "CRC", "CUC", "CUP", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD",
			"EGP", "ERN", "ETB", "EUR", "FJD", "FKP", "GBP", "GEL", "GHS", "GIP", "GMD", "GNF", "GTQ", "GYD", "HKD",
			"HNL", "HRK", "HTG", "HUF", "IDR", "ILS", "INR", "IQD", "IRR", "ISK", "JMD", "JOD", "JPY", "KES", "KGS",
			"KHR", "KMF", "KPW", "KRW", "KWD", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD", "LSL", "LTL", "LVL", "LYD",
			"MAD", "MDL", "MGA", "MKD", "MMK", "MNT", "MOP", "MRO", "MUR", "MVR", "MWK", "MXN", "MXV", "MYR", "MZN",
			"NAD", "NGN", "NIO", "NOK", "NPR", "NZD", "OMR", "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR",
			"RON", "RSD", "RUB", "RWF", "SAR", "SBD", "SCR", "SDG", "SEK", "SGD", "SHP", "SLL", "SOS", "SRD", "SSP",
			"STD", "SYP", "SZL", "THB", "TJS", "TMT", "TND", "TOP", "TRY", "TTD", "TWD", "TZS", "UAH", "UGX", "USD",
			"USN", "USS", "UYI", "UYU", "UZS", "VEF", "VND", "VUV", "WST", "XAF", "XAG", "XAU", "XBA", "XBB", "XBC",
			"XBD", "XCD", "XDR", "XFU", "XOF", "XPD", "XPF", "XPT", /*"XTS", "XXX",*/ "YER", "ZAR", "ZMK", "ZWL",
		);

		return in_array($strCurrencyCode, $arrCurrencyCodes);
	}

	/*
	 * Validates a IBAN field.
	 *
	 * @param	strIBANCode			the variable to be validated
	 * @param	bValidateIBANCode	flag that specifies if the IBAN code should be validated using a standard algorithm
	 * @return	true/false if it is valid or not
	 * */
	public static function checkIBAN($strIBANCode, $bValidateIBANCode = false)
	{
		if(!InvoiceValidator::checkString($strIBANCode))
			return false;

		if(!$bValidateIBANCode)
			return true;

		$strIBANCode=strtoupper($strIBANCode);
		$strIBANCode=preg_replace("/[^0-9A-Z]/", "", $strIBANCode);

		if(!((bool)preg_match("/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4}[A-Z0-9]{9,16}$/", $strIBANCode)))
			return false;

		static $arrIBANByCountryRegex = array(
			"AD" => "/^\d{8}[A-Z0-9]{12}$/",
			"AL" => "/^\d{8}[A-Z0-9]{16}$/",
			"AT" => "/^\d{16}$/",
			"BA" => "/^\d{16}$/",
			"BE" => "/^\d{12}$/",
			"BG" => "/^[A-Z]{4}\d{6}[A-Z0-9]{8}$/",
			"CH" => "/^\d{5}[A-Z0-9]{12}$/",
			"CY" => "/^\d{8}[A-Z0-9]{16}$/",
			"CZ" => "/^\d{20}$/",
			"DE" => "/^\d{18}$/",
			"DK" => "/^\d{14}$/",
			"EE" => "/^\d{16}$/",
			"ES" => "/^\d{20}$/",
			"FI" => "/^\d{14}$/",
			"FO" => "/^\d{14}$/",
			"FR" => "/^\d{10}[A-Z0-9]{11}\d\d$/",
			"GB" => "/^[A-Z]{4}\d{14}$/",
			"GI" => "/^[A-Z]{4}[A-Z0-9]{15}$/",
			"GL" => "/^\d{14}$/",
			"GR" => "/^\d{7}[A-Z0-9]{16}$/",
			"HR" => "/^\d{17}$/",
			"HU" => "/^\d{24}$/",
			"IE" => "/^[A-Z]{4}\d{14}$/",
			"IL" => "/^\d{19}$/",
			"IS" => "/^\d{22}$/",
			"IT" => "/^[A-Z]\d{10}[A-Z0-9]{12}$/",
			"LB" => "/^\d{4}[A-Z0-9]{20}$/",
			"LI" => "/^\d{5}[A-Z0-9]{12}$/",
			"LT" => "/^\d{16}$/",
			"LU" => "/^\d{3}[A-Z0-9]{13}$/",
			"LV" => "/^[A-Z]{4}[A-Z0-9]{13}$/",
			"MC" => "/^\d{10}[A-Z0-9]{11}\d\d$/",
			"ME" => "/^\d{18}$/",
			"MK" => "/^\d{3}[A-Z0-9]{10}\d\d$/",
			"MT" => "/^[A-Z]{4}\d{5}[A-Z0-9]{18}$/",
			"MU" => "/^[A-Z]{4}\d{19}[A-Z]{3}$/",
			"NL" => "/^[A-Z]{4}\d{10}$/",
			"NO" => "/^\d{11}$/",
			"PL" => "/^\d{8}[A-Z0-9]{16}$/",
			"PT" => "/^\d{21}$/",
			"RO" => "/^[A-Z]{4}[A-Z0-9]{16}$/",
			"RS" => "/^\d{18}$/",
			"SA" => "/^\d{2}[A-Z0-9]{18}$/",
			"SE" => "/^\d{20}$/",
			"SI" => "/^\d{15}$/",
			"SK" => "/^\d{20}$/",
			"SM" => "/^[A-Z]\d{10}[A-Z0-9]{12}$/",
			"TN" => "/^\d{20}$/",
			"TR" => "/^\d{5}[A-Z0-9]{17}$/"
		);

		if(isset($arrIBANByCountryRegex[substr($strIBANCode, 0, 2)]))
			if(!((bool)preg_match($arrIBANByCountryRegex[substr($strIBANCode, 0, 2)], substr($strIBANCode, 4))))
				return false;

		static $arrIBANCountryCodes = array(
			"AD", "AL", "AT", "BA", "BE", "BG", "CH", "CY", "CZ", "DE", "DK", "EE", "ES", "FI", "FO", "FR", "GB",
			"GI", "GL", "GR", "HR", "HU", "IE", "IL", "IS", "IT", "LB" ,"LI" ,"LT", "LU", "LV", "MC", "ME", "MK",
			"MT", "MU", "NL", "NO", "PL", "PT" ,"RO", "RS", "SA", "SE", "SI", "SK", "SM", "TN", "TR"
		);
		if(!in_array(substr($strIBANCode, 0, 2), $arrIBANCountryCodes))
			return false;
		$strIBANCode=substr($strIBANCode, 4).substr($strIBANCode, 0, 4);

		$r = 0;
		for($i = 0; $i < strlen($strIBANCode); $i++)
		{
			$nCharacter = ord($strIBANCode[$i]);
			if(48 <= $nCharacter && $nCharacter <= 57)//0-9
			{
				//Positions 1 and 2 cannot contain digits
				if($i == strlen($strIBANCode) - 4 || $i == strlen($strIBANCode) - 3)
					return false;
				$k = $nCharacter-48;
			}
			else if(65 <= $nCharacter && $nCharacter <= 90)//A-Z
			{
				//Positions 3 and 4 cannot contain letters
				if($i == strlen($strIBANCode) - 2 || $i == strlen($strIBANCode) - 1)
					return false;
				$k = $nCharacter - 55;
			}
			if ($k > 9)
				$r = (100 * $r + $k) % 97;
			else
				$r = (10 * $r + $k) % 97;
		}

		return $r == 1;
	}
}
