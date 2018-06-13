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

namespace PHPMD\TextUI;

use PHPMD\PHPMD;
use PHPMD\RuleSetFactory;
use PHPMD\Writer\StreamWriter;

/**
 * 这个类为PHPMD提供了一个命令行界面
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Command
{
    /**
     * Exit codes used by the phpmd command line tool.
     */
    const EXIT_SUCCESS   = 0,
          EXIT_EXCEPTION = 1,
          EXIT_VIOLATION = 2;

    /**
     *此方法创建一个PHPMD实例并基于此配置此对象
     *在用户的输入上，然后开始源分析。
     *
     *此方法的返回值可以用作退出代码。 一个值
     *等于<b> EXIT_SUCCESS </ b>表示没有违规或错误
     *在分析的代码中找到。 否则，此方法将返回一个值
     *等于<b> EXIT_VIOLATION </ b>。
     *
     *使用flag <b> - ignore-violations-on-exit </ b>会导致a
     * <b> EXIT_SUCCESS </ b>，即使发现任何违规行为。
     *
     * @param \PHPMD\TextUI\CommandLineOptions $opts
     * @param \PHPMD\RuleSetFactory $ruleSetFactory
     * @return integer
     */
    public function run(CommandLineOptions $opts, RuleSetFactory $ruleSetFactory)
    {
        if ($opts->hasVersion()) {
            fwrite(STDOUT, sprintf('PHPMD %s', $this->getVersion()) . PHP_EOL);
            return self::EXIT_SUCCESS;
        }

        // 创建一个报告流
        $stream = $opts->getReportFile() ? fopen($opts->getReportFile(), 'wb') : STDOUT;

        // 创建渲染器并配置输出
        $renderer = $opts->createRenderer();
        $renderer->setWriter(new StreamWriter($stream));

        $renderers = array($renderer);

        foreach ($opts->getReportFiles() as $reportFormat => $reportFile) {
            $reportRenderer = $opts->createRenderer($reportFormat);
            $reportRenderer->setWriter(new StreamWriter(fopen($reportFile, 'wb')));
            $renderers[] = $reportRenderer;
        }

        // 配置规则集工厂
        $ruleSetFactory->setMinimumPriority($opts->getMinimumPriority());
        if ($opts->hasStrict()) {
            $ruleSetFactory->setStrict();
        }

        $phpmd = new PHPMD();
        $phpmd->setOptions(
            array_filter(
                array(
                    'coverage' => $opts->getCoverageReport()
                )
            )
        );

        $extensions = $opts->getExtensions();

        if ($extensions !== null) {
            $phpmd->setFileExtensions(explode(',', $extensions));
        }

        $ignore = $opts->getIgnore();
        if ($ignore !== null) {
            $phpmd->setIgnorePattern(explode(',', $ignore));
        }

        $phpmd->processFiles(
            $opts->getInputPath(),
            $opts->getRuleSets(),
            $renderers,
            $ruleSetFactory
        );

        if ($phpmd->hasViolations() && !$opts->ignoreViolationsOnExit()) {
            return self::EXIT_VIOLATION;
        }
        return self::EXIT_SUCCESS;
    }

    /**
     * 返回当前版本号。
     *
     * @return string
     */
    private function getVersion()
    {
        $build = __DIR__ . '/../../../../../build.properties';

        $version = '@package_version@';
        if (file_exists($build)) {
            $data = @parse_ini_file($build);
            $version = $data['project.version'];
        }
        return $version;
    }

    /**
     * 调用shell脚本可以使用的主要方法返回
     * 值可以用作退出代码。
     *
     * @param array $args The raw command line arguments array.
     *
     * @return integer
     */
    public static function main(array $args)
    {
        try {
            $ruleSetFactory = new RuleSetFactory();
            $options = new CommandLineOptions($args, $ruleSetFactory->listAvailableRuleSets());
            $command = new Command();
            $exitCode = $command->run($options, $ruleSetFactory);
        } catch (\Exception $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            $exitCode = self::EXIT_EXCEPTION;
        }
        return $exitCode;
    }
}
