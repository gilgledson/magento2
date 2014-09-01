<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Catalog\Helper\Product;

/**
 * Class ProductList
 */
class ProductList
{
    /**
     * List mode configuration path
     */
    const XML_PATH_LIST_MODE = 'catalog/frontend/list_mode';

    const VIEW_MODE_LIST = 'view';
    const VIEW_MODE_GRID = 'grid';

    const DEFAULT_SORT_DIRECTION = 'asc';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Default limits per page
     *
     * @var array
     */
    protected $_defaultAvailableLimit  = array(10=>10,20=>20,50=>50);

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns available mode for view
     *
     * @return array|null
     */
    public function getAvailableViewMode()
    {
        switch ($this->scopeConfig->getValue(self::XML_PATH_LIST_MODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            case 'grid':
                $availableMode = array('grid' => __('Grid'));
                break;

            case 'list':
                $availableMode = array('list' => __('List'));
                break;

            case 'grid-list':
                $availableMode = array('grid' => __('Grid'), 'list' =>  __('List'));
                break;

            case 'list-grid':
                $availableMode = array('list' => __('List'), 'grid' => __('Grid'));
                break;
            default:
                $availableMode = null;
                break;
        }
        return $availableMode;
    }

    /**
     * Returns default view mode
     *
     * @param array $options
     * @return string
     */
    public function getDefaultViewMode($options = array())
    {
        if (empty($options)) {
            $options = $this->getAvailableViewMode();
        }
        return current(array_keys($options));
    }

    /**
     * Get default sort field
     *
     * @return null|string
     */
    public function getDefaultSortField()
    {
        return $this->scopeConfig->getValue(
            \Magento\Catalog\Model\Config::XML_PATH_LIST_DEFAULT_SORT_BY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve available limits for specified view mode
     *
     * @param string $mode
     * @return array
     */
    public function getAvailableLimit($mode)
    {
        if (!in_array($mode, array(self::VIEW_MODE_GRID, self::VIEW_MODE_LIST))) {
            return $this->_defaultAvailableLimit;
        }
        $perPageConfigKey = 'catalog/frontend/' . $mode . '_per_page_values';
        $perPageValues = (string)$this->scopeConfig->getValue(
            $perPageConfigKey,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        if ($this->scopeConfig->isSetFlag(
            'catalog/frontend/list_allow_all',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return ($perPageValues + array('all'=>__('All')));
        } else {
            return $perPageValues;
        }
    }

    /**
     * Retrieve default per page values
     *
     * @param string $viewMode
     * @return string (comma separated)
     */
    public function getDefaultLimitPerPageValue($viewMode)
    {
        if ($viewMode == self::VIEW_MODE_LIST) {
            return $this->scopeConfig->getValue(
                'catalog/frontend/list_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($viewMode == self::VIEW_MODE_GRID) {
            return $this->scopeConfig->getValue(
                'catalog/frontend/grid_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return 0;
    }
}