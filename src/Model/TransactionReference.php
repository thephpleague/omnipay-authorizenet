<?php

namespace Omnipay\AuthorizeNet\Model;

/**
 * Unlike most other gateways, Authorize.Net sometimes requires more than just the transaction ID to perform operations
 * on a prior transaction. For example, performing a refund requires the original transaction ID as well as some credit
 * card details used in the original transaction.
 *
 * Rather than requiring these additional parameters for subsequent operations, we create a composite transaction
 * reference containing the transaction ID as well as certain additional parameters. This class is an object-oriented
 * representation of that composite key that can be easily serialized and de-serialized.
 */
class TransactionReference
{
    private $transId;
    private $approvalCode;
    private $card;
    /** @var CardReference */
    private $cardReference;

    public function __construct($data = null)
    {
        if ($data) {
            $data = json_decode($data);
            if (isset($data->transId)) {
                $this->transId = $data->transId;
            }
            if (isset($data->approvalCode)) {
                $this->approvalCode = $data->approvalCode;
            }
            if (isset($data->card)) {
                $this->card = $data->card;
            }
            if (isset($data->cardReference)) {
                $this->cardReference = new CardReference($data->cardReference);
            }
        }
    }

    public function __toString()
    {
        $data = array();
        if (isset($this->approvalCode)) {
            $data['approvalCode'] = $this->approvalCode;
        }
        if (isset($this->transId)) {
            $data['transId'] = $this->transId;
        }
        if (isset($this->card)) {
            $data['card'] = $this->card;
        }
        if (isset($this->cardReference)) {
            $data['cardReference'] = (string)$this->cardReference;
        }
        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getTransId()
    {
        return $this->transId;
    }

    /**
     * @param string $transId
     */
    public function setTransId($transId)
    {
        $this->transId = $transId;
    }

    /**
     * @return string
     */
    public function getApprovalCode()
    {
        return $this->approvalCode;
    }

    /**
     * @param string $approvalCode
     */
    public function setApprovalCode($approvalCode)
    {
        $this->approvalCode = $approvalCode;
    }

    /**
     * @return object
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @param array $card
     */
    public function setCard($card)
    {
        $this->card = $card;
    }

    /**
     * @return CardReference
     */
    public function getCardReference()
    {
        return $this->cardReference;
    }

    /**
     * @param string|CardReference $cardReference
     */
    public function setCardReference($cardReference)
    {
        $this->cardReference = $cardReference;
    }
}
