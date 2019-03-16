<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * SIM and DPM both have identical needs when handling the notify request.
 */
class DPMCompleteResponse extends SIMCompleteResponse
{
}
