<?php

namespace Ordinary9843;

use Exception;
use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\Handler;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Handlers\ConvertHandler;

/**
 * @method string convert(string $file, float $version)
 * @method float guess(string $file)
 * @method string merge(string $file, array $files)
 * @method array split(string $file, string $path)
 * @method void setBinPath(string $binPath)
 * @method string getBinPath()
 * @method void setTmpPath(string $tmpPath)
 * @method string getTmpPath()
 * @method void setOptions(array $options)
 * @method array getOptions()
 * @method array getMessages()
 */
class Ghostscript
{
    /** @var Config */
    private $config = null;

    /** @var Handler */
    private $handler = null;

    /**
     * @param string $binPath
     * @param string $tmpPath
     */
    public function __construct(string $binPath = '', string $tmpPath = '')
    {
        $this->config = new Config([
            'binPath' => $binPath,
            'tmpPath' => $tmpPath
        ]);
        $this->handler = new Handler($this->config);
    }

    /**
     * @param string $name
     * @param array $arguments
     * 
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        switch ($name) {
            case 'convert':
                return $this->execute(ConvertHandler::class, $arguments);
            case 'guess':
                return $this->execute(GuessHandler::class, $arguments);
            case 'merge':
                return $this->execute(MergeHandler::class, $arguments);
            case 'split':
                return $this->execute(SplitHandler::class, $arguments);
            case 'setBinPath':
                return $this->handler->getConfig()->setBinPath(current($arguments));
            case 'getBinPath':
                return $this->handler->getConfig()->getBinPath();
            case 'setTmpPath':
                return $this->handler->getConfig()->setTmpPath(current($arguments));
            case 'getTmpPath':
                return $this->handler->getConfig()->getTmpPath();
            case 'setOptions':
                return $this->handler->setOptions(...$arguments);
            case 'getOptions':
                return $this->handler->getOptions();
            case 'getMessages':
                return $this->handler->getMessages();
            default:
                throw new Exception('Invalid method: ' . $name . '.');
        }
    }

    /**
     * @param string $class
     * @param array $arguments
     * 
     * @return mixed
     */
    private function execute(string $class, array $arguments)
    {
        $this->handler = new $class($this->config);

        return $this->handler->execute(...$arguments);
    }
}
