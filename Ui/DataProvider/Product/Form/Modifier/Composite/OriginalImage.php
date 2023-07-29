<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perspective\ImageAiVariations\Ui\DataProvider\Product\Form\Modifier\Composite;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form;
use Perspective\ImageAiVariations\Ui\DataProvider\Product\Form\Modifier\Composite;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OriginalImage extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function modifyMeta(array $meta)
    {
        $originalImagePath = Composite::CHILDREN_PATH . '/' . Composite::CONTAINER_ORIGINAL_IMAGE;
        $originalImageContainer['arguments']['data']['config'] = [
            'componentType' => Form\Fieldset::NAME,
            'additionalClasses' => 'admin__fieldset-section',
            'label' => __('Original Image'),
            'dataScope' => '',
            'visible' => true,
            'sortOrder' => 30,
        ];

        $promptComponent['arguments']['data']['config'] = [
            'componentType' => Form\Field::NAME,
            'formElement' => Form\Element\Input::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'label' => __('Write the prompt to an AI'),
            'dataScope' => 'product.ai_vars.promptComponent',
        ];

        $imageField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => \Magento\Ui\Component\Form\Element\DataType\Text::NAME,
            'component' => 'Perspective_ImageAiVariations/js/components/original-image',
            'elementTmpl' => 'Perspective_ImageAiVariations/form/element/canvas',
            'dataScope' => 'product.ai_vars.original-image',
            'labelVisible' => false,
        ];
        $originalImageContainer = $this->arrayManager->set(
            'children',
            $originalImageContainer,
            [
                'promptComponent' => $promptComponent,
                'imageField' => $imageField
            ]
        );
        return $this->arrayManager->set($originalImagePath, $meta, $originalImageContainer);
    }
}
