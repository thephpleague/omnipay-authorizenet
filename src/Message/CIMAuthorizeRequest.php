<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Model\CardReference;

/**
 * Creates a Authorize only transaction request for the specified customer profile
 */
class CIMAuthorizeRequest extends AIMAuthorizeRequest
{
    protected function addPayment(\SimpleXMLElement $data)
    {
        $this->validate('cardReference');

        /** @var mixed $req */
        $req = $data->transactionRequest;

        /** @var CardReference $cardRef */
        $cardRef = $this->getCardReference(false);

        $req->profile->customerProfileId = $cardRef->getCustomerProfileId();

        $req->profile->paymentProfile->paymentProfileId = $cardRef->getPaymentProfileId();

        if ($shippingProfileId = $cardRef->getShippingProfileId()) {
            $req->profile->shippingProfileId = $shippingProfileId;
        }

        $invoiceNumber = $this->getInvoiceNumber();
        if (!empty($invoiceNumber)) {
            $req->order->invoiceNumber = $invoiceNumber;
        }

        $description = $this->getDescription();
        if (!empty($description)) {
            $req->order->description = $description;
        }

        return $data;
    }

    protected function addBillingData(\SimpleXMLElement $data)
    {
        // Do nothing since billing information is already part of the customer profile
        return $data;
    }
}
