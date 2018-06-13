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
 * This is the abstract base class for pmd rules.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class AbstractRule implements Rule
{
    /**
     * 此规则实例的名称。
     *
     * @var string $_name
     */
    private $name = '';

    /**
     * 此规则的违规消息文本。
     *
     * @var string
     */
    private $message = '';

    /**
     * 自此规则可用时的版本。
     *
     * @var string
     */
    private $since = null;

    /**
     * 一个url将为此规则提供外部信息。
     *
     * @var string
     */
    private $externalInfoUrl = '';

    /**
     * 此规则的可选说明。
     *
     * @var string
     */
    private $description = '';

    /**
     * 此规则的代码示例列表。
     *
     * @var array(string)
     */
    private $examples = array();

    /**
     * 父规则集实例的名称。
     *
     * @var string
     */
    private $ruleSetName = '';

    /**
     * 这条规则的优先权。
     *
     * @var integer
     */
    private $priority = self::LOWEST_PRIORITY;

    /**
     * 此规则实例的配置属性。
     *
     * @var array(string=>string)
     */
    private $properties = array();

    /**
     * 此规则的对象报告。
     *
     * @var \PHPMD\Report
     */
    private $report = null;

    /**
     * 返回此规则实例的名称。
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 为此规则实例设置名称。
     *
     * @param string $name The rule name.
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 从此规则可用或<b> null </ b>时返回版本。
     *
     * @return string
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * 自此规则可用时设置版本。
     *
     * @param string $since The version number.
     *
     * @return void
     */
    public function setSince($since)
    {
        $this->since = $since;
    }

    /**
     * 返回此规则的违规消息文本。
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 为此规则设置违规消息文本
     *
     * @param string $message The violation message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * 返回一个url会为此规则提供外部信息。
     *
     * @return string
     */
    public function getExternalInfoUrl()
    {
        return $this->externalInfoUrl;
    }

    /**
     * 设置一个url会为此规则提供外部信息。
     *
     * @param string $externalInfoUrl The info url.
     *
     * @return void
     */
    public function setExternalInfoUrl($externalInfoUrl)
    {
        $this->externalInfoUrl = $externalInfoUrl;
    }

    /**
     * 返回此规则实例的描述文本。
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 设置此规则实例的描述文本。
     *
     * @param string $description The description text.
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 返回此规则的示例列表。
     *
     * @return array(string)
     */
    public function getExamples()
    {
        return $this->examples;
    }

    /**
     * 添加此规则的代码示例。
     *
     * @param string $example The code example.
     *
     * @return void
     */
    public function addExample($example)
    {
        $this->examples[] = $example;
    }

    /**
     * 返回此规则的优先级。
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * 设置此规则的优先级。
     *
     * @param integer $priority The rule priority
     *
     * @return void
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * 返回父规则集实例的名称。
     *
     * @return string
     */
    public function getRuleSetName()
    {
        return $this->ruleSetName;
    }

    /**
     * 设置父规则集实例的名称。
     *
     * @param string $ruleSetName The rule-set name.
     *
     * @return void
     */
    public function setRuleSetName($ruleSetName)
    {
        $this->ruleSetName = $ruleSetName;
    }

    /**
     * 返回此规则的违规报告。
     *
     * @return \PHPMD\Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * 设置此规则的违规报告
     *
     * @param \PHPMD\Report $report
     * @return void
     */
    public function setReport(Report $report)
    {
        $this->report = $report;
    }

    /**
     * 将配置属性添加到此规则实例。
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * 以布尔值形式返回已配置属性的值或抛出一个
     * 当<b> $ name </ b>不存在任何属性时为异常。
     *
     * @param string $name
     * @return boolean
     * @throws \OutOfBoundsException When no property for <b>$name</b> exists.
     */
    public function getBooleanProperty($name)
    {
        if (isset($this->properties[$name])) {
            return in_array($this->properties[$name], array('true', 'on', 1));
        }
        throw new \OutOfBoundsException('Property "' . $name . '" does not exist.');
    }

    /**
     * 以整数形式返回配置属性的值或抛出一个
     * 当<b> $ name </ b>不存在任何属性时为异常
     *
     * @param string $name
     * @return integer
     * @throws \OutOfBoundsException When no property for <b>$name</b> exists.
     */
    public function getIntProperty($name)
    {
        if (isset($this->properties[$name])) {
            return (int) $this->properties[$name];
        }
        throw new \OutOfBoundsException('Property "' . $name . '" does not exist.');
    }


    /**
     * 返回配置属性的原始字符串值或抛出一个
     * 当<b> $ name </ b>不存在任何属性时为异常。
     *
     * @param string $name
     * @return string
     * @throws \OutOfBoundsException When no property for <b>$name</b> exists.
     */
    public function getStringProperty($name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }
        throw new \OutOfBoundsException('Property "' . $name . '" does not exist.');

    }

    /**
     * 此方法为此违规类型和所有报告添加违规
     * 对于给定的<b> $ node </ b>实例。
     *
     * @param \PHPMD\AbstractNode $node
     * @param array $args
     * @param mixed $metric
     * @return void
     */
    protected function addViolation(
        AbstractNode $node,
        array $args = array(),
        $metric = null
    ) {
        $search  = array();
        $replace = array();
        foreach ($args as $index => $value) {
            $search[]  = '{' . $index . '}';
            $replace[] = $value;
        }

        $message = str_replace($search, $replace, $this->message);
        $ruleViolation = new RuleViolation($this, $node, $message, $metric);
        $this->report->addRuleViolation($ruleViolation);
    }

    /**
     * 该方法应该实现具体的违规分析算法
     * 规则实现。 所有的扩展类都必须实现这个方法。
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    abstract public function apply(AbstractNode $node);
}
