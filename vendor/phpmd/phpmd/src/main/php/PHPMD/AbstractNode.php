<?php
/**
 * This file is part of PHPMD.
 *
 * Copyright (c) 2008-2017, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD;

use PHPMD\Node\ASTNode;

/**
 * This is an abstract base class for PHPMD code nodes, it is just a wrapper
 * 抽象基类
 * 围绕PDepend的对象模型
 */
abstract class AbstractNode
{
    /**
     *
     * @var \PDepend\Source\AST\ASTArtifact|\PDepend\Source\AST\ASTNode $node
     */
    private $node = null;

    /**
     * 收集的此节点的度量标准。
     *
     * @var array(string=>mixed) $_metrics
     */
    private $metrics = null;

    /**
     * Constructs a new PHPMD node.
     *
     * @param \PDepend\Source\AST\ASTArtifact|\PDepend\Source\AST\ASTNode $node
     */
    public function __construct($node)
    {
        $this->node = $node;
    }

    /**
     * 魔术方法用于直接管理来自规则的请求
     * 到底层的PDepend ast节点。
     *
     * @param string $name
     * @param array $args
     * @return mixed
     * @throws \BadMethodCallException When the underlying PDepend node
     *         does not contain a method named <b>$name</b>.
     */
    public function __call($name, array $args)
    {
        if (method_exists($this->getNode(), $name)) {
            return call_user_func_array(array($this->getNode(), $name), $args);
        }
        throw new \BadMethodCallException(
            sprintf('Invalid method %s() called.', $name)
        );
    }

    /**
     * 没有父节点时，返回此节点的父节点或<b> null </ b>
     * 存在。
     *
     * @return ASTNode
     */
    public function getParent()
    {
        if (($node = $this->node->getParent()) === null) {
            return null;
        }
        return new ASTNode($node, $this->getFileName());
    }

    /**
     * 返回给定索引处的子节点。
     *
     * @param integer $index The child offset.
     *
     * @return \PHPMD\Node\ASTNode
     */
    public function getChild($index)
    {
        return new ASTNode(
            $this->node->getChild($index),
            $this->getFileName()
        );
    }

    /**
     * 返回此节点的给定类型的第一个子元素或<b> null </ b>
     * 没有给定类型的孩子。
     *
     * @param string $type The searched child type.
     * @return \PHPMD\AbstractNode
     */
    public function getFirstChildOfType($type)
    {
        $node = $this->node->getFirstChildOfType('PDepend\Source\AST\AST' . $type);
        if ($node === null) {
            return null;
        }
        return new ASTNode($node, $this->getFileName());
    }

    /**
     * 搜索此节点的给定子节点的所有子节点的递归类型
     *
     * @param string $type The searched child type.
     * @return \PHPMD\AbstractNode[]
     */
    public function findChildrenOfType($type)
    {
        $children = $this->node->findChildrenOfType('PDepend\Source\AST\AST' . $type);

        $nodes = array();
        foreach ($children as $child) {
            $nodes[] = new ASTNode($child, $this->getFileName());
        }
        return $nodes;
    }

    /**
     * 测试此节点是否表示给定的类型。
     *
     * @param string $type The expected node type.
     * @return boolean
     */
    public function isInstanceOf($type)
    {
        $class = 'PDepend\Source\AST\AST' . $type;
        return ($this->node instanceof $class);
    }

    /**
     * 返回底层节点的图像。
     * @return string
     */
    public function getImage()
    {
        return $this->node->getName();
    }

    /**
     * 返回此节点的源名称，可能是类或接口名称，
     * 或包，方法，函数名称
     *
     * @return string
     */
    public function getName()
    {
        return $this->node->getName();
    }

    /**
     * 返回php源代码文件中此节点的开始行。
     *
     * @return integer
     */
    public function getBeginLine()
    {
        return $this->node->getStartLine();
    }

    /**
     * 返回php源代码文件中此节点的结束行。
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->node->getEndLine();
    }

    /**
     * 返回声明源文件的名称。
     *
     * @return string
     */
    public function getFileName()
    {
        return (string) $this->node->getCompilationUnit()->getFileName();
    }

    /**
     * 返回包装的PDepend节点实例。
     *
     * @return \PDepend\Source\AST\ASTArtifact
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * 返回具体节点类型的文本表示/名称。
     *
     * @return string
     */
    public function getType()
    {
        $type = explode('\\', get_class($this));
        return preg_replace('(node$)', '', strtolower(array_pop($type)));
    }

    /**
     * 此方法将返回给定标识符的度量值
     * <b>null</b> when no such metric exists.
     *
     * @param string $name The metric name or abbreviation.
     *
     * @return mixed
     */
    public function getMetric($name)
    {

        if (isset($this->metrics[$name])) {
            return $this->metrics[$name];
        }
        return null;
    }

    /**
     * 该方法将为此节点设置度量标准
     *
     * @param array(string=>mixed) $metrics The collected node metrics.
     * @return void
     */
    public function setMetrics(array $metrics)
    {
        if ($this->metrics === null) {
            $this->metrics = $metrics;
        }
    }

    /**
     * Checks if this node has a suppressed annotation for the given rule
     * instance.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     */
    abstract public function hasSuppressWarningsAnnotationFor(Rule $rule);

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     *
     * @return string
     */
    abstract public function getFullQualifiedName();

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     *
     * @return string
     */
    abstract public function getParentName();

    /**
     * Returns the name of the parent package.
     *
     * @return string
     */
    abstract public function getNamespaceName();
}
