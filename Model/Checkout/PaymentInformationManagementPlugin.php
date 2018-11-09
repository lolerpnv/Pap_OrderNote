<?php
namespace Pap\OrderNote\Model\Checkout;

use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Model\ResourceModel\Grid;
use Pap\OrderNote\Model\OrderNote;
use Pap\OrderNote\Model\OrderNoteFactory;

class PaymentInformationManagementPlugin
{
	/** @var OrderNoteFactory $orderFactory */
	private $orderNoteFactory;

    /**
     * PaymentInformationManagementPlugin constructor.
     * @param OrderNoteFactory $orderNoteFactory
     */
    public function __construct(
        OrderNoteFactory $orderNoteFactory
    ) {
		$this->orderNoteFactory = $orderNoteFactory;
    }

    /**
     * @param PaymentInformationManagement $subject
     * @param $result
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface $billingAddress
     *
     * @return void $orderId
     * @throws \Exception
     */
    public function afterSavePaymentInformationAndPlaceOrder(
		PaymentInformationManagement $subject,
		$result,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
		$note = $paymentMethod->getAdditionalData()['order_note'];
		/** @var OrderNote $orderNote */
		$orderNote = $this->orderNoteFactory->create();

		$orderNote->setNote($note);
		$orderNote->setOrderId($result);

		$orderNote->setNote($note);

		$orderNote->save();

		return $result;
    }
}