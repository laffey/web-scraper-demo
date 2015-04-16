<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter;


/**
 * Class AdapterFactory
 * @package EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter
 */
class AdapterFactory {

    const NS = 'EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter\\';

    protected $classMap = array(
        TableDefinitionAdapter::TYPE    => self::NS . 'TableDefinitionAdapter',
        QueryAdapter::TYPE              => self::NS . 'QueryAdapter',
    );

    /**
     * @var string
     */
    protected $urlBase = '';

    public function __construct($urlBase)
    {
        $this->urlBase = $urlBase;
    }

    /**
     * @param string $type
     * @return AdapterInterface
     * @throws \RuntimeException            *if type is invalid
     */
    public function get($type)
    {
        if (array_key_exists($type, $this->classMap)) {
            return new $this->classMap[$type]($this->urlBase);
        }
        throw new \RuntimeException('Invalid adapter type: ' . $type);
    }

}