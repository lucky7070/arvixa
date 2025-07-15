(function (factory) {
	if (typeof define === "function" && define.amd) {
		define(["jquery", "./jquery.validate"], factory);
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory(require("jquery"));
	} else {
		factory(jQuery);
	}
}(function ($) {

	(function () {
		function stripHtml(value) {
			// Remove html tags and space chars
			return value.replace(/<.[^<>]*?>/g, " ").replace(/&nbsp;|&#160;/gi, " ")
				// Remove punctuation
				.replace(/[.(),;:!?%#$'\"_+=\/\-“”’]*/g, "");
		}
		$.validator.addMethod("maxWords", function (value, element, params) {
			return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length <= params;
		}, $.validator.format("Please enter {0} words or less."));

		$.validator.addMethod("minWords", function (value, element, params) {
			return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length >= params;
		}, $.validator.format("Please enter at least {0} words."));

		$.validator.addMethod("rangeWords", function (value, element, params) {
			var valueStripped = stripHtml(value),
				regex = /\b\w+\b/g;
			return this.optional(element) || valueStripped.match(regex).length >= params[0] && valueStripped.match(regex).length <= params[1];
		}, $.validator.format("Please enter between {0} and {1} words."));

	}());

	$.validator.addMethod("currency", function (value, element, param) {
		var isParamString = typeof param === "string",
			symbol = isParamString ? param : param[0],
			soft = isParamString ? true : param[1],
			regex;

		symbol = symbol.replace(/,/g, "");
		symbol = soft ? symbol + "]" : symbol + "]?";
		regex = "^[" + symbol + "([1-9]{1}[0-9]{0,2}(\\,[0-9]{3})*(\\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\\.[0-9]{0,2})?|0(\\.[0-9]{0,2})?|(\\.[0-9]{1,2})?)$";
		regex = new RegExp(regex);
		return this.optional(element) || regex.test(value);
	}, "Please specify a valid currency");

	// Older "accept" file extension method. Old docs: http://docs.jquery.com/Plugins/Validation/Methods/accept
	$.validator.addMethod("extension", function (value, element, param) {
		param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
		return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
	}, $.validator.format("Please enter a value with a valid extension."));

	$.validator.addMethod('filesize', function (value, element, param) {
		return this.optional(element) || ((element.files[0].size / 1024 / 1024) <= param)
	}, 'File size must be less than {0}mb');

	$.validator.addMethod("integer", function (value, element) {
		return this.optional(element) || /^-?\d+$/.test(value);
	}, "A positive or negative non-decimal number please");

	$.validator.addMethod("ipv4", function (value, element) {
		return this.optional(element) || /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(value);
	}, "Please enter a valid IP v4 address.");

	$.validator.addMethod("ipv6", function (value, element) {
		return this.optional(element) || /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(value);
	}, "Please enter a valid IP v6 address.");

	$.validator.addMethod("lettersonly", function (value, element) {
		return this.optional(element) || /^[a-z]+$/i.test(value);
	}, "Letters only please");

	$.validator.addMethod("notEqualTo", function (value, element, param) {
		return this.optional(element) || !$.validator.methods.equalTo.call(this, value, element, param);
	}, "Please enter a different value, values must not be the same.");

	$.validator.addMethod("nowhitespace", function (value, element) {
		return this.optional(element) || /^\S+$/i.test(value);
	}, "No white space please");

	$.validator.addMethod("exactlength", function (value, element, param) {
		return this.optional(element) || value.length == param;
	}, $.validator.format("Please enter exactly {0} characters/digits."));

	$.validator.addMethod("aadharcard", function (value, element) {
		var regexp = new RegExp(/^[2-9]{1}[0-9]{3}[0-9]{4}[0-9]{4}$/);
		// var regexp = new RegExp(/^[2-9]{1}[0-9]{3}\s[0-9]{4}\s[0-9]{4}$/);
		return this.optional(element) || regexp.test(value);
	}, "Invalid AadharCard Number.");

	$.validator.addMethod("pancard", function (value, element) {
		var regexp = new RegExp(/[A-Z]{5}[0-9]{4}[A-Z]{1}$/);
		return this.optional(element) || regexp.test(value);
	}, "Invalid PanCard Number.");

	$.validator.addMethod("customEmail", function (value, element) {
		var regexp = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		return this.optional(element) || regexp.test(value);
	}, "Invalid Email Address.");

	$.validator.addMethod("indiaMobile", function (value, element) {
		var regexp = new RegExp(/^(?:(?:\+|0{0,2})91(\s*|[-])?|[0]?)?([6789]\d{2}([ -]?)\d{3}([ -]?)\d{4})$/);
		return this.optional(element) || regexp.test(value);
	}, "Please provide valid indian mobile number.");

	$.validator.addMethod("upiId", function (value, element) {
		var regexp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+$/;
		return this.optional(element) || regexp.test(value);
	}, "Please provide a valid UPI ID (e.g., username@upi)");

	$.validator.addMethod("ifsc", function (value, element) {
		var regexp = new RegExp(/^[A-Za-z]{4}0[A-Z0-9a-z]{6}$/);
		return this.optional(element) || regexp.test(value);
	}, "Please provide valid IFSC code.");

	$.validator.addMethod("maxDate", function (value, element) {
		var curDate = new Date();
		var inputDate = new Date(value);
		if (inputDate < curDate)
			return true;
		return false;
	}, "Invalid Date!");

	$.validator.addMethod("minDate", function (value, element) {
		var curDate = new Date('1901-01-01');
		var inputDate = new Date(value);
		if (inputDate > curDate)
			return true;
		return false;
	}, "Invalid Date!");

	/**
	* Return true if the field value matches the given format RegExp
	*
	* @example $.validator.methods.pattern("AR1004",element,/^AR\d{4}$/)
	* @result true
	*
	* @example $.validator.methods.pattern("BR1004",element,/^AR\d{4}$/)
	* @result false
	*
	* @name $.validator.methods.pattern
	* @type Boolean
	* @cat Plugins/Validate/Methods
	*/
	$.validator.addMethod("pattern", function (value, element, param) {
		if (this.optional(element)) {
			return true;
		}
		if (typeof param === "string") {
			param = new RegExp("^(?:" + param + ")$");
		}
		return param.test(value);
	}, "Invalid format.");


	// TODO check if value starts with <, otherwise don't try stripping anything
	$.validator.addMethod("strippedminlength", function (value, element, param) {
		return $(value).text().length >= param;
	}, $.validator.format("Please enter at least {0} characters"));

	$.validator.addMethod("time", function (value, element) {
		return this.optional(element) || /^([01]\d|2[0-3]|[0-9])(:[0-5]\d){1,2}$/.test(value);
	}, "Please enter a valid time, between 00:00 and 23:59");

	$.validator.addMethod("time12h", function (value, element) {
		return this.optional(element) || /^((0?[1-9]|1[012])(:[0-5]\d){1,2}(\ ?[AP]M))$/i.test(value);
	}, "Please enter a valid time in 12-hour am/pm format");

	// Same as url, but TLD is optional
	$.validator.addMethod("url2", function (value, element) {
		return this.optional(element) || /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
	}, $.validator.messages.url);

	$.validator.addMethod("step", function (value, element, param) {
		if (this.optional(element)) return true;
		var step = param || 1;
		var val = parseFloat(value);

		// Handle floating point precision issues
		return Math.abs((val / step) % 1) < 0.000001 ||
			Math.abs((val / step) % 1 - 1) < 0.000001;
	}, "Please enter a valid step value");

	return $;
}));