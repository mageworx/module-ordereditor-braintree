<?php
declare(strict_types=1);

namespace MageWorx\OrderEditorBraintree\Plugin;

use MageWorx\OrderEditorBraintree\Helper\Data as Helper;
use PayPal\Braintree\Gateway\Helper\SubjectReader;

class GatewayConfigCanVoidHandler
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     * @param Helper $helper
     */
    public function __construct(
        SubjectReader $subjectReader,
        Helper        $helper
    ) {
        $this->subjectReader = $subjectReader;
        $this->helper        = $helper;
    }

    /**
     * Forced use of vault when order edited with braintree paypal and vault is available
     *
     * @param \Magento\Payment\Gateway\Config\ValueHandlerInterface $handler
     * @param bool $result
     * @param array $subject
     * @param $storeId
     * @return bool
     */
    public function afterHandle(
        \Magento\Payment\Gateway\Config\ValueHandlerInterface $handler,
        bool                                                  $result,
        array                                                 $subject,
                                                              $storeId = null
    ): bool {
        $paymentDO = $this->subjectReader->readPayment($subject);
        $payment   = $paymentDO->getPayment();

        if ($this->helper->isEnabled()) {
            if (!$result
                && $payment->getAdditionalInformation('mw_reauthorization_required')
                && $payment->getAdditionalInformation('mw_use_vault')
            ) {
                return true;
            }
        }

        return $result;
    }
}
