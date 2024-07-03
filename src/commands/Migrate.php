<?php

/*
 * This file is part of the tp5er/think-auth
 *
 * (c) pkg6 <https://github.com/pkg6>
 *
 * (L) Licensed <https://opensource.org/license/MIT>
 *
 * (A) zhiqiang <https://www.zhiqiang.wang>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace tp5er\think\auth\commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

abstract class Migrate extends Command
{
    /**
     * @var string
     */
    protected $tableName = "";
    /**
     * @var string
     */
    protected $model = "";

    /**
     * @var string
     */
    protected $cmdName = "auth:migrate-";

    /**
     * @return string
     */
    abstract protected function createTableSQLTemp();

    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName($this->cmdName)
            ->addArgument("table", Argument::OPTIONAL, "Generate database table names", $this->tableName)
            ->setDescription(sprintf('Create %s base table structure', $this->tableName));
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {
        $table = $input->getArgument("table") ?? $this->tableName;

        try {
            $this->app->db->execute(
                str_replace(
                    ['{%table%}'],
                    [$table],
                    $this->createTableSQLTemp()
                )
            );
            $output->writeln('<info>' . 'database table: `' . $table . '` created successfully.</info>');

            if ($table != $this->tableName) {
                $output->newLine();
                $output->highlight("Table name change requires new model extends " . $this->model);
                $output->newLine();
            }
        } catch (\Exception $exception) {
            $output->writeln('<error>' . 'database table: `' . $table . '` created error : ' . $exception->getMessage() . ' !</error>');
        }
    }
}
