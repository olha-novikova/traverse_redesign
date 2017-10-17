<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * IfwPsn_Vendor_Twig_NodeVisitor_Escaper implements output escaping.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class IfwPsn_Vendor_Twig_NodeVisitor_Escaper implements IfwPsn_Vendor_Twig_NodeVisitorInterface
{
    protected $statusStack = array();
    protected $blocks = array();
    protected $safeAnalysis;
    protected $traverser;
    protected $defaultStrategy = false;
    protected $safeVars = array();

    public function __construct()
    {
        $this->safeAnalysis = new IfwPsn_Vendor_Twig_NodeVisitor_SafeAnalysis();
    }

    /**
     * Called before child nodes are visited.
     *
     * @param IfwPsn_Vendor_Twig_NodeInterface $node The node to visit
     * @param IfwPsn_Vendor_Twig_Environment   $env  The Twig environment instance
     *
     * @return IfwPsn_Vendor_Twig_NodeInterface The modified node
     */
    public function enterNode(IfwPsn_Vendor_Twig_NodeInterface $node, IfwPsn_Vendor_Twig_Environment $env)
    {
        if ($node instanceof IfwPsn_Vendor_Twig_Node_Module) {
            if ($env->hasExtension('escaper') && $defaultStrategy = $env->getExtension('escaper')->getDefaultStrategy($node->getAttribute('filename'))) {
                $this->defaultStrategy = $defaultStrategy;
            }
            $this->safeVars = array();
        } elseif ($node instanceof IfwPsn_Vendor_Twig_Node_AutoEscape) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($node instanceof IfwPsn_Vendor_Twig_Node_Block) {
            $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ? $this->blocks[$node->getAttribute('name')] : $this->needEscaping($env);
        } elseif ($node instanceof IfwPsn_Vendor_Twig_Node_Import) {
            $this->safeVars[] = $node->getNode('var')->getAttribute('name');
        }

        return $node;
    }

    /**
     * Called after child nodes are visited.
     *
     * @param IfwPsn_Vendor_Twig_NodeInterface $node The node to visit
     * @param IfwPsn_Vendor_Twig_Environment   $env  The Twig environment instance
     *
     * @return IfwPsn_Vendor_Twig_NodeInterface The modified node
     */
    public function leaveNode(IfwPsn_Vendor_Twig_NodeInterface $node, IfwPsn_Vendor_Twig_Environment $env)
    {
        if ($node instanceof IfwPsn_Vendor_Twig_Node_Module) {
            $this->defaultStrategy = false;
            $this->safeVars = array();
        } elseif ($node instanceof IfwPsn_Vendor_Twig_Node_Expression_Filter) {
            return $this->preEscapeFilterNode($node, $env);
        } elseif ($node instanceof IfwPsn_Vendor_Twig_Node_Print) {
            return $this->escapePrintNode($node, $env, $this->needEscaping($env));
        }

        if ($node instanceof IfwPsn_Vendor_Twig_Node_AutoEscape || $node instanceof IfwPsn_Vendor_Twig_Node_Block) {
            array_pop($this->statusStack);
        } elseif ($node instanceof IfwPsn_Vendor_Twig_Node_BlockReference) {
            $this->blocks[$node->getAttribute('name')] = $this->needEscaping($env);
        }

        return $node;
    }

    protected function escapePrintNode(IfwPsn_Vendor_Twig_Node_Print $node, IfwPsn_Vendor_Twig_Environment $env, $type)
    {
        if (false === $type) {
            return $node;
        }

        $expression = $node->getNode('expr');

        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }

        $class = get_class($node);

        return new $class(
            $this->getEscaperFilter($type, $expression),
            $node->getLine()
        );
    }

    protected function preEscapeFilterNode(IfwPsn_Vendor_Twig_Node_Expression_Filter $filter, IfwPsn_Vendor_Twig_Environment $env)
    {
        $name = $filter->getNode('filter')->getAttribute('value');

        $type = $env->getFilter($name)->getPreEscape();
        if (null === $type) {
            return $filter;
        }

        $node = $filter->getNode('node');
        if ($this->isSafeFor($type, $node, $env)) {
            return $filter;
        }

        $filter->setNode('node', $this->getEscaperFilter($type, $node));

        return $filter;
    }

    protected function isSafeFor($type, IfwPsn_Vendor_Twig_NodeInterface $expression, $env)
    {
        $safe = $this->safeAnalysis->getSafe($expression);

        if (null === $safe) {
            if (null === $this->traverser) {
                $this->traverser = new IfwPsn_Vendor_Twig_NodeTraverser($env, array($this->safeAnalysis));
            }

            $this->safeAnalysis->setSafeVars($this->safeVars);

            $this->traverser->traverse($expression);
            $safe = $this->safeAnalysis->getSafe($expression);
        }

        return in_array($type, $safe) || in_array('all', $safe);
    }

    protected function needEscaping(IfwPsn_Vendor_Twig_Environment $env)
    {
        if (count($this->statusStack)) {
            return $this->statusStack[count($this->statusStack) - 1];
        }

        return $this->defaultStrategy ? $this->defaultStrategy : false;
    }

    protected function getEscaperFilter($type, IfwPsn_Vendor_Twig_NodeInterface $node)
    {
        $line = $node->getLine();
        $name = new IfwPsn_Vendor_Twig_Node_Expression_Constant('escape', $line);
        $args = new IfwPsn_Vendor_Twig_Node(array(new IfwPsn_Vendor_Twig_Node_Expression_Constant((string) $type, $line), new IfwPsn_Vendor_Twig_Node_Expression_Constant(null, $line), new IfwPsn_Vendor_Twig_Node_Expression_Constant(true, $line)));

        return new IfwPsn_Vendor_Twig_Node_Expression_Filter($node, $name, $args, $line);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
