<?php

namespace app\modules\logger;

/**
 * Interface StorageInterface
 * @package lav45\activityLogger
 */
interface StorageInterface
{
    /**
     * @param LogMessage $message
     * @return int
     */
    public function save($message);

    /**
     * @param LogMessage $message
     * @return int
     */
    public function delete($message);
}
