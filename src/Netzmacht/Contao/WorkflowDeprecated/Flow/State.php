<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Flow;

use Netzmacht\Contao\WorkflowDeprecated\Date\DateTime;

class State
{
    /**
     * @var string
     */
    private $stepName;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var bool
     */
    private $successFul;

    /**
     * @var DateTime
     */
    private $reachedAt;

    /**
     * @param $stepName
     * @param array $data
     * @param $isSuccessFul
     * @param DateTime $reachedAt
     * @param $userId
     */
    function __construct($stepName, array $data, DateTime $reachedAt, $isSuccessFul=true, $userId=null)
    {
        $this->data         = $data;
        $this->isSuccessFul = $isSuccessFul;
        $this->reachedAt    = $reachedAt;
        $this->stepName     = $stepName;
        $this->userId       = $userId;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function isSuccessFul()
    {
        return $this->successFul;
    }

    /**
     * @return string
     */
    public function getStepName()
    {
        return $this->stepName;
    }

    /**
     * @return DateTime
     */
    public function getReachedAt()
    {
        return $this->reachedAt;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

} 