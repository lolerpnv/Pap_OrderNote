<?php

namespace Pap\OrderNote\Model\Plugin\Quote\Cart;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;
use Magento\Customer\Model\Session;
use Magento\Quote\Model\Cart\ShippingMethod;
use Magento\Store\Model\ScopeInterface;

class ShippingMethodConverter
{

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfiguration;

    /**
     * @var ShippingMethodExtensionFactory
     */
    private $shippingMethodFactory;

    /**
     * ShippingMethodConverter constructor.
     *
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfiguration
     */
    public function __construct(
        Session $customerSession,
        ScopeConfigInterface $scopeConfiguration,
        ShippingMethodExtensionFactory $shippingMethodFactory
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfiguration = $scopeConfiguration;
        $this->shippingMethodFactory = $shippingMethodFactory;
    }

    /**
     * Plugin for adding additional attributes for shipping method data API provider
     *
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface $subject
     * @param $result
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface Shipping method data object.
     */
    public function afterModelToDataObject($subject, $result)
    {
        $data = [];

        // check customer note
        $customer = $this->customerSession->getCustomer();
        if($customer->getCustomerNote() !== null) {
            $data[] = $customer->getCustomerNote();
        }

        // get defaults
        $defaults = $this->scopeConfiguration->getValue(
            'customer/customer_note/notes',
            ScopeInterface::SCOPE_STORE
        );
        $defaults = explode(PHP_EOL,$defaults);

        $data = array_merge($data,$defaults);

        /** @var ShippingMethod $result */
        $shippingMethodExtension = $this->shippingMethodFactory->create();
        $shippingMethodExtension->setData('customer_notes',$data);

        $result->setExtensionAttributes($shippingMethodExtension);
        return $result;
    }
}
