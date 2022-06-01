<?php

namespace MageWorx\OrderEditorBraintree\Test\Unit\Model\Invoice\PaymentMethodProcessor\BraintreePayPalProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class IsReuathorizationRequiredTest extends \PHPUnit\Framework\TestCase
{
    const PROCESSOR_CLASS = '\MageWorx\OrderEditorBraintree\Model\Invoice\PaymentMethodProcessor\BraintreePayPalProcessor';

    /**
     * @var \MageWorx\OrderEditorBraintree\Model\Invoice\PaymentMethodProcessor\BraintreePayPalProcessor
     */
    protected $processor;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $orderMock;

    /**
     * @var \Magento\Sales\Api\Data\InvoiceInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $invoiceMock;

    /**
     * @var \Magento\Sales\Api\Data\OrderPaymentInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $paymentMock;

    /**
     * @var string
     */
    protected $paymentMethod;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->orderMock = $this->createMock('Magento\Sales\Api\Data\OrderInterface');
        $this->invoiceMock = $this->createMock('Magento\Sales\Api\Data\InvoiceInterface');
        $this->paymentMock = $this->createMock('Magento\Sales\Api\Data\OrderPaymentInterface');
        $this->paymentMethod = 'braintree_paypal';

        $this->orderMock->expects($this->once())->method('getPayment')
                               ->willReturn($this->paymentMock);

        $this->processor = $objectManager->getObject(
            self::PROCESSOR_CLASS,
            [
                'order' => $this->orderMock,
                'invoice' => $this->invoiceMock,
                'payment' => $this->paymentMethod
            ]
        );
    }

    /**
     * Estimate without product id in request will throw exception
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testPositiveIsReauthorizationRequired()
    {
        $this->processor->isReauthorizationRequired();
    }
}
