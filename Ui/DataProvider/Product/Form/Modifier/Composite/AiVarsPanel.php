<?php

namespace Perspective\ImageAiVariations\Ui\DataProvider\Product\Form\Modifier\Composite;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;
use Perspective\ImageAiVariations\Ui\DataProvider\Product\Form\Modifier\Composite;

class AiVarsPanel extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(LocatorInterface $locator, ArrayManager $arrayManager)
    {
        $this->locator = $locator;
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
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $panelConfig['arguments']['data']['config'] = [
            'componentType' => Form\Fieldset::NAME,
            'label' => __('AI Image Variations'),
            'collapsible' => true,
            'opened' => true,
            'sortOrder' => '800',
            'dataScope' => 'data'
        ];
        $this->meta = $this->arrayManager->set('ai_vars', $this->meta, $panelConfig);

        $this->addMessageBox();

        return $this->meta;
    }

    /**
     * Add message
     *
     * @return void
     */
    protected function addMessageBox()
    {
        $messagePath = Composite::CHILDREN_PATH . '/ai_vars_message';
        $messageConfig['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/html',
            'additionalClasses' => 'admin__fieldset-note',
            'content' => __('Create AI Image Variations for this product.'),
            'sortOrder' => 20,
            'visible' => true,
        ];

        $this->meta = $this->arrayManager->set($messagePath, $this->meta, $messageConfig);
    }
}
