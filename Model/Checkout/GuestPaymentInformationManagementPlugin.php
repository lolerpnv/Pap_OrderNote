<?php
namespace Pap\OrderNote\Model\Checkout;

use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Pap\OrderNote\Model\OrderNote;
use Pap\OrderNote\Model\OrderNoteFactory;
use Magento\Sales\Model\ResourceModel\Grid;

class GuestPaymentInformationManagementPlugin
{
	/**
     * @var OrderNoteFactory $orderFactory
     */
	protected $orderNoteFactory;

    /**
     * @param OrderNoteFactory $orderNoteFactory
     */
    public function __construct(
		OrderNoteFactory $orderNoteFactory
    ) {
		$this->orderNoteFactory = $orderNoteFactory;
    }

    /**
     * @param GuestPaymentInformationManagement $subject
     * @param $result
     * @param int $cartId
     * @param $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface $billingAddress
     *
     * @return int $orderId
     * @throws \Exception
     */
    public function afterSavePaymentInformationAndPlaceOrder(
		GuestPaymentInformationManagement $subject,
		$result,
        $cartId,
		$email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $note = $paymentMethod->getAdditionalData()['order_note'];
        /** @var OrderNote $orderNote */
        $orderNote = $this->orderNoteFactory->create();

        $orderNote->setNote($note);
        $orderNote->setOrderId($result);

        $orderNote->save();

        return $result;
    }
}