<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\OrderEditorBraintree\Gateway\Command;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use PayPal\Braintree\Gateway\Command\CaptureStrategyCommand as OriginalCaptureStrategyCommand;
use PayPal\Braintree\Gateway\Helper\SubjectReader;
use PayPal\Braintree\Model\Adapter\BraintreeAdapter;
use PayPal\Braintree\Model\Adapter\BraintreeSearchAdapter;
use MageWorx\OrderEditorBraintree\Helper\Data as Helper;

class CaptureStrategyCommand extends OriginalCaptureStrategyCommand implements CommandInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var CommandPoolInterface
     */
    protected $_commandPool;

    /**
     * @var SubjectReader
     */
    protected $_subjectReader;

    /**
     * @param CommandPoolInterface $commandPool
     * @param TransactionRepositoryInterface $repository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SubjectReader $subjectReader
     * @param BraintreeAdapter $braintreeAdapter
     * @param BraintreeSearchAdapter $braintreeSearchAdapter
     */
    public function __construct(
        CommandPoolInterface           $commandPool,
        TransactionRepositoryInterface $repository,
        FilterBuilder                  $filterBuilder,
        SearchCriteriaBuilder          $searchCriteriaBuilder,
        SubjectReader                  $subjectReader,
        BraintreeAdapter               $braintreeAdapter,
        BraintreeSearchAdapter         $braintreeSearchAdapter,
        Helper                         $helper
    ) {
        parent::__construct(
            $commandPool,
            $repository,
            $filterBuilder,
            $searchCriteriaBuilder,
            $subjectReader,
            $braintreeAdapter,
            $braintreeSearchAdapter
        );
        $this->_commandPool   = $commandPool;
        $this->_subjectReader = $subjectReader;
        $this->helper         = $helper;
    }

    /**
     * @inheritdoc
     * @throws NotFoundException
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $this->_subjectReader->readPayment($commandSubject);
        /** @var OrderPaymentInterface $paymentInfo */
        $paymentInfo = $paymentDO->getPayment();

        $useVault            = $paymentInfo->getAdditionalInformation('mw_use_vault');
        $needReauthorization = $paymentInfo->getAdditionalInformation('mw_reauthorization_required');
        if ($this->helper->isEnabled() && $needReauthorization && $useVault) {
            $command = self::VAULT_CAPTURE;
            $this->_commandPool->get($command)->execute($commandSubject);
        } else {
            parent::execute($commandSubject);
        }
    }
}
