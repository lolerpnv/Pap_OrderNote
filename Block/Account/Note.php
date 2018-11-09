<?php

namespace Pap\OrderNote\Block\Account;

use Magento\Customer\Model\ResourceModel\Customer\Interceptor;
use Magento\Customer\Model\Session;
use Magento\Directory\Block\Data;
use Magento\Directory\Helper\Data as DirHelper;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Note
 *
 * @package Note
 */
class Note extends Data
{
    /**
     * @var Interceptor
     */
    private $customerInterceptor;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        Context $context,
        DirHelper $directoryHelper,
        EncoderInterface $jsonEncoder,
        Config $configCacheType,
        CollectionFactory $regionCollectionFactory,
        CountryCollectionFactory $countryCollectionFactory,
        Session $customerSession,
        Interceptor $customerInterceptor,
        array $data = []
    ) {
        $this->customerInterceptor = $customerInterceptor;
        $this->customerSession = $customerSession;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl(
            'note/account/save'
        );
    }

    /**
     * @return string
     */
    public function getCurrentNote()
    {
        $customer = $this->customerSession->getCustomer();

        return $customer->getCustomerNote();
    }
}