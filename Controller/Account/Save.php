<?php

namespace Pap\OrderNote\Controller\Account;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\ResourceModel\Customer\Interceptor as CustomerInterceptor;

/**
 * Class Save
 *
 * @package Save
 */
class Save extends AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerInterceptor
     */
    protected $interceptor;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param CustomerInterceptor $interceptor
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        CustomerInterceptor $interceptor
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->interceptor = $interceptor;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     */
    public function execute()
    {

        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_buildUrl('note/account'))
            );
        }

        $note = $this->_request->getParam('note');
        $customer = $this->customerSession->getCustomer();

        $customer->setData('customer_note',$note);
        $this->interceptor->saveAttribute($customer,'customer_note');

        return $this->resultRedirectFactory->create()->setUrl(
        $this->_redirect->error($this->_buildUrl('note/account')));
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _buildUrl($route = '', $params = [])
    {
        return $this->_url->getUrl($route, $params);
    }
}