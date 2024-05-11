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

abstract class MigrateAbstract extends Command
{
    /**
     * @var string
     */
    protected $default_table = "";

    /**
     * @var string
     */
    protected $model = "";

    /**
     * @return string
     */
    public function cmd()
    {
        return $this->default_table;
    }

    /**
     * @return mixed
     */
    abstract protected function stubs();

    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:migrate-' . $this->cmd())
            ->addArgument("table", Argument::OPTIONAL, "Generate database table names", $this->default_table)
            ->setDescription(sprintf('Create %s base table structure', $this->default_table));
    }

    /**
     * @return string
     */
    protected function stubsFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR .
            'stubs' . DIRECTORY_SEPARATOR .
            "sql" . DIRECTORY_SEPARATOR .
            $this->stubs();
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {

        $table = $input->getArgument("table") ?? $this->default_table;

        try {
            $this->app->db->execute(
                str_replace(
                    ['{%table%}'],
                    [$table],
                    file_get_contents($this->stubsFile())
                )
            );
            $output->writeln('<info>' . 'database table: `' . $table . '` created successfully.</info>');

            if ($table != $this->default_table) {
                $output->newLine();
                $output->highlight("Table name change requires new model extends " . $this->model);
                $output->newLine();
            }

        } catch (\Exception $exception) {
            $output->writeln('<error>' . 'database table: `' . $table . '` created error : ' . $exception->getMessage() . ' !</error>');
        }
    }
}
