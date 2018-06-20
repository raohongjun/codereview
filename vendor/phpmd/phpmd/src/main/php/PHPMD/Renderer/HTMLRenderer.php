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

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\TextUI\Command;
/**
 * This renderer output a simple html file with all found violations and suspect
 * software artifacts.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class HTMLRenderer extends AbstractRenderer
{
    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     *
     * @return void
     */
    public function start()
    {
        $writer = $this->getWriter();

        $writer->write('<html><head><meta charset="utf-8"><title>PHP代码报告</title></head><body>');
        $writer->write(PHP_EOL);
        $writer->write('<center><h1>'.$this->folder.'项目分析报告</h1></center>');
        $writer->write(PHP_EOL);
        $writer->write('<table  border="1" align="center" cellspacing="0" cellpadding="3">' );
        $writer->write('<tr>');
        $writer->write('<th>序号</th><th>文件名</th><th>代码位置</th><th>所有者</th><th>问题描述</th>');
        $writer->write('</tr>');
        $writer->write(PHP_EOL);
    }

    /**
     * 该方法将在引擎完成源分析时调用
     * phase.
     *
     * @param \PHPMD\Report $report
     * @return void
     */
    public function renderReport(Report $report)
    {
        $index = 0;

        $writer = $this->getWriter();
        foreach ($report->getRuleViolations() as $violation) {
            $patharray=explode($this->folder,$violation->getFileName());
            $path=current($patharray).$this->folder;
            $filename='.'.end($patharray);
            $match = $this->gitMassge($path, $filename, $violation);

            $writer->write('<tr');
            if (++$index % 2 === 1) {
                $writer->write(' bgcolor="lightgrey"');
            }
            $writer->write('>');
            $writer->write(PHP_EOL);
            //序号
            $writer->write('<td align="center">');
            $writer->write($index);
            $writer->write('</td>');
            $writer->write(PHP_EOL);
            //文件名
            $writer->write('<td>');
            $writer->write(htmlentities($filename));
            $writer->write('</td>');
            $writer->write(PHP_EOL);
            //代码行号
            $writer->write('<td align="center" width="5%">');
            $writer->write($violation->getBeginLine());
            $writer->write('</td>');
            $writer->write(PHP_EOL);
            //所有者
            $writer->write('<td align="center" width="12%">');
            $writer->write($match[0]);
            $writer->write('</td>');
            $writer->write(PHP_EOL);
            //问题描述
            $writer->write('<td>');
            //添加跳转url
//            if ($violation->getRule()->getExternalInfoUrl()) {
//                $writer->write('<a href="');
//                $writer->write($violation->getRule()->getExternalInfoUrl());
//                $writer->write('">');
//            }
            $writer->write(htmlentities($violation->getDescription()));
//            if ($violation->getRule()->getExternalInfoUrl()) {
//                $writer->write('</a>');
//            }

            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('</tr>');
            $writer->write(PHP_EOL);
        }

        $writer->write('</table>');
        $this->glomProcessingErrors($report);
    }

    /**
     * 此方法将被称为引擎已完成报告处理
     * for all registered renderers.
     *
     * @return void
     */
    public function end()
    {
        $writer = $this->getWriter();
        $writer->write('</body></html>');
    }

    /**
     * 此方法将呈现出现处理错误的html表格。
     *
     * @param \PHPMD\Report $report
     * @return void
     * @since 1.2.1
     */
    private function glomProcessingErrors(Report $report)
    {
        if (false === $report->hasErrors()) {
            return;
        }

        $writer = $this->getWriter();

        $writer->write('<hr />');
        $writer->write('<center><h3>Processing errors</h3></center>');
        $writer->write('<table align="center" cellspacing="0" cellpadding="3">');
        $writer->write('<tr><th>File</th><th>Problem</th></tr>');

        $index = 0;
        foreach ($report->getErrors() as $error) {
            $writer->write('<tr');
            if (++$index % 2 === 1) {
                $writer->write(' bgcolor="lightgrey"');
            }
            $writer->write('>');
            $writer->write('<td>' . $error->getFile() . '</td>');
            $writer->write('<td>' . htmlentities($error->getMessage()) . '</td>');
            $writer->write('</tr>' . PHP_EOL);
        }

        $writer->write('</table>');
    }

    /**
     * @param $path
     * @param $filename
     * @param $violation
     * @return mixed
     */
    public function gitMassge($path, $filename, $violation)
    {
        $git = new \Codereview\gitCount($path);
        $name = $git->runCommand('cd ' . $path . ' && git blame ' . $filename . '  -L  ' . $violation->getBeginLine() . ',' . $violation->getBeginLine());
        preg_match("/\([\s\S].+?\)/", $name, $match);
        return $match;
    }
}
