<?php
namespace exface\Core\Actions;

use exface\Core\CommonLogic\AbstractAction;
use exface\Core\Interfaces\Tasks\TaskInterface;
use exface\Core\Interfaces\DataSources\DataTransactionInterface;
use exface\Core\Interfaces\Tasks\ResultInterface;
use exface\Core\Factories\ResultFactory;
use exface\Core\CommonLogic\UxonObject;
use exface\Core\CommonLogic\UxonWidgetSchema;
use exface\Core\CommonLogic\UxonSchema;
use exface\Core\CommonLogic\UxonDatatypeSchema;
use exface\Core\CommonLogic\UxonActionSchema;
use exface\Core\CommonLogic\UxonBehaviorSchema;

class UxonAutosuggest extends AbstractAction
{
    const SCHEMA_WIDGET = 'widget';
    const SCHEMA_ACTION = 'action';
    const SCHEMA_BEHAVIOR = 'behavior';
    const SCHEMA_DATATYPE = 'datatype';
    
    const TYPE_FIELD = 'field';
    const TYPE_VALUE = 'value';
    
    protected function perform(TaskInterface $task, DataTransactionInterface $transaction) : ResultInterface
    {
        $options = [];
        
        $currentText = $task->getParameter('text');
        $path = json_decode($task->getParameter('path'), true);
        $type = $task->getParameter('input');
        $uxon = UxonObject::fromJson($task->getParameter('uxon'));
        $schema = $task->getParameter('schema');
        
        switch (mb_strtolower($schema)) {
            case self::SCHEMA_WIDGET: 
                $schema = new UxonWidgetSchema($this->getWorkbench());
                break;
            case self::SCHEMA_ACTION:
                $schema = new UxonActionSchema($this->getWorkbench());
                break;
            case self::SCHEMA_DATATYPE:
                $schema = new UxonDatatypeSchema($this->getWorkbench());
                break;
            case self::SCHEMA_BEHAVIOR:
                $schema = new UxonBehaviorSchema($this->getWorkbench());
                break;
        }
        
        if (strcasecmp($type, self::TYPE_FIELD) === 0) {
            $options = $this->suggestPropertyNames($schema, $uxon, $path);
        } else {
            $options = $this->suggestPropertyValues($schema, $uxon, $path, $currentText);
        }
        
        return ResultFactory::createJSONResult($task, $options);
    }
    
    protected function suggestPropertyNames(UxonSchema $schema, UxonObject $uxon, array $path) : array
    {
        $entityClass = $schema->getEntityClass($uxon, $path);
        return $schema->getProperties($entityClass);
    }
    
    protected function suggestPropertyValues(UxonSchema $schema, UxonObject $uxon, array $path, string $valueText) : array
    {
        return $schema->getValidValues($uxon, $path, $valueText);
    }
}