<?php
namespace exface\Core\DataTypes;

use exface\Core\CommonLogic\DataTypes\EnumStaticDataTypeTrait;
use exface\Core\Interfaces\DataTypes\EnumDataTypeInterface;
use exface\Core\Exceptions\RuntimeException;

/**
 * Logical comparison operators: `=`, `==`, `<`, `>`, etc.
 * 
 * ## Single value comparators
 * 
 * - `=` - universal comparator similar to SQL's `LIKE` with % on both sides. Can compare different 
 * data types. If the left value is a string, becomes TRUE if it contains the right value. Case 
 * insensitive for strings
 * - `!=` - yields TRUE if `IS` would result in FALSE
 * - `==` - compares two single values of the same type. Case sensitive for stings. Normalizes the 
 * values before comparison though, so the date `-1 == 21.09.2020` will yield TRUE on the 22.09.2020. 
 * - `!==` - the inverse of `EQUALS`
 * - `<` - yields TRUE if the left value is less than the right one. Both values must be of
 * comparable types: e.g. numbers or dates.
 * - `<=` - yields TRUE if the left value is less than or equal to the right one. 
 * Both values must be of comparable types: e.g. numbers or dates.
 * - `>` - yields TRUE if the left value is greater than the right one. Both values must be of
 * comparable types: e.g. numbers or dates.
 * - `>=` - yields TRUE if the left value is greater than or equal to the right one. 
 * Both values must be of comparable types: e.g. numbers or dates.
 * 
 * ## List comparators
 * 
 * - `[` - IN-comparator - compares a value with each item in a list via EQUALS. Becomes true if the left
 * value equals at least on of the values in the list within the right value. The list on the
 * right side must consist of numbers or strings separated by commas or the attribute's value
 * list delimiter if filtering over an attribute. The right side can also be another type of
 * expression (e.g. a formula or widget link), that yields such a list.
 * - `![` - the inverse von `[` . Becomes true if the left value equals none of the values in the 
 * list within the right value. The list on the right side must consist of numbers or strings separated 
 * by commas or the attribute's value list delimiter if filtering over an attribute. The right side can 
 * also be another type of expression (e.g. a formula or widget link), that yields such a list.
 * - `][` - intersection - compares two lists with each other. Becomes TRUE when there is at least 
 * one element, that is present in both lists.
 * - `!][` - the inverse of `][`. Becomes TRUE if no element is part of both lists.
 * - `[[` - subset - compares two lists with each other. Becomes true when all elements of the left list 
 * are in the right list too
 * - `![[` - the inverse of `][`. Becomes true when at least one element of the left list is NOT in 
 * the right list.
 * 
 * @method ComparatorsDataType IS(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType IS_NOT(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType EQUALS(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType EQUALS_NOT(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType LESS_THAN(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType LESS_THAN_OR_EQUALS(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType GREATER_THAN(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType GREATER_THAN_OR_EQUALS(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType BETWEEN(\exface\Core\CommonLogic\Workbench $workbench)
 * 
 * @method ComparatorsDataType IN(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType NOT_IN(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType LIST_INTERSECTS(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType LIST_NOT_INTERSECTS(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType LIST_SUBSET(\exface\Core\CommonLogic\Workbench $workbench)
 * @method ComparatorsDataType LIST_NOT_SUBSET(\exface\Core\CommonLogic\Workbench $workbench)
 * 
 * @author Andrej Kabachnik
 *
 */
class ComparatorDataType extends StringDataType implements EnumDataTypeInterface
{
    use EnumStaticDataTypeTrait;
    
    /**
     * @const IN compares to each vaule in a list via EQUALS. Becomes true if the left
     * value equals at leas on of the values in the list within the right value.
     */
    const IN = '[';
    
    /**
     * @const NOT_IN the inverse von `[` . Becomes true if the left value equals none of the values in the 
     * list within the right value.
     */
    const NOT_IN = '![';
    
    /**
     * @const LIST_INTERSECTS compares two lists with each other. Becomes true when there is at least one element in both lists.
     */
    const LIST_INTERSECTS = '][';
    
    /**
     * @const LIST_NOT_INTERSECTS the inverse of `][`. Becomes true when there is no element, that is part of both lists.
     */
    const LIST_NOT_INTERSECTS = '!][';
    
    /**
     * @const LIST_SUBSET compares two lists with each other. Becomes true when all elements of the left list are in the right list too.
     */
    const LIST_SUBSET = '[[';
    
    /**
     * @const LIST_NOT_SUBSET the inverse of `[[`. Becomes true when at least one element of the left list is NOT in the right list.
     */
    const LIST_NOT_SUBSET = '![[';
    
    /**
     * @const IS universal comparator similar to SQL's `LIKE`. Can compare different data types.
     * If the left value is a string, becomes TRUE if it contains the right value. Case insensitive
     * for strings.
     */
    const IS = '=';
    
    /**
     * 
     * @const IS_NOT yields TRUE if `IS` would result in FALSE
     */
    const IS_NOT = '!=';
    
    /**
     * @const EQUALS compares two single values of the same type. Normalizes the values before comparison
     * though, so the date `-1 == 21.09.2020` will yield TRUE on the 22.09.2020. 
     */
    const EQUALS = '==';
    
    /**
     * 
     * @const EQUALS_NOT the opposite of `EQUALS`.
     */
    const EQUALS_NOT = '!==';
    
    /**
     * 
     * @const LESS_THAN yields TRUE if the left value is less than the right one. Both values must be of
     * comparable types: e.g. numbers or dates.
     */
    const LESS_THAN = '<';
    
    /**
     *
     * @const LESS_THAN_OR_EQUALS yields TRUE if the left value is less than or equal to the right one. 
     * Both values must be of comparable types: e.g. numbers or dates.
     */
    const LESS_THAN_OR_EQUALS = '<=';
    
    /**
     *
     * @const GREATER_THAN yields TRUE if the left value is greater than the right one. Both values must be of
     * comparable types: e.g. numbers or dates.
     */
    const GREATER_THAN = '>';
    
    /**
     *
     * @const GREATER_THAN_OR_EQUALS yields TRUE if the left value is greater than or equal to the right one. 
     * Both values must be of comparable types: e.g. numbers or dates.
     */
    const GREATER_THAN_OR_EQUALS = '>=';
    
    const BETWEEN = '..';
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\DataTypes\EnumDataTypeInterface::getLabels()
     */
    public function getLabels()
    {
        if (empty($this->labels)) {
            $translator = $this->getWorkbench()->getCoreApp()->getTranslator();
            
            foreach (static::getValuesStatic() as $val) {
                $this->labels[$val] = $translator->translate('GLOBAL.COMPARATOR.' . static::findKey($val));
            }
        }
        
        return $this->labels;
    }
    
    /**
     * 
     * @param string|ComparatorDataType $comparatorOrString
     * @return bool
     */
    public static function isNegative($comparatorOrString) : bool
    {
        $cmp = ($comparatorOrString instanceof ComparatorDataType) ? $comparatorOrString->__toString() : $comparatorOrString;
        switch ($cmp) {
            case self::EQUALS_NOT:
            case self::IS_NOT:
            case self::NOT_IN:
            case self::LIST_NOT_INTERSECTS:
            case self::LIST_NOT_SUBSET:
                return true;
        }
        return false;
    }
    
    /**
     * Returns TRUE if the given comparator can be inverted and FALSE otherwise
     * 
     * @param string|ComparatorDataType $comparatorOrString
     * @return bool
     */
    public static function isInvertable($comparatorOrString) : bool
    {
        if ($comparatorOrString instanceof ComparatorDataType) {
            $cmp = $comparatorOrString->__toString();
        } else {
            $cmp = $comparatorOrString;
        }
        switch ($cmp) {
            case self::BETWEEN: return false;
        }
        return true;
    }
    
    /**
     * 
     * @param string|ComparatorDataType $comparatorOrString
     * @throws RuntimeException
     * @return string|ComparatorDataType
     */
    public static function invert($comparatorOrString)
    {
        if ($comparatorOrString instanceof ComparatorDataType) {
            $asString = false;
            $cmp = $comparatorOrString->__toString();
        } else {
            $asString = true;
            $cmp = $comparatorOrString;
        }
        
        switch ($cmp) {
            case self::EQUALS: $inv = self::EQUALS_NOT; break;
            case self::EQUALS_NOT: $inv = self::EQUALS; break;
            case self::GREATER_THAN: $inv = self::LESS_THAN_OR_EQUALS; break;
            case self::GREATER_THAN_OR_EQUALS: $inv = self::LESS_THAN; break;
            case self::IN: $inv = self::NOT_IN; break;
            case self::NOT_IN: $inv = self::IN; break;
            case self::IS: $inv = self::IS_NOT; break;
            case self::IS_NOT: $inv = self::IS; break;
            case self::LESS_THAN: $inv = self::GREATER_THAN_OR_EQUALS; break;
            case self::LESS_THAN_OR_EQUALS: $inv = self::GREATER_THAN; break;
            case self::LIST_INTERSECTS: $inv = self::LIST_NOT_INTERSECTS; break;
            case self::LIST_NOT_INTERSECTS: $inv = self::LIST_INTERSECTS; break;
            case self::LIST_SUBSET: $inv = self::LIST_NOT_SUBSET; break;
            case self::LIST_NOT_SUBSET: $inv = self::LIST_SUBSET; break;
            default:
                throw new RuntimeException('Cannot invert comparator "' . $cmp . '"');
        }
        
        return $asString ? $inv : self::fromValue($comparatorOrString->getWorkbench(), $inv);
    }
}
?>