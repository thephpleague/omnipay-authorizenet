# Omnipay: Authorize.Net

**Authorize.Net driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/thephpleague/omnipay-authorizenet.png?branch=master)](https://travis-ci.org/thephpleague/omnipay-authorizenet)
[![Latest Stable Version](https://poser.pugx.org/omnipay/authorizenet/version.png)](https://packagist.org/packages/omnipay/authorizenet)
[![Total Downloads](https://poser.pugx.org/omnipay/authorizenet/d/total.png)](https://packagist.org/packages/omnipay/authorizenet)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Authorize.Net support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `league/omnipay` and `omnipay/authorizenet` with Composer:

```
composer require league/omnipay omnipay/authorizenet:"3.x@dev"
```

## Basic Usage

The following gateways are provided by this package:

* AuthorizeNet_AIM
* AuthorizeNet_CIM
* AuthorizeNet_SIM
* AuthorizeNet_DPM

In addition, `Accept.JS` is supported by the AIM driver. More details are provided below.

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Accept.JS

This gateway uses a JavaScript script to tokenize credit card details at the front end,
i.e. in the payment form.
Just the tokenized version of the credit card is then sent back to the merchant site,
where it is used as a proxy for the credit card.

The card is tokenized into two values returned in `opaqueData` object from Accept.JS:

* dataDescriptor - the type of opaque data, e.g. "COMMON.ACCEPT.INAPP.PAYMENT"
* dataValue - the value for the opaque data, e.g. "eyJjb2RlIjoiNT... {256 characters} ...idiI6IjEuMSJ9"

These two values must be POSTed back to the merchant application, usually as a part of the payment form.
Make sure the raw credit card details are NOT posted back to your site.
How this is handled is beyond this short note, but examples are always welcome in the documentation.

On the server, the tokenized detailt are passed into the `payment` or `authorize` request object.
You will still need to pass in the `CreditCard` object, as that contains details of the payee and
recipient, but just leave the credit card details of that object blank. For example:

```php
// $gateway is an instantiation of the AIM driver.
// $dataDescriptor and $dataValue come from the paymentr form at the front end.

$request = $gateway->purchase(
    [
        'notifyUrl' => '...',
        'amount' => $amount,
        'opaqueDataDescriptor' => $dataDescriptor,
        'opaqueDataValue' => $dataValue,
        ...
    ]
);
```

## DPM and SIM Signatures

DPM and SIM used to sign their requests with the `transactionKey` using the mdh HMAC algorithm.
From early 2019, this algorithm is being removed completely.
Instead, the SHA-512 HMAC algorithm is used to sign the DPM and SIM requsts,
and to validate the received  notifications.

To start using the SHA-512 signing, set your `signatureKey` in the gateway:

```php
$gateway->setSignatureKey('48D2C629E4A...{100}...E7CA3C4E6CD7223D');
```

The `signatureKey` can be generated in the *API Credentials & Keys* section of your account setings.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/thephpleague/omnipay-authorizenet/issues),
or better yet, fork the library and submit a pull request.
