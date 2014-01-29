<?php

namespace MajorApi\Quickbooks\Parser\Mixin;

use \DOMNode;

trait ParserMixin
{

    /** 
     * Retrieves a value from a query starting from a specific
     * DOMNode element. This is a shorthand method to avoid
     * doing all of the logic in it each time.
     *
     * @param string $query
     * @param DOMNode $xmlNode
     * @return mixed
     */
    public function queryValue($query, DOMNode $xmlNode)
    {   
        $value = null;

        $nodeList = $this->xpath
            ->query($query, $xmlNode);

        if ($nodeList->length > 0) {
            $value = $nodeList->item(0)->textContent;
        }

        return $value;
    }

}
