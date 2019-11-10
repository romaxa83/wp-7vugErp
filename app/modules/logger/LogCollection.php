<?php

namespace app\modules\logger;

/**
 * Class LogCollection
 * @package lav45\activityLogger
 */
class LogCollection
{
    /**
     * @var Manager
     */
    private $logger;
    /**
     * @var string
     */
    private $entityName;
    /**
     * @var string|int
     */
    private $entityId;
    /**
     * @var string
     */
    private $action;
    /**
     * @var string[]
     */
    private $messages = [];

    /**
     * LogCollection constructor.
     * @param Manager $logger
     * @param string $entityName
     */
    public function __construct($logger, $entityName)
    {
        $this->logger = $logger;
        $this->entityName = $entityName;
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setEntityId($value)
    {
        $this->entityId = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAction($value)
    {
        $this->action = $value;
        return $this;
    }

    /**
     * @param string $value
     */
    public function addMessage($value)
    {
        $this->messages[] = $value;
    }

    /**
     * @return string[]
     */
    private function removeMessages()
    {
        $messages = $this->messages;
        $this->messages = [];
        return $messages;
    }
    
    public function formattedMessage($model,$collection)
    {
        $newAttr = $model->getAttributes();
        $oldAttr = $model->getOldAttributes();
        foreach ($model->attributes() as $oneAttr) {            
            if(isset($oldAttr[$oneAttr])){
                if(!empty($oldAttr) && (strpos($oneAttr, 'price') !== false || strpos($oneAttr, 'total') !== false)){
                    if(strpos($newAttr[$oneAttr], '.') === false){
                        $newAttr[$oneAttr] .= '.0';
                    }
                    if(strpos($oldAttr[$oneAttr], '.') === false){
                        $oldAttr[$oneAttr] .= '.0';
                    }
                    $oldAttr[$oneAttr] = (double)str_pad($oldAttr[$oneAttr],14,0);
                    $newAttr[$oneAttr] = (double)str_pad($newAttr[$oneAttr] ,14,0);
                }
                if($newAttr[$oneAttr] !== $oldAttr[$oneAttr]){
                    $collection->addMessage($oneAttr . ' был изменен c ' . $oldAttr[$oneAttr] . ' на ' . $newAttr[$oneAttr]);
                }
            }else{
                if(!empty($newAttr[$oneAttr])){
                    $collection->addMessage($oneAttr . ' задано : ' . $newAttr[$oneAttr]);
                }
            }
        }
        return $collection;
    }

    /**
     * @return bool
     */
    public function push()
    {
        $messages = $this->removeMessages();
        if (empty($messages)) {
            return false;
        }
        
        return $this->logger->log(
            $this->entityName,
            $messages,
            $this->action,
            $this->entityId
        );
    }
}