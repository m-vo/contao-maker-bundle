<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\MakerBundle\Util;

class XliffMerger
{
    public function merge(\DOMDocument $root, \DOMDocument $document): \DOMDocument
    {
        $body = $root->getElementsByTagName('body')->item(0);
        $importNodes = $this->getImportNodes($document);

        /** @var \DOMElement $importNode */
        foreach ($importNodes as $importNode) {
            $id = $importNode->getAttribute('id');

            $duplicatesPath = new \DOMXPath($root);
            $duplicates = $duplicatesPath->query('//trans-unit[@id=\''.$id.'\']');

            if ($duplicates->length > 0) {
                continue;
            }

            $importedNode = $root->importNode($importNode, true);
            $body->appendChild($importedNode);
        }

        // Properly format the output xml
        $toFormat = $root->saveHTML($root);

        $root->preserveWhiteSpace = false;
        $root->formatOutput = true;
        $root->encoding = 'UTF-8';

        $root->loadXML($toFormat);

        return $root;
    }

    private function getImportNodes(\DOMDocument $document): array
    {
        $nodes = [];

        $xpath = new \DOMXPath($document);
        $elements = $xpath->query('//trans-unit[@id]');

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $nodes[] = $element;
        }

        return $nodes;
    }
}
