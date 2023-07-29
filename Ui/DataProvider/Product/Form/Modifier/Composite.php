<?php
namespace Perspective\ImageAiVariations\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\ModifierFactory;
class Composite extends AbstractModifier
{

    const CHILDREN_PATH = 'ai_vars/children';
    const CONTAINER_ORIGINAL_IMAGE = 'container_original_image';
    const CONTAINER_MASK_FOR_IMAGE = 'container_mask_for_image';

    /**
     * @var ModifierFactory
     */
    protected $modifierFactory;

    /**
     * @var array
     */
    protected $modifiers = [];

    /**
     * @var ModifierInterface[]
     */
    protected $modifiersInstances = [];

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @param ModifierFactory $modifierFactory
     * @param LocatorInterface $locator
     * @param array $modifiers
     */
    public function __construct(
        ModifierFactory $modifierFactory,
        LocatorInterface $locator,
        array $modifiers = []
    ) {
        $this->modifierFactory = $modifierFactory;
        $this->locator = $locator;
        $this->modifiers = $modifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->canShowAiPanel()) {
            foreach ($this->getModifiers() as $modifier) {
                $data = $modifier->modifyData($data);
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->canShowAiPanel()) {
            foreach ($this->getModifiers() as $modifier) {
                $meta = $modifier->modifyMeta($meta);
            }
        }

        return $meta;
    }

    /**
     * @return bool
     */
    protected function canShowAiPanel()
    {
        return true;
    }

    /**
     * Get modifiers list
     *
     * @return ModifierInterface[]
     */
    protected function getModifiers()
    {
        if (empty($this->modifiersInstances)) {
            foreach ($this->modifiers as $modifierClass) {
                $this->modifiersInstances[$modifierClass] = $this->modifierFactory->create($modifierClass);
            }
        }

        return $this->modifiersInstances;
    }
}
