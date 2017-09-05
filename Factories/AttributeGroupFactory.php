<?php
namespace exface\Core\Factories;

use exface\Core\Interfaces\Model\MetaObjectInterface;
use exface\Core\CommonLogic\Model\AttributeGroup;

abstract class AttributeGroupFactory extends AbstractFactory
{

    /**
     *
     * @param MetaObjectInterface $object            
     * @param string $alias            
     * @return AttributeGroup
     */
    public static function createForObject(MetaObjectInterface $object, $alias = null)
    {
        $exface = $object->getWorkbench();
        $group = new AttributeGroup($exface, $object);
        $group->setAlias($alias);
        switch ($alias) {
            case AttributeGroup::ALL:
                foreach ($object->getAttributes() as $attr) {
                    $group->add($attr);
                }
                break;
            case AttributeGroup::VISIBLE:
                foreach ($object->getAttributes() as $attr) {
                    if (! $attr->isHidden()) {
                        $group->add($attr);
                    }
                }
                break;
            case AttributeGroup::EDITABLE:
                foreach ($object->getAttributes() as $attr) {
                    if ($attr->isEditable()) {
                        $group->add($attr);
                    }
                }
                break;
            case AttributeGroup::REQUIRED:
                foreach ($object->getRequiredAttributes() as $attr) {
                    $group->add($attr);
                }
                break;
            case AttributeGroup::DEFAULT_DISPLAY:
                foreach ($object->getAttributes()->getDefaultDisplayList() as $attr) {
                    $group->add($attr);
                }
                break;
            default:
                // TODO load group from DB
                break;
        }
        return $group;
    }
}
?>