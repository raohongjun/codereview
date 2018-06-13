<?php
/**
 * This file is part of PHP Mess Detector.
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

/**
 * This factory class is used to create the {@link \PHPMD\RuleSet} instance
 * that PHPMD will use to analyze the source code.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class RuleSetFactory
{
    /**
     * 严格模式是否有效？
     *
     * @var boolean
     * @since 1.2.0
     */
    private $strict = false;

    /**
     * 由PEAR设置的数据目录或在类中设置的动态属性
     * constructor.
     *
     * @var string
     */
    private $location = '@data_dir@';

    /**
     * 规则加载的最低优先级。
     *
     * @var integer
     */
    private $minimumPriority = Rule::LOWEST_PRIORITY;

    /**
     * 构造一个新的默认规则集工厂实例。
     */
    public function __construct()
    {
        // PEAR安装程序解决方法
        if (strpos($this->location, '@data_dir') === 0) {
            $this->location = __DIR__ . '/../../resources';
        } else {
            $this->location .= '/PHPMD/resources';
        }
    }

    /**
     * 激活所有规则集的严格模式。
     *
     * @return void
     * @since 1.2.0
     */
    public function setStrict()
    {
        $this->strict = true;
    }

    /**
     * 设置规则必须具有的最低优先级。
     *
     * @param integer $minimumPriority The minimum priority value.
     *
     * @return void
     */
    public function setMinimumPriority($minimumPriority)
    {
        $this->minimumPriority = $minimumPriority;
    }

    /**
     * 为给定参数创建一组规则集实例。
     *
     * @param string $ruleSetFileNames Comma-separated string of rule-set filenames or identifier.
     * @return \PHPMD\RuleSet[]
     */
    public function createRuleSets($ruleSetFileNames)
    {
        $ruleSets = array();

        $ruleSetFileName = strtok($ruleSetFileNames, ',');
        while ($ruleSetFileName !== false) {
            $ruleSets[] = $this->createSingleRuleSet($ruleSetFileName);

            $ruleSetFileName = strtok(',');
        }
        return $ruleSets;
    }

    /**
     * 为给定的文件名或标识符创建一个规则集实例。
     *
     * @param string $ruleSetOrFileName The rule-set filename or identifier.
     * @return \PHPMD\RuleSet
     */
    public function createSingleRuleSet($ruleSetOrFileName)
    {
        $fileName = $this->createRuleSetFileName($ruleSetOrFileName);
        return $this->parseRuleSetNode($fileName);
    }

    /**
     * 列出可用的规则集标识符.
     *
     * @return array(string)
     */
    public function listAvailableRuleSets()
    {
        return array_merge(
            self::listRuleSetsInDirectory($this->location . '/rulesets/'),
            self::listRuleSetsInDirectory(getcwd() . '/rulesets/')
        );
    }

    /**
     * 此方法为规则集标识符创建文件名或返回
     * 当它已经是文件名时的输入。
     *
     * @param string $ruleSetOrFileName The rule-set filename or identifier.
     * @return string
     */
    private function createRuleSetFileName($ruleSetOrFileName)
    {
        if (file_exists($ruleSetOrFileName) === true) {
            return $ruleSetOrFileName;
        }

        $fileName = $this->location . '/' . $ruleSetOrFileName;
        if (file_exists($fileName) === true) {
            return $fileName;
        }

        $fileName = $this->location . '/rulesets/' . $ruleSetOrFileName . '.xml';
        if (file_exists($fileName) === true) {
            return $fileName;
        }

        $fileName = getcwd() . '/rulesets/' . $ruleSetOrFileName . '.xml';
        if (file_exists($fileName) === true) {
            return $fileName;
        }

        foreach (explode(PATH_SEPARATOR, get_include_path()) as $includePath) {
            $fileName = $includePath . '/' . $ruleSetOrFileName;
            if (file_exists($fileName) === true) {
                return $fileName;
            }
            $fileName = $includePath . '/' . $ruleSetOrFileName . ".xml";
            if (file_exists($fileName) === true) {
                return $fileName;
            }
        }
        
        throw new RuleSetNotFoundException($ruleSetOrFileName);
    }

    /**
     * 列出给定目录中的可用规则集标识符。
     *
     * @param string $directory The directory to scan for rule-sets.
     *
     * @return array(string)
     */
    private static function listRuleSetsInDirectory($directory)
    {
        $ruleSets = array();
        if (is_dir($directory)) {
            foreach (scandir($directory) as $file) {
                $matches = array();
                if (is_file($directory . $file) && preg_match('/^(.*)\.xml$/', $file, $matches)) {
                    $ruleSets[] = $matches[1];
                }
            }
        }
        return $ruleSets;
    }

    /**
     * 此方法解析给定文件中的规则集定义。
     *
     * @param string $fileName
     * @return \PHPMD\RuleSet
     */
    private function parseRuleSetNode($fileName)
    {
        // 隐藏错误信息
        $libxml = libxml_use_internal_errors(true);

        $xml = simplexml_load_string(file_get_contents($fileName));
        if ($xml === false) {
            // 将错误处理重置为先前的设置
            libxml_use_internal_errors($libxml);

            throw new \RuntimeException(trim(libxml_get_last_error()->message));
        }

        $ruleSet = new RuleSet();
        $ruleSet->setFileName($fileName);
        $ruleSet->setName((string) $xml['name']);

        if ($this->strict) {
            $ruleSet->setStrict();
        }

        foreach ($xml->children() as $node) {
            if ($node->getName() === 'php-includepath') {
                $includePath = (string) $node;
                
                if (is_dir(dirname($fileName) . DIRECTORY_SEPARATOR . $includePath)) {
                    $includePath = dirname($fileName) . DIRECTORY_SEPARATOR . $includePath;
                    $includePath = realpath($includePath);
                }
                
                $includePath = get_include_path() . PATH_SEPARATOR . $includePath;
                set_include_path($includePath);
            }
        }
        
        foreach ($xml->children() as $node) {
            if ($node->getName() === 'description') {
                $ruleSet->setDescription((string) $node);
            } elseif ($node->getName() === 'rule') {
                $this->parseRuleNode($ruleSet, $node);
            }
        }

        return $ruleSet;
    }

    /**
     * 此方法分析单个规则xml节点。 基于结构的基础
     * xml节点此方法将解析过程委托给另一个方法
     * 这个类。
     *
     * @param \PHPMD\RuleSet $ruleSet
     * @param \SimpleXMLElement $node
     * @return void
     */
    private function parseRuleNode(RuleSet $ruleSet, \SimpleXMLElement $node)
    {
        if (substr($node['ref'], -3, 3) === 'xml') {
            $this->parseRuleSetReferenceNode($ruleSet, $node);
        } elseif ('' === (string) $node['ref']) {
            $this->parseSingleRuleNode($ruleSet, $node);
        } else {
            $this->parseRuleReferenceNode($ruleSet, $node);
        }
    }

    /**
     * 此方法解析包含引用的完整规则集
     * 当前分析的规则集。
     *
     * @param \PHPMD\RuleSet $ruleSet
     * @param \SimpleXMLElement $ruleSetNode
     * @return void
     */
    private function parseRuleSetReferenceNode(RuleSet $ruleSet, \SimpleXMLElement $ruleSetNode)
    {
        $rules = $this->parseRuleSetReference($ruleSetNode);
        foreach ($rules as $rule) {
            if ($this->isIncluded($rule, $ruleSetNode)) {
                $ruleSet->addRule($rule);
            }
        }
    }

    /**
     * 分析由给定规则集xml元素引用的规则集xml文件。
     *
     * @param \SimpleXMLElement $ruleSetNode
     * @return \PHPMD\RuleSet
     * @since 0.2.3
     */
    private function parseRuleSetReference(\SimpleXMLElement $ruleSetNode)
    {
        $ruleSetFactory = new RuleSetFactory();
        $ruleSetFactory->setMinimumPriority($this->minimumPriority);

        return $ruleSetFactory->createSingleRuleSet((string) $ruleSetNode['ref']);
    }

    /**
     * Checks if the given rule is included/not excluded by the given rule-set
     * reference node.
     *
     * @param \PHPMD\Rule $rule
     * @param \SimpleXMLElement $ruleSetNode
     * @return boolean
     * @since 0.2.3
     */
    private function isIncluded(Rule $rule, \SimpleXMLElement $ruleSetNode)
    {
        foreach ($ruleSetNode->exclude as $exclude) {
            if ($rule->getName() === (string) $exclude['name']) {
                return false;
            }
        }
        return true;
    }

    /**
     * 该方法将创建一个单一的规则实例并将其添加到给定的实例中
     * {@link \PHPMD\RuleSet} object.
     *
     * @param \PHPMD\RuleSet $ruleSet
     * @param \SimpleXMLElement $ruleNode
     * @return void
     * @throws \PHPMD\RuleClassFileNotFoundException
     * @throws \PHPMD\RuleClassNotFoundException
     */
    private function parseSingleRuleNode(RuleSet $ruleSet, \SimpleXMLElement $ruleNode)
    {
        $fileName = "";
        $ruleSetFolderPath = dirname($ruleSet->getFileName());

        if (isset($ruleNode['file'])) {
            if (is_readable((string) $ruleNode['file'])) {
                $fileName = (string) $ruleNode['file'];

            } elseif (is_readable($ruleSetFolderPath . DIRECTORY_SEPARATOR . (string) $ruleNode['file'])) {
                $fileName = $ruleSetFolderPath . DIRECTORY_SEPARATOR . (string) $ruleNode['file'];
            }
        }

        $className = (string) $ruleNode['class'];
        
        if (!is_readable($fileName)) {
            $fileName = strtr($className, '\\', '/') . '.php';
        }

        if (!is_readable($fileName)) {
            $fileName = str_replace(array('\\', '_'), '/', $className) . '.php';
        }
        
        if (class_exists($className) === false) {
            $handle = @fopen($fileName, 'r', true);
            if ($handle === false) {
                throw new RuleClassFileNotFoundException($className);
            }
            fclose($handle);

            include_once $fileName;

            if (class_exists($className) === false) {
                throw new RuleClassNotFoundException($className);
            }
        }

        /* @var $rule \PHPMD\Rule */
        $rule = new $className();
        $rule->setName((string) $ruleNode['name']);
        $rule->setMessage((string) $ruleNode['message']);
        $rule->setExternalInfoUrl((string) $ruleNode['externalInfoUrl']);

        $rule->setRuleSetName($ruleSet->getName());

        if (trim($ruleNode['since']) !== '') {
            $rule->setSince((string) $ruleNode['since']);
        }

        foreach ($ruleNode->children() as $node) {
            if ($node->getName() === 'description') {
                $rule->setDescription((string) $node);
            } elseif ($node->getName() === 'example') {
                $rule->addExample((string) $node);
            } elseif ($node->getName() === 'priority') {
                $rule->setPriority((integer) $node);
            } elseif ($node->getName() === 'properties') {
                $this->parsePropertiesNode($rule, $node);
            }
        }

        if ($rule->getPriority() <= $this->minimumPriority) {
            $ruleSet->addRule($rule);
        }
    }

    /**
     * This method parses a single rule that was included from a different
     * rule-set.
     *
     * @param \PHPMD\RuleSet $ruleSet
     * @param \SimpleXMLElement $ruleNode
     * @return void
     */
    private function parseRuleReferenceNode(RuleSet $ruleSet, \SimpleXMLElement $ruleNode)
    {
        $ref = (string) $ruleNode['ref'];

        $fileName = substr($ref, 0, strpos($ref, '.xml/') + 4);
        $fileName = $this->createRuleSetFileName($fileName);

        $ruleName = substr($ref, strpos($ref, '.xml/') + 5);

        $ruleSetFactory = new RuleSetFactory();

        $ruleSetRef = $ruleSetFactory->createSingleRuleSet($fileName);
        $rule       = $ruleSetRef->getRuleByName($ruleName);

        if (trim($ruleNode['name']) !== '') {
            $rule->setName((string) $ruleNode['name']);
        }
        if (trim($ruleNode['message']) !== '') {
            $rule->setMessage((string) $ruleNode['message']);
        }
        if (trim($ruleNode['externalInfoUrl']) !== '') {
            $rule->setExternalInfoUrl((string) $ruleNode['externalInfoUrl']);
        }

        foreach ($ruleNode->children() as $node) {
            if ($node->getName() === 'description') {
                $rule->setDescription((string) $node);
            } elseif ($node->getName() === 'example') {
                $rule->addExample((string) $node);
            } elseif ($node->getName() === 'priority') {
                $rule->setPriority((integer) $node);
            } elseif ($node->getName() === 'properties') {
                $this->parsePropertiesNode($rule, $node);
            }
        }

        if ($rule->getPriority() <= $this->minimumPriority) {
            $ruleSet->addRule($rule);
        }
    }

    /**
     * This method parses a xml properties structure and adds all found properties
     * to the given <b>$rule</b> object.
     *
     * <code>
     *   ...
     *   <properties>
     *       <property name="foo" value="42" />
     *       <property name="bar" value="23" />
     *       ...
     *   </properties>
     *   ...
     * </code>
     *
     * @param \PHPMD\Rule $rule
     * @param \SimpleXMLElement $propertiesNode
     * @return void
     */
    private function parsePropertiesNode(Rule $rule, \SimpleXMLElement $propertiesNode)
    {
        foreach ($propertiesNode->children() as $node) {
            if ($node->getName() === 'property') {
                $this->addProperty($rule, $node);
            }
        }
    }

    /**
     * Adds an additional property to the given <b>$rule</b> instance.
     *
     * @param \PHPMD\Rule $rule
     * @param \SimpleXMLElement $node
     * @return void
     */
    private function addProperty(Rule $rule, \SimpleXMLElement $node)
    {
        $name  = trim($node['name']);
        $value = trim($this->getPropertyValue($node));
        if ($name !== '' && $value !== '') {
            $rule->addProperty($name, $value);
        }
    }

    /**
     * Returns the value of a property node. This value can be expressed in
     * two different notations. First version is an attribute named <b>value</b>
     * and the second valid notation is a child element named <b>value</b> that
     * contains the value as character data.
     *
     * @param \SimpleXMLElement $propertyNode
     * @return string
     * @since 0.2.5
     */
    private function getPropertyValue(\SimpleXMLElement $propertyNode)
    {
        if (isset($propertyNode->value)) {
            return (string) $propertyNode->value;
        }
        return (string) $propertyNode['value'];
    }

    /**
     * Returns an array of path exclude patterns in format described at
     *
     * http://pmd.sourceforge.net/pmd-5.0.4/howtomakearuleset.html#Excluding_files_from_a_ruleset
     *
     * @param string $fileName The filename of a rule-set definition.
     *
     * @return array|null
     * @throws \RuntimeException
     */
    public function getIgnorePattern($fileName)
    {
        $excludes = array();
        foreach (array_map('trim', explode(',', $fileName)) as $ruleSetFileName) {
            $ruleSetFileName = $this->createRuleSetFileName($ruleSetFileName);

            // Hide error messages
            $libxml = libxml_use_internal_errors(true);

            $xml = simplexml_load_string(file_get_contents($ruleSetFileName));
            if ($xml === false) {
                // Reset error handling to previous setting
                libxml_use_internal_errors($libxml);

                throw new \RuntimeException(trim(libxml_get_last_error()->message));
            }

            foreach ($xml->children() as $node) {
                if ($node->getName() === 'exclude-pattern') {
                    $excludes[] = '' . $node;
                }
            }

            return $excludes;
        }
        return null;
    }
}
